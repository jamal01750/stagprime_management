<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\MonthlyTarget;
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

// Import all necessary expense models
use App\Models\MonthlyOfflineCost;
use App\Models\MonthlyOnlineCost;
use App\Models\Installment;
use App\Models\InternshipRegistration;
use App\Models\StaffSalary;

// Import all necessary revenue models
use App\Models\ProductSale;
use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\CompanyProjectTransaction;
use App\Models\ClientProjectTransaction;

use App\Models\PriorityProduct;
use App\Models\PriorityNotification;
use App\Models\PriorityProductBudget;

class GenerateMonthlyTarget extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'target:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates the new monthly target based on the previous month\'s expenses and revenue performance.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting monthly target generation with new formula...');

        // 1. Determine the target period (Current Month) and calculation period (Previous Month)
        $targetDate = Carbon::now();
        $calculationDate = Carbon::now()->subMonthNoOverflow();
        
        $targetYear = $targetDate->year;
        $targetMonth = $targetDate->month;

        $calcYear = $calculationDate->year;
        $calcMonth = $calculationDate->month;

        $this->info("Calculating data from {$calculationDate->format('F, Y')} to set target for {$targetDate->format('F, Y')}.");

        $totalExpenses = 0;
        $totalRevenue = 0;
        // Dollar to Taka exchange rate
        $exchangeRates = app(ExchangeRate::class);
        
        $result = $exchangeRates->shouldBustCache()
            ->exchangeRate(
            'USD',
            'BDT',
            Carbon::now()
        );
        if ($result === null) {
            $result = 1; // Fallback to 1 if exchange rate is not available
        }
        $dollarToTakaRate = $result;

        // --- 2. Calculate Total Expenses for the PREVIOUS month ---
        $this->line('Calculating previous month\'s total expenses...');
        $totalExpenses += MonthlyOfflineCost::whereYear('paid_date', $calcYear)->whereMonth('paid_date', $calcMonth)->sum('amount');
        $paidOnline = MonthlyOnlineCost::whereYear('paid_date', $calcYear)->whereMonth('paid_date', $calcMonth)->get();
        foreach($paidOnline as $cost) { $totalExpenses += ($cost->amount_type == 'dollar') ? $cost->amount * $dollarToTakaRate : $cost->amount; }
        $activatedOnline = MonthlyOnlineCost::whereYear('activate_date', $calcYear)->whereMonth('activate_date', $calcMonth)->get();
        foreach($activatedOnline as $cost) { $totalExpenses += ($cost->activate_type == 'dollar') ? $cost->activate_cost * $dollarToTakaRate : $cost->activate_cost; }
        $totalExpenses += Installment::whereYear('pay_date', $calcYear)->whereMonth('pay_date', $calcMonth)->where('status', 'paid')->sum('installment_amount');
        $interns = InternshipRegistration::all();
        foreach ($interns as $intern) {
            if ($intern->pay_amount <= 0) continue;
            $installmentAmount = $intern->pay_amount / 3;
            $paidDates = [$intern->paid_date, $intern->paid_date2, $intern->paid_date3];
            foreach ($paidDates as $paidDate) {
                if ($paidDate && Carbon::parse($paidDate)->year == $calcYear && Carbon::parse($paidDate)->month == $calcMonth) {
                    $totalExpenses += $installmentAmount;
                }
            }
        }
        $totalExpenses += StaffSalary::whereYear('paid_date', $calcYear)->whereMonth('paid_date', $calcMonth)->sum('amount');
        $this->info("Previous Month Total Expenses: " . number_format($totalExpenses, 2) . " BDT");

        // --- 3. Calculate Total Revenue for the PREVIOUS month ---
        $this->line('Calculating previous month\'s total revenue...');
        $totalRevenue += ProductSale::whereYear('paid_date', $calcYear)->whereMonth('paid_date', $calcMonth)->sum('amount');
        $totalRevenue += Student::whereYear('admission_date', $calcYear)->whereMonth('admission_date', $calcMonth)->sum('paid_amount');
        $totalRevenue += StudentPayment::whereYear('pay_date', $calcYear)->whereMonth('pay_date', $calcMonth)->sum('pay_amount');
        $totalRevenue += CompanyProjectTransaction::whereYear('date', $calcYear)->whereMonth('date', $calcMonth)->where('type', 'profit')->sum('amount');
        $totalRevenue += ClientProjectTransaction::whereYear('date', $calcYear)->whereMonth('date', $calcMonth)->where('type', 'profit')->sum('amount');
        $this->info("Previous Month Total Revenue: " . number_format($totalRevenue, 2) . " BDT");
        
        // --- 4. Apply the new formula to calculate the final target ---
        $this->line('Calculating new target...');
        $baseTarget = $totalExpenses * 2;
        $shortage = $baseTarget - $totalRevenue;
        
        // Shortage cannot be negative. If revenue exceeds the base target, there is no shortage.
        $shortage = max(0, $shortage);
        
        $finalTarget = $baseTarget + $shortage;

        $this->info("Base Target (Expenses * 2): " . number_format($baseTarget, 2));
        $this->info("Shortage (Base Target - Revenue): " . number_format($shortage, 2));
        $this->info("Final Target for this month: " . number_format($finalTarget, 2));

        // --- 5. Save the Target for the CURRENT month ---
        MonthlyTarget::updateOrCreate(
            ['year' => $targetYear, 'month' => $targetMonth],
            ['target_amount' => $finalTarget]
        );

        $this->info("Monthly target generation complete. Target for {$targetDate->format('F, Y')} is set to: " . number_format($finalTarget, 2) . " BDT");
        
        // priority products notifications
        $extraBudget = $finalTarget - $totalExpenses;
        $this->info("Extra Budget: " . number_format($extraBudget, 2) . " BDT");

        PriorityProductBudget::updateOrCreate(
            ['year' => now()->year, 'month' => now()->month],
            ['extra_budget' => $extraBudget]
        );

        // Recalc notifications
        $this->updateProductNotifications($extraBudget);

        return 0;
    }


    protected function updateProductNotifications($extraBudget)
    {
        $priorityProducts = PriorityProduct::where('is_purchased', false)->get();

        foreach ($priorityProducts as $product) {
            $required = $product->amount * 2;

            if ($extraBudget >= $required) {
                PriorityNotification::updateOrCreate(
                    ['priority_product_id' => $product->id],
                    ['is_active' => true]
                );
            } else {
                PriorityNotification::where('priority_product_id', $product->id)->delete();
            }
        }
    }

}


### **How to Use and Test**
// **Manual Test (Recommended):** To see the result immediately for the current month, run the command manually from your terminal:
//     ```bash
//     php artisan target:generate
    

