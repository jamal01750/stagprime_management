<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Course List</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Course List</h2>
    <table>
        <thead>
            <tr>
                <th>SL</th>
                <th>Course Name</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses as $key => $course)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $course->course_name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
