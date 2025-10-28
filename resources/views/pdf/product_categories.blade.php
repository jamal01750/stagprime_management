<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Category List</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Product Category List</h2>
    <table>
        <thead>
            <tr>
                <th>SL</th>
                <th>Category Name</th>
                <th>Total Stock</th>
                <th>Current Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $key => $category)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->total_quantity }}</td>
                <td>{{ $category->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
