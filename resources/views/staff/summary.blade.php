@extends('layouts.app')

@section('title', 'Salary Summary')
@section('heading', 'Salary Summary')

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Salary Summary</h2>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded shadow">
            <p class="text-gray-600">This Month’s Total Salary</p>
            <h3 class="text-xl font-bold text-blue-700">৳{{ number_format($totalSalary, 2) }}</h3>
        </div>
        <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded shadow">
            <p class="text-gray-600">Paid Staff Count</p>
            <h3 class="text-xl font-bold text-green-700">{{ $paidCount }} </h3>
        </div>
        <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded shadow">
            <p class="text-gray-600">Unpaid Staff Count</p>
            <h3 class="text-xl font-bold text-red-700">{{ $unpaidCount }} </h3>
        </div>
    </div>

    {{-- Reminder Notifications --}}
    <div class="bg-yellow-50 p-4 rounded shadow">
        <h3 class="text-lg font-semibold text-yellow-700 mb-3">
            Upcoming Salary Reminders (Starts from {{ \Carbon\Carbon::parse($reminderDate)->format('d M, Y') }})
        </h3>

        @if($reminders->isEmpty())
            <p class="text-gray-600">No upcoming reminders. 🎉</p>
        @else
            <table class="w-full border border-gray-200 mt-3">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Staff Name</th>
                        <th class="px-4 py-2 text-left">Amount</th>
                        <th class="px-4 py-2 text-left">Salary Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reminders as $reminder)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $reminder->staff->name }}</td>
                            <td class="px-4 py-2">৳{{ number_format($reminder->amount, 2) }}</td>
                            <td class="px-4 py-2">
                                {{ \Carbon\Carbon::parse($reminder->salary_date)->format('d M, Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
