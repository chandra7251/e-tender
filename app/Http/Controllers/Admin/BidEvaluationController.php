<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Tender;
use App\Services\EvaluationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BidEvaluationController extends Controller
{
    public function __construct(protected EvaluationService $evaluationService) {}

    /**
     * Show evaluation form for all bids in a tender.
     */
    public function create(Tender $tender): View|RedirectResponse
    {
        if (!in_array($tender->status, ['closed', 'finished'])) {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', "Evaluasi hanya bisa dilakukan saat tender berstatus 'closed' atau 'finished'. Status saat ini: '{$tender->status}'.");
        }

        $criteria = $tender->evaluationCriteria()->orderBy('sort_order')->get();

        if ($criteria->isEmpty()) {
            return redirect()->route('admin.tenders.evaluation-criteria.create', $tender)
                ->with('error', 'Silakan tentukan kriteria evaluasi terlebih dahulu.');
        }

        $bids = $tender->bids()
            ->with(['vendor.user', 'evaluations'])
            ->orderBy('bid_amount', 'asc')
            ->get();

        if ($bids->isEmpty()) {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', 'Belum ada bid yang masuk pada tender ini.');
        }

        return view('admin.evaluations.score', compact('tender', 'criteria', 'bids'));
    }

    /**
     * Store evaluation scores for bids.
     */
    public function store(Request $request, Tender $tender): RedirectResponse
    {
        if (!in_array($tender->status, ['closed', 'finished'])) {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', "Evaluasi hanya bisa dilakukan saat tender berstatus 'closed' atau 'finished'.");
        }

        $criteria = $tender->evaluationCriteria;
        if ($criteria->isEmpty()) {
            return redirect()->back()->with('error', 'Kriteria evaluasi belum ditentukan.');
        }

        $request->validate([
            'scores'          => 'required|array',
            'scores.*'        => 'required|array',
            'scores.*.*'      => 'required|array',
            'scores.*.*.score'=> 'required|numeric|min:0',
            'scores.*.*.notes'=> 'nullable|string|max:500',
        ]);

        $allScores = $request->input('scores'); // [bid_id => [criteria_id => ['score' => x, 'notes' => '...']]]

        foreach ($allScores as $bidId => $criteriaScores) {
            $bid = Bid::where('tender_id', $tender->id)->findOrFail($bidId);

            // Validate scores don't exceed max
            foreach ($criteriaScores as $criteriaId => $data) {
                $criterion = $criteria->firstWhere('id', $criteriaId);
                if ($criterion && $data['score'] > $criterion->max_score) {
                    return back()->withInput()
                        ->with('error', "Skor untuk \"{$criterion->name}\" tidak boleh melebihi {$criterion->max_score}.");
                }
            }

            $this->evaluationService->saveBidEvaluations($bid, $criteriaScores);
        }

        return redirect()
            ->route('admin.tenders.show', $tender)
            ->with('success', 'Evaluasi bid berhasil disimpan untuk ' . count($allScores) . ' vendor.');
    }

    /**
     * Show ranking result with weighted scores.
     */
    public function ranking(Tender $tender): View|RedirectResponse
    {
        if (!in_array($tender->status, ['closed', 'finished'])) {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', 'Ranking hanya tersedia untuk tender yang sudah ditutup.');
        }

        $criteria = $tender->evaluationCriteria;
        $rankedBids = $tender->getRankedBids();
        $isFullyEvaluated = $this->evaluationService->isFullyEvaluated($tender);

        return view('admin.evaluations.ranking', compact('tender', 'criteria', 'rankedBids', 'isFullyEvaluated'));
    }
}
