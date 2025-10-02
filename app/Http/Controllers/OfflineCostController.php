<?php

namespace App\Http\Controllers;

use App\Models\MonthlyOfflineCost;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\OfflineCostCategory;

class OfflineCostController extends Controller
{

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
        
        if($request->last_date){
            $lastDate = $request->last_date;
            $status = 'unpaid';
            $paidDate = null;
        }else{
            $lastDate = null;
            $status = 'paid';
            $paidDate = now('Asia/Dhaka');
        }
        
        MonthlyOfflineCost::create([
            'year' => $request->year,
            'month' => $request->month,
            'last_date' => $lastDate,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'status' => $status,
            'paid_date' => $paidDate,
        ]);

        return redirect()->back()->with('success', 'Monthly expense added successfully.');
    }

    public function report(Request $request)
    {
        $filterType = $request->query('filter_type', 'month');
        $date = $request->query('date');
        $month = (int)$request->query('month', now('Asia/Dhaka')->month);
        $year = (int)$request->query('year', now('Asia/Dhaka')->year);
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = MonthlyOfflineCost::query();

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

        $categories = OfflineCostCategory::all();
        foreach ($expenses as $expense) {
            $category = $categories->firstWhere('id', $expense->category_id);
            $expense->category_name = $category ? $category->category : 'Unknown';
        }

        return view('offline_cost.report_offline_cost', [
            'filterType' => $filterType,
            'date' => $date,
            'month' => $month,
            'year' => $year,
            'startDate' => $startDate,
            'endDate' => $endDate,
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

        // 2. Query the new universal notifications table
        $notificationQuery = Notification::where('notifiable_id', $expense->id)
                                ->where('notifiable_type', MonthlyOfflineCost::class);

        if ($expense->status === 'paid') {
            // If the expense is marked as paid, clear the related notification.
            $notificationQuery->update([
                'status' => 'cleared',
                'cleared_at' => now('Asia/Dhaka')
            ]);
        } else {
            // If the expense is marked as unpaid again, reactivate the notification.
            // The scheduled command will then update its level and days_left on the next run.
            $notificationQuery->update([
                'status' => 'active',
                'cleared_at' => null
            ]);
        }

        return redirect()->route('offline.cost.report')->with('success', 'Expense status updated successfully.');
    }

}
