<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Models\TenderEvaluationCriteria;
use Illuminate\Http\JsonResponse;

class EvaluationApiController extends Controller
{
    public function ranking(Tender $tender): JsonResponse
    {
        $criteria = TenderEvaluationCriteria::where('tender_id', $tender->id)
            ->orderBy('weight', 'desc')
            ->get();

        if ($criteria->isEmpty()) {
            // Fallback: rank by price
            $bids = $tender->bids()
                ->with('vendor:id,company_name')
                ->where('is_valid', true)
                ->orderBy('bid_amount', 'asc')
                ->orderBy('submitted_at', 'asc')
                ->get()
                ->map(fn ($bid) => [
                    'bid_id'       => $bid->id,
                    'vendor'       => $bid->vendor->company_name ?? '-',
                    'bid_amount'   => $bid->bid_amount,
                    'submitted_at' => $bid->submitted_at->toIso8601String(),
                    'total_score'  => null,
                ]);

            return response()->json([
                'tender_id'    => $tender->id,
                'tender_title' => $tender->title,
                'mode'         => 'price_based',
                'criteria'     => [],
                'ranking'      => $bids->values(),
            ]);
        }

        $rankedBids = $tender->getRankedBids();

        $ranking = $rankedBids->map(fn ($bid, $i) => [
            'rank'         => $i + 1,
            'bid_id'       => $bid->id,
            'vendor'       => $bid->vendor->company_name ?? '-',
            'bid_amount'   => $bid->bid_amount,
            'submitted_at' => $bid->submitted_at->toIso8601String(),
            'total_score'  => $bid->total_weighted_score,
            'details'      => $bid->evaluation_details ?? [],
        ]);

        return response()->json([
            'tender_id'    => $tender->id,
            'tender_title' => $tender->title,
            'mode'         => 'multi_criteria',
            'criteria'     => $criteria->map(fn ($c) => [
                'id'        => $c->id,
                'name'      => $c->name,
                'weight'    => $c->weight,
                'max_score' => $c->max_score,
            ]),
            'ranking' => $ranking->values(),
        ]);
    }
}
