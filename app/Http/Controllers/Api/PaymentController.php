<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
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
        $summary = $this->payment->getTenderPayments($tenderId);
        return $this->success($summary);
    }

    /** Refund deposit vendor yang kalah */
    public function refundDeposit(int $paymentId): JsonResponse
    {
        $result = $this->payment->refundDeposit($paymentId);
        return $result['success']
            ? $this->success(null, $result['message'])
            : $this->error($result['message'], null, 422);
    }

    /** Client key untuk Snap JS di frontend */
    public function clientKey(): JsonResponse
    {
        return $this->success(['client_key' => $this->payment->getClientKey()]);
    }
}
