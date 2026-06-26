<?php

namespace App\Services;

use App\Models\Bid;
use App\Models\BidEvaluation;
use App\Models\Tender;
use App\Models\TenderEvaluationCriteria;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class EvaluationService
{
    /**
     * Store or update evaluation criteria for a tender.
     *
     * @param Tender $tender
     * @param array $criteriaData  [['name' => '...', 'weight' => 40, 'max_score' => 100, 'description' => '...'], ...]
     * @return void
     */
    public function saveCriteria(Tender $tender, array $criteriaData): void
    {
        DB::transaction(function () use ($tender, $criteriaData) {
            // Remove old criteria (cascade will delete evaluations)
            $tender->evaluationCriteria()->delete();

            foreach ($criteriaData as $index => $data) {
                TenderEvaluationCriteria::create([
                    'tender_id'   => $tender->id,
                    'name'        => $data['name'],
                    'weight'      => $data['weight'],
                    'max_score'   => $data['max_score'] ?? 100,
                    'description' => $data['description'] ?? null,
                    'sort_order'  => $index,
                ]);
            }

            ActivityLog::log(
                action: 'criteria_saved',
                module: 'evaluation',
                description: "Kriteria evaluasi tender \"{$tender->title}\" diperbarui (" . count($criteriaData) . " kriteria).",
                subjectType: Tender::class,
                subjectId: $tender->id,
            );
        });
    }

    /**
     * Save evaluation scores for a bid.
     *
     * @param Bid   $bid
     * @param array $scores  [criteria_id => ['score' => 85, 'notes' => '...'], ...]
     * @return void
     */
    public function saveBidEvaluations(Bid $bid, array $scores): void
    {
        DB::transaction(function () use ($bid, $scores) {
            foreach ($scores as $criteriaId => $data) {
                BidEvaluation::updateOrCreate(
                    [
                        'bid_id'      => $bid->id,
                        'criteria_id' => $criteriaId,
                    ],
                    [
                        'tender_id'    => $bid->tender_id,
                        'vendor_id'    => $bid->vendor_id,
                        'score'        => $data['score'],
                        'notes'        => $data['notes'] ?? null,
                        'evaluated_by' => auth()->id(),
                        'evaluated_at' => now(),
                    ]
                );
            }

            $vendor = $bid->vendor;
            ActivityLog::log(
                action: 'bid_evaluated',
                module: 'evaluation',
                description: "Bid vendor \"{$vendor->company_name}\" pada tender #{$bid->tender_id} dievaluasi.",
                subjectType: Bid::class,
                subjectId: $bid->id,
                newValues: $scores,
            );
        });
    }

    /**
     * Validate that total weight of criteria equals 100%.
     */
    public function validateTotalWeight(array $criteriaData): bool
    {
        $total = array_sum(array_column($criteriaData, 'weight'));
        return abs($total - 100) < 0.01;
    }

    /**
     * Check if all bids in a tender have been fully evaluated.
     */
    public function isFullyEvaluated(Tender $tender): bool
    {
        $criteriaCount = $tender->evaluationCriteria()->count();
        if ($criteriaCount === 0) {
            return true; // No criteria = no evaluation needed
        }

        $bidCount = $tender->bids()->count();
        if ($bidCount === 0) {
            return true;
        }

        $expectedTotal = $criteriaCount * $bidCount;
        $actualTotal = $tender->bidEvaluations()->count();

        return $actualTotal >= $expectedTotal;
    }
}
