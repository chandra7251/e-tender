<?php

use App\Console\Commands\AutoCloseBiddingTenders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Auto-close tender yang bidding_end-nya sudah lewat ────────────────────────
// Jalankan setiap 5 menit. Pastikan cron sudah dikonfigurasi di server:
//   * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
Schedule::command(AutoCloseBiddingTenders::class)
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/auto-close-tenders.log'));
