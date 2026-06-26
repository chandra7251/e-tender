<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Models\ActivityLog;
use App\Services\EvaluationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluationCriteriaController extends Controller
{
    public function __construct(protected EvaluationService $evaluationService) {}

    /**
     * Show criteria management form for a tender.
     */
    public function create(Tender $tender): View|RedirectResponse
    {
        if (in_array($tender->status, ['finished'])) {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', 'Kriteria tidak bisa diubah untuk tender yang sudah selesai.');
        }

        $criteria = $tender->evaluationCriteria()->orderBy('sort_order')->get();

        return view('admin.evaluations.criteria', compact('tender', 'criteria'));
    }

    /**
     * Store/update criteria for a tender.
     */
    public function store(Request $request, Tender $tender): RedirectResponse
    {
        if (in_array($tender->status, ['finished'])) {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', 'Kriteria tidak bisa diubah untuk tender yang sudah selesai.');
        }

        $request->validate([
            'criteria'               => 'required|array|min:1',
            'criteria.*.name'        => 'required|string|max:255',
            'criteria.*.weight'      => 'required|numeric|min:0.01|max:100',
            'criteria.*.max_score'   => 'required|integer|min:1|max:1000',
            'criteria.*.description' => 'nullable|string|max:500',
        ], [
            'criteria.required'            => 'Minimal harus ada 1 kriteria.',
            'criteria.*.name.required'     => 'Nama kriteria wajib diisi.',
            'criteria.*.weight.required'   => 'Bobot kriteria wajib diisi.',
            'criteria.*.weight.min'        => 'Bobot minimal 0.01%.',
        ]);

        $criteriaData = $request->input('criteria');

        if (!$this->evaluationService->validateTotalWeight($criteriaData)) {
            return back()->withInput()
                ->with('error', 'Total bobot semua kriteria harus berjumlah tepat 100%. Saat ini: '
                    . array_sum(array_column($criteriaData, 'weight')) . '%');
        }

        $this->evaluationService->saveCriteria($tender, $criteriaData);

        return redirect()
            ->route('admin.tenders.show', $tender)
            ->with('success', 'Kriteria evaluasi berhasil disimpan (' . count($criteriaData) . ' kriteria).');
    }
}
