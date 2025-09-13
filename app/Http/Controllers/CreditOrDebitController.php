<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditOrDebit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CreditOrDebitController extends Controller
{
    
    // Show the credit and debit summary
    public function index()
    {
        $today = now()->toDateString();
        $month = now()->month;
        $year  = now()->year;

        // Today
        $todayCredits = CreditOrDebit::whereDate('date', $today)->where('type', 'credit')->sum('amount');
        $todayDebits  = CreditOrDebit::whereDate('date', $today)->where('type', 'debit')->sum('amount');
        $todayBalance = $todayCredits - $todayDebits;

        // This Month
        $monthCredits = CreditOrDebit::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'credit')->sum('amount');
        $monthDebits  = CreditOrDebit::whereYear('date', $year)->whereMonth('date', $month)->where('type', 'debit')->sum('amount');
        $monthBalance = $monthCredits - $monthDebits;

        // This Year
        $yearCredits = CreditOrDebit::whereYear('date', $year)->where('type', 'credit')->sum('amount');
        $yearDebits  = CreditOrDebit::whereYear('date', $year)->where('type', 'debit')->sum('amount');
        $yearBalance = $yearCredits - $yearDebits;

        // Chart Data
        $labels = [];
        $dcredits = [];
        $ddebits = [];
        $dbalance = [];

        $rows = CreditOrDebit::selectRaw("
            DAY(`date`) as day,
            SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as credit_sum,
            SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) as debit_sum
        ")
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->groupBy(DB::raw('DAY(`date`)'))
        ->orderBy(DB::raw('DAY(`date`)'))
        ->get()
        ->keyBy('day');

        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $labels[]  = $day; // use day number instead of weekday name
            $c = (float)($rows[$day]->credit_sum ?? 0);
            $de = (float)($rows[$day]->debit_sum ?? 0);

            $dcredits[] = round($c, 2);
            $ddebits[]  = round($de, 2);
            $dbalance[] = round($c - $de, 2);
        }

        return view('credit_debit.credit_debit_summary', [
            'todayCredits' => $todayCredits,
            'todayDebits' => $todayDebits,
            'todayBalance' => $todayBalance,    
            'monthCredits' => $monthCredits,
            'monthDebits' => $monthDebits,
            'monthBalance' => $monthBalance,
            'yearCredits' => $yearCredits,
            'yearDebits' => $yearDebits,
            'yearBalance' => $yearBalance,
            'month' => $month,
            'year' => $year,
            'monthName' => Carbon::create()->month($month)->format('F'), // send month name
            'labels' => $labels,
            'dcredits' => $dcredits,
            'ddebits' => $ddebits,
            'dbalance' => $dbalance,
        ]);
    }



    // Create a new transaction
    public function showTransactionForm()
    {
        return view('credit_debit.add_transaction');
    }
    
    // Store a new transaction
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
          ]);
        CreditOrDebit::create([
            'date' => $request  ->input('date'),
            'type' => $request->input('type'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
        ]);
        return redirect()->back()->with('success', 'Transaction added successfully!');
    }
    
    // Report generation form
    public function report()
    {
        return view('credit_debit.report_generation');
    }
    
    // Report show
    public function showReports(Request $request)
    {
        // Fetch transactions within the specified date range
        $transactions = CreditOrDebit::whereBetween('date', [
            $request->start_date, $request->end_date
        ])->orderBy('date', 'desc')->get();
        
        $credits = $transactions->where('type', 'credit');
        $debits = $transactions->where('type', 'debit');
        $totalCredits = $credits->sum('amount');
        $totalDebits = $debits->sum('amount');
        $totalbalance = $totalCredits - $totalDebits;
        
        return view('credit_debit.report_generation', [
            'transactions' => $transactions,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'totalCredits' => number_format($totalCredits, 2),
            'totalDebits' => number_format($totalDebits, 2),
            'balance' => number_format($totalbalance, 2),
        ]);
        
    }

    // Download PDF report
    public function downloadPDF(Request $request)
    {

        $ids = $request->input('transaction_ids', []);

        if (empty($ids)) {
            $transactions = CreditOrDebit::whereBetween('date', [
            $request->start_date, $request->end_date
            ])->orderBy('date', 'asc')->get();
        }else {
            $transactions = CreditOrDebit::whereIn('id', $ids)->orderBy('date', 'asc')->get();
        }

        // Filter credits and debits safely
        $credits = $transactions->filter(fn($t) => $t->type === 'credit');
        $debits = $transactions->filter(fn($t) => $t->type === 'debit');
        $totalNewCredits = $credits->sum('amount');
        $totalNewDebits = $debits->sum('amount');
        $newBalance = $totalNewCredits - $totalNewDebits;

        $data = [
            'transactions' => $transactions,
            'totalNewCredits' => number_format($totalNewCredits, 2),
            'totalNewDebits' => number_format($totalNewDebits, 2),
            'newBalance' => number_format($newBalance, 2),
        ];

        // return $data
        $pdf = Pdf::loadView('pdf.credit_debit_report', $data);
        return $pdf->download('credit-debit-transaction-report.pdf');
    }

    
}
