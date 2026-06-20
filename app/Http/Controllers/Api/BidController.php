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

    // Helper privat: ambil vendor dari user yang login dan return error jika tidak ada
    // Diekstrak karena semua method di sini membutuhkan logika yang sama
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

    // Ambil data bid milik vendor yang login pada tender tertentu
    public function myBid(Tender $tender): JsonResponse
    {
        $vendor = $this->resolveVendor();
        // Jika resolveVendor mengembalikan JsonResponse, berarti ada error
        if ($vendor instanceof JsonResponse) return $vendor;

        $bid = Bid::where('tender_id', $tender->id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (!$bid) {
            return $this->error('Anda belum mengajukan bid pada tender ini.', null, 200);
        }

        return $this->success(new BidResource($bid));
    }

    // Kirim penawaran baru ke tender tertentu
    public function store(BidRequest $request, Tender $tender): JsonResponse
    {
        $vendor = $this->resolveVendor();
        if ($vendor instanceof JsonResponse) return $vendor;

        try {
            // Validasi: vendor harus sudah diapprove dan terdaftar di tender
            $this->biddingService->assertVendorCanBid($vendor, $tender);
            // Validasi: tender harus sedang dalam periode bidding aktif
            $this->biddingService->assertBiddingOpen($tender);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, $e->getCode() ?: 422);
        }

        // Proses submit bid dengan transaction + lock untuk cegah race condition
        $bid = $this->biddingService->submitBid(
            $vendor,
            $tender,
            (float) $request->input('bid_amount'),
            $request->input('notes')
        );

        return $this->created(new BidResource($bid), 'Bid berhasil diajukan.');
    }

    // Perbarui nilai penawaran yang sudah ada
    public function update(BidRequest $request, Tender $tender, Bid $bid): JsonResponse
    {
        $vendor = $this->resolveVendor();
        if ($vendor instanceof JsonResponse) return $vendor;

        // Pastikan bid memang milik vendor ini di tender yang benar — cegah edit bid orang lain
        if ($bid->vendor_id !== $vendor->id || $bid->tender_id !== $tender->id) {
            return $this->error('Bid tidak ditemukan.', null, 200);
        }

        try {
            // Re-validasi: pastikan kondisi masih memenuhi syarat saat update
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
