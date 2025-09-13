@extends('layouts.app')

@section('title', 'Dashboard | StagPrime Cost Management')
@section('heading', 'Dashboard')

@section('content')
    <div class="w-full flex flex-col space-y-6 md:space-y-8">
        <div class="bg-white rounded shadow p-4 md:p-6 flex flex-row items-start">
            <div class="w-1/5 flex">
                <h2 class="text-lg md:text-xl font-semibold mb-2 md:mb-4">Summary ({{$year}})</h2>
            </div>
            <div class="w-4/5">
                <ul class="space-y-1 md:space-y-2">
                    <li>Total Credit: <span class="font-bold mx-2 md:mx-4 text-green-600">BDT {{$totalCredits}}</span></li>
                    <li>Total Debit: <span class="font-bold mx-2 md:mx-4 text-red-600">BDT {{$totalDebits}}</span></li>
                    <li>Balance: <span class="font-bold mx-4 md:mx-9 text-blue-600">BDT {{$balance}}</span></li>
                </ul>
            </div>
        </div>

        @if(session('success'))
            <div class="w-full mb-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif 
        

        <div class="max-w-7xl mx-auto p-4 md:p-6">
            <div class="grid grid-cols-1 gap-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-xl md:text-2xl font-semibold">Monthly Transactions — {{ $year }}</h1>

                    <form method="get" action="{{ route('yearlyreports') }}" class="flex items-center gap-2">
                        <select name="year" class="border rounded px-2 py-1">
                            @for($y = now('Asia/Dhaka')->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                            @endfor
                        </select>
                        <button class="px-3 py-1 rounded bg-indigo-600 text-white">Go</button>
                    </form>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4 md:p-6">
                    <canvas id="txChart" height="120"></canvas>
                </div>
                <form action="{{ route('yearlysummary.downloadpdf') }}" method="POST" target="_blank" class="mt-4">
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4 md:p-6 overflow-x-auto">
                        <h2 class="text-lg font-medium mb-3">Yearly Summary
                            <button type="submit" class="px-5 mx-10 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Download Pdf</button>
                        </h2>
                        <table class="w-full text-sm">
                            <thead class="text-left border-b">
                                <tr>
                                    <th class="py-2">Month</th>
                                    <th class="py-2">Credit</th>
                                    <th class="py-2">Debit</th>
                                    <th class="py-2">Net (C - D)</th>
                                    <th class="py-2">Cumulative Balance</th>
                                    <th class="py-2">Target</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($labels as $i => $m)
                                    <tr class="border-b last:border-0">
                                        <td class="py-2">{{ $m }}</td>
                                        <td class="py-2">{{ number_format($mcredits[$i], 2) }}</td>
                                        <td class="py-2">{{ number_format($mdebits[$i], 2) }}</td>
                                        <td class="py-2">{{ number_format($mbalance[$i], 2) }}</td>
                                        <td class="py-2">{{ number_format($cumBal[$i], 2) }}</td>
                                        <td class="py-2">{{ number_format($target[$i], 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-100 font-semibold border-t">
                                    <td class="py-2">Total ({{ $year }})</td>
                                    <td class="py-2">{{ $totalCredits }}</td>
                                    <td class="py-2">{{ $totalDebits }}</td>
                                    <td class="py-2">{{ $balance }}</td>
                                    <td class="py-2">—</td>
                                    <td class="py-2">{{ $totaltarget }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        const toggleCheckBtn = document.getElementById('toggleCheck');
        const checkboxes = document.querySelectorAll('.transaction-checkbox');
        let allChecked = false;

        toggleCheckBtn.addEventListener('click', () => {
            allChecked = !allChecked;
            checkboxes.forEach(cb => cb.checked = allChecked);
            toggleCheckBtn.textContent = allChecked ? 'Unmark All' : 'Mark All';
        });
    </script>

    {{-- Chart.js CDN --}}
    <script type="application/json" id="tx-data">
        {   "labels": @json($labels), 
            "credits": @json($mcredits), 
            "debits": @json($mdebits), 
            "balance": @json($mbalance),
            "cumBal": @json($cumBal),
            "targets": @json($target),
        }
    </script>

    <script>
        const { labels, credits, debits, balance, cumBal, targets } =
            JSON.parse(document.getElementById('tx-data').textContent);
    

        const ctx = document.getElementById('txChart').getContext('2d');

        // Mixed chart: bars for Credit/Debit, lines for Balance/Target/Cumulative
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Credit',
                        data: credits,
                        borderWidth: 1
                    },
                    {
                        type: 'bar',
                        label: 'Debit',
                        data: debits,
                        borderWidth: 1
                    },
                    {
                        type: 'line',
                        label: 'Monthly Balance (C-D)',
                        data: balance,
                        tension: 0.3
                    },
                    {
                        type: 'line',
                        label: 'Cumulative Balance',
                        data: cumBal,
                        tension: 0.3
                    },
                    {
                        type: 'line',
                        label: 'Target',
                        data: targets,
                        borderDash: [6,6],
                        tension: 0
                    },
                ]
            },
            options: {
                responsive: true,
                // animation: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    tooltip: { callbacks: {
                        label: (ctx) => `${ctx.dataset.label}: ${Number(ctx.parsed.y ?? 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}`
                    }},
                    legend: { position: 'top' },
                    title: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (v) => Number(v).toLocaleString() }
                    }
                }
            }
        });
    </script>
@endsection



