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
            'submitted_at' => 'datetime',  
        ];
    }
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
