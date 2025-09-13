@extends('layouts.app')

@section('title', 'Monthly Revenue and Target | StagPrime Cost Management')
@section('heading', 'Monthly Revenue and Target')

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
                    <h2 class="text-lg md:text-xl font-semibold mb-2 md:mb-4">Set Monthly Target</h2>
        
                </div>
                <div class="w-4/5">
                    <!-- The form starts here -->
                    <form method="POST" action="{{ route('settarget') }}" class="space-y-3 md:space-y-4 w-full md:w-auto">
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
                            <label class="block text-sm font-medium text-gray-700">Target Amount</label>
                            <div class="flex space-x-2">
                                <input type="number" name="amount" step="0.01" class="mt-1 block w/3 rounded bg-white text-gray-900 border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 pl-[5px]" required>
                            </div>
                            @if ($errors->has('amount'))
                                <span class="text-red-600 text-xs">{{ $errors->first('amount') }}</span>
                            @endif
                        </div>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Submit</button>
                    </form>
                </div>
            </div>
        </div>
        

        
    </div>
    <div class="max-w-7xl mx-auto p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl md:text-2xl font-semibold">Monthly Revenue and Target — {{ $year }}</h1>

            <form method="get" action="{{ route('yearlysummary') }}" class="flex items-center gap-2">
                <select name="year" class="border rounded px-2 py-1">
                    @for($y = now('Asia/Dhaka')->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endfor
                </select>
                <button class="px-3 py-1 rounded bg-indigo-600 text-white">Go</button>
            </form>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4 md:p-6 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left border-b">
                    <tr>
                        <th class="py-2">Month</th>
                        <th class="py-2">Target</th>
                        <th class="py-2">Revenue</th>
                        <th class="py-2">Difference</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($labels as $i => $m)
                        <tr class="border-b last:border-0">
                            <td class="py-2">{{ $m }}</td>
                            <td class="py-2">{{ number_format($target[$i], 2) }}</td>
                            <td class="py-2">{{ number_format($mbalance[$i], 2) }}</td>
                            <td class="py-2">{{ number_format($difference[$i], 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-100 font-semibold border-t">
                        <td class="py-2">Total ({{ $year }})</td>
                        <td class="py-2">{{ $totaltarget }}</td>
                        <td class="py-2">{{ $balance }}</td>
                        <td class="py-2">{{ $totaldifference }}</td>
                        
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection




      