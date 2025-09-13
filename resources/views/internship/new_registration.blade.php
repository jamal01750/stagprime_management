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
                    <h2 class="text-lg md:text-xl font-semibold mb-2 md:mb-4">Create Batch and Course</h2>
                </div>
                <div class="w-4/5 flex">
                    <div class="w-full space-y-4">
                        <form method="post" action="{{ route('batch.create') }}" class="block">
                            @csrf
                            <input type="text" name="batch_name" placeholder="Type Batch Name" class="border-2 border-blue-600 rounded px-3 py-1 focus:border-blue-700 focus:ring-blue-700">
                            <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Create Batch</button>
                        </form>
                        <form method="post" action="{{ route('course.create') }}" class="block">
                            @csrf
                            <input type="text" name="course_name" placeholder="Type Course Name" class="border-2 border-blue-600 rounded px-3 py-1 focus:border-blue-700 focus:ring-blue-700">
                            <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Create Course</button>
                        </form>
                    </div> 
                </div>  
            </div>
        </div>
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

    </div>
</div>
@endsection