@extends('layouts.app')

@section('title', 'Add Installment | Loan and Installment')
@section('heading', 'Add Installment | Loan and Installment')

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

        {{-- Installment Form --}}
        <div class="bg-white rounded shadow p-4 md:p-6 flex flex-col md:flex-row items-start">
            <div class="w-full flex flex-row">
                <div class="w-1/5 flex flex-col flex-shrink-0 pr-4">
                    <h2 class="text-lg md:text-xl font-semibold mb-2 md:mb-4">Add Installment</h2>
                </div>
                <div class="w-4/5">
                    <form method="POST" action="{{ route('installment.store') }}" x-data="installmentForm()">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Loan Name</label>
                            <select name="loan_id" x-model="selectedLoan" @change="updateLoanData" class="mt-1 block w-full border-2 border-blue-600 rounded p-1">
                                <option value="">-- Select Loan --</option>
                                @foreach($loans as $loan)
                                    <option value="{{ $loan->id }}"
                                        data-amount="{{ $loan->installment_amount }}"
                                        data-due="{{ $loan->due_amount - $loan->installment_amount }}">
                                        {{ $loan->loan_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Installment Amount</label>
                            <input type="number" step="0.01" name="installment_amount" x-model="installmentAmount"
                                readonly class="mt-1 block w-full border-2 border-blue-600 rounded p-1">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Due Amount</label>
                            <input type="number" step="0.01" name="due_amount" x-model="dueAmount"
                                readonly class="mt-1 block w-full border-2 border-blue-600 rounded p-1">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Pay Date</label>
                            <input type="date" name="pay_date" required class="mt-1 block w-full border-2 border-blue-600 rounded p-1">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Next Date</label>
                            <input type="date" name="next_date" class="mt-1 block w-full border-2 border-blue-600 rounded p-1">
                        </div>

                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Submit</button>
                    </form>
                </div>
            </div>
        </div>

        
    </div>
</div>

<script>
    function installmentForm() {
        return {
            selectedLoan: '',
            installmentAmount: '',
            dueAmount: '',
            updateLoanData(event) {
                const option = event.target.options[event.target.selectedIndex];
                this.installmentAmount = option.getAttribute('data-amount');
                this.dueAmount = option.getAttribute('data-due');
            }
        }
    }
</script>
@endsection

