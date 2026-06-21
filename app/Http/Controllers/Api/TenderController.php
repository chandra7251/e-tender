<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\TenderResource;
use App\Http\Traits\ApiResponse;
use App\Models\Tender;
use App\Models\TenderParticipant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
class TenderController extends Controller
{
    use ApiResponse;
    public function index(Request $request): JsonResponse
    {
        $allowedStatuses = ['open', 'aanwijzing', 'bidding', 'closed', 'finished'];
        $query = Tender::query()
            ->with('photos') 
            ->whereIn('status', $allowedStatuses)
            ->when($request->input('status'), function ($q, $s) use ($allowedStatuses) {
                if (in_array($s, $allowedStatuses)) {
                    $q->where('status', $s);
                }
            })
            ->when($request->input('search'), fn ($q, $s) =>
                $q->where(function ($q2) use ($s) {
                    $q2->where('title', 'like', "%{$s}%")
                       ->orWhere('description', 'like', "%{$s}%");
                })
            )
            ->orderByDesc('created_at');
        $tenders = $query->limit(100)->get();
        $participantTenderIds = $this->getParticipantTenderIds(
            $tenders->pluck('id')
        );
        $data = $tenders->map(function ($tender) use ($participantTenderIds) {
            return (new TenderResource($tender))
                ->withParticipantStatus($participantTenderIds->contains($tender->id));
        });
        return $this->success($data->values());
    }
    public function show(Tender $tender): JsonResponse
    {
        if ($tender->status === 'draft') {
            return $this->error('Tender tidak ditemukan.', null, 200);
        }
        return $this->success(new TenderResource($tender->load('photos')));
    }
    private function getParticipantTenderIds(Collection $tenderIds): Collection
    {
        $vendor = auth('api')->user()?->vendor;
        if (!$vendor || $tenderIds->isEmpty()) {
            return collect();
        }
        return TenderParticipant::where('vendor_id', $vendor->id)
            ->whereIn('tender_id', $tenderIds)
            ->pluck('tender_id');
    }
}
