<?php

namespace App\Services;

use App\Models\TenderPayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Payment Gateway Service — Midtrans Integration
 * Handles deposit jaminan, invoice payment, escrow release, dan refund.
 */
class PaymentGatewayService
{
    private string $serverKey;
    private string $clientKey;
    private string $baseUrl;
    private bool   $isProduction;

    public function __construct()
    {
        $this->isProduction = config('services.midtrans.is_production', false);
        $this->serverKey    = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY', ''));
        $this->clientKey    = config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY', ''));
        $this->baseUrl      = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1'
            : 'https://app.sandbox.midtrans.com/snap/v1';
    }

    /**
     * Create payment for deposit jaminan peserta tender.
     * Vendor membayar deposit sebelum bisa submit penawaran.
     */
    public function createDepositPayment(int $tenderId, int $vendorId, float $depositAmount, array $vendorInfo): array
    {
        $orderId = 'DEPOSIT-' . $tenderId . '-' . $vendorId . '-' . time();

        $payload = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $depositAmount,
            ],
            'customer_details' => [
                'first_name' => $vendorInfo['name']     ?? 'Vendor',
                'email'      => $vendorInfo['email']    ?? 'vendor@zeta.id',
                'phone'      => $vendorInfo['phone']    ?? '08000000000',
            ],
            'item_details' => [[
                'id'       => 'DEPOSIT-JAMINAN',
                'price'    => (int) $depositAmount,
                'quantity' => 1,
                'name'     => 'Deposit Jaminan Tender #' . $tenderId,
            ]],
            'callbacks' => [
                'finish'    => config('app.url') . '/payment/finish',
                'error'     => config('app.url') . '/payment/error',
                'pending'   => config('app.url') . '/payment/pending',
            ],
        ];

        $response = $this->callMidtrans('/transactions', $payload);

        if ($response['success']) {
            // Simpan ke DB
            TenderPayment::create([
                'tender_id'    => $tenderId,
                'vendor_id'    => $vendorId,
                'order_id'     => $orderId,
                'type'         => 'deposit',
                'amount'       => $depositAmount,
                'status'       => 'pending',
                'snap_token'   => $response['data']['token'] ?? null,
                'snap_url'     => $response['data']['redirect_url'] ?? null,
            ]);
        }

        return $response;
    }

    /**
     * Create payment for contract invoice (instansi membayar ke vendor via escrow).
     */
    public function createContractPayment(int $contractId, float $amount, array $details): array
    {
        $orderId = 'CONTRACT-' . $contractId . '-' . time();

        $payload = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $amount,
            ],
            'customer_details' => [
                'first_name' => $details['instansi_name'] ?? 'Instansi',
                'email'      => $details['instansi_email'] ?? 'instansi@zeta.id',
            ],
            'item_details' => [[
                'id'       => 'CONTRACT-PAYMENT',
                'price'    => (int) $amount,
                'quantity' => 1,
                'name'     => 'Pembayaran Kontrak #' . $details['contract_number'],
            ]],
        ];

        return $this->callMidtrans('/transactions', $payload);
    }

    /**
     * Handle Midtrans payment notification (webhook callback).
     * Validates signature and updates payment status.
     */
    public function handleNotification(array $notification): array
    {
        // Validate signature key dari Midtrans
        $signatureKey = hash('sha512',
            $notification['order_id'] .
            $notification['status_code'] .
            $notification['gross_amount'] .
            $this->serverKey
        );

        if ($signatureKey !== $notification['signature_key']) {
            return ['success' => false, 'message' => 'Invalid signature'];
        }

        $orderId       = $notification['order_id'];
        $transactionStatus = $notification['transaction_status'];
        $fraudStatus   = $notification['fraud_status'] ?? 'accept';

        $payment = TenderPayment::where('order_id', $orderId)->first();
        if (!$payment) return ['success' => false, 'message' => 'Order tidak ditemukan'];

        // Map Midtrans status
        $newStatus = match(true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'paid',
            $transactionStatus === 'settlement'                           => 'paid',
            $transactionStatus === 'pending'                              => 'pending',
            in_array($transactionStatus, ['deny','expire','cancel'])      => 'failed',
            $transactionStatus === 'refund'                              => 'refunded',
            default                                                       => 'pending',
        };

        $payment->update([
            'status'        => $newStatus,
            'midtrans_data' => json_encode($notification),
            'paid_at'       => $newStatus === 'paid' ? now() : null,
        ]);

        // Jika deposit paid → update vendor permission di tender
        if ($newStatus === 'paid' && $payment->type === 'deposit') {
            $this->onDepositPaid($payment);
        }

        return ['success' => true, 'status' => $newStatus, 'order_id' => $orderId];
    }

    /**
     * Refund deposit to vendor who did not win.
     */
    public function refundDeposit(int $paymentId): array
    {
        $payment = TenderPayment::findOrFail($paymentId);
        if ($payment->status !== 'paid') {
            return ['success' => false, 'message' => 'Hanya pembayaran yang sudah lunas yang bisa direfund'];
        }

        $refundPayload = [
            'refund_key' => 'REFUND-' . $payment->order_id,
            'amount'     => (int) $payment->amount,
            'reason'     => 'Deposit dikembalikan - vendor tidak memenangkan tender',
        ];

        // Midtrans Refund API
        $response = Http::withBasicAuth($this->serverKey, '')
            ->post("https://api.sandbox.midtrans.com/v2/{$payment->order_id}/refund", $refundPayload);

        if ($response->successful()) {
            $payment->update(['status' => 'refunded', 'refunded_at' => now()]);
            return ['success' => true, 'message' => 'Refund berhasil diproses'];
        }

        return ['success' => false, 'message' => 'Gagal memproses refund: ' . $response->body()];
    }

    /**
     * Get Snap client key for frontend.
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    /**
     * Get payment status summary for a tender.
     */
    public function getTenderPayments(int $tenderId): array
    {
        $payments = TenderPayment::with('vendor')->where('tender_id', $tenderId)->get();
        return [
            'total_deposits'   => $payments->where('type', 'deposit')->where('status', 'paid')->count(),
            'total_amount'     => $payments->where('status', 'paid')->sum('amount'),
            'pending_count'    => $payments->where('status', 'pending')->count(),
            'refunded_count'   => $payments->where('status', 'refunded')->count(),
            'payments'         => $payments->toArray(),
        ];
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function callMidtrans(string $endpoint, array $payload): array
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->timeout(15)
                ->post($this->baseUrl . $endpoint, $payload);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error('Midtrans error: ' . $response->body());
            return ['success' => false, 'message' => $response->json('error_messages.0') ?? 'Midtrans error'];
        } catch (\Throwable $e) {
            Log::error('Midtrans exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Koneksi ke payment gateway gagal.'];
        }
    }

    private function onDepositPaid(TenderPayment $payment): void
    {
        // Update TenderBid atau flag bahwa vendor sudah bayar deposit
        \App\Models\TenderBid::where('tender_id', $payment->tender_id)
            ->where('vendor_id', $payment->vendor_id)
            ->update(['deposit_paid' => true]);
    }
}
