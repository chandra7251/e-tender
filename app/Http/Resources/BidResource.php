<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BidResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'ulid'             => $this->ulid,
            'tender_id'        => $this->tender_id,
            'vendor_id'        => $this->vendor_id,
            'bid_amount'       => (float) $this->bid_amount,
            'notes'            => $this->notes,
            'technical_status' => $this->technical_status,
            'technical_score'  => $this->technical_score,
            'price_score'      => $this->price_score,
            'submitted_at'     => $this->submitted_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),
            // BQ items - harga per item yang diajukan vendor
            'bid_items'        => $this->whenLoaded('bidItems', fn () =>
                $this->bidItems->map(fn ($bi) => [
                    'tender_item_id' => $bi->tender_item_id,
                    'unit_price'     => (float) $bi->unit_price,
                    'subtotal'       => (float) $bi->subtotal,
                ])
            ),
        ];
    }
}
