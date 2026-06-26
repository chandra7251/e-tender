<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorRating extends Model
{
    protected $fillable = [
        'vendor_id',
        'tender_id',
        'rated_by',
        'quality_score',
        'delivery_score',
        'communication_score',
        'compliance_score',
        'overall_score',
        'review',
    ];

    protected function casts(): array
    {
        return [
            'quality_score'       => 'integer',
            'delivery_score'      => 'integer',
            'communication_score' => 'integer',
            'compliance_score'    => 'integer',
            'overall_score'       => 'float',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_by');
    }

    /**
     * Auto-calculate overall score before saving.
     */
    protected static function booted(): void
    {
        static::saving(function (VendorRating $rating) {
            $scores = [
                $rating->quality_score,
                $rating->delivery_score,
                $rating->communication_score,
                $rating->compliance_score,
            ];
            $validScores = array_filter($scores, fn ($s) => $s > 0);
            $rating->overall_score = count($validScores) > 0
                ? round(array_sum($validScores) / count($validScores), 2)
                : 0;
        });
    }
}
