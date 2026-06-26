<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BidEvaluation extends Model
{
    protected $fillable = [
        'bid_id',
        'criteria_id',
        'tender_id',
        'vendor_id',
        'score',
        'notes',
        'evaluated_by',
        'evaluated_at',
    ];

    protected function casts(): array
    {
        return [
            'score'        => 'decimal:2',
            'evaluated_at' => 'datetime',
        ];
    }

    public function bid(): BelongsTo
    {
        return $this->belongsTo(Bid::class);
    }

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(TenderEvaluationCriteria::class, 'criteria_id');
    }

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    /**
     * Calculate weighted score: (score / max_score) * weight
     */
    public function getWeightedScoreAttribute(): float
    {
        if (!$this->criteria) {
            return 0;
        }

        $maxScore = $this->criteria->max_score ?: 100;
        return round(($this->score / $maxScore) * $this->criteria->weight, 2);
    }
}
