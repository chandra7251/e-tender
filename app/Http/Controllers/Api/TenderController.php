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

    // Tampilkan daftar tender yang bisa dilihat vendor (semua selain draft)
    public function index(Request $request): JsonResponse
    {
        // Status yang boleh dilihat vendor — 'draft' disembunyikan dari publik
        $allowedStatuses = ['open', 'aanwijzing', 'bidding', 'closed', 'finished'];

        $query = Tender::query()
            ->with('photos') // Eager-load foto agar tidak ada lazy load di Resource
            ->whereIn('status', $allowedStatuses)
            // Filter opsional berdasarkan status spesifik
            ->when($request->input('status'), function ($q, $s) use ($allowedStatuses) {
                if (in_array($s, $allowedStatuses)) {
                    $q->where('status', $s);
                }
            })
            // Pencarian teks di judul atau deskripsi
            ->when($request->input('search'), fn ($q, $s) =>
                $q->where(function ($q2) use ($s) {
                    $q2->where('title', 'like', "%{$s}%")
                       ->orWhere('description', 'like', "%{$s}%");
                })
            )
            ->orderByDesc('created_at');

        $tenders = $query->limit(100)->get();

        // Fix N+1: Pre-load ID tender yang diikuti vendor yang login dalam SATU query
        // Tanpa ini, setiap TenderResource akan query DB sendiri untuk is_participant
        $participantTenderIds = $this->getParticipantTenderIds(
            $tenders->pluck('id')
        );

        // Map setiap tender ke Resource dan inject status kepesertaan yang sudah di-pre-load
        $data = $tenders->map(function ($tender) use ($participantTenderIds) {
            return (new TenderResource($tender))
                ->withParticipantStatus($participantTenderIds->contains($tender->id));
        });

        // Response flat array — mobile melakukan filter & pagination sendiri (client-side)
        return $this->success($data->values());
    }

    // Tampilkan detail satu tender secara spesifik
    public function show(Tender $tender): JsonResponse
    {
        // Draft tidak boleh diakses vendor — return 404 agar tidak bocor info draft
        if ($tender->status === 'draft') {
            return $this->error('Tender tidak ditemukan.', null, 200);
        }

        // Untuk detail individual, is_participant di-query langsung di Resource (1 tender = 1 query)
        return $this->success(new TenderResource($tender->load('photos')));
    }

    // Helper privat: ambil kumpulan tender_id yang diikuti vendor yang login dalam 1 query
    private function getParticipantTenderIds(Collection $tenderIds): Collection
    {
        $vendor = auth('api')->user()?->vendor;

        // Jika tidak login atau tidak punya vendor record, return collection kosong
        if (!$vendor || $tenderIds->isEmpty()) {
            return collect();
        }

        // Query tunggal ke DB: ambil semua tender_id yang vendor ini ikut dari list halaman ini
        return TenderParticipant::where('vendor_id', $vendor->id)
            ->whereIn('tender_id', $tenderIds)
            ->pluck('tender_id');
    }
}
