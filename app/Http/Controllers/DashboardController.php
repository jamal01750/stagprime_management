<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditOrDebit;
use App\Models\OfflineCostCategory;
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
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

        session([
            'result' => $result,
        ]);

        $subcategories = OfflineCostCategory::all();

        $year   = (int)($request->query('year') ?? now('Asia/Dhaka')->year);
        // Pull monthly Credit & Debit

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
        $rows = CreditOrDebit::selectRaw("
            MONTH(`date`) as month,
            SUM(
                CASE 
                    WHEN type = 'credit' THEN amount
                    ELSE 0
                END
            ) as credit_sum,
            SUM(
                CASE 
                    WHEN type = 'debit' THEN amount
                    ELSE 0
                END
            ) as debit_sum
        ")
        ->whereYear('date', $year)
        ->groupBy(DB::raw('MONTH(`date`)'))
        ->orderBy(DB::raw('MONTH(`date`)'))
        ->get()
        ->keyBy('month'); // key by month 1..12

        // Pull targets (optional). If you don’t have table, set a default per-month target here.
        $targets = DB::table('monthly_targets')
            ->select('month','target_amount')
            ->where('year', $year)
            ->get()
            ->keyBy('month');

        // Build 12-month arrays
        $labels   = [];
        $mcredits  = [];
        $mdebits   = [];
        $mbalance = [];     // monthly net (credit - debit)
        $cumBal   = [];     // cumulative balance across the year
        $target   = [];

        $running = 0.0;
        for ($m = 1; $m <= 12; $m++) {
            $labels[]  = Carbon::create($year, $m, 1)->format('M'); // Jan, Feb...
            $c = (float)($rows[$m]->credit_sum ?? 0);
            $d = (float)($rows[$m]->debit_sum  ?? 0);
            $mcredits[] = round($c, 2);
            $mdebits[]  = round($d, 2);

            $net = $c - $d;
            $mbalance[] = round($net, 2);
            $running += $net;
            $cumBal[] = round($running, 2);

            $t = (float)($targets[$m]->target_amount ?? 1000000); // set your default if needed
            $target[] = round($t, 2);
        }
        
        $totalCredits = number_format(array_sum($mcredits), 2);
        $totalDebits = number_format(array_sum($mdebits), 2);
        $balance = number_format(array_sum($mcredits) - array_sum($mdebits), 2);
        $totaltarget = number_format(array_sum($target), 2);

        return view('welcome', [
            'subcategories' => $subcategories,
            'totalCredits' => $totalCredits,
            'totalDebits' => $totalDebits,
            'balance' => $balance,
            'labels' => $labels,
            'mcredits' => $mcredits,
            'mdebits' => $mdebits,
            'mbalance' => $mbalance,
            'cumBal' => $cumBal,
            'target' => $target,
            'totaltarget' => $totaltarget,
            'year' => $year,
        ]);
        
    }

    public function downloadPDF(Request $request)
    {
        $year = (int)($request->input('year') ?? now('Asia/Dhaka')->year);
        $fakeRequest = new Request(['year' => $year]);
        $indexData = $this->index($fakeRequest)->getData();

        $data = [
            'totalCredits' => $indexData['totalCredits'],
            'totalDebits' => $indexData['totalDebits'],
            'balance' => $indexData['balance'],
            'labels' => $indexData['labels'],
            'mcredits' => $indexData['mcredits'],
            'mdebits' => $indexData['mdebits'],
            'mbalance' => $indexData['mbalance'],
            'cumBal' => $indexData['cumBal'],
            'target' => $indexData['target'],
            'totaltarget' => $indexData['totaltarget'],
            'year' => $indexData['year'],
            'result' => session('result'),
        ];
        // return $data;

        $pdf = Pdf::loadView('pdf.monthly_summary', $data);
        return $pdf->download('monthly_summary.pdf');
    }

}








    

