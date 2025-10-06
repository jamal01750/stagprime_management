@extends('layouts.app')

@section('title', 'Report | Office Online Cost')
@section('heading', 'Report | Office Online Cost')

@section('content')
    <div class="w-full flex flex-col space-y-6 md:space-y-8">

        <!-- Filter Form -->
        <div class="bg-white rounded shadow p-4 md:p-6 mt-6">
            <form method="GET" action="{{ route('online.cost.report') }}" class="flex flex-wrap items-center gap-4 mb-6">

                {{-- Filter Type --}}
                <select name="filter_type" id="filter_type" onchange="toggleInputs()" class="border p-2 rounded">
                    <option value="day"   {{ $filterType=='day'?'selected':'' }}>Day</option>
                    <option value="month" {{ $filterType=='month'?'selected':'' }}>Month</option>
                    <option value="year"  {{ $filterType=='year'?'selected':'' }}>Year</option>
                    <option value="range" {{ $filterType=='range'?'selected':'' }}>Custom Range</option>
                </select>

                {{-- Day --}}
                <input type="date" name="date" id="dateInput" value="{{ $date ?? '' }}"
                    class="border p-2 rounded {{ $filterType!='day'?'hidden':'' }}">

                {{-- Month --}}
                <select name="month" id="monthInput" class="border p-2 rounded {{ $filterType!='month'?'hidden':'' }}">
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ ($month==$m)?'selected':'' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>

                {{-- Year --}}
                <select name="year" id="yearInput" class="border p-2 rounded {{ !in_array($filterType,['month','year'])?'hidden':'' }}">
                    @for($y=now()->year; $y>=2020; $y--)
                        <option value="{{ $y }}" {{ ($year==$y)?'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>

                {{-- Custom range --}}
                <input type="date" name="start_date" id="startDateInput" value="{{ $startDate ?? '' }}"
                    class="border p-2 rounded {{ $filterType!='range'?'hidden':'' }}">
                <input type="date" name="end_date" id="endDateInput" value="{{ $endDate ?? '' }}"
                    class="border p-2 rounded {{ $filterType!='range'?'hidden':'' }}">

                <button class="px-4 py-2 bg-blue-600 text-white rounded">Filter</button>
            </form>
        </div>



        <!-- Expense List Table -->
        <div class="bg-white rounded shadow p-4 md:p-6 mt-6 overflow-x-auto">
            <h2 class="text-lg font-semibold mb-4">Expense and Expire Date List</h2>
            <table class="min-w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">Expense Category</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Expense Note</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Purchase/Activate Date</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Purchase/Activate Cost</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Expire Date</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Renew Amount</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Paid Date</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td class="border px-4 py-2">{{ $expense->category_name }}</td>
                            <td class="border px-4 py-2">{{ $expense->description }}</td>
                            <td class="border px-4 py-2">{{ $expense->activate_date }}</td>
                            <td class="border px-4 py-2">{{ $expense->activate_type }} {{ number_format($expense->activate_cost, 2) }}</td>
                            <td class="border px-4 py-2">{{ $expense->expire_date }}</td>
                            <td class="border px-4 py-2">{{ $expense->amount_type }} {{ number_format($expense->amount, 2) }}</td>
                            <td class="border px-4 py-2">{{ $expense->paid_date ?? '-' }}</td>
                            <td class="border px-4 py-2">
                                @if($expense->status === 'paid')
                                    <span class="text-green-600 font-medium">Paid</span>
                                @else
                                    <form action="{{ route('online.cost.update', $expense->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="text-red-600 border rounded p-1">
                                            <option class="text-red-600 font-medium" value="unpaid" selected>UnPaid</option>
                                            <option class="text-green-600 font-medium" value="paid">Paid</option>
                                        </select>
                                        <button type="submit" class="text-green-600 hover:underline ml-2">Update Status</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">No expenses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        
    </div>

    <script>
        function toggleInputs() {
            let type = document.getElementById('filter_type').value;
            document.getElementById('dateInput').classList.add('hidden');
            document.getElementById('monthInput').classList.add('hidden');
            document.getElementById('yearInput').classList.add('hidden');
            document.getElementById('startDateInput').classList.add('hidden');
            document.getElementById('endDateInput').classList.add('hidden');

            if (type === 'day') {
                document.getElementById('dateInput').classList.remove('hidden');
            } else if (type === 'month') {
                document.getElementById('monthInput').classList.remove('hidden');
                document.getElementById('yearInput').classList.remove('hidden');
            } else if (type === 'year') {
                document.getElementById('yearInput').classList.remove('hidden');
            } else if (type === 'range') {
                document.getElementById('startDateInput').classList.remove('hidden');
                document.getElementById('endDateInput').classList.remove('hidden');
            }
        }
        toggleInputs();
    </script>

@endsection