<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Interns List PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: center; }
        th { background: #f3f4f6; }
        h2 { text-align: center; margin-bottom: 10px; }
        .totals { margin-top: 15px; font-weight: bold; }
        img { border-radius: 4px; }
    </style>
</head>
<body>
    <h2>Interns List Report</h2>
    <table>
        <thead>
            <tr>
                <th>Sl.</th>
                <th>Image</th>
                <th>ID</th>
                <th>Name</th>
                <th>Batch/Course</th>
                <th>Phone/Alt. Phone</th>
                <th>Nid/Birth</th>
                <th>Address</th>
                <th>Total Contract</th>
                <th>Total Paid</th>
                <th>Total Due</th>
                <th>Active</th>
                <th>Approval</th>
                <th>Admission Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $key => $student)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>
                    @if($student->image)
                        <img src="{{ public_path('storage/'.$student->image) }}" width="40" height="40">
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $student->intern_id }}</td>
                <td>{{ $student->internee_name }}</td>
                <td>{{ $student->batch->batch_name ?? 'N/A' }} / {{ $student->course->course_name ?? 'N/A' }}</td>
                <td>{{ $student->phone }} / {{ $student->alt_Phone }}</td>
                <td>{{ $student->nid_birth }}</td>
                <td>{{ $student->address }}</td>
                <td>{{ number_format($student->pay_amount, 2) }}</td>
                <td>{{ number_format($student->total_paid, 2) }}</td>
                <td>{{ number_format($student->due_amount, 2) }}</td>
                <td>{{ $student->active_status }}</td>
                <td>{{ $student->approve_status }}</td>
                <td>{{ $student->admission_date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p>Total Fee: {{ number_format($totals['total_contract'], 2) }}</p>
        <p>Total Paid: {{ number_format($totals['total_paid'], 2) }}</p>
        <p>Total Due: {{ number_format($totals['total_due'], 2) }}</p>
    </div>
</body>
</html>
