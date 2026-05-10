<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'tender_id'          => $this->tender_id,
            'winner_company'     => $this->winner?->company_name,
            'winning_bid_amount' => (float) $this->winning_bid_amount,
            'selection_method'   => $this->selection_method,
            'notes'              => $this->notes,
            'decided_at'         => $this->decided_at?->toIso8601String(),
        ];
    }
}
