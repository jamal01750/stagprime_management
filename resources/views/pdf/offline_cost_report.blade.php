<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Offline Cost Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Office Offline Cost Report</h2>

    <table>
        <thead>
            <tr>
                <th>Month / Year</th>
                <th>Category</th>
                <th>Amount(TK.)</th>
                <th>Last Date</th>
                <th>Note</th>
                <th>Payment</th>
                <th>Approval</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $t)
            <tr>
                <td>{{ date('F', mktime(0, 0, 0, $t->month, 1)) }} / {{ $t->year }}</td>
                <td>{{ $t->category->category ?? 'N/A' }}</td>
                <td>{{ number_format($t->amount, 2) }}</td>
                <td>{{ $t->last_date ?? '—' }}</td>
                <td>{{ $t->description ?? '—' }}</td>
                <td>{{ ucfirst($t->status) }}</td>
                <td>{{ ucfirst($t->approve_status) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2"><strong>Total</strong></td>
                <td><strong>{{ number_format($total, 2) }}</strong></td>
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
