<!DOCTYPE html>
<html>
<head>
    <title>Monthly Summary</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>    
</head>
<body>  
    <h2>Monthly Summary for {{ $year }}</h2>
    
    <table class="min-w-full text-sm">
        <thead>
            <tr>
                <th class="px-2 py-1 text-left">Month</th>
                <th class="px-2 py-1 text-left">Credit</th>
                <th class="px-2 py-1 text-left">Debit</th>
                <th class="px-2 py-1 text-left">Net (C - D)</th>
                <th class="px-2 py-1 text-left">Cumulative Balance</th>
                <th class="px-2 py-1 text-left">Target</th>
            </tr>
        </thead>
        <tbody>
            @foreach($labels as $i => $m)
                <tr>
                    <td class="px-2 py-1">{{ $m }}</td>
                    <td class="px-2 py-1">{{ number_format($mcredits[$i], 2) }}</td>
                    <td class="px-2 py-1">{{ number_format($mdebits[$i], 2) }}</td>
                    <td class="px-2 py-1">{{ number_format($mbalance[$i], 2) }}</td>
                    <td class="px-2 py-1">{{ number_format($cumBal[$i], 2) }}</td>
                    <td class="px-2 py-1">{{ number_format($target[$i], 2) }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td class="px-2 py-1">Total ({{ $year }})</td>
                <td class="px-2 py-1">{{ $totalCredits }}</td>
                <td class="px-2 py-1">{{ $totalDebits }}</td>
                <td class="px-2 py-1">{{ $balance }}</td>
                <td class="px-2 py-1">—</td>
                <td class="px-2 py-1">{{ $totaltarget }}</td>
            </tr>
        </tbody>
    </table>