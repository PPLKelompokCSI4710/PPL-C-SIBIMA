<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler entries (run via `php artisan schedule:run`)
Schedule::command('bimbingan:dispatch-schedule-reminders')->everyMinute();
Schedule::command('bimbingan:check-progress')->dailyAt('08:00');
