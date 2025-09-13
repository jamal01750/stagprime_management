<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class StudentController extends Controller
{
    
    // New Student Registration
    public function newRegistration()
    {
        $batches = Batch::all();
        $courses = Course::all();
        
        return view('students.new_registration', [
            'batches' => $batches,
            'courses' => $courses,
        ]);
    }

    // Store Student Registration data
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'student_name' => 'required|string|max:255',
            'phone' => 'required|digits:11',
            'alt_Phone' => 'nullable|digits:11',
            'nid_birth' => 'required|numeric',
            'address' => ['required', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
            'batch_id' => 'required',
            'course_id' => 'required',
            'batch_time' => 'required',
            'admission_date' => 'required|date',
            'total_fee' => 'required|numeric',
            'paid_amount' => 'required|numeric',
            'due_amount' => 'nullable|numeric',
            'payment_due_date' => 'nullable|date',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        // ---- Generate Student ID ----
        $lastStudent = Student::orderBy('id', 'desc')->first();
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
            $imagePath = $request->file('image')->storeAs('students', $fileName, 'public');
        }

        // ---- Save Student ----
        $student = Student::create([
            'image' => $imagePath, // stored as "students/sp-0001.jpg"
            'student_id' => $studentId,
            'student_name' => $request->student_name,
            'phone' => $request->phone,
            'alt_Phone' => $request->alt_Phone,
            'nid_birth' => $request->nid_birth,
            'address' => $request->address,
            'batch_id' => $request->batch_id,
            'course_id' => $request->course_id,
            'batch_time' => $request->batch_time,
            'admission_date' => $request->admission_date,
            'total_fee' => $request->total_fee,
            'paid_amount' => $request->paid_amount,
            'due_amount' => $request->due_amount,
            'payment_due_date' => $request->payment_due_date,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', "Student Registered Successfully. ID: {$studentId}");
    }

    // Running Student Lists
    public function runningList(){
        $students = Student::where('active_status','Running')->orderBy('admission_date','asc')->get();
        
        return view('students.running_list', [
            'students' => $students,
        ]);
    }

    // Student Payment Status Update
    public function paymentUpdate($id)
    {
        $student = Student::findOrFail($id);
        $student->payment_status = 'Paid';
        $student->due_amount = 0;
        $student->payment_due_date = null;
        $student->save();
        return redirect()->route('student.list.running')->with('success', 'Payment status updated to Paid.');  
    }

    // Student Active Status Update
    public function updateActiveStatus($id)
    {
        $student = Student::findOrFail($id);
        $student->active_status = 'Expired';
        $student->save();
        return redirect()->route('student.list.running')->with('success', 'Student status updated to Expired.');  
    }

    // Student Details
    public function studentDetails(Request $request, $student_id)
    {
        $student = Student::where('student_id', $student_id)->first();
        $batches = Batch::all();
        $courses = Course::all();   
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found.'); 
        }
        $student->batch_name = $batches->where('id', $student->batch_id)->first()->batch_name ?? 'N/A';
        $student->course_name = $courses->where('id', $student->course_id)->first()->course_name ?? 'N/A';
        return view('students.student_details', [   
            'student' => $student,
        ]);
    }

    // Student Individual Edit Page
    public function editStudent($id)
    {
        $student = Student::findOrFail($id);
        $batches = Batch::all();
        $courses = Course::all();
        return view('students.edit_student', [
            'student' => $student,
            'batches' => $batches,
            'courses' => $courses,
        ]);
    }

    // Student Individual Update
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'student_name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|digits:11',
            'alt_Phone' => 'nullable|digits:11',
            'nid_birth' => 'sometimes|required|numeric',
            'address' => ['sometimes', 'required', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
            'batch_id' => 'sometimes|required',
            'course_id' => 'sometimes|required',
            'batch_time' => 'sometimes|required',
            'admission_date' => 'sometimes|required|date',
            'total_fee' => 'sometimes|required|numeric',
            'paid_amount' => 'sometimes|required|numeric',
            'due_amount' => 'sometimes|required|numeric',
            'payment_due_date' => 'nullable|date',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
            'payment_status' => 'sometimes|in:Paid,Unpaid',
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

            $imagePath = $request->file('image')->storeAs('students', $fileName, 'public');

            $student->image = $imagePath;
        }

        // ---- Update only provided fields ----
        $student->fill($request->except(['image']));

        $student->save();

        return redirect()->back()->with('success', "Student {$student->student_id} updated successfully.");
    }

    // Expire Student Lists
    public function expireList(){
        $students = Student::where('active_status','Expired')->orderBy('admission_date','desc')->get();
        
        return view('students.expire_list', [
            'students' => $students,
        ]);
    }



}
