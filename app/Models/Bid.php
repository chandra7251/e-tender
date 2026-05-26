<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bid extends Model
{
    use SoftDeletes;

    /**
     * Boot: auto-generate ULID saat bid pertama kali dibuat.
     *
     * Kenapa ULID dan bukan UUID?
     * ULID = 48-bit timestamp (milidetik) + 80-bit random.
     * Bersifat lexicographically sortable → ULID yang lebih kecil = dibuat lebih awal.
     * Ini menjadi tie-breaker deterministik saat:
     *   - bid_amount sama, DAN
     *   - submitted_at (microsecond) sama (sangat jarang, tapi mungkin di server load tinggi)
     *
     * Urutan penentuan pemenang:
     *   ORDER BY bid_amount ASC, submitted_at ASC, ulid ASC → LIMIT 1
     */
    protected static function booted(): void
    {
        static::creating(function (Bid $bid) {
            if (empty($bid->ulid)) {
                $bid->ulid = (string) Str::ulid();
            }
        });
    }

    protected $fillable = [
        'tender_id',
        'vendor_id',
        'bid_amount',
        'notes',
        'submitted_at',
        'ulid',
    ];

    protected function casts(): array
    {
        return [
            'bid_amount'   => 'decimal:2',
            'submitted_at' => 'datetime',  // DATETIME(6) → presisi microsecond
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
