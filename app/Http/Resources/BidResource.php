<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BidResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'tender_id'    => $this->tender_id,
            'bid_amount'   => (float) $this->bid_amount,
            'notes'        => $this->notes,
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),
        ];
    }
}
