<?php

namespace App\Http\Controllers;

use App\Models\ClientProjectTransaction;
use App\Models\CompanyProjectTransaction;
use Illuminate\Http\Request;
use App\Models\MonthlyTarget;
use App\Models\Installment;
use App\Models\InternshipRegistration;
use App\Models\MonthlyOfflineCost;
use App\Models\MonthlyOnlineCost;
use App\Models\ProductSale;
use App\Models\StaffSalary;
use App\Models\Student;
use App\Models\StudentPayment;
use Carbon\Carbon;

class RevenueAndTargetController extends Controller
{
    public function index()
    {
        $currentDate = Carbon::now();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;

        // --- 1. Fetch This Month's Fixed Target ---
        $target = MonthlyTarget::where('year', $currentYear)->where('month', $currentMonth)->first();
        $targetAmount = $target->target_amount ?? 0;

        // --- 2. Calculate TOTAL LIVE REVENUE for the Current Month ---
        $totalRevenue = 0;
        $revenueByCategory = [];

        $revenueByCategory['Product Sales'] = ProductSale::whereYear('paid_date', $currentYear)->whereMonth('paid_date', $currentMonth)->sum('amount');
        
        $studentAdmissionRevenue = Student::whereYear('admission_date', $currentYear)->whereMonth('admission_date', $currentMonth)->sum('paid_amount')
                                 + StudentPayment::whereYear('pay_date', $currentYear)->whereMonth('pay_date', $currentMonth)->sum('pay_amount');
        $revenueByCategory['Student Admission'] = $studentAdmissionRevenue;

        $revenueByCategory['Company Projects'] = CompanyProjectTransaction::whereYear('date', $currentYear)->whereMonth('date', $currentMonth)->where('type', 'profit')->sum('amount');
        $revenueByCategory['Client Projects'] = ClientProjectTransaction::whereYear('date', $currentYear)->whereMonth('date', $currentMonth)->where('type', 'profit')->sum('amount');

        $totalRevenue = array_sum($revenueByCategory);

        // --- 3. Calculate TOTAL LIVE EXPENSE for the Current Month ---
        $totalExpense = 0;
        $dollarToTakaRate = session('result'); // Assuming this is set somewhere in your application

        $totalExpense += MonthlyOfflineCost::whereYear('paid_date', $currentYear)->whereMonth('paid_date', $currentMonth)->sum('amount');
        
        $paidOnline = MonthlyOnlineCost::whereYear('paid_date', $currentYear)->whereMonth('paid_date', $currentMonth)->get();
        foreach($paidOnline as $cost) { $totalExpense += ($cost->amount_type == 'dollar') ? $cost->amount * $dollarToTakaRate : $cost->amount; }
        $activatedOnline = MonthlyOnlineCost::whereYear('activate_date', $currentYear)->whereMonth('activate_date', $currentMonth)->get();
        foreach($activatedOnline as $cost) { $totalExpense += ($cost->activate_type == 'dollar') ? $cost->activate_cost * $dollarToTakaRate : $cost->activate_cost; }

        $totalExpense += Installment::whereYear('pay_date', $currentYear)->whereMonth('pay_date', $currentMonth)->where('status', 'paid')->sum('installment_amount');
        
        $interns = InternshipRegistration::all();
        foreach ($interns as $intern) {
            if ($intern->pay_amount <= 0) continue;
            $installmentAmount = $intern->pay_amount / 3;
            $paidDates = [$intern->paid_date, $intern->paid_date2, $intern->paid_date3];
            foreach ($paidDates as $paidDate) {
                if ($paidDate && Carbon::parse($paidDate)->year == $currentYear && Carbon::parse($paidDate)->month == $currentMonth) {
                    $totalExpense += $installmentAmount;
                }
            }
        }
        $totalExpense += StaffSalary::whereYear('paid_date', $currentYear)->whereMonth('paid_date', $currentMonth)->sum('amount');

        // --- 4. Calculate LIVE SHORTAGE for the Current Month ---
        // Shortage is the remaining amount needed to hit the target. It cannot be negative.
        $shortage = max(0, $targetAmount - $totalRevenue);

        // --- 5. Prepare data for the view ---
        $pieChartData = [
            'labels' => array_keys($revenueByCategory),
            'data' => array_values($revenueByCategory),
        ];

        return view('revenue_target.summary', compact(
            'targetAmount',
            'totalRevenue',
            'totalExpense',
            'shortage',
            'pieChartData'
        ));
    }

    

    public function expenseReport(Request $request)
    {
        // Get the selected category from the request, default to 'offline'
        $category = $request->query('category', 'offline');

        // Set current date context and a conversion rate for USD to BDT
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $dollarToTakaRate = session('result'); 
        // return $dollarToTakaRate;

        $alreadyExpended = 0;
        $pendingExpense = 0;
        
        // Initialize arrays for graph data
        $daysInMonth = Carbon::now()->daysInMonth;
        $labels = range(1, $daysInMonth);
        $dailyExpenses = array_fill(1, $daysInMonth, 0);
        $graphLabel = 'Expended Amount (Taka)'; // Default graph label

        // Calculate metrics based on the selected category
        switch ($category) {
            case 'offline':
                $alreadyExpended = MonthlyOfflineCost::whereYear('paid_date', $currentYear)
                    ->whereMonth('paid_date', $currentMonth)
                    ->sum('amount');
                $pendingExpense = MonthlyOfflineCost::whereYear('last_date', $currentYear)
                    ->whereMonth('last_date', $currentMonth)
                    ->where('status', 'unpaid')
                    ->sum('amount');
                $expendedRecords = MonthlyOfflineCost::whereYear('paid_date', $currentYear)
                    ->whereMonth('paid_date', $currentMonth)
                    ->get();
                foreach ($expendedRecords as $record) {
                    $day = Carbon::parse($record->paid_date)->day;
                    if (isset($dailyExpenses[$day])) {
                        $dailyExpenses[$day] += $record->amount;
                    }
                }
                break;

            case 'online':
                $paidAmounts = MonthlyOnlineCost::whereYear('paid_date', $currentYear)
                    ->whereMonth('paid_date', $currentMonth)
                    ->get();
                foreach($paidAmounts as $cost) {
                    $amountInTaka = ($cost->amount_type == 'dollar') ? $cost->amount * $dollarToTakaRate : $cost->amount;
                    $alreadyExpended += $amountInTaka;
                    $day = Carbon::parse($cost->paid_date)->day;
                     if (isset($dailyExpenses[$day])) {
                        $dailyExpenses[$day] += $amountInTaka;
                    }
                }
                $activatedCosts = MonthlyOnlineCost::whereYear('activate_date', $currentYear)
                    ->whereMonth('activate_date', $currentMonth)
                    ->get();
                foreach($activatedCosts as $cost) {
                    $amountInTaka = ($cost->activate_type == 'dollar') ? $cost->activate_cost * $dollarToTakaRate : $cost->activate_cost;
                    $alreadyExpended += $amountInTaka;
                    $day = Carbon::parse($cost->activate_date)->day;
                    if (isset($dailyExpenses[$day])) {
                        $dailyExpenses[$day] += $amountInTaka;
                    }
                }
                $pendingAmounts = MonthlyOnlineCost::whereYear('expire_date', $currentYear)
                    ->whereMonth('expire_date', $currentMonth)
                    ->where('status', 'unpaid')
                    ->get();
                foreach($pendingAmounts as $cost) {
                     $amountInTaka = ($cost->amount_type == 'dollar') ? $cost->amount * $dollarToTakaRate : $cost->amount;
                     $pendingExpense += $amountInTaka;
                }
                break;

            case 'installment':
                $alreadyExpended = Installment::whereYear('pay_date', $currentYear)
                    ->whereMonth('pay_date', $currentMonth)
                    ->where('status', 'paid')
                    ->sum('installment_amount');
                $pendingExpense = Installment::whereYear('next_date', $currentYear)
                    ->whereMonth('next_date', $currentMonth)
                    ->where('status', 'unpaid')
                    ->sum('installment_amount');
                $expendedRecords = Installment::whereYear('pay_date', $currentYear)
                    ->whereMonth('pay_date', $currentMonth)
                    ->where('status', 'paid')
                    ->get();
                foreach ($expendedRecords as $record) {
                    $day = Carbon::parse($record->pay_date)->day;
                    if (isset($dailyExpenses[$day])) {
                        $dailyExpenses[$day] += $record->installment_amount;
                    }
                }
                break;

            case 'intern':
                $interns = InternshipRegistration::all();
                
                foreach ($interns as $intern) {
                    if ($intern->pay_amount <= 0) continue; // Skip if no amount is due
                    $installmentAmount = $intern->pay_amount / 3;

                    // --- Calculate Already Expended & Graph Data based on actual paid dates ---
                    $paidDates = [$intern->paid_date, $intern->paid_date2, $intern->paid_date3];
                    foreach ($paidDates as $paidDate) {
                        if ($paidDate) {
                            $paymentDate = Carbon::parse($paidDate);
                            if ($paymentDate->year == $currentYear && $paymentDate->month == $currentMonth) {
                                $alreadyExpended += $installmentAmount;
                                $day = $paymentDate->day;
                                if (isset($dailyExpenses[$day])) {
                                    $dailyExpenses[$day] += $installmentAmount;
                                }
                            }
                        }
                    }

                    // --- Recalculate Pending Expense based on due dates vs paid status ---
                    $admissionDate = Carbon::parse($intern->admission_date);

                    // Check 1st Installment Due
                    $firstDueDate = $admissionDate->copy()->addMonth();
                    if ($firstDueDate->year == $currentYear && $firstDueDate->month == $currentMonth && is_null($intern->paid_date)) {
                        $pendingExpense += $installmentAmount;
                    }

                    // Check 2nd Installment Due
                    $secondDueDate = $admissionDate->copy()->addMonths(2);
                    if ($secondDueDate->year == $currentYear && $secondDueDate->month == $currentMonth && is_null($intern->paid_date2)) {
                        $pendingExpense += $installmentAmount;
                    }

                    // Check 3rd Installment Due
                    $thirdDueDate = $admissionDate->copy()->addMonths(3);
                    if ($thirdDueDate->year == $currentYear && $thirdDueDate->month == $currentMonth && is_null($intern->paid_date3)) {
                        $pendingExpense += $installmentAmount;
                    }
                }
                break;
                
            case 'staff':
                $alreadyExpended = StaffSalary::whereYear('paid_date', $currentYear)
                    ->whereMonth('paid_date', $currentMonth)
                    ->sum('amount');
                $pendingExpense = StaffSalary::whereYear('salary_date', $currentYear)
                    ->whereMonth('salary_date', $currentMonth)
                    ->where('status', 'Unpaid')
                    ->sum('amount');
                $expendedRecords = StaffSalary::whereYear('paid_date', $currentYear)
                    ->whereMonth('paid_date', $currentMonth)
                    ->get();
                foreach ($expendedRecords as $record) {
                    $day = Carbon::parse($record->paid_date)->day;
                    if (isset($dailyExpenses[$day])) {
                        $dailyExpenses[$day] += $record->amount;
                    }
                }
                break;
        }

        // Finalize graph data structure
        $graphData = [
            'labels' => $labels,
            'data' => array_values($dailyExpenses),
            'label' => $graphLabel,
        ];

        // Return the view with all the calculated data
        return view('revenue_target.expense_report', compact(
            'category',
            'alreadyExpended',
            'pendingExpense',
            'graphData'
        ));
    }

    public function revenueReport(Request $request)
    {
        // Get category from URL, default to 'product_sell'
        $category = $request->query('category', 'product_sell');

        // Set date context for the report
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $collectedRevenue = 0;
        $pendingRevenue = 0;
        
        // Initialize arrays for the graph
        $daysInMonth = Carbon::now()->daysInMonth;
        $labels = range(1, $daysInMonth);
        $dailyRevenue = array_fill(1, $daysInMonth, 0);

        // Calculate metrics based on the selected category
        switch ($category) {
            case 'product_sell':
                // Collected Revenue: Sum of amounts from paid sales this month
                $collectedRevenue = ProductSale::whereYear('paid_date', $currentYear)
                    ->whereMonth('paid_date', $currentMonth)
                    ->sum('amount');

                // Pending Revenue: Sum of amounts from unpaid sales due this month
                $pendingRevenue = ProductSale::whereYear('due_date', $currentYear)
                    ->whereMonth('due_date', $currentMonth)
                    ->where('status', 'unpaid')
                    ->sum('amount');

                // Graph Data: Daily collection from product sales
                $paidSales = ProductSale::whereYear('paid_date', $currentYear)
                    ->whereMonth('paid_date', $currentMonth)
                    ->get();
                foreach ($paidSales as $sale) {
                    $day = Carbon::parse($sale->paid_date)->day;
                    if (isset($dailyRevenue[$day])) {
                        $dailyRevenue[$day] += $sale->amount;
                    }
                }
                break;

            case 'student_admission':
                // Collected Revenue (Part 1): Initial payments from new admissions this month
                $admissionPayments = Student::whereYear('admission_date', $currentYear)
                    ->whereMonth('admission_date', $currentMonth)
                    ->sum('paid_amount');

                // Collected Revenue (Part 2): Installments/other payments received this month
                $installmentPayments = StudentPayment::whereYear('pay_date', $currentYear)
                    ->whereMonth('pay_date', $currentMonth)
                    ->sum('pay_amount');
                
                $collectedRevenue = $admissionPayments + $installmentPayments;

                // Pending Revenue: Current due amounts from students whose next payment is due this month
                $pendingRevenue = Student::whereYear('payment_due_date', $currentYear)
                    ->whereMonth('payment_due_date', $currentMonth)
                    ->where('payment_status', 'Unpaid')
                    ->sum('due_amount');

                // Graph Data: Combine daily revenue from both sources
                $newAdmissions = Student::whereYear('admission_date', $currentYear)
                    ->whereMonth('admission_date', $currentMonth)
                    ->get();
                foreach ($newAdmissions as $student) {
                    $day = Carbon::parse($student->admission_date)->day;
                    if(isset($dailyRevenue[$day])) $dailyRevenue[$day] += $student->paid_amount;
                }
                $paidInstallments = StudentPayment::whereYear('pay_date', $currentYear)
                    ->whereMonth('pay_date', $currentMonth)
                    ->get();
                foreach ($paidInstallments as $payment) {
                    $day = Carbon::parse($payment->pay_date)->day;
                    if(isset($dailyRevenue[$day])) $dailyRevenue[$day] += $payment->pay_amount;
                }
                break;

            case 'company_project':
                // Collected Revenue: Sum of 'profit' transactions this month
                $collectedRevenue = CompanyProjectTransaction::whereYear('date', $currentYear)
                    ->whereMonth('date', $currentMonth)
                    ->where('type', 'profit')
                    ->sum('amount');

                $pendingRevenue = 0; // As per requirements

                // Graph Data: Daily profit from company projects
                $transactions = CompanyProjectTransaction::whereYear('date', $currentYear)
                    ->whereMonth('date', $currentMonth)
                    ->where('type', 'profit')
                    ->get();
                foreach ($transactions as $transaction) {
                    $day = Carbon::parse($transaction->date)->day;
                    if(isset($dailyRevenue[$day])) $dailyRevenue[$day] += $transaction->amount;
                }
                break;
                
            case 'client_project':
                 // Collected Revenue: Sum of 'profit' transactions this month
                $collectedRevenue = ClientProjectTransaction::whereYear('date', $currentYear)
                    ->whereMonth('date', $currentMonth)
                    ->where('type', 'profit')
                    ->sum('amount');

                $pendingRevenue = 0; // As per requirements

                // Graph Data: Daily profit from client projects
                $transactions = ClientProjectTransaction::whereYear('date', $currentYear)
                    ->whereMonth('date', $currentMonth)
                    ->where('type', 'profit')
                    ->get();
                foreach ($transactions as $transaction) {
                    $day = Carbon::parse($transaction->date)->day;
                    if(isset($dailyRevenue[$day])) $dailyRevenue[$day] += $transaction->amount;
                }
                break;
        }

        // Finalize graph data structure
        $graphData = [
            'labels' => $labels,
            'data' => array_values($dailyRevenue),
        ];

        return view('revenue_target.revenue_report', compact(
            'category',
            'collectedRevenue',
            'pendingRevenue',
            'graphData'
        ));
    }
}

