@extends('layouts.app')

@section('title', 'Interns List | Office Interns')
@section('heading', 'Interns List | Office Interns')

@section('content')
<div class="bg-white p-6 rounded shadow-md">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <div class="bg-white rounded shadow p-4 md:p-6 mt-6 overflow-x-auto">
        <h2 class="text-lg md:text-xl font-semibold mb-4">Running Interns List</h2>
        <table class="min-w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-3 py-2">Intern ID</th>
                    <th class="border border-gray-300 px-3 py-2">Name</th>
                    <th class="border border-gray-300 px-3 py-2">Contract Amount</th>
                    <th class="border border-gray-300 px-3 py-2">Total Paid</th>
                    <th class="border border-gray-300 px-3 py-2">Upcoming Amount</th>
                    <th class="border border-gray-300 px-3 py-2">Upcoming Date</th>
                    <th class="border border-gray-300 px-3 py-2">Update Payment</th>
                    <th class="border border-gray-300 px-3 py-2">Active Status</th>
                    <th class="border border-gray-300 px-3 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td class="border px-3 py-2 text-center">{{ $student->intern_id }}</td>
                    <td class="border px-3 py-2">{{ $student->internee_name }}</td>
                    <td class="border px-3 py-2">{{ $student->pay_amount }}</td>
                    <td class="border px-3 py-2">{{ $student->total_paid }}</td>
                    <td class="border px-3 py-2">{{ $student->upcoming_amount }}</td>
                    <td class="border px-3 py-2">{{ $student->upcoming_date }}</td>
                    <td class="border px-3 py-2 text-center">
                        @if($student->total_paid < $student->pay_amount)
                            <a href="{{ route('internship.payment.update', $student->id) }}"
                            class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                            Update
                            </a>
                        @else
                            <button class="px-3 py-1 bg-gray-400 text-white rounded cursor-not-allowed" disabled>
                                Fully Paid
                            </button>
                        @endif
                    </td>
                    <td class="border px-3 py-2 text-center">
                        <select class="px-2 py-1 rounded">
                            <option class="bg-green-100 text-green-700">Running</option>
                            <option class="bg-red-100 text-red-700">Expired</option>
                        </select>
                        <a href="{{ route('internship.active.status.update', $student->id) }}" 
                            class="ml-2 px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                            Mark as Expired
                        </a>
                    </td>
                    <td class="border px-3 py-2 flex space-x-2">
                        <a href="{{ route('internship.individual', $student->id) }}" 
                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                        View Details
                        </a>
                        <a href="{{ route('internship.edit', $student->id) }}" 
                        class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                        Edit/Update
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center p-4 text-gray-500">No Interns found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
