<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Details - {{ $student->student_name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #333; margin: 20px; }
        .header { text-align: center; background: #4f46e5; color: white; padding: 10px; border-radius: 8px; }
        .section { margin-top: 10px; }
        .section h3 { background: #f3f4f6; padding: 6px 10px; border-radius: 6px; }
        .info-table { width: 100%; border-collapse: collapse; margin-top: 3px; }
        .info-table td { padding: 2px 2px; vertical-align: top; }
        .info-table td.label { width: 30%; font-weight: bold; color: #111; }
        .status { display: inline-block; padding: 3px 8px; border-radius: 4px; color: white; font-size: 12px; }
        .running { background: #16a34a; }
        .inactive { background: #dc2626; }
        .paid { background: #16a34a; }
        .unpaid { background: #f59e0b; }
        .image-container { text-align: center; margin-top: 10px; }
        img { border-radius: 8px; border: 2px solid #ddd; }
    </style>
</head>
<body>

    <div class="image-container">
        @if($student->image)
            <img src="{{ public_path('storage/' . $student->image) }}" alt="Student Image" width="120" height="120">
        @else
            <p>No Image Available</p>
        @endif
    </div>

    <div class="section">
        <h3>Personal Info</h3>
        <table class="info-table">
            <tr><td class="label">Name</td><td>{{ $student->student_name }}</td></tr>
            <tr><td class="label">Student Id</td><td>{{ $student->student_id }}</td></tr>
            <tr><td class="label">Phone</td><td>{{ $student->phone }}</td></tr>
            <tr><td class="label">Alt Phone</td><td>{{ $student->alt_Phone ?? '-' }}</td></tr>
            <tr><td class="label">NID/Birth No</td><td>{{ $student->nid_birth }}</td></tr>
            <tr><td class="label">Address</td><td>{{ $student->address }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Course & Batch Info</h3>
        <table class="info-table">
            <tr><td class="label">Course</td><td>{{ $student->course->course_name ?? 'N/A' }}</td></tr>
            <tr><td class="label">Batch</td><td>{{ $student->batch->batch_name ?? 'N/A' }}</td></tr>
            <tr><td class="label">Batch Time</td><td>{{ $student->batch_time }}</td></tr>
            <tr><td class="label">Admission Date</td><td>{{ $student->admission_date }}</td></tr>
            <tr>
                <td class="label">Active Status</td>
                <td>
                    <span class="status {{ $student->active_status === 'Running' ? 'running' : 'inactive' }}">
                        {{ $student->active_status }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Payment Info</h3>
        <table class="info-table">
            <tr><td class="label">Total Fee</td><td>{{ number_format($student->total_fee, 2) }}</td></tr>
            <tr><td class="label">Paid Amount</td><td>{{ number_format($student->calculated_paid, 2) }}</td></tr>
            <tr>
                <td class="label">Due Amount</td>
                <td style="color: {{ $student->due_amount > 0 ? 'red' : 'green' }}">
                    {{ number_format($student->due_amount, 2) }}
                </td>
            </tr>
            <tr>
                <td class="label">Payment Status</td>
                <td>
                    <span class="status {{ $student->payment_status === 'Paid' ? 'paid' : 'unpaid' }}">
                        {{ $student->payment_status }}
                    </span>
                </td>
            </tr>
            <tr><td class="label">Next Payment Date</td><td>{{ $student->payment_due_date ?? '-' }}</td></tr>
            <tr><td class="label">Description</td><td>{{ $student->description ?? '-' }}</td></tr>
        </table>
    </div>

</body>
</html>
