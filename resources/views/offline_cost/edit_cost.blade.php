@extends('layouts.app')

@section('title', 'Edit Offline Cost')
@section('heading', 'Edit Offline Cost')

@section('content')
<div class="w-full flex flex-col space-y-6 md:space-y-8">

    @if(session('success'))
        <div class="w-full mb-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif 

    <div class="bg-white rounded shadow p-4 md:p-6">
        <h2 class="text-lg md:text-xl font-semibold mb-4">Update Expense Details</h2>

        <form method="POST" action="{{ route('offline.cost.transaction.update', $transaction->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Select Year</label>
                <select name="year" class="border rounded px-2 py-1">
                    @for($y = now('Asia/Dhaka')->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" @selected($y == $transaction->year)>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Select Month</label>
                <select name="month" class="border rounded px-2 py-1">
                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $monthName)
                        <option value="{{ $index + 1 }}" @selected($index + 1 == $transaction->month)>{{ $monthName }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category_id" class="mt-1 block w-full border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-900">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected($category->id == $transaction->category_id)>
                            {{ $category->category }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Amount</label>
                <input type="number" name="amount" step="0.01"
                       value="{{ $transaction->amount }}"
                       class="mt-1 block w-full rounded border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 pl-[5px]" required>
                @error('amount')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Last Payment Date</label>
                <input type="date" name="last_date"
                       value="{{ $transaction->last_date }}"
                       class="mt-1 block w-full border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-900">
                @error('last_date')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Note</label>
                <textarea name="description" rows="3" class="mt-1 block w-full border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-900 pl-[5px]">{{ $transaction->description }}</textarea>
                @error('description')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>

            @if(auth()->user()->role === 'admin')
                <div>
                    <label class="block text-sm font-medium">Approval Status</label>
                    <select name="approve_status" class="mt-1 block w-full border-2 border-blue-600 rounded">
                        <option value="pending" {{ $transaction->approve_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $transaction->approve_status == 'approved' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>
            @endif

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">
                    Update
                </button>
                <a href="{{ route('offline.cost.report') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
