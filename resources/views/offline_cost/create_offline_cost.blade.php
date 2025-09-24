@extends('layouts.app')

@section('title', 'Add Cost | Office Offline Cost')
@section('heading', 'Add Cost | Office Offline Cost')

@section('content')
    <div class="w-full flex flex-col space-y-6 md:space-y-8">

        @if(session('success'))
            <div class="w-full mb-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif 
        <!-- Category Creation -->
        <div class="w-full flex flex-row">
            <div class="w-1/5 flex flex-col flex-shrink-0 pr-4">
                <h2 class="text-lg md:text-xl font-semibold mb-2 md:mb-4">Create Category</h2>
            </div>
            <div class="w-4/5 flex">
                <form method="post" action="{{ route('offline.category.create') }}" class="block">
                    @csrf
                    <input type="text" name="category" placeholder="Type Category Name" class="border-2 border-blue-600 rounded px-3 py-1 focus:border-blue-700 focus:ring-blue-700" required>
                    <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Create Category</button>
                </form>
            </div>  
        </div>


        <div class="bg-white rounded shadow p-4 md:p-6 flex flex-col md:flex-row items-start">
                <div class="w-full flex flex-row">
                <div class="w-1/5 flex flex-col flex-shrink-0 pr-4">
                    <h2 class="text-lg md:text-xl font-semibold mb-2 md:mb-4">Set Monthly Expense</h2>
                </div>
                <div class="w-4/5">
                    <!-- The form starts here -->
                    <form method="POST" action="{{ route('offline.cost.store') }}" class="space-y-3 md:space-y-4 w-full md:w-auto">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Select Year</label>
                            <select name="year" class="border rounded px-2 py-1">
                            @for($y = now('Asia/Dhaka')->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                            @endfor
                            </select>  
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Select Month</label>
                            <select name="month" class="border rounded px-2 py-1">
                                @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $monthName)
                                    <option value="{{ $index + 1 }}" @selected($index + 1 == $month)>{{ $monthName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <div class="flex space-x-4 mt-1">
                                <select name="category_id" class="mt-1 block w-full border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-900">
                                    <option>Select Category</option>    
                                    @foreach($categories as $category)    
                                        <option  value="{{$category -> id}}">{{$category -> category}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount</label>
                            <div class="flex space-x-2">
                                <input type="number" name="amount" step="0.01" class="mt-1 block w-full rounded bg-white text-gray-900 border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 pl-[5px]" required>
                            </div>
                            @if ($errors->has('amount'))
                            <span class="text-red-600 text-xs">{{ $errors->first('amount') }}</span>
                            @endif
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Payment Date</label>
                            <input type="date" name="last_date" class="mt-1 block w-full border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-900">
                                @if ($errors->has('last_date'))
                                <span class="text-red-600 text-xs">{{ $errors->first('last_date') }}</span>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Note</label>
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
@endsection