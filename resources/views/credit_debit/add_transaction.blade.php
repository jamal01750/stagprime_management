@extends('layouts.app')

@section('title', 'Add Transaction | Daily Credit & Debit')
@section('heading', 'Add Transaction | Daily Credit & Debit')

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-green-100 border border-green-300 text-green-800 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif

<div class="flex justify-center mb-10">
    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-2xl">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-3">Add Credit / Debit</h2>

        <form method="POST" action="{{ route('credit.debit.transaction.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" required
                    class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="type" value="credit" class="text-green-600" checked>
                        <span>Credit</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="type" value="debit" class="text-red-600">
                        <span>Debit</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                <input type="number" step="0.01" name="amount" required
                    class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                <textarea name="description" rows="3"
                    class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <button type="submit"
                class="w-full md:w-auto px-6 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 transition shadow-md font-medium">
                Submit
            </button>
        </form>
    </div>
</div>

<div class="bg-white shadow-lg rounded-2xl mt-6 p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Pending Credit/Debit Transactions</h3>
        <!-- <form action="{{ route('credit.debit.pending.download.pdf') }}" method="POST" target="_blank">
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
                    <th class="px-3 py-2 text-left">Date</th>
                    <th class="px-3 py-2 text-left">Credit (৳)</th>
                    <th class="px-3 py-2 text-left">Debit (৳)</th>
                    <th class="px-3 py-2 text-left">Comment</th>
                    <th class="px-3 py-2 text-left">Status</th>
                    <th class="px-3 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-3 py-2">{{ $transaction->date }}</td>
                    <td class="px-3 py-2 text-green-600">
                        {{ $transaction->type == 'credit' ? number_format($transaction->amount, 2) : '0.00' }}
                    </td>
                    <td class="px-3 py-2 text-red-600">
                        {{ $transaction->type == 'debit' ? number_format($transaction->amount, 2) : '0.00' }}
                    </td>
                    <td class="px-3 py-2">{{ $transaction->description ?? '-' }}</td>
                    <td class="px-3 py-2">{{ ucfirst($transaction->approve_status) }}</td>
                    <td class="px-3 py-2">
                        <div class="flex gap-2">
                            <a href="{{ route('credit.debit.transaction.edit', $transaction->id) }}"
                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs font-medium">
                                Edit
                            </a>
                            <form action="{{ route('credit.debit.transaction.destroy', $transaction->id) }}" method="POST"
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
                    <td class="px-3 py-2">Total</td>
                    <td class="px-3 py-2 text-green-600">{{ $totalCredits }}</td>
                    <td class="px-3 py-2 text-red-600">{{ $totalDebits }}</td>
                    <td class="px-3 py-2 text-blue-600">Balance: {{ $balance }}</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
