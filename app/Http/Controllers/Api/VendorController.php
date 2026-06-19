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

    // Batas maksimum data yang dikembalikan per request untuk mencegah response terlalu besar
    private const MAX_RESULTS = 50;

    // Tampilkan semua tender yang diikuti vendor yang sedang login
    public function myTenders(): JsonResponse
    {
        $vendor = auth('api')->user()?->vendor;

        if (!$vendor) {
            return $this->success([], 'Tidak ada tender karena profil vendor tidak ditemukan.');
        }

        // Cari tender yang vendor ini terdaftar sebagai peserta
        // Batasi 50 hasil terbaru untuk menghindari response yang terlalu besar
        $tenders = Tender::whereHas('participants', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })
            ->where('status', '!=', 'draft') // Sembunyikan draft dari vendor
            ->with([
                'photos', // Eager-load foto untuk menghindari N+1
                // Hanya ambil data join milik vendor ini saja
                'participants' => function ($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id)->select('tender_id', 'vendor_id', 'joined_at');
                },
            ])
            ->orderByDesc('created_at')
            ->limit(self::MAX_RESULTS)
            ->get();

        // Tambahkan joined_at ke setiap item resource
        $data = $tenders->map(function ($tender) {
            $resource              = (new TenderResource($tender))->withParticipantStatus(true)->toArray(request());
            $resource['joined_at'] = $tender->participants->first()?->joined_at?->toIso8601String();
            return $resource;
        });

        return $this->success($data, 'Daftar tender yang diikuti berhasil dimuat.');
    }

    // Tampilkan hasil tender (menang/kalah) untuk tender yang sudah selesai
    public function myResults(): JsonResponse
    {
        $vendor = auth('api')->user()?->vendor;

        if (!$vendor) {
            return $this->success([], 'Tidak ada hasil karena profil vendor tidak ditemukan.');
        }

        // Hanya ambil tender dengan status closed atau finished (sudah ada pemenang)
        // Batasi 50 hasil terbaru
        $tenders = Tender::whereHas('participants', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })
            ->whereIn('status', ['closed', 'finished'])
            ->with([
                'result.winner',                                          // Data pemenang
                'bids' => fn($q) => $q->where('vendor_id', $vendor->id), // Hanya bid milik vendor ini
            ])
            ->orderByDesc('created_at')
            ->limit(self::MAX_RESULTS)
            ->get();

        $data = $tenders->map(function ($tender) use ($vendor) {
            $myBid  = $tender->bids->first();  // Bid vendor ini di tender ini
            $result = $tender->result;

            return [
                'tender_id'          => $tender->id,
                'tender_title'       => $tender->title,
                'tender_status'      => $tender->status,
                // Bandingkan winner_vendor_id dengan id vendor yang login
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
