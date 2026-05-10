<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BidHistory extends Model
{
    /**
     * Append-only audit table — no soft deletes, no updated_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'bid_id',
        'tender_id',
        'vendor_id',
        'old_bid_amount',
        'new_bid_amount',
        'notes',
        'changed_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_bid_amount' => 'decimal:2',
            'new_bid_amount' => 'decimal:2',
            'changed_at'     => 'datetime',
            'created_at'     => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function bid(): BelongsTo
    {
        return $this->belongsTo(Bid::class);
    }

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
