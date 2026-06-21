<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BidRequest;
use App\Http\Resources\BidResource;
use App\Http\Traits\ApiResponse;
use App\Models\Bid;
use App\Models\Tender;
use App\Models\Vendor;
use App\Services\BiddingService;
use Illuminate\Http\JsonResponse;
class BidController extends Controller
{
    use ApiResponse;
    public function __construct(protected BiddingService $biddingService) {}
    private function resolveVendor(): Vendor|JsonResponse
    {
        $vendor = auth('api')->user()?->vendor;
        if (!$vendor) {
            return $this->error(
                'Profil vendor tidak ditemukan. Silakan lengkapi profil Anda terlebih dahulu.',
                null,
                403
            );
        }
        return $vendor;
    }
    public function myBid(Tender $tender): JsonResponse
    {
        $vendor = $this->resolveVendor();
        if ($vendor instanceof JsonResponse) return $vendor;
        $bid = Bid::where('tender_id', $tender->id)
            ->where('vendor_id', $vendor->id)
            ->first();
        if (!$bid) {
            return $this->error('Anda belum mengajukan bid pada tender ini.', null, 200);
        }
        return $this->success(new BidResource($bid));
    }
    public function store(BidRequest $request, Tender $tender): JsonResponse
    {
        $vendor = $this->resolveVendor();
        if ($vendor instanceof JsonResponse) return $vendor;
        try {
            $this->biddingService->assertVendorCanBid($vendor, $tender);
            $this->biddingService->assertBiddingOpen($tender);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, $e->getCode() ?: 422);
        }
        $bid = $this->biddingService->submitBid(
            $vendor,
            $tender,
            (float) $request->input('bid_amount'),
            $request->input('notes')
        );
        return $this->created(new BidResource($bid), 'Bid berhasil diajukan.');
    }
    public function update(BidRequest $request, Tender $tender, Bid $bid): JsonResponse
    {
        $vendor = $this->resolveVendor();
        if ($vendor instanceof JsonResponse) return $vendor;
        if ($bid->vendor_id !== $vendor->id || $bid->tender_id !== $tender->id) {
            return $this->error('Bid tidak ditemukan.', null, 200);
        }
        try {
            $this->biddingService->assertVendorCanBid($vendor, $tender);
            $this->biddingService->assertBiddingOpen($tender);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, $e->getCode() ?: 422);
        }
        $bid = $this->biddingService->updateBid(
            $bid,
            $vendor,
            $tender,
            (float) $request->input('bid_amount'),
            $request->input('notes')
        );
        return $this->success(new BidResource($bid), 'Bid berhasil diperbarui.');
    }
}
