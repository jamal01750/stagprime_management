@extends('layouts.app')

@section('title', 'Report | Office Online Cost')
@section('heading', 'Report | Office Online Cost')

@section('content')
    <div class="w-full flex flex-col space-y-6 md:space-y-8">

        <!-- Filter Form -->
        <div class="bg-white rounded shadow p-4 md:p-6 mt-6">
            <form method="GET" action="{{ route('online.cost.report') }}" class="flex space-x-4">
                <div>
                    <label class="block text-sm font-medium">Year</label>
                    <select name="year" class="border rounded px-2 py-1">
                        @for($y = now('Asia/Dhaka')->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Month</label>
                    <select name="month" class="border rounded px-2 py-1">
                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $index => $monthName)
                            <option value="{{ $index + 1 }}" @selected($index + 1 == $month)>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Filter</button>
                </div>
            </form>
        </div>

        <!-- Expense List Table -->
        <div class="bg-white rounded shadow p-4 md:p-6 mt-6 overflow-x-auto">
            <h2 class="text-lg font-semibold mb-4">Monthly Expense and Expire Date List</h2>
            <table class="w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">Expense Category</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Expense Note</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Activate Date</th>
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
                            <td colspan="6" class="text-center py-4 text-gray-500">No expenses found for this month.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        
    </div>
@endsection