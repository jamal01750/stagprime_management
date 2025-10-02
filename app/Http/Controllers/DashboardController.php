<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

// Import All Necessary Models
use App\Models\ClientProject;
use App\Models\ClientProjectDebit;
use App\Models\ClientProjectTransaction;
use App\Models\CompanyProjectTransaction;
use App\Models\CreditOrDebit;
use App\Models\Installment;
use App\Models\InternshipRegistration;
use App\Models\Loan;
use App\Models\MonthlyOfflineCost;
use App\Models\MonthlyOnlineCost;
use App\Models\MonthlyTarget;
use App\Models\PriorityNotification;
use App\Models\ProductLoss;
use App\Models\ProductSale;
use App\Models\StaffSalary;
use App\Models\Student;
use App\Models\StudentPayment;
use Illuminate\Support\Facades\DB;
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use Carbon\Month;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $currentYear = $now->year;
        $currentMonth = $now->month;
        
        // --- DATA COLLECTIONS ---
        $allTimeData = $this->getAllTimeSummary();
        $currentMonthData = $this->getCurrentMonthSummary($currentYear, $currentMonth);
        $chartsData = $this->getChartsData($currentYear, $currentMonth, $currentMonthData['target']);
        $alerts = $this->getAlertsAndReminders();
        $recentTransactions = $this->getRecentTransactions();

        return view('welcome', compact(
            'allTimeData',
            'currentMonthData',
            'chartsData',
            'alerts',
            'recentTransactions'
        ));
    }

    /**
     * Get All-Time Financial Summary Data.
     */
    private function getAllTimeSummary()
    {
        // 1. Total Revenue (All Time)
        $totalRevenue = ProductSale::sum('amount')
            + Student::sum('paid_amount')
            + StudentPayment::sum('pay_amount')
            + CompanyProjectTransaction::where('type', 'profit')->sum('amount')
            + ClientProjectTransaction::where('type', 'profit')->sum('amount')
            + CreditOrDebit::where('type', 'credit')->sum('amount');

        // 2. Total Expense (All Time)
        $totalExpense = MonthlyOfflineCost::where('status', 'paid')->sum('amount')
            + Installment::where('status', 'paid')->sum('installment_amount')
            + StaffSalary::where('status', 'Paid')->sum('amount')
            + CreditOrDebit::where('type', 'debit')->sum('amount')
            // + ProductLoss::sum('loss_amount')
            + CompanyProjectTransaction::where('type', 'invest')->sum('amount')
            + ClientProjectTransaction::where('type', 'invest')->sum('amount');
            // + CompanyProjectTransaction::whereIn('type', ['invest', 'loss'])->sum('amount')
            // + ClientProjectTransaction::whereIn('type', ['invest', 'loss'])->sum('amount');
        
        $internSalaries = InternshipRegistration::sum('total_paid');
        $totalExpense += $internSalaries;
        
        // Handle online costs with currency conversion
        $dollarToTakaRate = $this->getExchangeRate();
        $paidOnline = MonthlyOnlineCost::whereNotNull('paid_date')->get();
        foreach($paidOnline as $cost) { $totalExpense += ($cost->amount_type == 'dollar') ? $cost->amount * $dollarToTakaRate : $cost->amount; }
        $activatedOnline = MonthlyOnlineCost::whereNotNull('activate_date')->get();
        foreach($activatedOnline as $cost) { $totalExpense += ($cost->activate_type == 'dollar') ? $cost->activate_cost * $dollarToTakaRate : $cost->activate_cost; }

        // 3. Net Profit / Loss
        $netProfitLoss = $totalRevenue - $totalExpense;

        // 4. Outstanding Loan
        $outstandingLoan = Loan::sum('due_amount');

        // 5. Client Receivables
        $clientReceivables = 0;
        $clientRec = ClientProject::where('status', 'unpaid')->get();
        foreach($clientRec as $cost) { $clientReceivables += ($cost->currency == 'dollar') ? $cost->due_amount * $dollarToTakaRate : $cost->due_amount; }

        // 6. Student Receivables
        $studentReceivables = Student::where('payment_status', 'Unpaid')->sum('due_amount');

        return [
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'netProfitLoss' => $netProfitLoss,
            'outstandingLoan' => $outstandingLoan,
            'clientReceivables' => $clientReceivables,
            'studentReceivables' => $studentReceivables,
        ];
    }

    /**
     * Get Current Month Financial Summary Data.
     */
    private function getCurrentMonthSummary($year, $month)
    {
        $dollarToTakaRate = $this->getExchangeRate();

        // --- REVENUE (This Month) ---
        $productRevenue = ProductSale::whereYear('paid_date', $year)->whereMonth('paid_date', $month)->sum('amount');
        $studentRevenue = Student::whereYear('admission_date', $year)->whereMonth('admission_date', $month)->sum('paid_amount')
                        + StudentPayment::whereYear('pay_date', $year)->whereMonth('pay_date', $month)->sum('pay_amount');
        $ownProjectProfit = CompanyProjectTransaction::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'profit')->sum('amount');
        $clientProjectProfit = ClientProjectTransaction::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'profit')->sum('amount');
        $totalCredit = CreditOrDebit::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'credit')->sum('amount');
        $collectedRevenue = $productRevenue + $studentRevenue + $ownProjectProfit + $clientProjectProfit + $totalCredit;
        
        // --- EXPENSE (This Month) ---
        $expenseOffline = MonthlyOfflineCost::whereYear('paid_date', $year)->whereMonth('paid_date', $month)->sum('amount');
        $expenseInstallments = Installment::whereYear('pay_date', $year)->whereMonth('pay_date', $month)->where('status', 'paid')->sum('installment_amount');
        $expenseIntern = $this->calculateMonthlyInternSalary($year, $month);
        $expenseStaff = StaffSalary::whereYear('paid_date', $year)->whereMonth('paid_date', $month)->sum('amount');
        $totalDebit = CreditOrDebit::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'debit')->sum('amount');
        
        $expenseOnline = 0;
        $paidOnline = MonthlyOnlineCost::whereYear('paid_date', $year)->whereMonth('paid_date', $month)->get();
        foreach($paidOnline as $cost) { $expenseOnline += ($cost->amount_type == 'dollar') ? $cost->amount * $dollarToTakaRate : $cost->amount; }
        $activatedOnline = MonthlyOnlineCost::whereYear('activate_date', $year)->whereMonth('activate_date', $month)->get();
        foreach($activatedOnline as $cost) { $expenseOnline += ($cost->activate_type == 'dollar') ? $cost->activate_cost * $dollarToTakaRate : $cost->activate_cost; }

        $totalExpense = $expenseOffline + $expenseInstallments + $expenseIntern + $expenseStaff + $totalDebit + $expenseOnline;
        $totalExpense += CompanyProjectTransaction::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'invest')->sum('amount');
        $totalExpense += ClientProjectTransaction::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'invest')->sum('amount');
        
        // --- PENDING (This Month) ---
        $studentPending = Student::whereYear('payment_due_date', $year)->whereMonth('payment_due_date', $month)->where('payment_status', 'Unpaid')->sum('due_amount');
        
        $clientPending = 0;
        $clientPen = ClientProjectDebit::whereYear('pay_date', $year)->whereMonth('pay_date', $month)->where('status', 'unpaid')->get();
        foreach($clientPen as $cost) { $clientPending += ($cost->currency == 'dollar') ? $cost->pay_amount * $dollarToTakaRate : $cost->pay_amount; }

        $internPending = $this->calculateMonthlyInternSalary($year, $month, 'pending');
        $staffPending = StaffSalary::whereYear('salary_date', $year)->whereMonth('salary_date', $month)->where('status', 'Unpaid')->sum('amount');
        $offlinePending = MonthlyOfflineCost::whereYear('last_date', $year)->whereMonth('last_date', $month)->where('status', 'unpaid')->sum('amount');
        $installmentsPending = Installment::whereYear('pay_date', $year)->whereMonth('pay_date', $month)->where('status', 'unpaid')->sum('installment_amount');
        $onlinePending = 0;
        $expire = MonthlyOnlineCost::whereYear('expire_date', $year)->whereMonth('expire_date', $month)->where('status', 'unpaid')->get();
        foreach($expire as $cost) { $onlinePending += ($cost->amount_type == 'dollar') ? $cost->amount * $dollarToTakaRate : $cost->amount; }
        

        // --- OTHER (This Month) ---
        $target = MonthlyTarget::where('year', $year)->where('month', $month)->first()->target_amount ?? 0;
        $shortfall = max(0, $target - $collectedRevenue);
        $netProfitLoss = $collectedRevenue - $totalExpense;
        $productLoss = ProductLoss::whereYear('created_at', $year)->whereMonth('created_at', $month)->sum('loss_amount');
        $ownProjectLoss = CompanyProjectTransaction::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'loss')->sum('amount');
        $clientProjectLoss = ClientProjectTransaction::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'loss')->sum('amount');

        return compact(
            'target', 'shortfall', 'collectedRevenue', 'totalExpense', 'netProfitLoss', 'totalCredit', 'totalDebit',
            'studentPending', 'internPending', 'staffPending', 'offlinePending', 'onlinePending', 'installmentsPending',
            'productRevenue', 'productLoss', 'ownProjectProfit', 'ownProjectLoss', 'clientProjectProfit', 'clientProjectLoss', 'clientPending'
        );
    }

    /**
     * Get Data for all charts.
     */
    private function getChartsData($year, $month, $monthlyTarget)
    {
        $dollarToTakaRate = $this->getExchangeRate();
        // 1. Monthly Revenue vs Expense Bar Chart (Current Year)
        $monthlyPerformance = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyPerformance['labels'][] = Carbon::create()->month($m)->shortMonthName;
            $monthlyPerformance['revenue'][] = $this->calculateMonthlyRevenue($year, $m);
            $monthlyPerformance['expense'][] = $this->calculateMonthlyExpense($year, $m);
        }

        // 2. Monthly Target Progress Line Chart (Current Month)
        $targetProgress = [
            'labels' => [],
            'revenue' => [],
            'target' => $monthlyTarget
        ];
        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
        $cumulativeRevenue = 0;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            $dailyRevenue = $this->getDailyRevenue($date);
            $cumulativeRevenue += $dailyRevenue;
            
            $targetProgress['labels'][] = $day;
            $targetProgress['revenue'][] = $cumulativeRevenue;
        }

        // 3. Category-wise Expense Pie Chart (Current Month)
        $expenseBreakdown = [
            'labels' => ['Offline', 'Online', 'Interns', 'Staff', 'Installments', 'Debit', 'Own Project', 'Client Project'],
            'data' => [
                MonthlyOfflineCost::whereYear('paid_date', $year)->whereMonth('paid_date', $month)->sum('amount'),
                $this->calculateMonthlyOnlineExpense($year, $month),
                $this->calculateMonthlyInternSalary($year, $month),
                StaffSalary::whereYear('paid_date', $year)->whereMonth('paid_date', $month)->sum('amount'),
                Installment::whereYear('pay_date', $year)->whereMonth('pay_date', $month)->where('status', 'paid')->sum('installment_amount'),
                CreditOrDebit::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'debit')->sum('amount'),
                CompanyProjectTransaction::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'invest')->sum('amount'),
                ClientProjectTransaction::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'invest')->sum('amount')
            ]
        ];

        // 4. Client Payment Status Pie Chart
        $paid1 = 0;
        $client1 = ClientProject::whereYear('created_at', $year)->whereMonth('created_at', $month)->get();
        foreach($client1 as $cost) { $paid1 += ($cost->currency == 'dollar') ? $cost->advance_amount * $dollarToTakaRate : $cost->advance_amount; }
        
        $paid2 = 0;
        $client2 = ClientProjectDebit::whereYear('pay_date', $year)->whereMonth('pay_date', $month)->where('status', 'paid')->get();
        foreach($client2 as $cost) { $paid2 += ($cost->currency == 'dollar') ? $cost->pay_amount * $dollarToTakaRate : $cost->pay_amount; }

        $clientPending = 0;
        $clientPen = ClientProjectDebit::whereYear('pay_date', $year)->whereMonth('pay_date', $month)->whereDate('pay_date', '>', now())->where('status', 'unpaid')->get();
        foreach($clientPen as $cost) { $clientPending += ($cost->currency == 'dollar') ? $cost->pay_amount * $dollarToTakaRate : $cost->pay_amount; }
        
        $clientOverdue = 0;
        $clientDue = ClientProjectDebit::whereYear('pay_date', $year)->whereMonth('pay_date', $month)->whereDate('pay_date', '<=', now())->where('status', 'unpaid')->get();
        foreach($clientDue as $cost) { $clientOverdue += ($cost->currency == 'dollar') ? $cost->pay_amount * $dollarToTakaRate : $cost->pay_amount; }
        
        
        $clientStatus = [
            'paid' => $paid1 + $paid2,
            'pending' => $clientPending,
            'overdue' => $clientOverdue,
        ];

        // 5. Student Payment Status Pie Chart
        $studentStatus = [
            'paid' => Student::whereYear('admission_date', $year)->whereMonth('admission_date', $month)->sum('paid_amount')
                      + StudentPayment::whereYear('pay_date', $year)->whereMonth('pay_date', $month)->sum('pay_amount'),
            'pending' => Student::whereYear('payment_due_date', $year)->whereMonth('payment_due_date', $month)->whereDate('payment_due_date', '>', now())->where('payment_status', 'Unpaid')->sum('due_amount'),
            'overdue' => Student::whereYear('payment_due_date', $year)->whereMonth('payment_due_date', $month)->whereDate('payment_due_date', '<=', now())->where('payment_status', 'Unpaid')->sum('due_amount'),
        ];
        
        // 6. Loan Repayment Progress (REFORMATTED FOR CHART.JS)
        $loanRepayment = [
            'labels' => [],
            'data' => []
        ];
        $loans = Loan::all();
        $totalLoan = $loans->sum('loan_amount');

        foreach (range(1, 12) as $m) {
            $monthEnd = now('Asia/Dhaka')->setDate($year, $m, 1)->endOfMonth();
            $loanRepayment['labels'][] = $monthEnd->shortMonthName;

            $paidInstallments = Installment::where('status', 'paid')
                ->whereBetween('pay_date', ["{$year}-01-01", $monthEnd])
                ->sum('installment_amount');
            
            $loanRepayment['data'][] = max($totalLoan - $paidInstallments, 0);
        }
        
        return compact('monthlyPerformance', 'targetProgress', 'expenseBreakdown', 'clientStatus', 'studentStatus', 'loanRepayment');
    }

    /**
     * Get Alerts and Reminders.
     */
    private function getAlertsAndReminders()
    {
        $upcomingPayments = MonthlyOfflineCost::with('category')
            ->where('status', 'unpaid')->whereDate('last_date', '>=', now())
            ->orderBy('last_date', 'asc')->take(2)->get();

        $loanRepayments = Installment::with('loan')
            ->where('status', 'unpaid')->whereDate('pay_date', '>=', now())
            ->orderBy('pay_date', 'asc')->take(2)->get();

        $clientPending = ClientProjectDebit::with('project')
            ->where('status', 'unpaid')->whereDate('pay_date', '<=', now())
            ->orderBy('pay_date', 'asc')->take(2)->get();

        $studentPending = Student::where('payment_status', 'Unpaid')
            ->whereDate('payment_due_date', '<=', now())
            ->orderBy('payment_due_date', 'asc')->take(2)->get();
            
        $priorityAlerts = PriorityNotification::with('product')
            ->where('is_active', true)->latest()->take(2)->get();

        return compact('upcomingPayments', 'loanRepayments', 'clientPending', 'studentPending', 'priorityAlerts');
    }

    /**
     * Get Recent Transactions.
     */
    private function getRecentTransactions()
    {
        $credits = CreditOrDebit::where('type', 'credit')->latest()->take(5)->get()
            ->map(function ($item) {
                return ['date' => $item->date, 'description' => "Credit", 'amount' => $item->amount, 'type' => 'Revenue'];
            });

        $debits = CreditOrDebit::where('type', 'debit')->latest()->take(5)->get()
            ->map(function ($item) {
                return ['date' => $item->date, 'description' => "Debit", 'amount' => $item->amount, 'type' => 'Expense'];
            });

        $offlineCosts = MonthlyOfflineCost::latest('paid_date')->take(5)->get()
            ->map(function ($item) {
                return ['date' => $item->paid_date, 'description' => "Offline Cost", 'amount' => $item->amount, 'type' => 'Expense'];
            });

        $sales = ProductSale::latest('paid_date')->take(5)->get()
            ->map(function ($item) {
                return ['date' => $item->paid_date, 'description' => "Product Sale", 'amount' => $item->amount, 'type' => 'Revenue'];
            });
        
        $studentPayments = StudentPayment::latest('pay_date')->take(5)->get()
            ->map(function ($item) {
                return ['date' => $item->pay_date, 'description' => "Student Payment", 'amount' => $item->pay_amount, 'type' => 'Revenue'];
            });

        $allTransactions = $credits->merge($debits)->merge($offlineCosts)->merge($sales)->merge($studentPayments);

        return $allTransactions->sortByDesc('date')->take(10);
    }
    
    // --- HELPER FUNCTIONS ---

    private function getExchangeRate() {
        if (session('result')) {
            return session('result');
        }
        $exchangeRates = app(ExchangeRate::class);
        $result = $exchangeRates->shouldBustCache()->exchangeRate('USD', 'BDT', Carbon::now());
        session(['result' => $result ?? 1]); // Fallback to 1
        return $result ?? 1;
    }

    private function calculateMonthlyInternSalary($year, $month, $type = 'paid') {
        $totalSalary = 0;
        $interns = InternshipRegistration::all();
        
        foreach ($interns as $intern) {
            if ($intern->pay_amount <= 0) continue;
            $installmentAmount = $intern->pay_amount / 3;
            
            if ($type === 'paid') {
                $paidDates = [$intern->paid_date, $intern->paid_date2, $intern->paid_date3];
                foreach ($paidDates as $paidDate) {
                    if ($paidDate && Carbon::parse($paidDate)->year == $year && Carbon::parse($paidDate)->month == $month) {
                        $totalSalary += $installmentAmount;
                    }
                }
            } else { // 'pending'
                $admissionDate = Carbon::parse($intern->admission_date);
                $dueDates = [
                    $admissionDate->copy()->addMonth(),
                    $admissionDate->copy()->addMonths(2),
                    $admissionDate->copy()->addMonths(3)
                ];
                if (is_null($intern->paid_date) && $dueDates[0]->isSameMonth(Carbon::create($year, $month))) $totalSalary += $installmentAmount;
                if (is_null($intern->paid_date2) && $dueDates[1]->isSameMonth(Carbon::create($year, $month))) $totalSalary += $installmentAmount;
                if (is_null($intern->paid_date3) && $dueDates[2]->isSameMonth(Carbon::create($year, $month))) $totalSalary += $installmentAmount;
            }
        }
        return $totalSalary;
    }

    private function calculateMonthlyRevenue($year, $month) {
        return ProductSale::whereYear('paid_date', $year)->whereMonth('paid_date', $month)->sum('amount')
            + Student::whereYear('admission_date', $year)->whereMonth('admission_date', $month)->sum('paid_amount')
            + StudentPayment::whereYear('pay_date', $year)->whereMonth('pay_date', $month)->sum('pay_amount')
            + CompanyProjectTransaction::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'profit')->sum('amount')
            + ClientProjectTransaction::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'profit')->sum('amount')
            + CreditOrDebit::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'credit')->sum('amount');
    }

    private function calculateMonthlyExpense($year, $month) {
        $expense = MonthlyOfflineCost::whereYear('paid_date', $year)->whereMonth('paid_date', $month)->sum('amount')
            + Installment::whereYear('pay_date', $year)->whereMonth('pay_date', $month)->where('status', 'paid')->sum('installment_amount')
            + $this->calculateMonthlyInternSalary($year, $month)
            + StaffSalary::whereYear('paid_date', $year)->whereMonth('paid_date', $month)->sum('amount')
            + CreditOrDebit::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'debit')->sum('amount')
            + CompanyProjectTransaction::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'invest')->sum('amount')
            + ClientProjectTransaction::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'invest')->sum('amount');
        
        $expense += $this->calculateMonthlyOnlineExpense($year, $month);
        return $expense;
    }
    
    private function calculateMonthlyOnlineExpense($year, $month) {
        $expenseOnline = 0;
        $dollarToTakaRate = $this->getExchangeRate();
        $paidOnline = MonthlyOnlineCost::whereYear('paid_date', $year)->whereMonth('paid_date', $month)->get();
        foreach($paidOnline as $cost) { $expenseOnline += ($cost->amount_type == 'dollar') ? $cost->amount * $dollarToTakaRate : $cost->amount; }
        $activatedOnline = MonthlyOnlineCost::whereYear('activate_date', $year)->whereMonth('activate_date', $month)->get();
        foreach($activatedOnline as $cost) { $expenseOnline += ($cost->activate_type == 'dollar') ? $cost->activate_cost * $dollarToTakaRate : $cost->activate_cost; }
        return $expenseOnline;
    }

    private function getDailyRevenue($date) {
        return ProductSale::whereDate('paid_date', $date)->sum('amount')
            + Student::whereDate('admission_date', $date)->sum('paid_amount')
            + StudentPayment::whereDate('pay_date', $date)->sum('pay_amount')
            + CompanyProjectTransaction::whereDate('date', $date)->where('type', 'profit')->sum('amount')
            + ClientProjectTransaction::whereDate('date', $date)->where('type', 'profit')->sum('amount')
            + CreditOrDebit::whereDate('date', $date)->where('type', 'credit')->sum('amount');
    }
}