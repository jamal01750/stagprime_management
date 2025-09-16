<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MonthlyOfflineCost;
use App\Models\OfflinePaymentNotification;
use Carbon\Carbon;

class GenerateOfflineNotifications extends Command
{
    protected $signature = 'notifications:generate-offline';
    protected $description = 'Generate or update offline cost notifications';

    public function handle()
    {
        $today = now('Asia/Dhaka');

        $expenses = MonthlyOfflineCost::where('status', 'unpaid')->get();

        foreach ($expenses as $expense) {
            $lastDate = Carbon::parse($expense->last_date, 'Asia/Dhaka');

            $rawDiff = $today->diffInDays($lastDate, false); // negative if overdue
            $diff = $rawDiff > 0 ? $rawDiff + 1 : $rawDiff;

            // Determine color
            if ($diff <= 10 && $diff > 7) {
                $level = 'green';
            } elseif ($diff <= 7 && $diff > 3) {
                $level = 'yellow';
            } elseif ($diff <= 3 || $diff < 0) {
                $level = 'red';
            } else {
                continue; // no action needed
            }

            $notification = OfflinePaymentNotification::where('monthly_offline_cost_id', $expense->id)
                ->where('status', 'active')
                ->latest()
                ->first();

            if (! $notification) {
                // First time notification
                OfflinePaymentNotification::create([
                    'monthly_offline_cost_id' => $expense->id,
                    'level' => $level,
                    'status' => 'active',
                    'days_left' => $diff,
                    'generated_at' => $today,
                    'updated_level_at' => $today,
                ]);
            } else {
                // Update existing color if it has changed
                if ($notification->level !== $level) {
                    $notification->update([
                        'level' => $level,
                        'days_left' => $diff,
                        'updated_level_at' => $today,
                    ]);
                }
            }
        }

        // Clear notifications if expense is paid
        $paidExpenses = MonthlyOfflineCost::where('status','paid')->pluck('id');
        if ($paidExpenses->isNotEmpty()) {
            OfflinePaymentNotification::whereIn('monthly_offline_cost_id',$paidExpenses)
                ->where('status','active')
                ->update(['status'=>'cleared','cleared_at'=>$today]);
        }

        $this->info('Notifications generated/updated successfully.');
    }
}
