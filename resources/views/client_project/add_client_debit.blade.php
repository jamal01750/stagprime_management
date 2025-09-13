@extends('layouts.app')

@section('title', 'Add Client Debit | Client Project')
@section('heading', 'Client Debit Entry | Client Project')

@section('content')
<div class="w-full flex flex-col space-y-6 md:space-y-8">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded shadow p-4 md:p-6">
        <h2 class="text-lg md:text-xl font-semibold mb-4">Add Client Debit</h2>
        <form method="POST" action="{{ route('client.debit.store') }}" class="space-y-4" id="debitForm">
            @csrf

            {{-- Project Dropdown --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Project Name</label>
                <select name="project_id" id="projectSelect" class="w-full border-2 border-blue-600 rounded px-3 py-1" required>
                    <option value="">-- Select Project --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}"
                            data-currency="{{ $project->currency }}"
                            data-contract="{{ $project->contract_amount }}"
                            data-advance="{{ $project->advance_amount }}">
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Currency --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Transaction Currency</label>
                <input type="text" name="currency" id="currencyField" class="w-full border-2 border-blue-600 rounded px-3 py-1 bg-gray-100" readonly required>
            </div>

            {{-- Pay Amount --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Pay Amount</label>
                <input type="number" name="pay_amount" id="payAmount" step="0.01" class="w-full border-2 border-blue-600 rounded px-3 py-1" required>
            </div>

            {{-- Due Amount --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Due Amount</label>
                <input type="number" name="due_amount" id="dueAmount" step="0.01" class="w-full border-2 border-blue-600 rounded px-3 py-1 bg-gray-100" readonly required>
            </div>

            {{-- Pay Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Pay Date</label>
                <input type="date" name="pay_date" class="w-full border-2 border-blue-600 rounded px-3 py-1" required>
            </div>

            {{-- Next Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Next Date</label>
                <input type="date" name="next_date" class="w-full border-2 border-blue-600 rounded px-3 py-1">
            </div>

            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">
                Save Debit
            </button>
        </form>
    </div>
</div>

{{-- JavaScript for Dynamic Calculation --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const projectSelect = document.getElementById("projectSelect");
    const currencyField = document.getElementById("currencyField");
    const payAmount = document.getElementById("payAmount");
    const dueAmount = document.getElementById("dueAmount");

    let baseDue = 0;

    projectSelect.addEventListener("change", function () {
        const selected = this.options[this.selectedIndex];
        const currency = selected.getAttribute("data-currency");
        const contract = parseFloat(selected.getAttribute("data-contract")) || 0;
        const advance = parseFloat(selected.getAttribute("data-advance")) || 0;

        baseDue = contract - advance;

        currencyField.value = currency;
        dueAmount.value = baseDue;
        payAmount.value = "";
    });

    payAmount.addEventListener("input", function () {
        const pay = parseFloat(this.value) || 0;
        dueAmount.value = (baseDue - pay).toFixed(2);
    });
});
</script>
@endsection
