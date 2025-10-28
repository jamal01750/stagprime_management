<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Course;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BatchCourseController extends Controller
{
    public function index()
    {
        $batches = Batch::all();
        $courses = Course::all();
        
        return view('batch_course.index', [
            'batches' => $batches,
            'courses' => $courses,
        ]);
    }

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

    public function updateBatch(Request $request, $id)
    {
        $batch = Batch::findOrFail($id);
        $batch->update(['batch_name' => $request->name]);
        return response()->json(['success' => true]);
    }

    public function destroyBatch($id)
    {
        Batch::destroy($id);
        return response()->json(['success' => true]);
    }

    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $course->update(['course_name' => $request->name]);
        return response()->json(['success' => true]);
    }

    public function destroyCourse($id)
    {
        Course::destroy($id);
        return response()->json(['success' => true]);
    }

    public function downloadBatchPdf()
    {
        $batches = Batch::all();
        $pdf = Pdf::loadView('pdf.batch-list', compact('batches'))
                ->setPaper('A4', 'portrait');
        return $pdf->download('batch-list.pdf');
    }

    public function downloadCoursePdf()
    {
        $courses = Course::all();
        $pdf = Pdf::loadView('pdf.course-list', compact('courses'))
                ->setPaper('A4', 'portrait');
        return $pdf->download('course-list.pdf');
    }
}