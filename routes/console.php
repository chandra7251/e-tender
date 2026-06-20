<?php

use App\Console\Commands\UpdateTenderStatuses;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Otomatis perbarui status tender sesuai tanggal & kondisi bisnis ───────────
// Mencakup: draft→open, open→aanwijzing, aanwijzing→bidding, bidding→finished|closed
//
// Jalankan setiap 5 menit. Pastikan cron sudah dikonfigurasi di server:
//   * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
// Catatan Lokal (Windows/Herd): withoutOverlapping() & runInBackground() dihapus
// karena tidak kompatibel dengan Windows dan rawan stale-mutex saat command crash.
// Untuk produksi (Linux/aaPanel), kedua flag ini aman ditambahkan kembali.
Schedule::command(UpdateTenderStatuses::class)
    ->everyMinute()
    ->appendOutputTo(storage_path('logs/update-tender-statuses.log'));
