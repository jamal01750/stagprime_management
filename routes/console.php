<?php

// use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\Staff;
use App\Models\StaffSalary;

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

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule your command here
Schedule::command('notifications:generate-offline')
    ->everyMinute()
    ->timezone('Asia/Dhaka');

// Auto-generate StaffSalary on 1st of every month at 00:05 AM
Schedule::call(function () {
    $staffList = Staff::all();
    foreach ($staffList as $staff) {
        StaffSalary::firstOrCreate(
            [
                'staff_id'    => $staff->id,
                'salary_date' => now()->startOfMonth()->day(7)->toDateString(),
            ],
            [
                'amount' => $staff->amount,
            ]
        );
    }
})->monthlyOn(1, '00:05')->timezone('Asia/Dhaka');

// Auto-generate MonthlyTarget on 1st of every month at 00:10 AM
Schedule::command('target:generate')
    ->monthlyOn(1, '00:10')
    ->timezone('Asia/Dhaka')
    ->onSuccess(function () {
        Log::info('Monthly target generation completed successfully.');
    })
    ->onFailure(function () {
        Log::error('Monthly target generation failed.');
    });