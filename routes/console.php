<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('alerts:send-residency-now')
    ->dailyAt('08:00')
    ->withoutOverlapping();

Schedule::command('alerts:send-passport-now')
    ->dailyAt('08:05')
    ->withoutOverlapping();

// Thursday 12:00 (Asia/Riyadh): weekly project report missing alerts.
Schedule::command('alerts:weekly-project-reports')
    ->weeklyOn(4, '12:00')
    ->withoutOverlapping();

Schedule::command('audit:prune-old-logs')
    ->dailyAt('02:15')
    ->withoutOverlapping();
