<?php

use App\Console\Commands\UpdateTenderStatuses;
use App\Console\Commands\SendTenderDeadlineReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(UpdateTenderStatuses::class)
    ->everyMinute()
    ->appendOutputTo(storage_path('logs/update-tender-statuses.log'));

Schedule::command(SendTenderDeadlineReminders::class)
    ->everyFiveMinutes()
    ->appendOutputTo(storage_path('logs/deadline-reminders.log'));
