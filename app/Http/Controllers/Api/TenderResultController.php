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

    // Tampilkan hasil tender (pemenang, metode seleksi, dll)
    // Dapat diakses semua vendor yang sudah login — transparansi pengadaan
    public function show(Tender $tender): JsonResponse
    {
        // Eager-load relasi winner untuk menghindari lazy-load di dalam Resource
        $result = $tender->result()->with(['winner'])->first();

        if (!$result) {
            return $this->error('Hasil tender belum tersedia.', null, 404);
        }

        return $this->success(new TenderResultResource($result));
    }

    // Tampilkan info pemenang + perbandingan bid vendor yang login
    public function winner(Tender $tender): JsonResponse
    {
        // Eager-load winner agar tidak ada query tambahan saat akses $result->winner
        $result = $tender->result()->with(['winner'])->first();

        if (!$result) {
            return $this->error('Pemenang belum ditentukan.', null, 404);
        }

        // Vendor bisa null jika token valid tapi vendor record tidak ada (edge case)
        $vendor   = auth('api')->user()?->vendor;
        $isWinner = $vendor && ($result->winner_vendor_id === $vendor->id);

        // Ambil bid milik vendor yang login — null jika vendor tidak ada atau belum bid
        // Null check eksplisit untuk mencegah crash ketika $vendor null
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
            // Null jika vendor tidak ada atau belum pernah bid di tender ini
            'my_bid_amount'      => $myBid ? (float) $myBid->bid_amount : null,
        ]);
    }
}
