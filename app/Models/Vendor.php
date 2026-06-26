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
        'is_blacklisted',
        'blacklist_reason',
        'blacklisted_at',
        'blacklisted_by',
    ];
    protected function casts(): array
    {
        return [
            'verified_at'    => 'datetime',
            'blacklisted_at' => 'datetime',
            'is_blacklisted' => 'boolean',
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
    public function blacklister(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blacklisted_by');
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
    public function ratings(): HasMany
    {
        return $this->hasMany(VendorRating::class);
    }

    /**
     * Get average rating across all tenders.
     */
    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->ratings()->avg('overall_score');
        return $avg ? round((float) $avg, 2) : null;
    }

    /**
     * Check if vendor can participate in tenders.
     */
    public function canParticipate(): bool
    {
        return $this->verification_status === 'approved' && !$this->is_blacklisted;
    }

    public function qualification()
    {
        return $this->hasOne(\App\Models\VendorQualification::class);
    }
    public function certifications()
    {
        return $this->hasMany(\App\Models\VendorCertification::class);
    }
    public function contracts()
    {
        return $this->hasMany(\App\Models\Contract::class);
    }
    public function complaints()
    {
        return $this->hasMany(\App\Models\TenderComplaint::class);
    }

}