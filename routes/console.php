<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// run the checkAndUpdatePaymentStatus method every minute 
// php artisan schedule:work to run locally
// Schedule::command(app(\App\Http\Controllers\Admin\RegistrationController::class)
//         ->checkAndUpdatePaymentStatus())->everyMinute();