<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffCategory;
use App\Models\StaffSalary;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StaffSalaryController extends Controller
{

    public function index()
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        // All salaries for this month
        $salaries = StaffSalary::with('staff')
            ->whereYear('salary_date', $year)
            ->whereMonth('salary_date', $month)
            ->get();

        $totalSalary = $salaries->sum('amount');
        $paidCount   = $salaries->where('status', 'Paid')->count();
        $unpaidCount = $salaries->where('status', 'Unpaid')->count();

        // Reminder: salary_date always 10th → show reminders 7 days before
        $reminderDate = Carbon::createFromDate($year, $month, 10)->subDays(7)->toDateString();

        $reminders = StaffSalary::with('staff')
            ->whereYear('salary_date', $year)
            ->whereMonth('salary_date', $month)
            ->whereDate('salary_date', '=', Carbon::createFromDate($year, $month, 10))
            ->where('status', 'Unpaid')
            ->get();

        return view('staff.summary', compact(
            'totalSalary',
            'paidCount',
            'unpaidCount',
            'reminders',
            'reminderDate'
        ));
    }

    public function createStaff()
    {
        $categories = StaffCategory::all();
        return view('staff.create', compact('categories'));
    }

    
    public function createStaffCategory(Request $request)
    {
        
        $request->validate([
            'category' => 'required|string|max:255',
        ]);

        StaffCategory::create([
            'category' => $request->category,
        ]);

        return redirect()->back()->with('success', 'Staff category created successfully.');
    }

    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'category_id' => 'required|exists:staff_categories,id',
            'join_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        // Create a new staff member
        $staff = new Staff();
        $staff->name = $request->name;
        $staff->designation = $request->designation;
        $staff->category_id = $request->category_id;
        $staff->join_date = $request->join_date;
        $staff->amount = $request->amount;
        $staff->description = $request->description;
        $staff->save();

        return redirect()->back()->with('success', 'Staff member created successfully.');
    }

    // Staff Salary list
    public function staffSalaryList()
    {
        $salaries = StaffSalary::with('staff.staffcategory')->latest()->paginate(20);
        return view('staff.list', compact('salaries'));
    }

    // Staff Salary Payment

    public function paySalary(Request $request, $id)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $salary = StaffSalary::findOrFail($id);
        $salary->status = 'Paid';
        $salary->paid_date = $request->payment_date;
        $salary->payment_method = $request->payment_method;
        $salary->note = $request->note;
        $salary->save();

        return response()->json([
            'success' => true,
            'message' => 'Salary paid successfully.',
            'id' => $salary->id,
        ]);
    }

    public function markPaid($id)
    {
        $salary = StaffSalary::findOrFail($id);
        $salary->status = 'Paid';
        $salary->paid_date = now(); // auto-set today’s date
        $salary->payment_method = 'Cash'; // optional placeholder
        $salary->save();

        return response()->json([
            'success' => true,
            'message' => 'Salary marked as paid successfully.',
            'id' => $salary->id,
        ]);
    }

    // Staff Salary Report
    public function staffSalaryReport(Request $request)
    {
            
        $type = $request->get('type', 'monthly'); // default: monthly
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);
        $staffId = $request->get('staff_id');

        $reportData = [];

        if ($type === 'monthly') {
            $reportData['title'] = "Monthly Report - " . Carbon::createFromDate($year, $month)->format('F Y');
            $reportData['salaries'] = StaffSalary::with('staff')
                ->whereYear('salary_date', $year)
                ->whereMonth('salary_date', $month)
                ->get();
            $reportData['totalPaid'] = $reportData['salaries']->where('status', 'Paid')->sum('amount');
            $reportData['totalUnpaid'] = $reportData['salaries']->where('status', 'Unpaid')->sum('amount');
        }

        if ($type === 'yearly') {
            $reportData['title'] = "Yearly Report - $year";
            $reportData['salaries'] = StaffSalary::with('staff')
                ->whereYear('salary_date', $year)
                ->get()
                ->groupBy('staff_id');
        }

        if ($type === 'individual' && $staffId) {
            $staff = Staff::findOrFail($staffId);
            $salaries = StaffSalary::where('staff_id', $staffId)->get();
            $reportData['title'] = "Individual Report - {$staff->name}";
            $reportData['staff'] = $staff;
            $reportData['paidMonths'] = $salaries->where('status', 'Paid')->count();
            $reportData['totalPaid'] = $salaries->where('status', 'Paid')->sum('amount');
            $reportData['totalUnpaid'] = $salaries->where('status', 'Unpaid')->sum('amount');
        }

        if ($type === 'service') {
            $staff = Staff::all();
            $reportData['title'] = "Service Time Report";
            $reportData['staff'] = $staff->map(function ($s) {
                $joinDate = Carbon::parse($s->join_date);
                return [
                    'name' => $s->name,
                    'join_date' => $s->join_date,
                    'total_paid' => $s->salaries->where('status', 'Paid')->sum('amount'),
                    'total_unpaid' => $s->salaries->where('status', 'Unpaid')->sum('amount'),
                    'duration' => $joinDate->diffForHumans(['parts' => 3, 'join' => ', ']),
                ];
            });
        }

        return view('staff.report', compact('type', 'reportData'));
    }

}
