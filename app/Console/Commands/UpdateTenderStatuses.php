<?php

namespace App\Console\Commands;

use App\Models\Bid;
use App\Models\Tender;
use App\Models\TenderHistory;
use App\Models\TenderResult;
use Illuminate\Console\Command;

class UpdateTenderStatuses extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tender:update-statuses
                            {--dry-run : Tampilkan perubahan yang akan terjadi tanpa benar-benar menyimpan}';

    /**
     * The console command description.
     */
    protected $description = 'Otomatis perbarui status tender berdasarkan tanggal & kondisi bisnis:
                              draft→open, open→aanwijzing (jika ada), aanwijzing→bidding, bidding→finished|closed.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('⚠  MODE DRY-RUN: Tidak ada perubahan yang disimpan.');
            $this->newLine();
        }

        $totalChanged = 0;
        $totalChanged += $this->transitionDraftToOpen($dryRun);
        $totalChanged += $this->transitionOpenToAanwijzing($dryRun);
        $totalChanged += $this->transitionAanwijzingToBidding($dryRun);
        $totalChanged += $this->closeBiddingTenders($dryRun);

        $this->newLine();
        if ($totalChanged === 0) {
            $this->info('✓ Tidak ada tender yang perlu diperbarui statusnya.');
        } else {
            $this->info("✓ Total {$totalChanged} tender berhasil diperbarui statusnya.");
        }

        return self::SUCCESS;
    }

    // ─── Fase 1: draft → open ────────────────────────────────────────────────

    private function transitionDraftToOpen(bool $dryRun): int
    {
        $tenders = Tender::where('status', 'draft')
            ->whereNotNull('start_date')
            ->where('start_date', '<=', now())
            ->get();

        if ($tenders->isEmpty()) {
            return 0;
        }

        $this->info("📋 [draft → open] Ditemukan {$tenders->count()} tender:");

        foreach ($tenders as $tender) {
            $this->line("   • #{$tender->id} \"{$tender->title}\" (start_date: {$tender->start_date->format('d M Y H:i')})");

            if (!$dryRun) {
                $this->applyTransition($tender, 'draft', 'open',
                    "Tender telah dibuka (Open). Silakan ikuti dan ajukan penawaran Anda."
                );
            }
        }

        return $dryRun ? 0 : $tenders->count();
    }

    // ─── Fase 2: open → aanwijzing ───────────────────────────────────────────

    private function transitionOpenToAanwijzing(bool $dryRun): int
    {
        $tenders = Tender::where('status', 'open')
            ->whereNotNull('aanwijzing_date')
            ->where('aanwijzing_date', '<=', now())
            ->get();

        if ($tenders->isEmpty()) {
            return 0;
        }

        $this->info("📋 [open → aanwijzing] Ditemukan {$tenders->count()} tender:");

        foreach ($tenders as $tender) {
            $this->line("   • #{$tender->id} \"{$tender->title}\" (aanwijzing_date: {$tender->aanwijzing_date->format('d M Y H:i')})");

            if (!$dryRun) {
                $this->applyTransition($tender, 'open', 'aanwijzing',
                    "Tender memasuki fase Aanwijzing. Silakan perhatikan pengumuman terbaru."
                );
            }
        }

        return $dryRun ? 0 : $tenders->count();
    }

    // ─── Fase 3: open|aanwijzing → bidding ───────────────────────────────────
    // Jika aanwijzing_date null, maka fase aanwijzing di-skip:
    // tender tetap 'open' sampai bidding_start tiba, lalu langsung → bidding.

    private function transitionAanwijzingToBidding(bool $dryRun): int
    {
        // Tangani dua kasus:
        // (a) status=aanwijzing + bidding_start <= now()
        // (b) status=open + aanwijzing_date IS NULL + bidding_start <= now()
        $tenders = Tender::where('bidding_start', '<=', now())
            ->whereNotNull('bidding_start')
            ->where(function ($q) {
                $q->where('status', 'aanwijzing')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'open')
                         ->whereNull('aanwijzing_date');
                  });
            })
            ->get();

        if ($tenders->isEmpty()) {
            return 0;
        }

        $this->info("📋 [→ bidding] Ditemukan {$tenders->count()} tender:");

        foreach ($tenders as $tender) {
            $fromStatus = $tender->status;
            $this->line("   • #{$tender->id} \"{$tender->title}\" ({$fromStatus} → bidding, bidding_start: {$tender->bidding_start->format('d M Y H:i')})");

            if (!$dryRun) {
                $skipNote = $fromStatus === 'open' ? ' (fase aanwijzing di-skip karena aanwijzing_date null)' : '';
                $this->applyTransition($tender, $fromStatus, 'bidding',
                    "Fase Bidding telah dimulai{$skipNote}. Silakan ajukan penawaran (bid) terbaik Anda sekarang."
                );
            }
        }

        return $dryRun ? 0 : $tenders->count();
    }

    // ─── Fase 4: bidding → finished | closed ─────────────────────────────────

    private function closeBiddingTenders(bool $dryRun): int
    {
        $tenders = Tender::with(['bids', 'result'])
            ->where('status', 'bidding')
            ->whereNotNull('bidding_end')
            ->where('bidding_end', '<=', now())
            ->get();

        if ($tenders->isEmpty()) {
            return 0;
        }

        $this->info("📋 [bidding → finished|closed] Ditemukan {$tenders->count()} tender:");

        $changed = 0;

        foreach ($tenders as $tender) {
            $hasBids = $tender->bids->isNotEmpty();

            if (!$hasBids) {
                // Tidak ada bid sama sekali → closed
                $this->line("   • #{$tender->id} \"{$tender->title}\" → closed (tidak ada bid)");

                if (!$dryRun) {
                    $this->applyTransition($tender, 'bidding', 'closed',
                        "Tender telah ditutup (Closed) karena tidak ada penawaran yang masuk selama masa bidding."
                    );
                }
            } else {
                // Ada bid → pilih pemenang (tertinggi), lalu finished
                $winningBid = $tender->bids
                    ->sortByDesc('bid_amount')
                    ->sortBy('submitted_at') // tie-breaker: submitted lebih awal menang jika amount sama
                    ->first();

                $this->line("   • #{$tender->id} \"{$tender->title}\" → finished (pemenang: vendor #{$winningBid->vendor_id}, Rp " . number_format($winningBid->bid_amount, 0, ',', '.') . ")");

                if (!$dryRun) {
                    // Buat TenderResult jika belum ada (admin belum pilih manual)
                    if (!$tender->result) {
                        TenderResult::create([
                            'tender_id'          => $tender->id,
                            'winner_vendor_id'   => $winningBid->vendor_id,
                            'winning_bid_id'     => $winningBid->id,
                            'winning_bid_amount' => $winningBid->bid_amount,
                            'selection_method'   => 'auto',
                            'notes'              => 'Pemenang dipilih otomatis oleh sistem berdasarkan bid tertinggi.',
                            'decided_at'         => now(),
                            'decided_by'         => null,
                        ]);
                    }

                    $this->applyTransition($tender, 'bidding', 'finished',
                        "Tender telah selesai (Finished). Pemenang telah ditentukan dengan penawaran tertinggi Rp " .
                        number_format($winningBid->bid_amount, 0, ',', '.') . "."
                    );
                }
            }

            $changed++;
        }

        return $dryRun ? 0 : $changed;
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    private function applyTransition(Tender $tender, string $oldStatus, string $newStatus, string $description): void
    {
        $tender->update(['status' => $newStatus]);

        TenderHistory::create([
            'tender_id'   => $tender->id,
            'actor_id'    => null, // null = sistem otomatis
            'action'      => 'status_changed',
            'old_status'  => $oldStatus,
            'new_status'  => $newStatus,
            'description' => $description,
            'created_at'  => now(),
        ]);

        // Send Notification
        $usersToNotify = collect();

        if ($oldStatus === 'draft' && $newStatus === 'open') {
            // Send to all vendors
            $usersToNotify = \App\Models\User::where('role', 'vendor')->get();
        } else {
            // Send only to participating vendors
            $usersToNotify = $tender->participants()->with('vendor.user')->get()->pluck('vendor.user')->filter();
        }

        if ($usersToNotify->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send(
                $usersToNotify,
                new \App\Notifications\TenderStatusChanged($tender, $oldStatus, $newStatus, $description)
            );
        }
    }
}
