@extends('layouts.app')

@section('title', 'Summary | Daily Credit & Debit')
@section('heading', 'Summary | Daily Credit & Debit')

@section('content')
<div class="w-full flex flex-col space-y-6 md:space-y-8">

    {{-- First Div: Summary Table --}}
    <div class="bg-white rounded shadow p-4 md:p-6">
        <h2 class="text-lg md:text-xl font-semibold mb-4">Summary Overview</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 divide-y divide-gray-200 text-sm md:text-base">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold">-</th>
                        <th class="px-4 py-2 text-left font-semibold">Today</th>
                        <th class="px-4 py-2 text-left font-semibold">This Month</th>
                        <th class="px-4 py-2 text-left font-semibold">This Year</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-4 py-2 font-medium">Total Credit</td>
                        <td class="px-4 py-2 text-green-600 font-bold">BDT {{ $todayCredits }}</td>
                        <td class="px-4 py-2 text-green-600 font-bold">BDT {{ $monthCredits }}</td>
                        <td class="px-4 py-2 text-green-600 font-bold">BDT {{ $yearCredits }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium">Total Debit</td>
                        <td class="px-4 py-2 text-red-600 font-bold">BDT {{ $todayDebits }}</td>
                        <td class="px-4 py-2 text-red-600 font-bold">BDT {{ $monthDebits }}</td>
                        <td class="px-4 py-2 text-red-600 font-bold">BDT {{ $yearDebits }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium">Balance</td>
                        <td class="px-4 py-2 text-blue-600 font-bold">BDT {{ $todayBalance }}</td>
                        <td class="px-4 py-2 text-blue-600 font-bold">BDT {{ $monthBalance }}</td>
                        <td class="px-4 py-2 text-blue-600 font-bold">BDT {{ $yearBalance }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Combined Monthly Chart -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4 md:p-6">
        <canvas id="txChart" height="120"></canvas>
    </div>
    
    <!-- Monthly Credit Chart -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4 md:p-6">
        <canvas id="mcChart" height="120"></canvas>
    </div>

    <!-- Monthly Debit Chart -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4 md:p-6">
        <canvas id="mdChart" height="120"></canvas>
    </div>

    <!-- Monthly Balance Chart -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4 md:p-6">
        <canvas id="mbChart" height="120"></canvas>
    </div>
</div>

<!-- Monthly chart script -->
<script type="application/json" id="tx-data">
  {"labels": @json($labels), "credits": @json($dcredits), "debits": @json($ddebits), "balance": @json($dbalance)}
</script>

<script>
    const { labels, credits, debits, balance } =
        JSON.parse(document.getElementById('tx-data').textContent);

    // Combined Chart
    new Chart(document.getElementById('txChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Credit',
                    data: credits,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Debit',
                    data: debits,
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Balance (Credit - Debit)',
                    data: balance,
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.3
                }
            ]
        },
        options: chartOptions("{{ $monthName }} {{ $year }} Transactions")
    });

    // Credit Only
    new Chart(document.getElementById('mcChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Credit',
                    data: credits,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: chartOptions("{{ $monthName }} {{ $year }} Credits")
    });

    // Debit Only
    new Chart(document.getElementById('mdChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Debit',
                    data: debits,
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: chartOptions("{{ $monthName }} {{ $year }} Debits")
    });

    // Balance Only
    new Chart(document.getElementById('mbChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Balance',
                    data: balance,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.3
                }
            ]
        },
        options: chartOptions("{{ $monthName }} {{ $year }} Balance")
    });

    // Reusable Chart Options
    function chartOptions(title) {
        return {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                title: { display: true, text: title },
                tooltip: {
                    callbacks: {
                        label: (ctx) =>
                            `${ctx.dataset.label}: ${Number(ctx.parsed.y ?? 0).toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            })}`
                    }
                },
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: (v) => Number(v).toLocaleString() }
                }
            }
        };
    }
</script>
@endsection
