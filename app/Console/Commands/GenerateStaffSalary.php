<?php

namespace App\Console\Commands;

use App\Models\Staff;
use App\Models\StaffSalary;
use Illuminate\Console\Command;

class GenerateStaffSalary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-staff-salary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate staff salaries for the current month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
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

        $this->info('Staff salaries generated successfully.');
        return 0;
    }
}
