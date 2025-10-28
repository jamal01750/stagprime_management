<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\StudentPayment;
use Barryvdh\DomPDF\Facade\Pdf;


class StudentController extends Controller
{
    
    // New Student Registration
    public function newRegistration()
    {
        $batches = Batch::all();
        $courses = Course::all();
        $students = Student::where('approve_status','pending')
            ->orderBy('admission_date', 'desc')
            ->paginate(10); // Paginate 10 students per page
        
        return view('students.new_registration', [
            'batches' => $batches,
            'courses' => $courses,
            'students' => $students,
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

    // Student Payment
    public function payment(){
        $students = Student::where('payment_status','Unpaid')->orderBy('admission_date','asc')->get();
        return view('students.payment_form', [
            'students' => $students,
        ]);
    }

    // Store Student Payment
    public function paymentStore(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'pay_amount' => 'required|numeric|min:1',
            'due_amount' => 'required|numeric',
            'pay_date' => 'required|date',
            'next_date' => 'nullable|date',
        ]);

        $student = Student::findOrFail($request->student_id);

        // --- Insert into student_payments table ---
        StudentPayment::create([
            'student_id' => $student->id,
            'pay_amount' => $request->pay_amount,
            'due_amount' => $request->due_amount,
            'pay_date' => $request->pay_date,
            'next_date' => $request->next_date,
        ]);

        // --- Update student table ---
        $student->due_amount = $request->due_amount;
        $student->payment_due_date = $request->next_date;

        // if ($student->due_amount <= 0) {
        //     $student->payment_status = 'Paid';
        //     $student->due_amount = 0;
        // } else {
        //     $student->payment_status = 'Unpaid';
        // }

        $student->save();

        return redirect()->back()->with('success', 'Payment recorded successfully for ' . $student->student_name);
    }

    
    // Student Details
    public function studentDetails($id)
    {
        $student = Student::findOrFail($id);
        $batches = Batch::all();
        $courses = Course::all(); 
        $payments = StudentPayment::where('student_id', $id)->get();
        $extraPayments = $payments->sum('pay_amount');  
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found.'); 
        }
        $student->batch_name = $batches->where('id', $student->batch_id)->first()->batch_name ?? 'N/A';
        $student->course_name = $courses->where('id', $student->course_id)->first()->course_name ?? 'N/A';
        $student->calculated_paid = $student->paid_amount + $extraPayments; // Total paid amount
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
    public function updateStudent(Request $request, Student $student)
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
            'approve_status' => 'sometimes|in:pending,approved',
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
        $student->fill($request->except(['image','student_id'])); // Exclude image and student_id from mass assignment

        $student->save();

        return redirect()->back()->with('success', "Student {$student->student_id} updated successfully.");
    }

    // Student Delete
    public function destroyStudent($id)
    {
        $student = Student::findOrFail($id);    
        // Delete image if exists
        if ($student->image && Storage::disk('public')->exists($student->image)) {
            Storage::disk('public')->delete($student->image);
        }
        $student->delete();    
        return redirect()->back()->with('success', "Student {$student->student_id} deleted successfully.");
    }

    
    public function downloadPdf($id)
    {
        $student = Student::with(['batch', 'course', 'payments'])->findOrFail($id);
        
        $extraPayments = $student->payments->sum('pay_amount');
        $student->calculated_paid = $student->paid_amount + $extraPayments;

        $pdf = Pdf::loadView('pdf.student_individual', compact('student'))->setPaper('a4', 'portrait');

        $fileName = $student->student_name . '_details.pdf';
        return $pdf->stream($fileName);
        // return $pdf->download($fileName);
    }

    // Students List with Filters
    public function list(Request $request)
    {
        $query = Student::query();

        // 🔹 Filtering
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('active_status')) {
            $query->where('active_status', $request->active_status);
        }

        if ($request->filled('approve_status')) {
            $query->where('approve_status', $request->approve_status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('admission_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', 'like', '%' . $request->student_id . '%');
        }

        $students = $query->orderBy('created_at','desc')->paginate(10);

        return view('students.running_list', compact('students'));
    }

    // Student Payment Status Update
    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate(['payment_status' => 'required']);
        $student = Student::findOrFail($id);
        $student->update(['payment_status' => $request->payment_status]);
        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

    // Student Active Status Update
    public function updateActiveStatus(Request $request, $id)
    {
        $request->validate(['active_status' => 'required']);
        $student = Student::findOrFail($id);
        $student->update(['active_status' => $request->active_status]);
        return redirect()->back()->with('success', 'Active status updated successfully.');
    }

    public function updateApprovalStatus(Request $request, $id)
    {
        $request->validate(['approve_status' => 'required']);
        $student = Student::findOrFail($id);
        $student->update(['approve_status' => $request->approve_status]);
        return redirect()->back()->with('success', 'Approval status updated successfully.');
    }

    // Download Students List PDF with Filters

    public function downloadListPdf(Request $request)
    {
        $query = Student::with(['batch', 'course', 'payments']);

        // 🔹 Apply Filters (same as your list())
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('active_status')) {
            $query->where('active_status', $request->active_status);
        }
        if ($request->filled('approve_status')) {
            $query->where('approve_status', $request->approve_status);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('admission_date', [$request->start_date, $request->end_date]);
        }

        $students = $query->get();

        // 🔹 Calculate per-student and total payments
        $total_fee = 0;
        $total_paid = 0;
        $total_due = 0;

        foreach ($students as $student) {
            // Paid from main table + sum from student_payments
            $extraPayments = $student->payments->sum('pay_amount');

            $student->calculated_paid = $student->paid_amount + $extraPayments;
            // $student->calculated_due = $student->total_fee - $student->calculated_paid;

            $total_fee += $student->total_fee;
            $total_paid += $student->calculated_paid;
            $total_due += $student->due_amount;
        }

        $totals = [
            'total_fee' => $total_fee,
            'total_paid' => $total_paid,
            'total_due' => $total_due,
        ];

        // 🔹 Generate PDF (landscape)
        $pdf = Pdf::loadView('pdf.students_list', compact('students', 'totals'))
                    ->setPaper('a4', 'landscape');

        return $pdf->stream('students_list.pdf');
    }


}
