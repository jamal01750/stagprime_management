<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Category Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background: #f1f1f1; }
        h2 { text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>All Category Report</h2>

    <p><strong>From:</strong> {{ $startDate ?? 'All Time' }}  
       <strong>To:</strong> {{ $endDate ?? 'All Time' }}</p>

    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Total Stock</th>
                <th>Current Stock</th>
                <th>Sold Qty</th>
                <th>Loss Qty</th>
                <th>Return Qty</th>
                <th>Revenue (TK.)</th>
                <th>Loss (TK.)</th>
                <th>Return (TK.)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $cat)
            <tr>
                <td>{{ $cat['name'] }}</td>
                <td>{{ $cat['total_stock'] }}</td>
                <td>{{ $cat['current_stock'] }}</td>
                <td>{{ $cat['sell_qty'] }}</td>
                <td>{{ $cat['loss_qty'] }}</td>
                <td>{{ $cat['return_qty'] }}</td>
                <td>{{ number_format($cat['revenue'],2) }}</td>
                <td>{{ number_format($cat['loss'],2) }}</td>
                <td>{{ number_format($cat['return'],2) }}</td>
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th>Total</th>
                <th>{{ $totals['total_stock'] }}</th>
                <th>{{ $totals['current_stock'] }}</th>
                <th>{{ $totals['sell_qty'] }}</th>
                <th>{{ $totals['loss_qty'] }}</th>
                <th>{{ $totals['return_qty'] }}</th>
                <th>{{ number_format($totals['revenue'],2) }}</th>
                <td>{{ number_format($totals['loss'],2) }}</td>
                <td>{{ number_format($totals['return'],2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
