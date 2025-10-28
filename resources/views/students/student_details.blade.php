@extends('layouts.app')

@section('title', 'Student Details | Office Student')
@section('heading', 'Student Details | Office Student')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">

        {{-- Header with Image --}}
        <div class="flex flex-col md:flex-row items-center p-6 bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
            <div class="flex-shrink-0">
                @if($student->image)
                    <img src="{{ asset('storage/'.$student->image) }}" 
                         alt="{{ $student->student_name }}" 
                         class="h-32 w-32 object-cover rounded-full border-4 border-white shadow">
                @else
                    <div class="h-32 w-32 flex items-center justify-center bg-gray-200 text-gray-500 rounded-full border-4 border-white shadow">
                        No Image
                    </div>
                @endif
            </div>
            <div class="mt-4 md:mt-0 md:ml-6 text-center md:text-left">
                <h2 class="text-2xl font-bold">{{ $student->student_name }}</h2>
                <p class="text-sm opacity-80">ID: {{ $student->student_id }}</p>
                <p class="mt-2"><span class="font-semibold">Course:</span> {{ $student->course_name ?? 'N/A' }}</p>
                <p><span class="font-semibold">Batch:</span> {{ $student->batch_name ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Details --}}
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div>
                <h3 class="text-lg font-semibold mb-2">Personal Info</h3>
                <p><span class="font-semibold">Phone:</span> {{ $student->phone }}</p>
                <p><span class="font-semibold">Alt Phone:</span> {{ $student->alt_Phone ?? '-' }}</p>
                <p><span class="font-semibold">NID/Birth No:</span> {{ $student->nid_birth }}</p>
                <p><span class="font-semibold">Address:</span> {{ $student->address }}</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-2">Admission Info</h3>
                <p><span class="font-semibold">Admission Date:</span> {{ $student->admission_date }}</p>
                <p><span class="font-semibold">Batch Time:</span> {{ $student->batch_time }}</p>
                <p><span class="font-semibold">Active Status:</span> 
                    <span class="px-2 py-1 rounded text-white 
                        {{ $student->active_status === 'Running' ? 'bg-green-500' : 'bg-red-500' }}">
                        {{ $student->active_status }}
                    </span>
                </p>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-2">Payment Info</h3>
                <p><span class="font-semibold">Total Fee:</span> {{ number_format($student->total_fee, 2) }}</p>
                <p><span class="font-semibold">Paid:</span> {{ number_format($student->calculated_paid, 2) }}</p>
                <p><span class="font-semibold">Due:</span> 
                    <span class="{{ $student->due_amount > 0 ? 'text-red-600 font-bold' : 'text-green-600 font-bold' }}">
                        {{ number_format($student->due_amount, 2) }}
                    </span>
                </p>
                <p><span class="font-semibold">Payment Status:</span> 
                    <span class="px-2 py-1 rounded text-white 
                        {{ $student->payment_status === 'Paid' ? 'bg-green-600' : 'bg-yellow-500' }}">
                        {{ $student->payment_status }}
                    </span>
                </p>
                <p><span class="font-semibold">Next Payment Date:</span> {{ $student->payment_due_date ?? '-' }}</p>
                <p><span class="font-semibold">Description:</span> {{ $student->description ?? '-' }}</p>
            </div>

        </div>
        
        {{-- Actions --}}
        <div class="p-6 border-t flex justify-end space-x-3">
            <a href="{{ route('student.download.pdf', $student->id) }}" target="_blank" 
                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                Download PDF
            </a>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('student.edit', $student->id) }}" 
               class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
               Edit/Update
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
