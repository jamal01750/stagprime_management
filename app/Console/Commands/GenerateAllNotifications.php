<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\MonthlyOfflineCost;
use App\Models\MonthlyOnlineCost;
use App\Models\ClientProjectDebit;
use App\Models\CompanyProject;
use App\Models\Installment;
use App\Models\InternshipRegistration;
use App\Models\StaffSalary;
use App\Models\Student;
use Carbon\Carbon;

class GenerateAllNotifications extends Command
{
    protected $signature = 'notifications:generate-all';
    protected $description = 'Generate or update notifications for all sectors';

    public function handle()
    {
        $this->info('Generating notifications for all sectors...');

        $this->generateOfflineCostNotifications();
        $this->generateOnlineCostNotifications();
        $this->generateClientProjectNotifications();
        $this->generateStudentPaymentNotifications();
        $this->generateInternPaymentNotifications();
        $this->generateStaffSalaryNotifications();
        $this->generateLoanInstallmentNotifications();
        $this->generateCompanyProjectNotifications();

        $this->info('All notifications generated successfully.');
        return 0;
    }

    /**
     * A centralized and robust function to handle the creation and updating of notifications.
     */
    private function processNotification($item, $dueDate, $message, $type, $actionRoute, $actionParams = [])
    {
        $today = now('Asia/Dhaka')->startOfDay();
        $dueDate = Carbon::parse($dueDate, 'Asia/Dhaka')->startOfDay();

        // Calculate the difference in days. A negative value means the due date is in the past.
        $daysLeft = $today->diffInDays($dueDate, false);
        // $daysLeft = $diff > 0 ? $diff + 1 : $diff;

        // If the due date is more than 10 days away, we don't need a notification.
        // Clear any existing active notification for this item and stop processing.
        if ($daysLeft > 10) {
            Notification::where('notifiable_id', $item->id)
                ->where('notifiable_type', get_class($item))
                ->where('status', 'active')
                ->update(['status' => 'cleared', 'cleared_at' => now()]);
            return;
        }

        // Determine the notification level based on your rules.
        // Using red, blue, green as requested.
        if ($daysLeft <= 3) {
            $level = 'red';
        } elseif ($daysLeft <= 7) {
            $level = 'blue';
        } else { // 8 to 10 days left
            $level = 'green';
        }

        // Find the existing notification for this specific item.
        $existingNotification = Notification::where('notifiable_id', $item->id)
            ->where('notifiable_type', get_class($item))
            ->first();

        // Prepare the data payload for the notification.
        $notificationData = [
            'type' => $type,
            'level' => $level,
            'message' => $message,
            'due_date' => $dueDate,
            'days_left' => $daysLeft,
            'action_route' => $actionRoute,
            'action_params' => json_encode($actionParams),
            'status' => 'active',
            // This is the key fix: Only update the 'updated_level_at' timestamp if the level has actually changed.
            // If it's a new notification, the database default will handle the timestamp.
            'updated_level_at' => ($existingNotification && $existingNotification->level !== $level) ? now() : ($existingNotification->updated_level_at ?? now()),
        ];
        
        // Use updateOrCreate to either create a new notification or update an existing one.
        Notification::updateOrCreate(
            [
                'notifiable_id' => $item->id,
                'notifiable_type' => get_class($item),
            ],
            $notificationData
        );
    }

    private function generateOfflineCostNotifications()
    {
        $expenses = MonthlyOfflineCost::with('category')->where('status', 'unpaid')->whereNotNull('last_date')->get();
        foreach ($expenses as $expense) {
            $message = "Payment due for Offline Cost: {$expense->category->category}. Amount: ৳{$expense->amount}.";
            $this->processNotification($expense, $expense->last_date, $message, 'pay', 'offline.cost.report');
        }
        // Clear notifications for items that have been paid.
        $paidIds = MonthlyOfflineCost::where('status', 'paid')->pluck('id');
        Notification::whereIn('notifiable_id', $paidIds)->where('notifiable_type', MonthlyOfflineCost::class)->update(['status' => 'cleared', 'cleared_at' => now()]);
    }

    private function generateOnlineCostNotifications()
    {
        $expenses = MonthlyOnlineCost::with('category')->where('status', 'unpaid')->whereNotNull('expire_date')->get();
        foreach ($expenses as $expense) {
            $currencySymbol = $expense->amount_type == 'taka' ? '৳' : '$';
            $message = "Subscription renewal for: {$expense->category->category}. Amount: {$currencySymbol}{$expense->amount}.";
            $this->processNotification($expense, $expense->expire_date, $message, 'pay', 'online.cost.report');
        }
        $paidIds = MonthlyOnlineCost::where('status', 'paid')->pluck('id');
        Notification::whereIn('notifiable_id', $paidIds)->where('notifiable_type', MonthlyOnlineCost::class)->update(['status' => 'cleared', 'cleared_at' => now()]);
    }

    private function generateClientProjectNotifications()
    {
        $debits = ClientProjectDebit::with('project')->where('status', 'unpaid')->whereNotNull('pay_date')->get();
        foreach ($debits as $debit) {
            $currencySymbol = $debit->currency == 'Dollar' ? '$' : '৳';
            $message = "Collect payment for {$debit->project->project_name}. Amount: {$currencySymbol}{$debit->pay_amount}.";
            $this->processNotification($debit, $debit->pay_date, $message, 'collect', 'client.project.list');
        }
        $paidIds = ClientProjectDebit::where('status', 'paid')->pluck('id');
        Notification::whereIn('notifiable_id', $paidIds)->where('notifiable_type', ClientProjectDebit::class)->update(['status' => 'cleared', 'cleared_at' => now()]);
    }

    private function generateStudentPaymentNotifications()
    {
        $students = Student::where('payment_status', 'Unpaid')->whereNotNull('payment_due_date')->get();
        foreach ($students as $student) {
            $message = "Collect due from Student: {$student->student_name} (ID: {$student->student_id}). Due: ৳{$student->due_amount}.";
            $this->processNotification($student, $student->payment_due_date, $message, 'collect', 'student.list.running');
        }
        $paidIds = Student::where('payment_status', 'Paid')->pluck('id');
        Notification::whereIn('notifiable_id', $paidIds)->where('notifiable_type', Student::class)->update(['status' => 'cleared', 'cleared_at' => now()]);
    }

    private function generateInternPaymentNotifications()
    {
        $interns = InternshipRegistration::where('active_status', 'Running')->where('payment_status', '!=', 'Full paid')->get();
        foreach ($interns as $intern) {
            $installment = $intern->pay_amount / 3;
            $upcoming_date = null;
            if ($intern->total_paid == 0) {
                $upcoming_date = Carbon::parse($intern->admission_date)->addMonth();
            } elseif ($intern->total_paid > 0 && $intern->total_paid < ($installment * 2)) {
                $upcoming_date = Carbon::parse($intern->admission_date)->addMonths(2);
            } elseif ($intern->total_paid >= ($installment * 2) && $intern->total_paid < $intern->pay_amount) {
                $upcoming_date = Carbon::parse($intern->admission_date)->addMonths(3);
            }

            if ($upcoming_date) {
                $message = "Pay salary to Intern: {$intern->internee_name} (ID: {$intern->intern_id}). Amount: ৳{$installment}.";
                $this->processNotification($intern, $upcoming_date, $message, 'pay', 'internship.list.running');
            }
        }
        $paidIds = InternshipRegistration::where('payment_status', 'Full paid')->pluck('id');
        Notification::whereIn('notifiable_id', $paidIds)->where('notifiable_type', InternshipRegistration::class)->update(['status' => 'cleared', 'cleared_at' => now()]);
    }

    private function generateStaffSalaryNotifications()
    {
        $salaries = StaffSalary::with('staff')->where('status', 'Unpaid')->whereNotNull('salary_date')->get();
        foreach ($salaries as $salary) {
            $message = "Pay salary to Staff: {$salary->staff->name}. Amount: ৳{$salary->amount}.";
            $this->processNotification($salary, $salary->salary_date, $message, 'pay', 'staff.salary.list');
        }
        $paidIds = StaffSalary::where('status', 'Paid')->pluck('id');
        Notification::whereIn('notifiable_id', $paidIds)->where('notifiable_type', StaffSalary::class)->update(['status' => 'cleared', 'cleared_at' => now()]);
    }

    private function generateLoanInstallmentNotifications()
    {
        $installments = Installment::with('loan')->where('status', 'unpaid')->whereNotNull('pay_date')->get();
        foreach ($installments as $installment) {
            $message = "Loan installment due for {$installment->loan->loan_name}. Amount: ৳{$installment->installment_amount}.";
            // Example of passing route parameters for the action button
            $params = ['loan' => $installment->loan_id];
            $this->processNotification($installment, $installment->pay_date, $message, 'pay', 'loan.report', $params);
        }
        $paidIds = Installment::where('status', 'paid')->pluck('id');
        Notification::whereIn('notifiable_id', $paidIds)->where('notifiable_type', Installment::class)->update(['status' => 'cleared', 'cleared_at' => now()]);
    }

    private function generateCompanyProjectNotifications()
    {
        $projects = CompanyProject::with('transactions')->get();
        $lossThreshold = 100000;
        $projectIdsToClear = [];

        foreach ($projects as $project) {
            $totalLoss = $project->transactions->where('type', 'loss')->sum('amount');
            if ($totalLoss > $lossThreshold) {
                Notification::updateOrCreate(
                    [
                        'notifiable_id' => $project->id,
                        'notifiable_type' => get_class($project),
                    ],
                    [
                        'type' => 'info',
                        'level' => 'red',
                        'message' => "Company Project '{$project->project_name}' has exceeded the loss threshold. Total Loss: ৳{$totalLoss}.",
                        'due_date' => null,
                        'days_left' => null,
                        'action_route' => 'company.project.list',
                        'action_params' => json_encode([]),
                        'status' => 'active',
                        'updated_level_at' => now(),
                    ]
                );
            } else {
                // If the loss is now below the threshold, mark it for clearing.
                $projectIdsToClear[] = $project->id;
            }
        }
        
        // Clear notifications for all projects that have recovered from high loss.
        if (!empty($projectIdsToClear)) {
            Notification::whereIn('notifiable_id', $projectIdsToClear)
                ->where('notifiable_type', CompanyProject::class)
                ->where('status', 'active')
                ->update(['status' => 'cleared', 'cleared_at' => now()]);
        }
    }
}

