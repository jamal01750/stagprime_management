@extends('layouts.app')

@section('title', 'Students List | Office Student')
@section('heading', 'Students List | Office Student')

@section('content')
<div class="bg-white p-6 rounded shadow-md">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full border-collapse border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="border border-gray-300 px-3 py-2">Student ID</th>
                <th class="border border-gray-300 px-3 py-2">Name</th>
                <th class="border border-gray-300 px-3 py-2">Phone</th>
                <th class="border border-gray-300 px-3 py-2">Payment Status</th>
                <th class="border border-gray-300 px-3 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
            <tr>
                <td class="border px-3 py-2 text-center">{{ $student->student_id }}</td>
                <td class="border px-3 py-2">{{ $student->student_name }}</td>
                <td class="border px-3 py-2">{{ $student->phone }}</td>
                <td class="border px-3 py-2 text-center">
                    @if($student->payment_status === 'Paid')
                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded">Paid</span>
                    @else
                        <select class="px-2 py-1 rounded">
                            <option class="bg-red-100 text-red-700">Unpaid</option>
                            <option class="bg-green-100 text-green-700">Paid</option>
                        </select>
                        <a href="{{ route('student.payment.update', $student->id) }}" 
                           class="ml-2 px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                           Mark as Paid
                        </a>
                    @endif
                </td>
                <td class="border px-3 py-2 flex space-x-2">
                    <a href="{{ route('student.individual', $student->id) }}" 
                       class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                       View Details
                    </a>
                    <a href="{{ route('student.edit', $student->id) }}" 
                       class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                       Edit/Update
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center p-4 text-gray-500">No students found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
