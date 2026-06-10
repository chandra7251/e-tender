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

    /** Get my bid */
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

    /** Submit bid */
    public function store(BidRequest $request, Tender $tender): JsonResponse
    {
        $vendor = auth()->user()->vendor;

        try {
            // Validasi kelayakan vendor
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

    /** Update bid */
    public function update(BidRequest $request, Tender $tender, Bid $bid): JsonResponse
    {
        $vendor = auth()->user()->vendor;

        // Validasi kepemilikan bid
        if ($bid->vendor_id !== $vendor->id || $bid->tender_id !== $tender->id) {
            return $this->error('Bid tidak ditemukan.', null, 404);
        }

        try {
            // Validasi status vendor
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
