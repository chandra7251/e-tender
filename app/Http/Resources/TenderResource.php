<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'description'     => $this->description,
            'specification'   => $this->specification,
            'status'          => $this->status,
            'start_date'      => $this->start_date?->toIso8601String(),
            'end_date'        => $this->end_date?->toIso8601String(),
            'aanwijzing_date' => $this->aanwijzing_date?->toIso8601String(),
            'bidding_start'   => $this->bidding_start?->toIso8601String(),
            'bidding_end'     => $this->bidding_end?->toIso8601String(),
            'created_at'      => $this->created_at?->toIso8601String(),
        ];
    }
}
