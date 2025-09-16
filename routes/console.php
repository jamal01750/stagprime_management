<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // Add this import
use Illuminate\Support\Facades\Log;

Artisan::command('clear:cache', function () {
    $this->call('cache:clear');
    $this->info('Application cache cleared!');
})->purpose('Clear the application cache');

Artisan::command('clear:config', function () {
    $this->call('config:clear');
    $this->info('Configuration cache cleared!');
})->purpose('Clear the configuration cache');

Artisan::command('optimize:clear', function () {
    $this->call('optimize:clear');
    $this->info('Application optimized and caches cleared!');
})->purpose('Clear all caches and optimize the application');

// Artisan::command('log:clear', function () {
//     Log::getLogger()->clear();
//     $this->info('Log files cleared!');
// })->purpose('Clear the log files');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule your command here
Schedule::command('notifications:generate-offline')
    ->everyMinute()
    ->timezone('Asia/Dhaka');
