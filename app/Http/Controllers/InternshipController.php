<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternshipRegistration;
use App\Models\Batch;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class InternshipController extends Controller
{


    // New Internship Registration
    public function newRegistration()
    {
        $batches = Batch::all();
        $courses = Course::all();
        $students = InternshipRegistration::where('approve_status','pending')
            ->orderBy('admission_date', 'desc')
            ->paginate(2); 
        
        return view('internship.new_registration', [
            'batches' => $batches,
            'courses' => $courses,
            'students' => $students,
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
            $lastNumber = (int) str_replace('SPI-', '', $lastStudent->intern_id);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        $studentId = 'SPI-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

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

     
    // Intern Payment Update
    public function paymentUpdate($id)
    {
        $student = InternshipRegistration::findOrFail($id);
        $installment = $student->pay_amount / 3;

        if ($student->total_paid < $student->pay_amount) {
            $student->total_paid += $installment;

            // Installment sequence check
            if ($student->total_paid == $installment) {
                $student->paid_date = Carbon::now()->toDateString();
            } elseif ($student->total_paid == $installment * 2) {
                $student->paid_date2 = Carbon::now()->toDateString();
            } elseif ($student->total_paid >= $student->pay_amount) {
                $student->paid_date3 = Carbon::now()->toDateString();
            }

            // Update payment status
            if ($student->total_paid >= $student->pay_amount) {
                $student->payment_status = 'Full paid';
                $student->total_paid = $student->pay_amount; // Ensure no overpayment
            } else {
                $student->payment_status = 'Partial';
            }

            $student->save();

            return redirect()->route('internship.list')
                ->with('success', "Payment updated successfully for {$student->intern_id}.");
        }

        return redirect()->route('internship.list')
            ->with('success', "{$student->intern_id} is already fully paid.");
    }

    // Intern Details
    public function studentDetails($id)
    {
        $student = InternshipRegistration::findOrFail($id);
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
            $fileName = strtolower($student->intern_id) . '.' . $extension;

            $imagePath = $request->file('image')->storeAs('internships', $fileName, 'public');

            $student->image = $imagePath;
        }

        // ---- Update only provided fields ----
        $student->fill($request->except(['image','intern_id']));

        $student->save();

        return redirect()->back()->with('success', "Intern {$student->intern_id} updated successfully.");
    }

    // Intern Delete
    public function destroyStudent($id)
    {
        $student = InternshipRegistration::findOrFail($id);    
        // Delete image if exists
        if ($student->image && Storage::disk('public')->exists($student->image)) {
            Storage::disk('public')->delete($student->image);
        }
        $student->delete();    
        return redirect()->back()->with('success', "Intern {$student->intern_id} deleted successfully.");
    }

    
    public function downloadPdf($id)
    {
        $student = InternshipRegistration::with(['batch', 'course'])->findOrFail($id);
        
        $pdf = Pdf::loadView('pdf.intern_individual', compact('student'))->setPaper('a4', 'portrait');

        $fileName = $student->internee_name . '_details.pdf';
        return $pdf->stream($fileName);
        // return $pdf->download($fileName);
    }

    // Students List with Filters
    public function list(Request $request)
    {
        $query = InternshipRegistration::query();

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

        if ($request->filled('intern_id')) {
            $query->where('intern_id', 'like', '%' . $request->intern_id . '%');
        }

        $students = $query->orderBy('created_at','desc')->paginate(2);

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
                $student->upcoming_date = \Carbon\Carbon::parse($student->admission_date)->addMonth()->toDateString();
            } elseif ($student->total_paid == $installment) {
                $student->upcoming_date = \Carbon\Carbon::parse($student->admission_date)->addMonths(2)->toDateString();
            } elseif ($student->total_paid == $installment * 2) {
                $student->upcoming_date = \Carbon\Carbon::parse($student->admission_date)->addMonths(3)->toDateString();
            } elseif ($student->total_paid == $student->pay_amount) {
                $student->upcoming_date = null;
            }
        }

        return view('internship.running_list', compact('students'));

    }

   
    // Student Active Status Update
    public function updateActiveStatus(Request $request, $id)
    {
        $request->validate(['active_status' => 'required']);
        $student = InternshipRegistration::findOrFail($id);
        $student->update(['active_status' => $request->active_status]);
        return redirect()->back()->with('success', 'Active status updated successfully.');
    }

    public function updateApprovalStatus(Request $request, $id)
    {
        $request->validate(['approve_status' => 'required']);
        $student = InternshipRegistration::findOrFail($id);
        $student->update(['approve_status' => $request->approve_status]);
        return redirect()->back()->with('success', 'Approval status updated successfully.');
    }

    // Download Students List PDF with Filters

    public function downloadListPdf(Request $request)
    {
        $query = InternshipRegistration::with(['batch', 'course']);

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
        $pay_amount = 0;
        $total_paid = 0;
        $total_due = 0;

        foreach ($students as $student) {
            $student->due_amount = $student->pay_amount - $student->total_paid;
            
            $pay_amount += $student->pay_amount;
            $total_paid += $student->total_paid;
            $total_due += $student->due_amount;
        }

        $totals = [
            'total_contract' => $pay_amount,
            'total_paid' => $total_paid,
            'total_due' => $total_due,
        ];

        // 🔹 Generate PDF (landscape)
        $pdf = Pdf::loadView('pdf.intern_list', compact('students', 'totals'))
                    ->setPaper('a4', 'landscape');

        return $pdf->stream('intern_list.pdf');
    }


}
