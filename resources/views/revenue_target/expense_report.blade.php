@extends('layouts.app')

@section('title', 'Expense Report | Monthly Revenue and Target')
@section('heading', 'Expense Report | Monthly Revenue and Target')


@section('content')
<!-- Category Selector -->
    <div>    
        <div class="mb-6">
            <form action="{{ route('expense.report') }}" method="GET" class="max-w-sm">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Select Expense Category</label>
                <select name="category" id="category" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 transition" onchange="this.form.submit()">
                    <option value="offline" {{ $category == 'offline' ? 'selected' : '' }}>Offline Cost</option>
                    <option value="online" {{ $category == 'online' ? 'selected' : '' }}>Online Cost</option>
                    <option value="installment" {{ $category == 'installment' ? 'selected' : '' }}>Installment</option>
                    <option value="intern" {{ $category == 'intern' ? 'selected' : '' }}>Intern Salary</option>
                    <option value="staff" {{ $category == 'staff' ? 'selected' : '' }}>Staff Salary</option>
                </select>
            </form>
        </div>

        <!-- Data Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-600 mb-2">Already Expended This Month</h2>
                <p class="text-4xl font-bold text-green-600">৳{{ number_format($alreadyExpended, 2) }}</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-600 mb-2">Pending Expense This Month</h2>
                <p class="text-4xl font-bold text-red-600">৳{{ number_format($pendingExpense, 2) }}</p>
            </div>
        </div>

        <!-- Graph -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
             <h2 class="text-xl font-bold text-gray-900 mb-4">Daily Expenditure for {{ \Carbon\Carbon::now()->format('F Y') }} - {{ ucfirst($category) }} Costs</h2>
            <canvas id="expenseChart" class="w-full"></canvas>
        </div>
    </div>

    <script>

        const ctx = document.getElementById('expenseChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($graphData['labels']) !!},
            datasets: [{
                label: '{{ $graphData['label'] }}',
                data: {!! json_encode($graphData['data']) !!},
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.4,
            }]
        },
    });
         
      
    </script>

    


@endsection

