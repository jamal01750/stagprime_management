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

        <div class="bg-white shadow-lg rounded-2xl mt-6 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Pending Approval Offline Cost</h3>
                <!-- <form action="{{ route('offline.cost.download.pdf') }}" method="POST" target="_blank">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        Download PDF
                    </button>
                </form> -->
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-3 py-2 text-left">Month / Year</th>
                            <th class="px-3 py-2 text-left">Category</th>
                            <th class="px-3 py-2 text-left">Amount (৳)</th>
                            <th class="px-3 py-2 text-left">Last Payment Date</th>
                            <th class="px-3 py-2 text-left">Note</th>
                            <th class="px-3 py-2 text-left">Payment Status</th>
                            <th class="px-3 py-2 text-left">Approval Status</th>
                            <th class="px-3 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $transaction->month }}/{{ $transaction->year }}</td>
                            <td class="px-3 py-2">
                                {{ $transaction->category->category ?? 'N/A' }}
                            </td>
                            <td class="px-3 py-2 text-red-600">
                                {{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="px-3 py-2">{{ $transaction->last_date }}</td>
                            <td class="px-3 py-2">{{ $transaction->description }}</td>
                            <td class="px-3 py-2">{{ $transaction->status }}</td>
                            <td class="px-3 py-2">{{ ucfirst($transaction->approve_status) }}</td>
                            <td class="px-3 py-2">
                                <div class="flex gap-2">
                                    <a href="{{ route('offline.cost.transaction.edit', $transaction->id) }}"
                                        class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs font-medium">
                                        Edit
                                    </a>
                                    <form action="{{ route('offline.cost.transaction.destroy', $transaction->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this transaction?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-50 font-semibold border-t">
                            <td class="px-3 py-2"></td>
                            <td class="px-3 py-2">Total</td>
                            <td class="px-3 py-2 text-blue-600">{{ $total }}</td>
                            <td class="px-3 py-2 text-red-600"></td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection