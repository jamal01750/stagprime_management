@extends('layouts.app')

@section('title', 'Add Intern | Internship Registration')
@section('heading', 'Add Intern | Internship Registration')

@section('content')
<div x-data="{ openId: null }">
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
                    <h2 class="text-lg md:text-xl font-semibold mb-2 md:mb-4">New Internship Registration</h2>
                </div>
                <div class="w-4/5">
                    <!-- The form starts here -->
                    <form method="POST" action="{{ route('internship.registration.store') }}" enctype="multipart/form-data" class="space-y-3 md:space-y-4 w-full md:w-auto">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium">Upload Image</label>
                            <input type="file" name="image" accept="image/*" class="w-full border rounded p-2">
                            @error('image') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="internee_name" class="mt-1 block w-full border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-900 pl-[5px]" required>
                            @if ($errors->has('internee_name'))
                                <span class="text-red-600 text-xs">{{ $errors->first('internee_name') }}</span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <div class="flex space-x-2">
                                <input type="number" name="phone" step="0.01" class="mt-1 block w-full rounded bg-white text-gray-900 border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 pl-[5px]" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alternative Phone Number</label>
                            <div class="flex space-x-2">
                                <input type="number" name="alt_Phone" step="0.01" class="mt-1 block w-full rounded bg-white text-gray-900 border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 pl-[5px]">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NID / Birth Registration Number</label>
                            <div class="flex space-x-2">
                                <input type="number" name="nid_birth" step="0.01" class="mt-1 block w-full rounded bg-white text-gray-900 border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 pl-[5px]" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" name="address" class="mt-1 block w-full border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-900 pl-[5px]" required>
                            @if ($errors->has('address'))
                                <span class="text-red-600 text-xs">{{ $errors->first('address') }}</span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Batch Name</label>
                            <div class="flex space-x-4 mt-1">
                                <select name="batch_id" class="mt-1 block w-full border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-900">
                                    <option>Select Batch</option>    
                                    @foreach($batches as $batch)    
                                        <option  value="{{$batch -> id}}">{{$batch -> batch_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Course Name</label>
                            <div class="flex space-x-4 mt-1">
                                <select name="course_id" class="mt-1 block w-full border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-900">
                                    <option>Select Course</option>    
                                    @foreach($courses as $course)    
                                        <option  value="{{$course -> id}}">{{$course -> course_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Batch Time</label>
                            <div class="flex space-x-4 mt-1">
                                <input type="time" name="batch_time" class="mt-1 block w-full border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-900" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Admission Date</label>
                            <input type="date" name="admission_date" class="mt-1 block w-full border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-900" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pay/Contract Amount</label>
                            <div class="flex space-x-2">
                                <input type="number" name="pay_amount" step="0.01" class="mt-1 block w-full rounded bg-white text-gray-900 border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 pl-[5px]" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Comment</label>
                            <textarea name="description" rows="3" class="mt-1 block w-full border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-900 pl-[5px]"></textarea>
                            @if ($errors->has('description'))
                                <span class="text-red-600 text-xs">{{ $errors->first('description') }}</span>
                            @endif
                        </div>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Submit</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white rounded shadow p-4 md:p-6 mt-6 overflow-x-auto">
            <h2 class="text-lg md:text-xl font-semibold mb-4">Pending Approval Interns List</h2>
            <table class="min-w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-3 py-2">Intern ID</th>
                        <th class="border border-gray-300 px-3 py-2">Name</th>
                        <th class="border border-gray-300 px-3 py-2">Contract Amount</th>
                        <th class="border border-gray-300 px-3 py-2">Total Paid</th>
                        <th class="border border-gray-300 px-3 py-2">Active Status</th>
                        <th class="border border-gray-300 px-3 py-2">Approve Status</th>
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
                        <td class="border px-3 py-2">{{ $student->active_status }}</td>
                        <td class="border px-3 py-2">{{ $student->approve_status }}</td>
                        <td class="border px-3 py-2 flex space-x-2">
                            <a href="{{ route('internship.individual', $student->id) }}" 
                            class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-medium">
                            View
                            </a>
                            <a href="{{ route('internship.edit', $student->id) }}" 
                            class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs font-medium">
                            Edit
                            </a>
                            <form action="{{ route('internship.delete', $student->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this Intern?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium">
                                    Delete
                                </button>
                            </form>
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
        <div class="mt-4">
            {{ $students->links() }}
        </div>

    </div>
</div>
@endsection