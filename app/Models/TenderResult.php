<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderResult extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tender_id',
        'winner_vendor_id',
        'winning_bid_id',
        'winning_bid_amount',
        'selection_method',
        'notes',
        'decided_by',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'winning_bid_amount' => 'decimal:2',
            'decided_at'         => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'winner_vendor_id');
    }

    public function winningBid(): BelongsTo
    {
        return $this->belongsTo(Bid::class, 'winning_bid_id');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class);
    }
}
