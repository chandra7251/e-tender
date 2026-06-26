<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
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
        'evaluation_method',
        'technical_weight',
        'price_weight',
        'passing_grade',
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
            'technical_weight'   => 'float',
            'price_weight'       => 'float',
            'passing_grade'      => 'float',
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
    public function evaluationCriteria(): HasMany
    {
        return $this->hasMany(TenderEvaluationCriteria::class)->orderBy('sort_order');
    }
    public function bidEvaluations(): HasMany
    {
        return $this->hasMany(BidEvaluation::class);
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
    public function hasCriteria(): bool
    {
        return $this->evaluationCriteria()->exists();
    }
    public function isAanwijzingSkipped(): bool
    {
        return is_null($this->aanwijzing_date);
    }
    /**
     * Get ranked bids with weighted total scores.
     * Returns a collection of bids with `total_weighted_score` attribute.
     */
    public function getRankedBids(): Collection
    {
        $criteria = $this->evaluationCriteria;

        if ($criteria->isEmpty()) {
            // Fallback: rank by lowest price
            return $this->bids()
                ->with(['vendor.user'])
                ->orderBy('bid_amount', 'asc')
                ->orderBy('submitted_at', 'asc')
                ->orderBy('ulid', 'asc')
                ->get()
                ->map(function ($bid) {
                    $bid->total_weighted_score = null;
                    $bid->evaluation_details = [];
                    return $bid;
                });
        }

        $bids = $this->bids()
            ->with(['vendor.user', 'evaluations.criteria'])
            ->get();

        return $bids->map(function ($bid) use ($criteria) {
            $details = [];
            $totalWeighted = 0;

            foreach ($criteria as $criterion) {
                $evaluation = $bid->evaluations->firstWhere('criteria_id', $criterion->id);
                $score = $evaluation ? (float) $evaluation->score : 0;
                $maxScore = $criterion->max_score ?: 100;
                $weighted = round(($score / $maxScore) * $criterion->weight, 2);
                $totalWeighted += $weighted;

                $details[] = [
                    'criteria_id'   => $criterion->id,
                    'criteria_name' => $criterion->name,
                    'weight'        => (float) $criterion->weight,
                    'max_score'     => $maxScore,
                    'raw_score'     => $score,
                    'weighted_score'=> $weighted,
                ];
            }

            $bid->total_weighted_score = round($totalWeighted, 2);
            $bid->evaluation_details = $details;

            return $bid;
        })->sortByDesc('total_weighted_score')->values();
    }

    public function items()
    {
        return $this->hasMany(\App\Models\TenderItem::class);
    }
    public function complaints()
    {
        return $this->hasMany(\App\Models\TenderComplaint::class);
    }
    public function contract()
    {
        return $this->hasOne(\App\Models\Contract::class);
    }
    public function requirements()
    {
        return $this->hasMany(\App\Models\TenderRequirement::class);
    }

}