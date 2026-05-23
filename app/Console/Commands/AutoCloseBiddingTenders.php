<?php

namespace App\Console\Commands;

use App\Models\Tender;
use App\Models\TenderHistory;
use Illuminate\Console\Command;

class AutoCloseBiddingTenders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tender:auto-close
                            {--dry-run : Tampilkan tender yang akan ditutup tanpa benar-benar mengubah status}';

    /**
     * The console command description.
     */
    protected $description = 'Otomatis ubah status tender dari bidding → closed jika bidding_end sudah terlewat.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        // Cari semua tender dengan status bidding dan bidding_end sudah lewat
        $expired = Tender::where('status', 'bidding')
            ->whereNotNull('bidding_end')
            ->where('bidding_end', '<', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('Tidak ada tender yang perlu ditutup.');
            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Judul Tender', 'Bidding End', 'Status'],
            $expired->map(fn ($t) => [
                $t->id,
                $t->title,
                $t->bidding_end->format('d M Y H:i'),
                $t->status,
            ])
        );

        if ($dryRun) {
            $this->warn("[DRY RUN] {$expired->count()} tender akan ditutup. Tidak ada perubahan disimpan.");
            return self::SUCCESS;
        }

        $closed = 0;

        foreach ($expired as $tender) {
            $tender->update(['status' => 'closed']);

            TenderHistory::create([
                'tender_id'   => $tender->id,
                'actor_id'    => null, // sistem, bukan admin manual
                'action'      => 'status_changed',
                'old_status'  => 'bidding',
                'new_status'  => 'closed',
                'description' => 'Tender otomatis ditutup karena bidding_end sudah terlewat (auto-close by scheduler).',
                'created_at'  => now(),
            ]);

            $closed++;
            $this->line("  ✓ Tender #{$tender->id} '{$tender->title}' → closed");
        }

        $this->info("{$closed} tender berhasil ditutup.");

        return self::SUCCESS;
    }
}
