<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WinnerSelectionRequest;
use App\Models\Bid;
use App\Models\Tender;
use App\Models\TenderHistory;
use App\Models\TenderResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class WinnerSelectionController extends Controller
{
    /**
     * Show the winner selection form.
     * FIX BUG-01: Tender harus berstatus 'closed' sebelum winner bisa dipilih.
     */
    public function create(Tender $tender): View
    {
        // hanya bisa pilih winner saat tender sudah closed
        if ($tender->status !== 'closed') {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', "Pemenang hanya bisa dipilih saat tender berstatus 'closed'. Status saat ini: '{$tender->status}'.");
        }

        // winner sudah pernah dipilih
        if ($tender->result()->exists()) {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', 'Pemenang tender sudah dipilih sebelumnya.');
        }

        // tidak ada bid
        if ($tender->bids()->count() === 0) {
            return redirect()->back()
                ->with('error', 'Tender belum memiliki bid.');
        }

        $bids = $tender->bids()
            ->with(['vendor.user'])
            ->orderBy('bid_amount', 'asc')
            ->orderBy('submitted_at', 'asc')
            ->orderBy('ulid', 'asc')
            ->get();

        return view('admin.winners.create', compact('tender', 'bids'));
    }


    public function store(WinnerSelectionRequest $request, Tender $tender): RedirectResponse
    {
        // status harus closed
        if ($tender->status !== 'closed') {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', "Pemenang hanya bisa dipilih saat tender berstatus 'closed'.");
        }

        // winner sudah ada
        if ($tender->result()->exists()) {
            return redirect()->route('admin.tenders.show', $tender)
                ->with('error', 'Pemenang tender sudah dipilih.');
        }

        $bid = Bid::with('vendor')->findOrFail($request->input('bid_id'));

        // Pastikan bid milik tender ini
        if ($bid->tender_id !== $tender->id) {
            return redirect()->back()->with('error', 'Bid tidak berasal dari tender ini.');
        }

        try {
            DB::transaction(function () use ($tender, $bid, $request) {
                // Simpan hasil pemenang
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

                // Auto-set status ke finished
                $tender->update(['status' => 'finished']);

                // Catat di history
                TenderHistory::create([
                    'tender_id'   => $tender->id,
                    'actor_id'    => auth()->id(),
                    'action'      => 'winner_selected',
                    'old_status'  => 'closed',
                    'new_status'  => 'finished',
                    'description' => "Pemenang dipilih: {$bid->vendor->company_name}, "
                                   . "bid Rp " . number_format($bid->bid_amount, 0, ',', '.')
                                   . ". Status tender diubah ke 'finished'.",
                    'created_at'  => now(),
                ]);

                // Notifikasi ke semua peserta tender
                $participants = $tender->participants()->with('vendor.user')->get();
                $usersToNotify = $participants->pluck('vendor.user')->filter();

                if ($usersToNotify->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send(
                        $usersToNotify,
                        new \App\Notifications\TenderStatusChanged(
                            $tender, 'closed', 'finished',
                            "Pemenang tender telah dipilih: {$bid->vendor->company_name}."
                        )
                    );
                }
            });
        } catch (\Throwable $e) {
            Log::error('Gagal simpan winner tender', [
                'tender_id' => $tender->id,
                'bid_id'    => $bid->id,
                'error'     => $e->getMessage(),
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menyimpan pemenang. Silakan coba lagi.');
        }

        return redirect()
            ->route('admin.tenders.result.show', $tender)
            ->with('success', "Pemenang berhasil dipilih. Status tender diubah ke 'finished'.");
    }
}
