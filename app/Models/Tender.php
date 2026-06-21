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
        'open_bidding_price',
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
            'open_bidding_price' => 'float',
            'start_date'         => 'datetime',
            'end_date'           => 'datetime',
            'aanwijzing_date'    => 'datetime',
            'bidding_start'      => 'datetime',
            'bidding_end'        => 'datetime',
        ];
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function participants(): HasMany
    {
        return $this->hasMany(TenderParticipant::class);
    }
    public function photos(): HasMany
    {
        return $this->hasMany(TenderPhoto::class);
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
    public function hasParticipants(): bool
    {
        return $this->participants()->exists();
    }
    public function hasBids(): bool
    {
        return $this->bids()->exists();
    }
    public function hasWinner(): bool
    {
        return $this->result()->exists();
    }
    public function isAanwijzingSkipped(): bool
    {
        return is_null($this->aanwijzing_date);
    }
}
