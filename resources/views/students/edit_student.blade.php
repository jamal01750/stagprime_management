@extends('layouts.app')

@section('title', 'Edit Student | Student Registration')
@section('heading', 'Edit Student | Student Registration')

@section('content')
<div class="w-full flex flex-col space-y-6 md:space-y-8">

    @if(session('success'))
        <div class="w-full mb-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="bg-white rounded shadow p-4 md:p-6 flex flex-col md:flex-row items-start">
        <div class="w-full flex flex-row">
            <div class="w-1/5 flex flex-col flex-shrink-0 pr-4">
                <h2 class="text-lg md:text-xl font-semibold mb-2 md:mb-4">Edit Student</h2>
            </div>
            <div class="w-4/5">
                <form method="POST" action="{{ route('student.update', $student->id) }}" enctype="multipart/form-data" class="space-y-3 md:space-y-4 w-full md:w-auto">
                    @csrf
                    @method('PUT')

                    {{-- Current Image --}}
                    <div>
                        <label class="block text-sm font-medium">Current Image</label>
                        @if($student->image)
                            <img src="{{ asset('storage/'.$student->image) }}" class="h-24 w-24 object-cover rounded mb-2">
                        @else
                            <p class="text-gray-500 text-sm">No image uploaded</p>
                        @endif
                        <input type="file" name="image" accept="image/*" class="w-full border rounded p-2">
                        @error('image') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Name</label>
                        <input type="text" name="student_name" value="{{ old('student_name', $student->student_name) }}" class="mt-1 block w-full border-2 border-blue-600 rounded pl-2" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" class="mt-1 block w-full border-2 border-blue-600 rounded pl-2" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Alternative Phone Number</label>
                        <input type="text" name="alt_Phone" value="{{ old('alt_Phone', $student->alt_Phone) }}" class="mt-1 block w-full border-2 border-blue-600 rounded pl-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium">NID / Birth Registration Number</label>
                        <input type="text" name="nid_birth" value="{{ old('nid_birth', $student->nid_birth) }}" class="mt-1 block w-full border-2 border-blue-600 rounded pl-2" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Address</label>
                        <input type="text" name="address" value="{{ old('address', $student->address) }}" class="mt-1 block w-full border-2 border-blue-600 rounded pl-2" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Batch Name</label>
                        <select name="batch_id" class="mt-1 block w-full border-2 border-blue-600 rounded">
                            @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ $student->batch_id == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->batch_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Course Name</label>
                        <select name="course_id" class="mt-1 block w-full border-2 border-blue-600 rounded">
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $student->course_id == $course->id ? 'selected' : '' }}>
                                    {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Batch Time</label>
                        <input type="time" name="batch_time" value="{{ old('batch_time', $student->batch_time) }}" class="mt-1 block w-full border-2 border-blue-600 rounded" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Admission Date</label>
                        <input type="date" name="admission_date" value="{{ old('admission_date', $student->admission_date) }}" class="mt-1 block w-full border-2 border-blue-600 rounded" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Total Fee</label>
                        <input type="number" name="total_fee" value="{{ old('total_fee', $student->total_fee) }}" step="0.01" class="mt-1 block w-full border-2 border-blue-600 rounded" required>
                    </div>
                
                    <div>
                        <label class="block text-sm font-medium">Due Amount</label>
                        <input type="number" name="due_amount" value="{{ old('due_amount', $student->due_amount) }}" step="0.01" class="mt-1 block w-full border-2 border-blue-600 rounded" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Payment Due Date</label>
                        <input type="date" name="payment_due_date" value="{{ old('payment_due_date', $student->payment_due_date) }}" class="mt-1 block w-full border-2 border-blue-600 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Comment</label>
                        <textarea name="description" rows="3" class="mt-1 block w-full border-2 border-blue-600 rounded">{{ old('description', $student->description) }}</textarea>
                    </div>
                    @if(auth()->user()->role === 'admin')
                    <div>
                        <label class="block text-sm font-medium">Payment Status</label>
                        <!-- <input type="text" name="payment_status" value="{{ old('payment_status', $student->payment_status) }}" class="mt-1 block w-full border-2 border-blue-600 rounded" required> -->
                        <select name="payment_status" class="mt-1 block w-full border-2 border-blue-600 rounded" required>
                            <option value="Paid" {{ $student->payment_status == 'Paid' ? 'selected' : '' }}>Paid</option>
                            <option value="Unpaid" {{ $student->payment_status == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Active Status</label>
                        <select name="active_status" class="mt-1 block w-full border-2 border-blue-600 rounded">
                            <option value="Running" {{ $student->active_status == 'Running' ? 'selected' : '' }}>Running</option>
                            <option value="Expired" {{ $student->active_status == 'Expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Approval Status</label>
                        <select name="approve_status" class="mt-1 block w-full border-2 border-blue-600 rounded">
                            <option value="pending" {{ $student->approve_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $student->approve_status == 'approved' ? 'selected' : '' }}>Approved</option>
                        </select>
                    </div>
                    @endif
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
