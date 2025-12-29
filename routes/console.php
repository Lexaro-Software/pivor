<?php

use App\Jobs\SendTaskRemindersJob;
use App\Modules\EmailIntegration\Jobs\SyncInboundEmailsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Email sync - runs every 5 minutes
Schedule::job(new SyncInboundEmailsJob)->everyFiveMinutes();

// Task reminders - runs every hour at the start of the hour
Schedule::job(new SendTaskRemindersJob)->hourly();
