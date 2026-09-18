<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::command('app:send-coupon-reminder')->daily();
Schedule::command('app:send-scheduled-coupon')->everyMinute();
Schedule::command('app:mark-expired-coupons')->daily();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
