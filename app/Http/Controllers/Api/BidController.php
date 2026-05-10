<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BidRequest;
use App\Http\Resources\BidResource;
use App\Http\Traits\ApiResponse;
use App\Models\Bid;
use App\Models\Tender;
use App\Services\BiddingService;
use Illuminate\Http\JsonResponse;

class BidController extends Controller
{
    use ApiResponse;

    public function __construct(protected BiddingService $biddingService) {}

    /** GET /api/tenders/{tender}/bids/me */
    public function myBid(Tender $tender): JsonResponse
    {
        $vendor = auth()->user()->vendor;

        $bid = Bid::where('tender_id', $tender->id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (!$bid) {
            return $this->error('Anda belum mengajukan bid pada tender ini.', null, 404);
        }

        return $this->success(new BidResource($bid));
    }

    /** POST /api/tenders/{tender}/bids */
    public function store(BidRequest $request, Tender $tender): JsonResponse
    {
        $vendor = auth()->user()->vendor;

        // Guard: already has a bid — should use update instead
        $existing = Bid::where('tender_id', $tender->id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if ($existing) {
            return $this->error(
                'Anda sudah memiliki bid. Gunakan endpoint update untuk mengubah bid.',
                null, 422
            );
        }

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

    /** PUT /api/tenders/{tender}/bids/{bid} */
    public function update(BidRequest $request, Tender $tender, Bid $bid): JsonResponse
    {
        $vendor = auth()->user()->vendor;

        // Guard: vendor can only update their own bid
        if ($bid->vendor_id !== $vendor->id || $bid->tender_id !== $tender->id) {
            return $this->error('Bid tidak ditemukan.', null, 404);
        }

        try {
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
