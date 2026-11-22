<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('bookings:expire-pending')->everyMinute();
Schedule::command('bookings:sync-trip-status')
    ->everyTenMinutes()
    ->timezone(config('app.timezone', 'Asia/Makassar'));
