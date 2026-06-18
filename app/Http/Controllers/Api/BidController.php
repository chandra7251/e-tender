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

    // Fungsi buat ngambil data bid/penawaran harga yang udah dikirim sama vendor yang lagi login
    public function myBid(Tender $tender): JsonResponse
    {
        $vendor = auth('api')->user()->vendor;

        $bid = Bid::where('tender_id', $tender->id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (!$bid) {
            return $this->error('Anda belum mengajukan bid pada tender ini.', null, 404);
        }

        return $this->success(new BidResource($bid));
    }

    // Fungsi buat ngajuin/submit penawaran harga baru ke tender tertentu
    public function store(BidRequest $request, Tender $tender): JsonResponse
    {
        $vendor = auth('api')->user()->vendor;

        try {
            // Cek dulu vendornya boleh ikutan nge-bid ga, misal udah diapprove atau belum
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

    // Fungsi buat ngubah nilai penawaran harga (kalo misalkan salah masukin angka)
    public function update(BidRequest $request, Tender $tender, Bid $bid): JsonResponse
    {
        $vendor = auth('api')->user()->vendor;

        // Cek dulu beneran bid punya dia apa bukan, jangan sampe ngedit bid orang lain wkwk
        if ($bid->vendor_id !== $vendor->id || $bid->tender_id !== $tender->id) {
            return $this->error('Bid tidak ditemukan.', null, 404);
        }

        try {
            // Cek ulang sapa tau vendornya udah ga boleh ikutan lagi pas mau ngedit
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

