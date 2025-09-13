<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternshipRegistration;
use App\Models\Batch;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class InternshipController extends Controller
{

    // Create Batch 
    public function createBatch(Request $request)
    {
        $request->validate([
            'batch_name' => 'required|string|max:255',
        ]);

        Batch::create([
            'batch_name' => $request->batch_name,
        ]);

        return redirect()->back()->with('success', 'Batch created successfully.');
    }

    // Create Course
    public function createCourse(Request $request)
    {
        $request->validate([
            'course_name' => 'required|string|max:255',
        ]);

        Course::create([
            'course_name' => $request->course_name,
        ]);

        return redirect()->back()->with('success', 'Course created successfully.');
    }

    // New Internship Registration
    public function newRegistration()
    {
        $batches = Batch::all();
        $courses = Course::all();
        
        return view('internship.new_registration', [
            'batches' => $batches,
            'courses' => $courses,
        ]);
    }

    // Store Internship Registration data
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'internee_name' => 'required|string|max:255',
            'phone' => 'required|digits:11',
            'alt_Phone' => 'nullable|digits:11',
            'nid_birth' => 'required|numeric',
            'address' => ['required', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
            'batch_id' => 'required',
            'course_id' => 'required',
            'batch_time' => 'required',
            'admission_date' => 'required|date',
            'pay_amount' => 'required|numeric',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        // ---- Generate Student ID ----
        $lastStudent = InternshipRegistration::orderBy('id', 'desc')->first();
        if ($lastStudent) {
            $lastNumber = (int) str_replace('SP-', '', $lastStudent->student_id);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        $studentId = 'SP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // ---- Handle Image Upload ----
        $imagePath = null;
        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension(); // jpg, png, etc.
            $fileName = strtolower($studentId) . '.' . $extension; // e.g. sp-0001.jpg
            $imagePath = $request->file('image')->storeAs('internships', $fileName, 'public');
        }

        // ---- Save Student ----
        $student = InternshipRegistration::create([
            'image' => $imagePath, // stored as "internships/sp-0001.jpg"
            'intern_id' => $studentId,
            'internee_name' => $request->internee_name,
            'phone' => $request->phone,
            'alt_Phone' => $request->alt_Phone,
            'nid_birth' => $request->nid_birth,
            'address' => $request->address,
            'batch_id' => $request->batch_id,
            'course_id' => $request->course_id,
            'batch_time' => $request->batch_time,
            'admission_date' => $request->admission_date,
            'pay_amount' => $request->pay_amount,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', "Intern Registered Successfully. ID: {$studentId}");
    }

    // Running Intern Lists
    public function runningList()
    {
        $students = InternshipRegistration::where('active_status', 'Running')
            ->orderBy('admission_date', 'asc')
            ->get();

        foreach ($students as $student) {
            $installment = $student->pay_amount / 3;

            // Upcoming amount logic
            if ($student->pay_amount > $student->total_paid) {
                $student->upcoming_amount = $installment;
            } else {
                $student->upcoming_amount = 0;
            }

            // Upcoming date logic
            if ($student->total_paid == 0) {
                $student->upcoming_date = \Carbon\Carbon::parse($student->admission_date)->addMonth();
            } elseif ($student->total_paid == $installment) {
                $student->upcoming_date = \Carbon\Carbon::parse($student->admission_date)->addMonths(2);
            } elseif ($student->total_paid == $installment * 2) {
                $student->upcoming_date = \Carbon\Carbon::parse($student->admission_date)->addMonths(3);
            } elseif ($student->total_paid == $student->pay_amount) {
                $student->upcoming_date = null;
            }
        }

        return view('internship.running_list', compact('students'));
    }

    // Intern Payment Update
    public function paymentUpdate($id)
    {
        $student = InternshipRegistration::findOrFail($id);
        $installment = $student->pay_amount / 3;

        if ($student->total_paid < $student->pay_amount) {
            $student->total_paid += $installment;

            // Update payment status
            if ($student->total_paid >= $student->pay_amount) {
                $student->payment_status = 'Full paid';
            } else {
                $student->payment_status = 'Partial';
            }

            $student->save();
            return redirect()->route('internship.list.running')
                ->with('success', "Payment updated successfully for {$student->intern_id}.");
        }

        return redirect()->route('internship.list.running')
            ->with('success', "{$student->intern_id} is already fully paid.");
    }


    // Intern Active Status Update
    public function updateActiveStatus($id)
    {
        $student = InternshipRegistration::findOrFail($id);
        $student->active_status = 'Expired';
        $student->save();
        return redirect()->route('internship.list.running')->with('success', 'Student status updated to Expired.');  
    }

    // Intern Details
    public function studentDetails(Request $request, $student_id)
    {
        $student = InternshipRegistration::where('intern_id', $student_id)->first();
        $batches = Batch::all();
        $courses = Course::all();   
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found.'); 
        }
        $student->batch_name = $batches->where('id', $student->batch_id)->first()->batch_name ?? 'N/A';
        $student->course_name = $courses->where('id', $student->course_id)->first()->course_name ?? 'N/A';
        return view('internship.intern_details', [   
            'student' => $student,
        ]);
    }

    // Intern Individual Edit Page
    public function editStudent($id)
    {
        $student = InternshipRegistration::findOrFail($id);
        $batches = Batch::all();
        $courses = Course::all();
        return view('internship.edit_student', [
            'student' => $student,
            'batches' => $batches,
            'courses' => $courses,
        ]);
    }

    // Intern Individual Update
    public function update(Request $request, InternshipRegistration $student)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'internee_name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|digits:11',
            'alt_Phone' => 'nullable|digits:11',
            'nid_birth' => 'sometimes|required|numeric',
            'address' => ['sometimes', 'required', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
            'batch_id' => 'sometimes|required',
            'course_id' => 'sometimes|required',
            'batch_time' => 'sometimes|required',
            'admission_date' => 'sometimes|required|date',
            'pay_amount' => 'sometimes|required|numeric',
            'total_paid' => 'sometimes|required|numeric',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
            'payment_status' => 'sometimes|in:Full paid,Partial',
            'active_status' => 'sometimes|in:Running,Expired',
        ]);

        // ---- Handle Image Update ----
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($student->image && Storage::disk('public')->exists($student->image)) {
                Storage::disk('public')->delete($student->image);
            }

            // Keep same studentId, rename file with same ID
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileName = strtolower($student->student_id) . '.' . $extension;

            $imagePath = $request->file('image')->storeAs('internships', $fileName, 'public');

            $student->image = $imagePath;
        }

        // ---- Update only provided fields ----
        $student->fill($request->except(['image']));

        $student->save();

        return redirect()->back()->with('success', "Intern {$student->intern_id} updated successfully.");
    }

    // Expire Intern Lists
    public function expireList(){
        $students = InternshipRegistration::where('active_status','Expired')->orderBy('admission_date','desc')->get();
        
        return view('internship.expire_list', [
            'students' => $students,
        ]);
    }


}
