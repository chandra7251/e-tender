<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\TenderResource;
use App\Http\Traits\ApiResponse;
use App\Models\Tender;
use Illuminate\Http\JsonResponse;
class VendorController extends Controller
{
    use ApiResponse;
    private const MAX_RESULTS = 50;
    public function myTenders(): JsonResponse
    {
        $vendor = auth('api')->user()?->vendor;
        if (!$vendor) {
            return $this->success([], 'Tidak ada tender karena profil vendor tidak ditemukan.');
        }
        $tenders = Tender::whereHas('participants', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })
            ->where('status', '!=', 'draft') 
            ->with([
                'photos', 
                'participants' => function ($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id)->select('tender_id', 'vendor_id', 'joined_at');
                },
            ])
            ->orderByDesc('created_at')
            ->limit(self::MAX_RESULTS)
            ->get();
        $data = $tenders->map(function ($tender) {
            $resource              = (new TenderResource($tender))->withParticipantStatus(true)->toArray(request());
            $resource['joined_at'] = $tender->participants->first()?->joined_at?->toIso8601String();
            return $resource;
        });
        return $this->success($data, 'Daftar tender yang diikuti berhasil dimuat.');
    }
    public function myResults(): JsonResponse
    {
        $vendor = auth('api')->user()?->vendor;
        if (!$vendor) {
            return $this->success([], 'Tidak ada hasil karena profil vendor tidak ditemukan.');
        }
        $tenders = Tender::whereHas('participants', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })
            ->whereIn('status', ['closed', 'finished'])
            ->with([
                'result.winner',                                          
                'bids' => fn($q) => $q->where('vendor_id', $vendor->id), 
            ])
            ->orderByDesc('created_at')
            ->limit(self::MAX_RESULTS)
            ->get();
        $data = $tenders->map(function ($tender) use ($vendor) {
            $myBid  = $tender->bids->first();  
            $result = $tender->result;
            return [
                'tender_id'          => $tender->id,
                'tender_title'       => $tender->title,
                'tender_status'      => $tender->status,
                'is_winner'          => $result?->winner_vendor_id === $vendor->id,
                'my_bid_amount'      => $myBid?->bid_amount,
                'winner_company'     => $result?->winner?->company_name,
                'winning_bid_amount' => $result?->winning_bid_amount,
                'decided_at'         => $result?->decided_at?->toIso8601String(),
            ];
        });
        return $this->success($data, 'Hasil tender berhasil dimuat.');
    }
}
