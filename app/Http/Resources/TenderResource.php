<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Cek apakah vendor yang login adalah peserta tender ini
        $vendor        = $request->user()?->vendor;
        $isParticipant = false;

        if ($vendor) {
            $isParticipant = $this->participants()
                ->where('vendor_id', $vendor->id)
                ->exists();
        }

        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'description'     => $this->description,
            'specification'   => $this->specification,
            'photo_url'       => $this->photo_url,   // null jika belum ada foto
            'status'          => $this->status,
            'start_date'      => $this->start_date?->toIso8601String(),
            'end_date'        => $this->end_date?->toIso8601String(),
            'aanwijzing_date' => $this->aanwijzing_date?->toIso8601String(),
            'bidding_start'   => $this->bidding_start?->toIso8601String(),
            'bidding_end'     => $this->bidding_end?->toIso8601String(),
            'is_participant'  => $isParticipant,
            'created_at'      => $this->created_at?->toIso8601String(),
        ];
    }
}
