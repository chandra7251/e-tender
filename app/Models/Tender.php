<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tender extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'specification',
        'start_date',
        'end_date',
        'aanwijzing_date',
        'bidding_start',
        'bidding_end',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date'      => 'datetime',
            'end_date'        => 'datetime',
            'aanwijzing_date' => 'datetime',
            'bidding_start'   => 'datetime',
            'bidding_end'     => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(TenderParticipant::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(TenderAnnouncement::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(TenderResult::class);
    }

    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TenderHistory::class);
    }
}
