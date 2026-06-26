<?php
namespace App\Console\Commands;

use App\Mail\TenderDeadlineReminder;
use App\Models\Tender;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTenderDeadlineReminders extends Command
{
    protected $signature = 'tender:send-deadline-reminders';
    protected $description = 'Kirim email reminder ke vendor saat bidding akan berakhir dalam 24h atau 1h.';

    public function handle(): int
    {
        $now = now();

        // Window 24h: bidding_end antara 23:50 s/d 24:10 dari sekarang
        $window24hStart = $now->copy()->addHours(23)->addMinutes(50);
        $window24hEnd   = $now->copy()->addHours(24)->addMinutes(10);

        // Window 1h: bidding_end antara 50m s/d 1h10m dari sekarang
        $window1hStart  = $now->copy()->addMinutes(50);
        $window1hEnd    = $now->copy()->addHours(1)->addMinutes(10);

        $reminders = collect();

        Tender::where('status', 'bidding')
            ->where(function ($q) use ($window24hStart, $window24hEnd, $window1hStart, $window1hEnd) {
                $q->whereBetween('bidding_end', [$window24hStart, $window24hEnd])
                  ->orWhereBetween('bidding_end', [$window1hStart, $window1hEnd]);
            })
            ->with(['participants.vendor.user'])
            ->each(function (Tender $tender) use ($window24hStart, $window24hEnd, &$reminders) {
                $type = $tender->bidding_end->between($window24hStart, $window24hEnd) ? '24h' : '1h';

                foreach ($tender->participants as $participant) {
                    $user = $participant->vendor?->user;
                    if ($user && $user->email) {
                        Mail::to($user->email)->queue(new TenderDeadlineReminder($tender, $type));
                        $reminders->push([$user->email, $tender->title, $type]);
                    }
                }
            });

        if ($reminders->isEmpty()) {
            $this->info('✓ Tidak ada reminder yang perlu dikirim.');
        } else {
            $this->info("✓ {$reminders->count()} email reminder dikirim.");
            $this->table(['Email', 'Tender', 'Tipe'], $reminders->toArray());
        }

        return self::SUCCESS;
    }
}
