@extends('layouts.app')

@section('title', 'Add Transaction | Daily Credit & Debit')
@section('heading', 'Add Transaction | Daily Credit & Debit')

@section('content')
@if(session('success'))
    <div class="w-full mb-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    </div>
@endif 
<div class="w-full flex justify-center">
    <div class="bg-white shadow-lg rounded-2xl p-6 md:p-8 w-full max-w-2xl">
        <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6 border-b pb-3">
            Add Credit / Debit
        </h2>

        <!-- The form starts here -->
        <form method="POST" action="{{ route('credit.debit.transaction.store') }}" class="space-y-5">
            @csrf

            <!-- Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" 
                    class="w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           text-gray-900" required>
            </div>

            <!-- Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <div class="flex items-center space-x-6">
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="type" value="credit" 
                            class="text-green-600 focus:ring-green-500" checked>
                        <span class="text-gray-700">Credit</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="type" value="debit" 
                            class="text-red-600 focus:ring-red-500">
                        <span class="text-gray-700">Debit</span>
                    </label>
                </div>
            </div>

            <!-- Amount -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                <input type="number" name="amount" step="0.01" 
                    class="w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           text-gray-900" required>
            </div>

            <!-- Comment -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                <textarea name="description" rows="3" 
                    class="w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           text-gray-900"></textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" 
                    class="w-full md:w-auto px-6 py-2.5 bg-green-600 text-white rounded-xl 
                           hover:bg-green-700 transition font-medium shadow-md">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
