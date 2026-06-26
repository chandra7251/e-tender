<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\TenderResultResource;
use App\Http\Traits\ApiResponse;
use App\Models\Bid;
use App\Models\Tender;
use Illuminate\Http\JsonResponse;
class TenderResultController extends Controller
{
    use ApiResponse;
    public function show(Tender $tender): JsonResponse
    {
        $result = $tender->result()->with(['winner'])->first();
        if (!$result) {
            return $this->error('Hasil tender belum tersedia.', null, 404);
        }
        return $this->success(new TenderResultResource($result));
    }
    public function winner(Tender $tender): JsonResponse
    {
        $result = $tender->result()->with(['winner'])->first();
        if (!$result) {
            return $this->error('Pemenang belum ditentukan.', null, 404);
        }
        $vendor   = auth('api')->user()?->vendor;
        $isWinner = $vendor && ($result->winner_vendor_id === $vendor->id);
        $myBid = $vendor
            ? Bid::where('tender_id', $tender->id)
                ->where('vendor_id', $vendor->id)
                ->first()
            : null;
        return $this->success([
            'winner_company'     => $result->winner->company_name ?? null,
            'winning_bid_amount' => (float) $result->winning_bid_amount,
            'selection_method'   => $result->selection_method,
            'decided_at'         => $result->decided_at?->toIso8601String(),
            'is_winner'          => $isWinner,
            'my_bid_amount'      => $myBid ? (float) $myBid->bid_amount : null,
        ]);
    }
}
