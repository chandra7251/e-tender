<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Bid;
use App\Models\Tender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwoEnvelopeController extends Controller
{
    /**
     * Show Amplop 1 (Technical) evaluation page.
     */
    public function technical(Tender $tender): View|RedirectResponse
    {
        if (!in_array($tender->status, ['closed', 'finished'])) {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', "Evaluasi teknis hanya tersedia saat tender 'closed' atau 'finished'.");
        }

        if ($tender->evaluation_method !== 'two_envelope') {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', 'Tender ini tidak menggunakan metode evaluasi 2 amplop.');
        }

        $bids = $tender->bids()
            ->with(['vendor.user'])
            ->orderBy('submitted_at', 'asc')
            ->get();

        $technicalCriteria = $tender->evaluationCriteria()
            ->where('envelope', 'technical')
            ->orderBy('sort_order')
            ->get();

        return view('admin.evaluations.two-envelope-technical', compact('tender', 'bids', 'technicalCriteria'));
    }

    /**
     * Save technical evaluation results (pass/fail).
     */
    public function saveTechnical(Request $request, Tender $tender): RedirectResponse
    {
        $validated = $request->validate([
            'evaluations'                     => 'required|array',
            'evaluations.*.technical_status'  => 'required|in:passed,failed',
            'evaluations.*.technical_score'   => 'required|numeric|min:0|max:100',
            'evaluations.*.technical_notes'   => 'nullable|string|max:500',
        ]);

        $passingGrade = $tender->passing_grade ?? 70;
        $passedCount = 0;
        $failedCount = 0;

        foreach ($validated['evaluations'] as $bidId => $data) {
            $bid = Bid::where('tender_id', $tender->id)->findOrFail($bidId);

            // Auto-determine status based on passing grade
            $score = (float) $data['technical_score'];
            $status = $score >= $passingGrade ? 'passed' : 'failed';

            $bid->update([
                'technical_status' => $status,
                'technical_score'  => $score,
                'technical_notes'  => $data['technical_notes'] ?? null,
            ]);

            $status === 'passed' ? $passedCount++ : $failedCount++;
        }

        ActivityLog::log(
            action: 'technical_evaluation',
            module: 'tender',
            description: "Evaluasi teknis tender \"{$tender->title}\": {$passedCount} lulus, {$failedCount} gugur.",
            subjectType: Tender::class,
            subjectId: $tender->id,
        );

        return redirect()
            ->route('admin.tenders.show', $tender)
            ->with('success', "Evaluasi teknis disimpan: {$passedCount} vendor lulus, {$failedCount} vendor gugur.");
    }

    /**
     * Show Amplop 2 (Price) — only shows vendors who passed technical.
     */
    public function price(Tender $tender): View|RedirectResponse
    {
        if ($tender->evaluation_method !== 'two_envelope') {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', 'Tender ini tidak menggunakan metode evaluasi 2 amplop.');
        }

        $passedBids = $tender->bids()
            ->with(['vendor.user'])
            ->where('technical_status', 'passed')
            ->orderBy('bid_amount', 'asc')
            ->get();

        $failedBids = $tender->bids()
            ->with(['vendor.user'])
            ->where('technical_status', 'failed')
            ->orderBy('bid_amount', 'asc')
            ->get();

        $priceCriteria = $tender->evaluationCriteria()
            ->where('envelope', 'price')
            ->orderBy('sort_order')
            ->get();

        return view('admin.evaluations.two-envelope-price', compact(
            'tender', 'passedBids', 'failedBids', 'priceCriteria'
        ));
    }

    /**
     * Show combined ranking (technical weight + price weight).
     */
    public function combinedRanking(Tender $tender): View|RedirectResponse
    {
        if ($tender->evaluation_method !== 'two_envelope') {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', 'Tender ini tidak menggunakan metode evaluasi 2 amplop.');
        }

        $techWeight = $tender->technical_weight ?? 60;
        $priceWeight = $tender->price_weight ?? 40;

        // Only passed bids
        $passedBids = $tender->bids()
            ->with(['vendor.user'])
            ->where('technical_status', 'passed')
            ->get();

        // Calculate combined score
        $lowestBid = $passedBids->min('bid_amount');

        $rankedBids = $passedBids->map(function ($bid) use ($techWeight, $priceWeight, $lowestBid) {
            $techScore = $bid->technical_score ?? 0;
            $priceScore = $lowestBid > 0 && $bid->bid_amount > 0
                ? ($lowestBid / $bid->bid_amount) * 100
                : 0;

            $bid->price_score_calculated = round($priceScore, 2);
            $bid->combined_score = round(
                ($techScore * $techWeight / 100) + ($priceScore * $priceWeight / 100),
                2
            );

            return $bid;
        })->sortByDesc('combined_score')->values();

        return view('admin.evaluations.two-envelope-ranking', compact(
            'tender', 'rankedBids', 'techWeight', 'priceWeight'
        ));
    }
}
