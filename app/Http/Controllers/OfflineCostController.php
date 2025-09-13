<?php

namespace App\Http\Controllers;

use App\Models\MonthlyOfflineCost;
use Illuminate\Http\Request;
use App\Models\CreditOrDebit;
use App\Models\OfflineCostCategory;

class OfflineCostController extends Controller
{
    public function index(Request $request)
    {
        $year   = (int)($request->query('year') ?? now('Asia/Dhaka')->year);
        $month = (int)($request->query('month') ?? now('Asia/Dhaka')->month);
        $categories = OfflineCostCategory::all();
        
        // Get monthly expenses
        $expenses = MonthlyOfflineCost::get()
            ->where('year', $year)
            ->where('month', $month);
            

        // Attach paid date & status
        foreach ($expenses as $expense) {
            $paid = CreditOrDebit::where('subcategory_id', $expense->sub_category_id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('type', 'debit')
                ->where('category', 'offline')
                ->first();
            $expense->paid_date = $paid?->date;
            $expense->paid_amount = $paid?->amount ?? 0;
            $expense->status = $paid ? 'Paid' : 'Unpaid';

            // $sub_category = $subcategories->firstWhere('id', $expense->sub_category_id);
            // $expense->sub_category_name = $sub_category ? $sub_category->sub_category : 'Unknown';
        }

        return view('monthly_offline_cost',[
            'month' => $month,
            'year' => $year,
            'categories' => $categories,
            'expenses' => $expenses,
        ]);

    }

    // Add a new offline cost category
    public function offlineCostCategory(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        OfflineCostCategory::create([
            'category' => $request->category,
        ]);

        return redirect()->back()->with('success', 'Offline cost category added successfully.');
    }

    // Show the form to create a new offline cost
    public function create()
    {
        $year   = (int)(now('Asia/Dhaka')->year);
        $month = (int)(now('Asia/Dhaka')->month);
        
        $categories = OfflineCostCategory::all();
        return view('offline_cost.create_offline_cost', [
            'year' => $year,
            'month' => $month,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);
        MonthlyOfflineCost::create([
            'year' => $request->year,
            'month' => $request->month,
            'last_date' => $request->last_date,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Monthly expense added successfully.');
    }

    public function report(Request $request)
    {
        $year = (int)($request->query('year') ?? now('Asia/Dhaka')->year);
        $month = (int)($request->query('month') ?? now('Asia/Dhaka')->month);
        
        $expenses = MonthlyOfflineCost::where('year', $year)
            ->where('month', $month)
            ->get();

        $categories = OfflineCostCategory::all();
        foreach ($expenses as $expense) {
            $category = $categories->firstWhere('id', $expense->category_id);
            $expense->category_name = $category ? $category->category : 'Unknown';
        }

        return view('offline_cost.report_offline_cost', [
            'year' => $year,
            'month' => $month,
            'expenses' => $expenses,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:paid,unpaid',
        ]);

        $expense = MonthlyOfflineCost::findOrFail($id);
        $expense->status = $request->status;
        $expense->paid_date = $request->status === 'paid' ? now('Asia/Dhaka') : null;
        $expense->save();

        return redirect()->route('offline.cost.report')->with('success', 'Expense status updated successfully.');
    }
}
