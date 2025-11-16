<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule cleanup tasks
Schedule::command('cleanup:orphaned-temp-files')
    ->daily()
    ->at('02:00')
    ->description('Clean up orphaned temporary files from document generation');

Schedule::command('cleanup:stale-lock-files')
    ->hourly()
    ->description('Clean up stale lock files from preview cache');

Schedule::command('cleanup:preview-cache --days=7')
    ->daily()
    ->at('03:00')
    ->description('Clean up preview cache entries older than 7 days');

Schedule::command('cleanup:temp-files')
    ->daily()
    ->at('04:00')
    ->description('Clean up temporary ZIP and progress files older than 24 hours');
