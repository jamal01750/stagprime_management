@extends('layouts.app')

@section('title', 'Interns List | Office Intern')
@section('heading', 'Interns List | Office Intern')

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
                <th class="border border-gray-300 px-3 py-2">Intern ID</th>
                <th class="border border-gray-300 px-3 py-2">Name</th>
                <th class="border border-gray-300 px-3 py-2">Phone</th>
                <th class="border border-gray-300 px-3 py-2">Payment Status</th>
                <th class="border border-gray-300 px-3 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
            <tr>
                <td class="border px-3 py-2 text-center">{{ $student->intern_id }}</td>
                <td class="border px-3 py-2">{{ $student->internee_name }}</td>
                <td class="border px-3 py-2">{{ $student->phone }}</td>
                <td class="border px-3 py-2 text-center">
                    @if($student->payment_status === 'Full paid')
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded">Full paid</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded">Partial</span>
                    @endif
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
@endsection
