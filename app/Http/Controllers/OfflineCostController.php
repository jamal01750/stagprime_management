<?php

namespace App\Http\Controllers;

use App\Models\MonthlyOfflineCost;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\OfflineCostCategory;
use Barryvdh\DomPDF\Facade\Pdf;

class OfflineCostController extends Controller
{

    public function categoryCreate()
    {
        $categories = OfflineCostCategory::orderBy('id', 'desc')->get();
        return view('offline_cost.offline_category', [
            'categories' => $categories,
        ]);
    }
    
    // Add a new offline cost category
    public function categoryStore(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        OfflineCostCategory::create([
            'category' => $request->category,
        ]);

        return redirect()->back()->with('success', 'Offline cost category added successfully.');
    }

    public function updatecategory(Request $request, $id)
    {
        $category = OfflineCostCategory::findOrFail($id);
        $category->update(['category' => $request->name]);

        return response()->json(['success' => true]);
    }

    public function destroycategory($id)
    {
        $category = OfflineCostCategory::findOrFail($id);
        $category->delete();

        return response()->json(['success' => true]);
    }

    public function downloadCategoryPdf()
    {
        $categories = OfflineCostCategory::orderBy('id', 'asc')->get();
        $pdf = Pdf::loadView('pdf.offline_category_list', compact('categories'))
                  ->setPaper('a4', 'portrait');
        return $pdf->stream('category-list.pdf');
    }

    // Show the form to create a new offline cost
    public function create()
    {
        $year   = (int)(now('Asia/Dhaka')->year);
        $month = (int)(now('Asia/Dhaka')->month);
        
        $categories = OfflineCostCategory::all();
        $transactions = MonthlyOfflineCost::with('category')
                        ->where('approve_status', 'pending')
                        ->orderBy('created_at', 'desc')
                        ->paginate(2); 
        $total = $transactions->sum('amount');
        return view('offline_cost.create_offline_cost', [
            'year' => $year,
            'month' => $month,
            'categories' => $categories,
            'transactions' => $transactions,
            'total' => $total,
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

    // 🔹 Edit
    public function edit($id)
    {
        $transaction = MonthlyOfflineCost::findOrFail($id);
        $categories = OfflineCostCategory::all();

        return view('offline_cost.edit_cost', [
            'transaction' => $transaction,
            'categories' => $categories,
            'year' => $transaction->year,
            'month' => $transaction->month,
        ]);
    }

    // 🔹 Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:' . now()->year,
            'month' => 'required|integer|min:1|max:12',
            'category_id' => 'required|exists:offline_cost_categories,id',
            'amount' => 'required|numeric|min:0',
            'last_date' => 'nullable|date',
            'description' => 'nullable|string|max:500',
        ]);

        $transaction = MonthlyOfflineCost::findOrFail($id);
        $transaction->update([
            'year' => $request->year,
            'month' => $request->month,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'last_date' => $request->last_date,
            'description' => $request->description,
            'approve_status' => $request->approve_status,
        ]);

        return redirect()->back()->with('success', 'Offline cost updated successfully.');
    }

    // 🔹 Delete
    public function destroy($id)
    {
        $transaction = MonthlyOfflineCost::findOrFail($id);
        $transaction->delete();

        return redirect()->back()->with('success', 'Offline cost deleted successfully.');
    }

    public function updatePayment(Request $request, $id)
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

        return redirect()->route('offline.cost.report')->with('success', 'Payment status updated successfully.');
    }

    // 🔹 Report Page
    public function report(Request $request)
    {
        $query = MonthlyOfflineCost::with('category');

        // ✅ Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('approve_status')) {
            $query->where('approve_status', $request->approve_status);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // ✅ Month range filter (requires year)
        if ($request->filled('start_month') && $request->filled('end_month') && $request->filled('year')) {
            $startMonth = (int) $request->start_month;
            $endMonth = (int) $request->end_month;
            $query->whereBetween('month', [$startMonth, $endMonth]);
        }

        $transactions = $query->orderByDesc('year')->orderByDesc('month')->paginate(2);

        $total = $transactions->sum('amount');

        return view('offline_cost.report_offline_cost', [
            'transactions' => $transactions,
            'total' => number_format($total, 2),
        ]);
    }

    public function updateApprovalStatus(Request $request, $id)
    {
        $request->validate(['approve_status' => 'required']);
        $student = MonthlyOfflineCost::findOrFail($id);
        $student->update(['approve_status' => $request->approve_status]);
        return redirect()->back()->with('success', 'Approval status updated successfully.');
    }

    // 🔹 Download PDF
    public function downloadReportPdf(Request $request)
    {
        $query = MonthlyOfflineCost::with('category');

        // Apply same filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('approve_status')) {
            $query->where('approve_status', $request->approve_status);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('start_month') && $request->filled('end_month') && $request->filled('year')) {
            $query->whereBetween('month', [(int)$request->start_month, (int)$request->end_month]);
        }

        $transactions = $query->orderBy('year')->orderBy('month')->get();
        $total = $transactions->sum('amount');

        $pdf = Pdf::loadView('pdf.offline_cost_report', compact('transactions', 'total'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('Offline_Cost_Report.pdf');
    }

}
