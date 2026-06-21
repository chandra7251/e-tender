<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Vendor extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'company_name',
        'phone',
        'address',
        'verification_status',
        'verification_notes',
        'verified_by',
        'verified_at',
    ];
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
    public function documents(): HasMany
    {
        return $this->hasMany(VendorDocument::class);
    }
    public function tenderParticipants(): HasMany
    {
        return $this->hasMany(TenderParticipant::class);
    }
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }
    public function wonResults(): HasMany
    {
        return $this->hasMany(TenderResult::class, 'winner_vendor_id');
    }
    public function submissions(): HasMany
    {
        return $this->hasMany(VendorSubmission::class);
    }
}
