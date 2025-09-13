@extends('layouts.app')

@section('title', 'Add Loan | Loan and Installment')
@section('heading', 'Add Loan | Loan and Installment')

@section('content')
<div x-data="{ openId: null }">
    <div class="w-full flex flex-col space-y-6 md:space-y-8">

        {{-- Success Message --}}
        @if(session('success'))
            <div class="w-full mb-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif 

        {{-- Loan Form --}}
        <div class="bg-white rounded shadow p-4 md:p-6 flex flex-col md:flex-row items-start">
            <div class="w-full flex flex-row">
                <div class="w-1/5 flex flex-col flex-shrink-0 pr-4">
                    <h2 class="text-lg md:text-xl font-semibold mb-2 md:mb-4">Add Loan</h2>
                </div>
                <div class="w-4/5">
                    <form method="POST" action="{{ route('loan.store') }}" class="space-y-3 md:space-y-4 w-full md:w-auto">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Loan Name</label>
                            <input type="text" name="loan_name" required class="mt-1 block w-full border-2 border-blue-600 rounded p-1">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Loan Amount</label>
                            <input type="number" name="loan_amount" step="0.01" required class="mt-1 block w-full border-2 border-blue-600 rounded p-1">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Installment</label>
                            <div class="flex space-x-2">
                                <input type="number" name="installment_number" required class="mt-1 block w-full border-2 border-blue-600 rounded p-1">
                                <select name="installment_type" class="mt-1 block rounded border-2 border-blue-600 bg-white text-gray-900">
                                    <option value="month">Month</option>
                                    <option value="week">Week</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Installment Amount</label>
                            <input type="number" step="0.01" name="installment_amount" required class="mt-1 block w-full border-2 border-blue-600 rounded p-1">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Due Amount</label>
                            <input type="number" name="due_amount" required class="mt-1 block w-full border-2 border-blue-600 rounded p-1">
                        </div>

                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Submit</button>
                    </form>
                </div>
            </div>
        </div>

        
    </div>
</div>
@endsection
 
