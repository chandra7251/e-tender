<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TenderResultController extends Controller
{
    /**
     * Show the result of a tender.
     */
    public function show(Tender $tender): View
    {
        $result = $tender->result()->with(['winner.user', 'winningBid', 'decider', 'purchaseOrder'])->first();

        abort_if(is_null($result), 404, 'Hasil tender belum tersedia.');

        return view('admin.results.show', compact('tender', 'result'));
    }

    /**
     * Mark tender as finished.
     */
    public function finish(Tender $tender): RedirectResponse
    {
        // Validasi status tender
        abort_if(
            $tender->status !== 'closed',
            422,
            "Tender hanya bisa diselesaikan dari status 'closed'. Status saat ini: '{$tender->status}'."
        );

        // Validasi pemenang
        abort_if(is_null($tender->result), 422, 'Pilih pemenang terlebih dahulu sebelum menyelesaikan tender.');

        // Validasi PO
        abort_if(
            !$tender->purchaseOrder()->exists(),
            422,
            'Buat Purchase Order terlebih dahulu sebelum menyelesaikan tender.'
        );

        $old = $tender->status;
        $tender->update(['status' => 'finished']);

        \App\Models\TenderHistory::create([
            'tender_id'   => $tender->id,
            'actor_id'    => auth()->id(),
            'action'      => 'status_changed',
            'old_status'  => $old,
            'new_status'  => 'finished',
            'description' => 'Tender ditandai selesai oleh admin.',
            'created_at'  => now(),
        ]);

        return redirect()
            ->route('admin.tenders.result.show', $tender)
            ->with('success', 'Tender berhasil ditandai sebagai Finished.');
    }
}
