<?php

namespace App\Http\Controllers;

use Carbon\Month;
use Illuminate\Http\Request;
use App\Models\MonthlyTarget;
use App\Models\CreditOrDebit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RevenueAndTargetController extends Controller
{
    public function index(Request $request)
    {
        $year   = (int)($request->query('year') ?? now('Asia/Dhaka')->year);
        $month = (int)($request->query('month') ?? now('Asia/Dhaka')->month);
        $result = session('result'); 

        // $rows = CreditOrDebit::selectRaw("
        //     MONTH(`date`) as month,
        //     SUM(
        //         CASE 
        //             WHEN type = 'credit' AND amount_type = 'dollar' THEN amount * ?
        //             WHEN type = 'credit' THEN amount
        //             ELSE 0
        //         END
        //     ) as credit_sum,
        //     SUM(
        //         CASE 
        //             WHEN type = 'debit' AND amount_type = 'dollar' THEN amount * ?
        //             WHEN type = 'debit' THEN amount
        //             ELSE 0
        //         END
        //     ) as debit_sum
        // ", [$result, $result])
        // ->whereYear('date', $year)
        // ->groupBy(DB::raw('MONTH(`date`)'))
        // ->orderBy(DB::raw('MONTH(`date`)'))
        // ->get()
        // ->keyBy('month'); // key by month 1..12


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
        // Pull targets (optional). If you don’t have table, set a default per-month target here.
        $targets = DB::table('monthly_targets')
            ->select('month','amount')
            ->where('year', $year)
            ->get()
            ->keyBy('month');

        // Build 12-month arrays
        $labels   = [];
        $mbalance = [];     // monthly net (credit - debit)
        $target   = [];
        $difference = [];

        $running = 0.0;
        for ($m = 1; $m <= 12; $m++) {
            $labels[]  = Carbon::create($year, $m, 1)->format('M'); // Jan, Feb...
            $c = (float)($rows[$m]->credit_sum ?? 0);
            $d = (float)($rows[$m]->debit_sum  ?? 0);

            $net = $c - $d;
            $mbalance[] = round($net, 2);
            $difference[] = round(($targets[$m]->amount ?? 1000000) - $net, 2); // net - target
            
            $t = (float)($targets[$m]->amount ?? 1000000); 
            $target[] = round($t, 2);
        }
        
        $totaltarget = number_format(array_sum($target), 2);
        $balance = number_format(array_sum($mbalance), 2);
        $totaldifference = number_format(array_sum($difference), 2);
        
        return view('revenueandtarget', [
            'month' => $month,
            'year' => $year,
            'target' => $target,
            'totaltarget' => $totaltarget,
            'mbalance' => $mbalance,
            'balance' => $balance,
            'difference' => $difference,
            'totaldifference' => $totaldifference,
            'labels' => $labels,
        ]);
       
    }
    public function settarget(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020',
            'month' => 'required|integer|min:1|max:12',
            'amount' => 'required|numeric|min:0',
        ]);

        MonthlyTarget::updateOrCreate(
            ['year' => $request->year, 'month' => $request->month],
            ['amount' => $request->amount]
        );

        // Logic to save the target amount for the specified year
        // ...

        return redirect()->route('revenueandtarget')->with('success', 'Target set successfully.');
    }
}
