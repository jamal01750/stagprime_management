@extends('layouts.app')

@section('title', 'Revenue Report | Monthly Revenue and Target')
@section('heading', 'Revenue Report | Monthly Revenue and Target')


@section('content')
<!-- Category Selector -->
    <div>
        <!-- Category Selector -->
        <div class="mb-6">
            <form action="{{ route('revenue.report') }}" method="GET" class="max-w-sm">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Select Revenue Category</label>
                <select name="category" id="category" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 transition" onchange="this.form.submit()">
                    <option value="product_sell" {{ $category == 'product_sell' ? 'selected' : '' }}>Product Sell</option>
                    <option value="student_admission" {{ $category == 'student_admission' ? 'selected' : '' }}>Student Admission</option>
                    <option value="company_project" {{ $category == 'company_project' ? 'selected' : '' }}>Company Project</option>
                    <option value="client_project" {{ $category == 'client_project' ? 'selected' : '' }}>Client Project</option>
                </select>
            </form>
        </div>

        <!-- Data Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-600 mb-2">Collected Revenue This Month</h2>
                <p class="text-4xl font-bold text-green-600">৳{{ number_format($collectedRevenue, 2) }}</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-600 mb-2">Pending Revenue This Month</h2>
                <p class="text-4xl font-bold text-orange-500">৳{{ number_format($pendingRevenue, 2) }}</p>
            </div>
        </div>

        <!-- Graph -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
             <h2 class="text-xl font-bold text-gray-900 mb-4">Daily Revenue for {{ \Carbon\Carbon::now()->format('F Y') }} - {{ ucwords(str_replace('_', ' ', $category)) }}</h2>
            <canvas id="revenueChart" class="w-full" height="400"></canvas>
        </div>
    </div>

    <script>

        const ctx = document.getElementById('revenueChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($graphData['labels']) !!},
            datasets: [{
                label: 'Collected Revenue (Taka)',
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

