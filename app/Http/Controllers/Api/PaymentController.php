<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\TenderPayment;
use App\Models\TenderResult;
use App\Services\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentGatewayService $payment) {}

    /** Buat payment deposit jaminan */
    public function createDeposit(Request $request): JsonResponse
    {
        $request->validate([
            'tender_id'     => 'required|integer|exists:tenders,id',
            'deposit_amount'=> 'required|numeric|min:10000',
        ]);
        $user   = auth('api')->user();
        $vendor = $user->vendor;
        if (!$vendor) return $this->error('Vendor tidak ditemukan.', null, 404);

        $result = $this->payment->createDepositPayment(
            $request->tender_id,
            $vendor->id,
            $request->deposit_amount,
            ['name' => $user->name, 'email' => $user->email, 'phone' => $vendor->phone ?? '']
        );
        return $result['success']
            ? $this->success($result['data'], 'Silakan selesaikan pembayaran.')
            : $this->error($result['message'], null, 502);
    }

    /** Midtrans notification webhook */
    public function notification(Request $request): JsonResponse
    {
        $result = $this->payment->handleNotification($request->all());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /** Status pembayaran untuk tender */
    public function tenderPayments(int $tenderId): JsonResponse
    {
        $actor = auth('api')->user();
        if (in_array($actor->role, ['admin', 'super_admin'], true)) {
            $payments = TenderPayment::where('tender_id', $tenderId)->get();
        } elseif ($actor->role === 'vendor' && $actor->vendor) {
            $payments = TenderPayment::where('tender_id', $tenderId)->where('vendor_id', $actor->vendor->id)->get();
            if ($payments->isEmpty()) return $this->error('Payment tidak ditemukan.', null, 404);
        } else {
            return $this->error('Tidak diizinkan.', null, 403);
        }

        return $this->success([
            'total_deposits' => $payments->where('type', 'deposit')->where('status', 'paid')->count(),
            'total_amount' => $payments->where('status', 'paid')->sum('amount'),
            'pending_count' => $payments->where('status', 'pending')->count(),
            'refunded_count' => $payments->where('status', 'refunded')->count(),
            'payments' => $payments->map(fn (TenderPayment $payment) => $this->paymentSummary($payment))->values(),
        ]);
    }

    /** Refund deposit vendor yang kalah */
    public function refundDeposit(int $paymentId): JsonResponse
    {
        $payment = TenderPayment::find($paymentId);
        if (!$payment) return $this->error('Payment tidak ditemukan.', null, 404);

        $actor = auth('api')->user();
        if (!in_array($actor->role, ['admin', 'super_admin'], true)) return $this->error('Tidak diizinkan.', null, 403);

        $result = TenderResult::where('tender_id', $payment->tender_id)->first();
        if ($payment->type !== 'deposit' || $payment->status !== 'paid' || $payment->refunded_at || !$result || $result->winner_vendor_id === $payment->vendor_id) {
            return $this->error('Payment tidak memenuhi syarat refund.', null, 422);
        }

        $result = $this->payment->refundDeposit($paymentId);
        if ($result['success']) {
            ActivityLog::log(
                action: 'payment_refunded',
                module: 'payment',
                description: "Refund deposit diproses untuk payment #{$payment->id}.",
                userId: $actor->id,
                subjectType: TenderPayment::class,
                subjectId: $payment->id,
                oldValues: ['status' => $payment->status, 'refunded_at' => $payment->refunded_at],
                newValues: ['status' => 'refunded'],
            );
        }
        return $result['success']
            ? $this->success(null, $result['message'])
            : $this->error($result['message'], null, 422);
    }

    /** Client key untuk Snap JS di frontend */
    public function clientKey(): JsonResponse
    {
        return $this->success(['client_key' => $this->payment->getClientKey()]);
    }

    private function paymentSummary(TenderPayment $payment): array
    {
        return [
            'id' => $payment->id,
            'tender_id' => $payment->tender_id,
            'vendor_id' => $payment->vendor_id,
            'order_id' => $payment->order_id,
            'type' => $payment->type,
            'amount' => $payment->amount,
            'status' => $payment->status,
            'paid_at' => $payment->paid_at,
            'refunded_at' => $payment->refunded_at,
            'created_at' => $payment->created_at,
        ];
    }
}
