@extends('layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Welcome back, ' . Auth::user()->name)

@section('content')
<div class="space-y-6">

    <!-- Section 1 & 2: Summaries (Full Width) -->
    <section>
        <h2 class="text-lg font-bold text-gray-800 mb-4">Financial Summary</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
             <div class="bg-white p-4 rounded-xl shadow-sm border">
                <h3 class="text-xs font-semibold text-gray-500 uppercase">Total Revenue</h3>
                <p class="text-2xl font-bold text-cyan-600 mt-1">৳{{ number_format($allTimeData['totalRevenue'], 0) }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border">
                <h3 class="text-xs font-semibold text-gray-500 uppercase">Total Expense</h3>
                <p class="text-2xl font-bold text-rose-500 mt-1">৳{{ number_format($allTimeData['totalExpense'], 0) }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border">
                <h3 class="text-xs font-semibold text-gray-500 uppercase">Net Profit / Loss</h3>
                <p class="text-2xl font-bold mt-1 {{ $allTimeData['netProfitLoss'] >= 0 ? 'text-green-600' : 'text-red-600' }}">৳{{ number_format($allTimeData['netProfitLoss'], 0) }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border">
                <h3 class="text-xs font-semibold text-gray-500 uppercase">Outstanding Loan</h3>
                <p class="text-2xl font-bold text-indigo-600 mt-1">৳{{ number_format($allTimeData['outstandingLoan'], 0) }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border">
                <h3 class="text-xs font-semibold text-gray-500 uppercase">Client Receivables</h3>
                <p class="text-2xl font-bold text-amber-600 mt-1">৳{{ number_format($allTimeData['clientReceivables'], 0) }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border">
                <h3 class="text-xs font-semibold text-gray-500 uppercase">Student Receivable</h3>
                <p class="text-2xl font-bold text-sky-600 mt-1">৳{{ number_format($allTimeData['studentReceivables'], 0) }}</p>
            </div>
        </div>
    </section>

    <section>
        <h2 class="text-lg font-bold text-gray-800 mb-4">This Month's Summary ({{ \Carbon\Carbon::now()->format('F, Y') }})</h2>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-10 gap-3">
             @php
                $cards = [
                    ['label' => 'Target', 'value' => $currentMonthData['target'], 'cite' => 12],
                    ['label' => 'Collected Revenue', 'value' => $currentMonthData['collectedRevenue'], 'cite' => 14],
                    ['label' => 'Shortfall', 'value' => $currentMonthData['shortfall'], 'cite' => 13],
                    ['label' => 'Expense', 'value' => $currentMonthData['totalExpense'], 'cite' => 15],
                    ['label' => 'Net Profit/Loss', 'value' => $currentMonthData['netProfitLoss'], 'cite' => 16],
                    ['label' => 'Total Credit', 'value' => $currentMonthData['totalCredit'], 'cite' => 17],
                    ['label' => 'Total Debit', 'value' => $currentMonthData['totalDebit'], 'cite' => 18],
                    ['label' => 'Student Pending (Receive)', 'value' => $currentMonthData['studentPending'], 'cite' => 19],
                    ['label' => 'Client Pending (Receive)', 'value' => $currentMonthData['clientPending'], 'cite' => 31],
                    ['label' => 'Intern Pending (Pay)', 'value' => $currentMonthData['internPending'], 'cite' => 20],
                    ['label' => 'Staff Pending (Pay)', 'value' => $currentMonthData['staffPending'], 'cite' => 21],
                    ['label' => 'Offline Cost Pending', 'value' => $currentMonthData['offlinePending'], 'cite' => 22],
                    ['label' => 'Online Cost Pending', 'value' => $currentMonthData['onlinePending'], 'cite' => 23],
                    ['label' => 'Installments Pending', 'value' => $currentMonthData['installmentsPending'], 'cite' => 24],
                    ['label' => 'Product Revenue', 'value' => $currentMonthData['productRevenue'], 'cite' => 25],
                    ['label' => 'Product Loss', 'value' => $currentMonthData['productLoss'], 'cite' => 26],
                    ['label' => 'Own Project Profit', 'value' => $currentMonthData['ownProjectProfit'], 'cite' => 27],
                    ['label' => 'Own Project Loss', 'value' => $currentMonthData['ownProjectLoss'], 'cite' => 28],
                    ['label' => 'Client Project Profit', 'value' => $currentMonthData['clientProjectProfit'], 'cite' => 29],
                    ['label' => 'Client Project Loss', 'value' => $currentMonthData['clientProjectLoss'], 'cite' => 30],
                ];
            @endphp
            @foreach ($cards as $card)
            <div class="bg-gray-100 p-3 rounded-lg text-center">
                <h4 class="text-xs text-gray-600 font-medium">{{ $card['label'] }}</h4>
                <p class="text-base font-bold text-gray-900">৳{{ number_format($card['value'], 0) }}</p>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Main Grid Layout for content below summaries -->
    <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-8 pt-4">

        <!-- === Left Column (2/3 width) === -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Chart Section -->
            <section class="bg-white p-4 sm:p-6 rounded-xl shadow-md border">
                <h3 class="font-bold text-gray-800 text-base">Monthly Revenue vs Expense ({{date('Y')}})</h3>
                <div class="h-80 mt-4"><canvas id="revenueExpenseChart"></canvas></div>
            </section>

            <!-- Monthly Target Progress Line Chart -->
            <section class="bg-white p-4 sm:p-6 rounded-xl shadow-md border">
                <h3 class="font-bold text-gray-800 text-base">Monthly Target Progress ({{date('Y')}})</h3>
                <div class="h-80 mt-4"><canvas id="targetProgressChart"></canvas></div>
            </section>
            
            <!-- NEW Loan Repayment Chart Section -->
            <section class="bg-white p-4 sm:p-6 rounded-xl shadow-md border">
                <h3 class="font-bold text-gray-800 text-base">Loan Repayment Progress ({{date('Y')}})</h3>
                <div class="h-80 mt-4"><canvas id="loanRepaymentChart"></canvas></div>
            </section>

            <!-- Recent Transactions Section -->
            <section class="bg-white p-4 sm:p-6 rounded-xl shadow-md border">
                <h3 class="font-bold text-gray-800 mb-4">Recent Transactions</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                         <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left">Details</th>
                                <th scope="col" class="px-4 py-3 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($transaction['date'])->format('d M, Y') }}</div>
                                    <div class="font-medium text-gray-800">{{ $transaction['description'] }}</div>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold {{ $transaction['type'] == 'Revenue' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction['type'] == 'Expense' ? '-' : '+' }} ৳{{ number_format($transaction['amount'], 0) }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="px-4 py-10 text-center text-gray-500">No recent transactions.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- === Right Column (1/3 width) === -->
        <div class="lg:col-span-1 space-y-8 mt-8 lg:mt-0">
            
            <section class="bg-white p-4 sm:p-6 rounded-xl shadow-md border">
                <h3 class="font-bold text-gray-800 mb-4">Alerts & Reminders</h3>
                <div class="space-y-4">
                     @if($alerts['upcomingPayments']->isEmpty() && $alerts['loanRepayments']->isEmpty() && $alerts['clientPending']->isEmpty() && $alerts['studentPending']->isEmpty() && $alerts['priorityAlerts']->isEmpty())
                        <p class="text-sm text-center text-gray-500 py-4">No new alerts.</p>
                    @else
                        @if($alerts['upcomingPayments']->isNotEmpty())
                        <div class="flex items-start p-3 bg-yellow-50 text-yellow-800 rounded-lg">
                             <div class="flex-shrink-0 w-8 text-center pt-0.5"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>
                            <p class="ml-2 text-sm"><strong>Upcoming Payment:</strong> {{ $alerts['upcomingPayments']->first()->monthlyOfflineCost->category->category }} on {{ \Carbon\Carbon::parse($alerts['upcomingPayments']->first()->monthlyOfflineCost->last_date)->format('d M') }}</p>
                        </div>
                        @endif
                        @if($alerts['loanRepayments']->isNotEmpty())
                        <div class="flex items-start p-3 bg-blue-50 text-blue-800 rounded-lg">
                             <div class="flex-shrink-0 w-8 text-center pt-0.5"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01" /></svg></div>
                            <p class="ml-2 text-sm"><strong>Loan Repayment:</strong> {{ $alerts['loanRepayments']->first()->loan->loan_name }} due on {{ \Carbon\Carbon::parse($alerts['loanRepayments']->first()->pay_date)->format('d M') }}</p>
                        </div>
                        @endif
                        @if($alerts['clientPending']->isNotEmpty())
                        <div class="flex items-start p-3 bg-red-50 text-red-800 rounded-lg">
                            <div class="flex-shrink-0 w-8 text-center pt-0.5"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg></div>
                            <p class="ml-2 text-sm"><strong>Client Payment Overdue:</strong> {{ $alerts['clientPending']->first()->project->project_name }} was due on {{ \Carbon\Carbon::parse($alerts['clientPending']->first()->pay_date)->format('d M') }}</p>
                        </div>
                        @endif
                         @if($alerts['studentPending']->isNotEmpty())
                        <div class="flex items-start p-3 bg-red-50 text-red-800 rounded-lg">
                            <div class="flex-shrink-0 w-8 text-center pt-0.5"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg></div>
                            <p class="ml-2 text-sm"><strong>Student Payment Overdue:</strong> {{ $alerts['studentPending']->first()->student_name }} was due on {{ \Carbon\Carbon::parse($alerts['studentPending']->first()->payment_due_date)->format('d M') }}</p>
                        </div>
                        @endif
                        @if($alerts['priorityAlerts']->isNotEmpty())
                        <div class="flex items-start p-3 bg-green-50 text-green-800 rounded-lg">
                            <div class="flex-shrink-0 w-8 text-center pt-0.5"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
                            <p class="ml-2 text-sm"><strong>Priority Purchase Ready:</strong> {{ $alerts['priorityAlerts']->first()->product->name }} is ready for purchase.</p>
                        </div>
                        @endif
                    @endif
                </div>
            </section>
            
            <section class="bg-white p-4 sm:p-6 rounded-xl shadow-md border">
                <h3 class="font-bold text-gray-800 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-3">
                     <a href="{{ route('student.registration') }}" class="flex items-center justify-center p-3 bg-gray-100 hover:bg-blue-100 text-gray-700 font-semibold rounded-lg transition text-xs">➕ Add Student</a>
                    <a href="{{ route('offline.cost.create') }}" class="flex items-center justify-center p-3 bg-gray-100 hover:bg-blue-100 text-gray-700 font-semibold rounded-lg transition text-xs">➕ Add Offline Cost</a>
                    <a href="{{ route('product.add') }}" class="flex items-center justify-center p-3 bg-gray-100 hover:bg-blue-100 text-gray-700 font-semibold rounded-lg transition text-xs">➕ Add Product</a>
                    <a href="{{ route('client.project.create') }}" class="flex items-center justify-center p-3 bg-gray-100 hover:bg-blue-100 text-gray-700 font-semibold rounded-lg transition text-xs">➕ Add Client Project</a>
                    <a href="{{ route('loan.create') }}" class="flex items-center justify-center p-3 bg-gray-100 hover:bg-blue-100 text-gray-700 font-semibold rounded-lg transition text-xs">➕ Add Loan</a>
                </div>
            </section>
            
             <section class="bg-white p-4 sm:p-6 rounded-xl shadow-md border space-y-6">
                <div>
                    <h3 class="font-bold text-gray-800 text-base">Expense Breakdown</h3>
                    <div class="h-48 mt-2 flex items-center justify-center"><canvas id="expensePieChart"></canvas></div>
                </div>
                <div class="border-t pt-6">
                    <h3 class="font-bold text-gray-800 text-base">Client Payment Status</h3>
                     <div class="h-40 mt-2 flex items-center justify-center"><canvas id="clientStatusChart"></canvas></div>
                </div>
                <div class="border-t pt-6">
                     <h3 class="font-bold text-gray-800 text-base">Student Payment Status</h3>
                     <div class="h-40 mt-2 flex items-center justify-center"><canvas id="studentStatusChart"></canvas></div>
                </div>
            </section>
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const commonOptions = { responsive: true, maintainAspectRatio: false, scales: { y: { ticks: { font: { size: 10 } } }, x: { ticks: { font: { size: 10 } } } } };

    // 1. Revenue vs Expense Bar Chart
    const revExpCtx = document.getElementById('revenueExpenseChart').getContext('2d');
    new Chart(revExpCtx, {
        type: 'bar',
        data: {
            labels: @json($chartsData['monthlyPerformance']['labels']),
            datasets: [
                { label: 'Revenue', data: @json($chartsData['monthlyPerformance']['revenue']), backgroundColor: 'rgba(54, 162, 235, 0.7)', borderRadius: 4 },
                { label: 'Expense', data: @json($chartsData['monthlyPerformance']['expense']), backgroundColor: 'rgba(255, 99, 132, 0.7)', borderRadius: 4 }
            ]
        },
        options: { ...commonOptions, plugins: { legend: { display: true, position: 'bottom', labels: { font: { size: 10 }, usePointStyle: true, boxWidth: 8 } } } }
    });

    // 2. Monthly Target Progress Line Chart
    const targetCtx = document.getElementById('targetProgressChart').getContext('2d');
    const targetProgressData = @json($chartsData['targetProgress']);
    new Chart(targetCtx, {
        type: 'line',
        data: {
            labels: targetProgressData.labels,
            datasets: [{
                label: 'Revenue Progress',
                data: targetProgressData.revenue,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                tension: 0.1,
                fill: true,
            }, {
                label: 'Target',
                data: Array(targetProgressData.labels.length).fill(targetProgressData.target),
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2,
                borderDash: [5, 5],
                pointRadius: 0
            }]
        },
        options: { ...commonOptions, scales: { y: { beginAtZero: true } } }
    });

    // 3. NEW Loan Repayment Line Chart
    const loanRepayCtx = document.getElementById('loanRepaymentChart').getContext('2d');
    new Chart(loanRepayCtx, {
        type: 'line',
        data: {
            labels: @json($chartsData['loanRepayment']['labels']),
            datasets: [{
                label: 'Remaining Loan',
                data: @json($chartsData['loanRepayment']['data']),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: { ...commonOptions, scales: { y: { beginAtZero: true } } }
    });

    // 3. Category-wise Expense Pie Chart
    const expensePieCtx = document.getElementById('expensePieChart').getContext('2d');
    new Chart(expensePieCtx, {
        type: 'pie',
        data: {
            labels: @json($chartsData['expenseBreakdown']['labels']),
            datasets: [{ data: @json($chartsData['expenseBreakdown']['data']), backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF', '#4D5360'] }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: true, position: 'right', labels: { font: { size: 10 }, boxWidth: 15 } } } }
    });

    // 4. Client Payment Status Doughnut Chart
    const clientStatusCtx = document.getElementById('clientStatusChart').getContext('2d');
    new Chart(clientStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Pending', 'Overdue'],
            datasets: [{ data: [@json($chartsData['clientStatus']['paid']), @json($chartsData['clientStatus']['pending']), @json($chartsData['clientStatus']['overdue'])], backgroundColor: ['#4BC0C0', '#FFCE56', '#FF6384'] }]
        },
         options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: true, position: 'left', labels: { font: { size: 10 }, boxWidth: 15 } } }, cutout: '60%' }
    });

    // 5. Student Payment Status Doughnut Chart
    const studentStatusCtx = document.getElementById('studentStatusChart').getContext('2d');
    new Chart(studentStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Pending', 'Overdue'],
            datasets: [{ data: [@json($chartsData['studentStatus']['paid']), @json($chartsData['studentStatus']['pending']), @json($chartsData['studentStatus']['overdue'])], backgroundColor: ['#4BC0C0', '#FFCE56', '#FF6384'] }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: true, position: 'left', labels: { font: { size: 10 }, boxWidth: 15 } } }, cutout: '60%' }
    });
});
</script>
@endsection

