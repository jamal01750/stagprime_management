<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffCategory;
use App\Models\StaffSalary;
use Illuminate\Http\Request;

class StaffSalaryController extends Controller
{
    
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
            'salary_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        // Create a new staff member
        $staff = new Staff();
        $staff->name = $request->name;
        $staff->designation = $request->designation;
        $staff->category_id = $request->category_id;
        $staff->join_date = $request->join_date;
        $staff->salary_date = $request->salary_date;
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
}
