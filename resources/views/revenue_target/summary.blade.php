@extends('layouts.app')

@section('title', 'Summary Report | Monthly Revenue and Target')
@section('heading', 'Summary Report | Monthly Revenue and Target')


@section('content')
    <div>
        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Column 1: Data Cards -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Card 1: Target -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-500 mb-1">This Month's Target</h2>
                    <p class="text-4xl font-bold text-indigo-600">৳{{ number_format($targetAmount, 2) }}</p>
                </div>
                
                <!-- Card 2: Revenue -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-500 mb-1">Total Revenue (Live)</h2>
                    <p class="text-4xl font-bold text-green-600">৳{{ number_format($totalRevenue, 2) }}</p>
                </div>

                <!-- Card 3: Expense -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-500 mb-1">Total Expense (Live)</h2>
                    <p class="text-4xl font-bold text-red-600">৳{{ number_format($totalExpense, 2) }}</p>
                </div>

                <!-- Card 4: Shortage -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-500 mb-1">Target Shortage</h2>
                    <p class="text-4xl font-bold text-orange-500">৳{{ number_format($shortage, 2) }}</p>
                    <p class="text-sm text-gray-500 mt-2">Amount needed to reach the target.</p>
                </div>
            </div>

            <!-- Column 2: Pie Chart -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Revenue by Category</h2>
                <div style="position: relative; height:40vh; min-height: 450px;">
                    <canvas id="revenuePieChart"></canvas>
                </div>
            </div>

        </div>

        <!-- Section for Bar Chart -->
        <div class="mt-8 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Target vs. Achieved Revenue</h2>
            <div style="position: relative; height:50vh; min-height: 400px;">
                 <canvas id="targetVsRevenueBarChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Pie Chart for Revenue Categories
            const pieCtx = document.getElementById('revenuePieChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($pieChartData['labels']) !!},
                    datasets: [{
                        label: 'Revenue',
                        data: {!! json_encode($pieChartData['data']) !!},
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });

            // 2. Bar Chart for Target vs. Revenue
            const barCtx = document.getElementById('targetVsRevenueBarChart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: ['Target vs. Achieved'],
                    datasets: [
                        {
                            label: 'Target',
                            data: [{{ $targetAmount }}],
                            backgroundColor: 'rgba(99, 102, 241, 0.7)',
                            borderColor: 'rgba(99, 102, 241, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Achieved Revenue',
                            data: [{{ $totalRevenue }}],
                            backgroundColor: 'rgba(34, 197, 94, 0.7)',
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '৳' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) { label += ': '; }
                                    if (context.parsed.y !== null) {
                                        label += '৳' + context.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection