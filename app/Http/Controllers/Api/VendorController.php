<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TenderResource;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class VendorController extends Controller
{
    use ApiResponse;

    // Fungsi buat nampilin tender apa aja yang diikutin sama si vendor ini
    public function myTenders(): JsonResponse
    {
        $vendor = auth('api')->user()?->vendor;

        if (!$vendor) {
            return $this->success([], 'Tidak ada tender karena profil vendor tidak ditemukan.');
        }

        // Query buat nyari tender yang ada id vendor ini di tabel partisipannya
        $tenders = \App\Models\Tender::whereHas('participants', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })
            ->where('status', '!=', 'draft')
            ->with(['participants' => function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id)->select('tender_id', 'vendor_id', 'joined_at');
            }])
            ->orderByDesc('created_at')
            ->get();

        // Kita tambahin data 'joined_at' manual ke response JSON-nya
        $data = $tenders->map(function ($tender) {
            $resource = (new TenderResource($tender))->toArray(request());
            $resource['joined_at'] = $tender->participants->first()?->joined_at?->toIso8601String();
            return $resource;
        });

        return $this->success($data, 'Daftar tender yang diikuti berhasil dimuat.');
    }

    // Fungsi buat liat hasil pengumuman menang/kalah di tender yang diikutin
    public function myResults(): JsonResponse
    {
        $vendor = auth('api')->user()?->vendor;

        if (!$vendor) {
            return $this->success([], 'Tidak ada hasil karena profil vendor tidak ditemukan.');
        }

        // Cuma ambil tender yang udah kelar (closed/finished)
        $tenders = \App\Models\Tender::whereHas('participants', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })
            ->whereIn('status', ['closed', 'finished'])
            ->with([
                'result.winner',
                'bids' => fn($q) => $q->where('vendor_id', $vendor->id),
            ])
            ->orderByDesc('created_at')
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

