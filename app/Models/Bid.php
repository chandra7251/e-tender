<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bid extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tender_id',
        'vendor_id',
        'bid_amount',
        'notes',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'bid_amount'   => 'decimal:2',
            'submitted_at' => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(BidHistory::class);
    }
}
