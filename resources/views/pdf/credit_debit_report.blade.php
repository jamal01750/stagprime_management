<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transactions Report</title>
    <style>
        @page { margin: 30px 40px; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #222;
        }
        h1 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 8px;
            color: #111;
        }
        .meta {
            text-align: center;
            font-size: 12px;
            color: #555;
            margin-bottom: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f3f3f3;
            font-weight: bold;
        }
        .credit {
            color: green;
            text-align: right;
        }
        .debit {
            color: red;
            text-align: right;
        }
        .total-row {
            background-color: #f7f7f7;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>

    <h1>Credit & Debit Transactions</h1>
    <div class="meta">
        Generated at: {{ $generated_at }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Credit (TK.)</th>
                <th>Debit (TK.)</th>
                <th>Comment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $t)
            <tr>
                <td>{{ $t->date }}</td>

                <td class="credit">
                    @if($t->type == 'credit')
                        {{ number_format($t->amount, 2) }}
                    @else
                        -
                    @endif
                </td>

                <td class="debit">
                    @if($t->type == 'debit')
                        {{ number_format($t->amount, 2) }}
                    @else
                        -
                    @endif
                </td>

                <td>{{ $t->description ?? '-' }}</td>
                <td>{{ ucfirst($t->approve_status) }}</td>
            </tr>
            @endforeach

            <tr class="total-row">
                <td><strong>Total</strong></td>
                <td class="credit">TK. {{ $totalCredits }}</td>
                <td class="debit">TK. {{ $totalDebits }}</td>
                <td colspan="2"><strong>Balance:</strong> TK. {{ $balance }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} StagPrime Management System
    </div>

</body>
</html>
