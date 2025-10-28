<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Products List</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Products List</h2>
    <table>
        <thead>
            <tr>
                <th>SL</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $key => $product)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $product->category->name }}</td>
                <td>{{ $product->quantity }}</td>
                <td>{{ $product->amount_type == 'taka' ? 'TK.' : '$' }}{{ number_format($product->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" style="font-weight: bold;">Total</td>
                <td style="text-align: center; font-weight: bold;">{{ $totalQuantity }}</td>
                <td style="text-align: center; font-weight: bold;">
                    TK. {{ number_format($totalAmount, 2) }}
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
