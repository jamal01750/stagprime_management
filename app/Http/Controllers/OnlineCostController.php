<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditOrDebit;
use App\Models\MonthlyOnlineCost;
use App\Models\OnlineCostCategory;

class OnlineCostController extends Controller
{
    // public function index(Request $request)
    // {
    //     $year   = (int)($request->query('year') ?? now('Asia/Dhaka')->year);
    //     $month = (int)($request->query('month') ?? now('Asia/Dhaka')->month);
    //     $subcategories = OnlineCostCategory::all();
    //     // Get monthly expenses
    //     $expenses = MonthlyOnlineCost::get()
    //         ->where('year', $year)
    //         ->where('month', $month);
            

    //     // Attach paid date & status
    //     foreach ($expenses as $expense) {
    //         $paid = CreditOrDebit::where('subcategory_id', $expense->sub_category_id)
    //             ->whereYear('date', $year)
    //             ->whereMonth('date', $month)
    //             ->where('type', 'debit')
    //             ->where('category', 'online')
    //             ->first();
    //         $expense->paid_date = $paid?->date;
    //         $expense->paid_amount = $paid?->amount ?? 0;
    //         $expense->status = $paid ? 'Paid' : 'Unpaid';
    //         $expense->paid_type = $paid?->amount_type == 'taka' ? '৳ ' : '$ ';

    //         $sub_category = $subcategories->firstWhere('id', $expense->sub_category_id);
    //         $expense->sub_category_name = $sub_category ? $sub_category->sub_category : 'Unknown';
    //     }

    //     return view('yearly_online_cost',[
    //         'year' => $year,
    //         'month' => $month,
    //         'subcategories' => $subcategories,
    //         'expenses' => $expenses,
    //     ]);

    // }

    public function onlineCostCategory(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        OnlineCostCategory::create([
            'category' => $request->category,
        ]);

        return redirect()->back()->with('success', 'Online cost category added successfully.');
    }
    public function create()
    {
        $year   = (int)(now('Asia/Dhaka')->year);
        $month = (int)(now('Asia/Dhaka')->month);
        
        $categories = OnlineCostCategory::all();
        return view('online_cost.create_online_cost', [
            'year' => $year,
            'month' => $month,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer',
            'activate_cost' => 'nullable|numeric|min:0',
            'amount' => 'nullable|numeric|min:0',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        MonthlyOnlineCost::create([   
            'year' => $request->year,
            'month' => $request->month,
            'category_id' => $request->category_id,
            'activate_date' => $request->activate_date,
            'expire_date' => $request->expire_date,
            'activate_type' => $request->activate_type,
            'activate_cost' => $request->activate_cost,
            'amount_type' => $request->amount_type,
            'amount' => $request->amount,
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Online expense stored successfully.');
    }

    public function report(Request $request)
    {
        
        $filterType = $request->query('filter_type', 'month');
        $date = $request->query('date');
        $month = (int)$request->query('month', now('Asia/Dhaka')->month);
        $year = (int)$request->query('year', now('Asia/Dhaka')->year);
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = MonthlyOnlineCost::query();

        if ($filterType === 'day' && $date) {
            $query->whereDate('created_at', $date);
        } elseif ($filterType === 'month') {
            $query->where('year', $year)->where('month', $month);
        } elseif ($filterType === 'year') {
            $query->where('year', $year);
        } elseif ($filterType === 'range' && $startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $expenses = $query->get();

        $categories = OnlineCostCategory::all();
        foreach ($expenses as $expense) {
            $category = $categories->firstWhere('id', $expense->category_id);
            $expense->category_name = $category ? $category->category : 'Unknown';
            $expense->activate_type = $expense->activate_type == 'taka' ? '৳' : '$';
            $expense->amount_type   = $expense->amount_type == 'taka' ? '৳' : '$';
        }

        return view('online_cost.report_online_cost', [
            'expenses'    => $expenses,
            'filterType'  => $filterType,
            'date'        => $request->query('date'),
            'month'       => $request->query('month'),
            'year'        => $request->query('year'),
            'startDate'   => $request->query('start_date'),
            'endDate'     => $request->query('end_date'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:paid,unpaid',
        ]);

        $expense = MonthlyOnlineCost::findOrFail($id);
        $expense->status = $request->status;
        $expense->paid_date = $request->status === 'paid' ? now('Asia/Dhaka') : null;
        $expense->save();

        return redirect()->route('online.cost.report')->with('success', 'Expense status updated successfully.');
    }
}
