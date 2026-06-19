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

        // ─── Tie-breaker Order ─────────────────────────────────────────────────
        // Jika dua vendor punya bid_amount yang sama:
        //   1. submitted_at ASC  → siapa yang submit duluan (presisi microsecond)
        //   2. ulid ASC          → tie-breaker akhir (ULID encode timestamp milidetik + random)
        //
        // Bid di posisi [0] pada $bids adalah pemenang "natural" jika admin memilih
        // berdasarkan nilai terendah + waktu tercepat.
        $bids = $tender->bids()
            ->with(['vendor.user'])
            ->orderBy('bid_amount', 'asc')
            ->orderBy('submitted_at', 'asc')
            ->orderBy('ulid', 'asc')
            ->get();

        return view('admin.winners.create', compact('tender', 'bids'));
    }

    /**
     * Store the selected winner.
     * - Atomik dalam DB transaction
     * - Auto-set status tender ke 'finished' setelah winner dipilih
     * - Kirim notifikasi ke semua peserta
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

        // Eager load vendor agar tidak N+1 saat generate description
        $bid = Bid::with('vendor')->findOrFail($request->input('bid_id'));

        // Pastikan bid milik tender ini
        abort_if($bid->tender_id !== $tender->id, 422, 'Bid tidak berasal dari tender ini.');

        try {
            DB::transaction(function () use ($tender, $bid, $request) {
                // 1. Simpan hasil pemenang
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

                // 2. Auto-set status ke 'finished' — tidak perlu admin ubah manual
                $tender->update(['status' => 'finished']);

                // 3. Catat di history dengan transisi status yang benar
                TenderHistory::create([
                    'tender_id'   => $tender->id,
                    'actor_id'    => auth()->id(),
                    'action'      => 'winner_selected',
                    'old_status'  => 'closed',
                    'new_status'  => 'finished', // status sudah otomatis berubah
                    'description' => "Pemenang dipilih: {$bid->vendor->company_name}, "
                                   . "bid Rp " . number_format($bid->bid_amount, 0, ',', '.')
                                   . ". Status tender diubah ke 'finished'.",
                    'created_at'  => now(),
                ]);

                // 4. Notifikasi ke semua peserta tender
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
