<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderResource extends JsonResource
{
    /**
     * Opsional: flag is_participant bisa di-inject dari luar oleh controller
     * untuk menghindari N+1 query pada list tender.
     * Jika tidak di-set, akan di-query langsung (hanya cocok untuk single-tender endpoint).
     */
    public ?bool $isParticipantOverride = null;

    // Factory method untuk set flag dari luar tanpa query ke DB
    public function withParticipantStatus(bool $value): static
    {
        $this->isParticipantOverride = $value;
        return $this;
    }

    public function toArray(Request $request): array
    {
        // Jika is_participant sudah di-inject controller (list view), pakai itu langsung.
        // Jika tidak (single detail view), query sekali ke DB — tetap efisien untuk 1 tender.
        if ($this->isParticipantOverride !== null) {
            $isParticipant = $this->isParticipantOverride;
        } else {
            // Untuk route detail individual (GET /api/tenders/{id}), cek ke DB langsung.
            // Gunakan auth('api') bukan $request->user() karena route ini public
            // dan $request->user() selalu null tanpa middleware auth:api.
            $vendor        = auth('api')->user()?->vendor;
            $isParticipant = $vendor
                ? $this->participants()->where('vendor_id', $vendor->id)->exists()
                : false;
        }

        return [
            'id'                 => $this->id,
            'title'              => $this->title,
            'description'        => $this->description,
            'specification'      => $this->specification,
            'open_bidding_price' => $this->open_bidding_price,
            // Ambil URL foto pertama saja — foto sudah di-eager load via with('photos')
            'photo_url'          => $this->photos->first()?->photo_url,
            'photos'             => $this->photos->map(fn($photo) => $photo->photo_url)->values()->toArray(),
            'status'             => $this->status,
            'start_date'         => $this->start_date?->toIso8601String(),
            'end_date'           => $this->end_date?->toIso8601String(),
            'aanwijzing_date'    => $this->aanwijzing_date?->toIso8601String(),
            'bidding_start'      => $this->bidding_start?->toIso8601String(),
            'bidding_end'        => $this->bidding_end?->toIso8601String(),
            'is_participant'     => $isParticipant,
            'created_at'         => $this->created_at?->toIso8601String(),
        ];
    }
}
