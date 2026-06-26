<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenderEvaluationCriteria extends Model
{
    protected $table = 'tender_evaluation_criteria';

    protected $fillable = [
        'tender_id',
        'name',
        'weight',
        'max_score',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'weight'    => 'decimal:2',
            'max_score' => 'integer',
        ];
    }

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(BidEvaluation::class, 'criteria_id');
    }
}
