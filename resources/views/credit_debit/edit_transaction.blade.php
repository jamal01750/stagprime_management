@extends('layouts.app')

@section('title', 'Edit Transaction')
@section('heading', 'Edit Transaction')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-semibold mb-6 border-b pb-3 text-gray-800">Edit Transaction</h2>

    <form method="POST" action="{{ route('credit.debit.transaction.update', $transaction->id) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
            <input type="date" name="date" value="{{ $transaction->date }}" required
                class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2">
                    <input type="radio" name="type" value="credit" {{ $transaction->type == 'credit' ? 'checked' : '' }}>
                    <span>Credit</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="type" value="debit" {{ $transaction->type == 'debit' ? 'checked' : '' }}>
                    <span>Debit</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
            <input type="number" step="0.01" name="amount" value="{{ $transaction->amount }}" required
                class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
            <textarea name="description" rows="3"
                class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-2 focus:ring-blue-500">{{ $transaction->description }}</textarea>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Update</button>
            <a href="{{ route('credit.debit.report') }}"
                class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">Cancel</a>
        </div>
    </form>
</div>
@endsection
