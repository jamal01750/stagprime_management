<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pending Losses List</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Lost Products List</h2>
    <table>
        <thead>
            <tr>
                <th>SL</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Amount</th>
                <th>Comment</th>
            </tr>
        </thead>
        <tbody>
            @foreach($losses as $key => $loss)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $loss->category->name }}</td>
                <td>{{ $loss->quantity }}</td>
                <td>{{ $loss->amount_type == 'taka' ? 'TK.' : '$' }}{{ number_format($loss->loss_amount, 2) }}</td>
                <td>{{ $loss-> description }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" style="font-weight: bold;">Total</td>
                <td style="text-align: center; font-weight: bold;">{{ $totalQuantity }}</td>
                <td style="text-align: center; font-weight: bold;">
                    TK. {{ number_format($totalAmount, 2) }}
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
