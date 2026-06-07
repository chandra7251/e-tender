<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TIMEZONE DIAGNOSIS ===\n";
echo "APP timezone: " . config('app.timezone') . "\n";
echo "PHP timezone: " . date_default_timezone_get() . "\n";
echo "now(): " . now() . "\n";
echo "now()->toDateTimeString(): " . now()->toDateTimeString() . "\n";
echo "\n";

$tender = App\Models\Tender::where('title', 'helm')->first();
if ($tender) {
    echo "=== TENDER 'helm' ===\n";
    echo "status: " . $tender->status . "\n";
    echo "start_date (cast): " . $tender->start_date . "\n";
    echo "start_date raw DB: " . $tender->getRawOriginal('start_date') . "\n";
    echo "now() >= start_date: " . (now() >= $tender->start_date ? 'YES - SHOULD TRIGGER' : 'NO - tidak trigger') . "\n";
    echo "now() UTC: " . now()->toDateTimeString() . "\n";
    echo "start_date UTC: " . $tender->start_date->toDateTimeString() . "\n";
} else {
    echo "Tender 'helm' tidak ditemukan, cari semua draft:\n";
    $drafts = App\Models\Tender::where('status', 'draft')->get(['id', 'title', 'start_date', 'status']);
    foreach ($drafts as $d) {
        echo "  #{$d->id} '{$d->title}' start={$d->start_date} raw={$d->getRawOriginal('start_date')}\n";
        echo "  now() >= start_date: " . (now() >= $d->start_date ? 'YES' : 'NO') . "\n";
    }
}

echo "\n=== QUERY SIMULATION ===\n";
$found = App\Models\Tender::where('status', 'draft')
    ->whereNotNull('start_date')
    ->where('start_date', '<=', now())
    ->get(['id', 'title', 'start_date']);
echo "draft+start_date<=now() count: " . $found->count() . "\n";
foreach ($found as $t) {
    echo "  #{$t->id} '{$t->title}' start={$t->start_date}\n";
}
