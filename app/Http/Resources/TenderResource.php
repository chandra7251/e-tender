<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderResource extends JsonResource
{

    public ?bool $isParticipantOverride = null;

    // Factory method untuk set flag dari luar tanpa query ke DB
    public function withParticipantStatus(bool $value): static
    {
        $this->isParticipantOverride = $value;
        return $this;
    }

    public function toArray(Request $request): array
    {
        if ($this->isParticipantOverride !== null) {
            $isParticipant = $this->isParticipantOverride;
        } else {

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
