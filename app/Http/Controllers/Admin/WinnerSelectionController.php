<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WinnerSelectionRequest;
use App\Models\Bid;
use App\Models\Tender;
use App\Models\TenderHistory;
use App\Models\TenderResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WinnerSelectionController extends Controller
{
    /**
     * Show the winner selection form.
     * FIX BUG-01: Tender harus berstatus 'closed' sebelum winner bisa dipilih.
     */
    public function create(Tender $tender): View
    {
        // Guard: hanya bisa pilih winner saat tender sudah closed
        abort_if(
            $tender->status !== 'closed',
            422,
            "Pemenang hanya bisa dipilih saat tender berstatus 'closed'. Status saat ini: '{$tender->status}'."
        );

        // Guard: winner sudah pernah dipilih
        abort_if($tender->result()->exists(), 422, 'Pemenang tender sudah dipilih sebelumnya.');

        // Guard: tidak ada bid
        abort_if($tender->bids()->count() === 0, 422, 'Tender belum memiliki bid.');

        $bids = $tender->bids()
            ->with(['vendor.user'])
            ->orderBy('bid_amount', 'asc')
            ->get();

        return view('admin.winners.create', compact('tender', 'bids'));
    }

    /**
     * Store the selected winner.
     * FIX BUG-01: Guard status + FIX LOW-02: eager load vendor.
     */
    public function store(WinnerSelectionRequest $request, Tender $tender): RedirectResponse
    {
        // Guard: status harus closed
        abort_if(
            $tender->status !== 'closed',
            422,
            "Pemenang hanya bisa dipilih saat tender berstatus 'closed'."
        );

        // Guard: winner sudah ada
        abort_if($tender->result()->exists(), 422, 'Pemenang tender sudah dipilih.');

        // FIX LOW-02: eager load vendor agar tidak N+1 saat generate description
        $bid = Bid::with('vendor')->findOrFail($request->input('bid_id'));

        // Pastikan bid milik tender ini
        abort_if($bid->tender_id !== $tender->id, 422, 'Bid tidak berasal dari tender ini.');

        TenderResult::create([
            'tender_id'          => $tender->id,
            'winner_vendor_id'   => $bid->vendor_id,
            'winning_bid_id'     => $bid->id,
            'winning_bid_amount' => $bid->bid_amount,
            'selection_method'   => $request->input('selection_method'),
            'notes'              => $request->input('notes'),
            'decided_by'         => auth()->id(),
            'decided_at'         => now(),
        ]);

        TenderHistory::create([
            'tender_id'   => $tender->id,
            'actor_id'    => auth()->id(),
            'action'      => 'winner_selected',
            'old_status'  => $tender->status,
            'new_status'  => $tender->status,
            'description' => "Pemenang dipilih: {$bid->vendor->company_name}, "
                           . "bid Rp " . number_format($bid->bid_amount, 0, ',', '.'),
            'created_at'  => now(),
        ]);

        return redirect()
            ->route('admin.tenders.result.show', $tender)
            ->with('success', 'Pemenang tender berhasil dipilih.');
    }
}
