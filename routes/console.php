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
Schedule::command(UpdateTenderStatuses::class)
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/update-tender-statuses.log'));
