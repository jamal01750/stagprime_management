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
        $transactions = CreditOrDebit::where('approve_status','pending')
                            ->orderBy('date', 'desc')
                            // ->get()
                            ->paginate(5);

        $credits = $transactions->where('type', 'credit');
        $debits = $transactions->where('type', 'debit');
        $totalCredits = $credits->sum('amount');
        $totalDebits = $debits->sum('amount');
        $totalbalance = $totalCredits - $totalDebits;
        
        return view('credit_debit.add_transaction', [
            'transactions' => $transactions,
            'totalCredits' => number_format($totalCredits, 2),
            'totalDebits' => number_format($totalDebits, 2),
            'balance' => number_format($totalbalance, 2),
        ]);
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
            'approve_status' => 'pending',
        ]);
        return redirect()->back()->with('success', 'Transaction added successfully!');
    }

    // edit a transaction

    public function edit($id)
    {
        $transaction = CreditOrDebit::findOrFail($id);
        return view('credit_debit.edit_transaction', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        $transaction = CreditOrDebit::findOrFail($id);

        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        $transaction->update([
            'date' => $request->input('date'),
            'type' => $request->input('type'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
        ]);

        return redirect()->route('credit.debit.report')->with('success', 'Transaction updated successfully!');
    }

    public function destroy($id)
    {
        $transaction = CreditOrDebit::findOrFail($id);
        $transaction->delete();

        return redirect()->route('credit.debit.report')->with('success', 'Transaction deleted successfully!');
    }
  

    // Download only pending transactions as PDF
    public function downloadPendingPDF()
    {
        $transactions = CreditOrDebit::where('approve_status', 'pending')
            ->orderBy('date', 'asc')
            ->get();

        if ($transactions->isEmpty()) {
            return redirect()->back()->with('success', 'No pending transactions found.');
        }

        // Calculate totals
        $credits = $transactions->where('type', 'credit');
        $debits = $transactions->where('type', 'debit');
        $totalCredits = $credits->sum('amount');
        $totalDebits = $debits->sum('amount');
        $balance = $totalCredits - $totalDebits;

        $data = [
            'transactions' => $transactions,
            'totalCredits' => number_format($totalCredits, 2),
            'totalDebits' => number_format($totalDebits, 2),
            'balance' => number_format($balance, 2),
            'generated_at' => now('Asia/Dhaka')->format('d M Y, h:i A'),
        ];

        $pdf = Pdf::loadView('pdf.credit_debit_report', $data)
                ->setPaper('a4', 'portrait');


        return $pdf->stream('pending-transactions.pdf');
        // return $pdf->download('pending-transactions.pdf');
    }

    // ✅ Initial + filtered report page
    public function report(Request $request)
    {
        $query = CreditOrDebit::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('approve_status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->orderBy('date', 'desc')->paginate(10);

        $totalCredits = $transactions->where('type', 'credit')->sum('amount');
        $totalDebits = $transactions->where('type', 'debit')->sum('amount');
        $balance = $totalCredits - $totalDebits;

        return view('credit_debit.report_generation', [
            'transactions' => $transactions,
            'totalCredits' => number_format($totalCredits, 2),
            'totalDebits' => number_format($totalDebits, 2),
            'balance' => number_format($balance, 2),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'selected_status' => $request->status,
        ]);
    }

    // ✅ AJAX status update
    public function updateStatus(Request $request)
    {
        $transaction = CreditOrDebit::find($request->id);

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found']);
        }

        $transaction->approve_status = $request->status;
        $transaction->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    // ✅ Download selected or all transactions as PDF
    public function downloadPdf(Request $request)
    {
        $ids = $request->transaction_ids ?? null;

        $query = CreditOrDebit::query();
        if ($ids) {
            $query->whereIn('id', $ids);
        }

        $transactions = $query->orderBy('date', 'asc')->get();

        // Filter credits and debits safely
        $credits = $transactions->filter(fn($t) => $t->type === 'credit');
        $debits = $transactions->filter(fn($t) => $t->type === 'debit');
        $totalCredits = $credits->sum('amount');
        $totalDebits = $debits->sum('amount');
        $balance = $totalCredits - $totalDebits;

        $data = [
            'transactions' => $transactions,
            'totalCredits' => number_format($totalCredits, 2),
            'totalDebits' => number_format($totalDebits, 2),
            'balance' => number_format($balance, 2),
            'generated_at' => now('Asia/Dhaka')->format('d M Y, h:i A'),
        ];

        $pdf = Pdf::loadView('pdf.credit_debit_report', $data)
                ->setPaper('a4', 'portrait');
        
        return $pdf->stream('Transaction_Report.pdf');
    }
    
    
}
