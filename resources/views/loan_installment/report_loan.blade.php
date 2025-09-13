@extends('layouts.app')

@section('title', 'Report | Loan and Installment')
@section('heading', 'Report | Loan and Installment')

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

        {{-- Loan List --}}
        <div class="bg-white rounded shadow p-4 md:p-6 mt-6 overflow-x-auto">
            <h2 class="text-lg font-semibold mb-4">Loan List</h2>
            <table class="w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">Loan Name</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Total Loan Amount</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Total Installments</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Installment Amount</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Due Amount</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $ln)
                        <tr>
                            <td class="border px-4 py-2">{{ $ln->loan_name }}</td>
                            <td class="border px-4 py-2">{{ number_format($ln->loan_amount, 2) }}</td>
                            <td class="border px-4 py-2">{{ $ln->installment_number }} {{ $ln->installment_type }}</td>
                            <td class="border px-4 py-2">{{ number_format($ln->installment_amount, 2) }}</td>
                            <td class="border px-4 py-2">{{ number_format($ln->due_amount, 2) }}</td>
                            <td class="border px-4 py-2">
                                <a href="{{ route('loan.installments', $ln->id) }}" class="text-blue-600 hover:underline">
                                    View Installments
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="border px-4 py-2 text-center text-gray-500">No Loans Record found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Loan Installments -->
        @if(isset($installments))
            <div class="bg-white rounded shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Installments of {{ $loan->loan_name }}</h2>
                <table class="w-full border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 border">#</th>
                            <th class="px-4 py-2 border">Installment Amount</th>
                            <th class="px-4 py-2 border">Due Amount</th>
                            <th class="px-4 py-2 border">Pay Date</th>
                            <th class="px-4 py-2 border">Next Date</th>
                            <th class="px-4 py-2 border">Status</th>
                            <th class="px-4 py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($installments as $i => $inst)
                        <tr>
                            <td class="border px-4 py-2">{{ $i+1 }}</td>
                            <td class="border px-4 py-2">{{ number_format($inst->installment_amount,2) }}</td>
                            <td class="border px-4 py-2">{{ number_format($inst->due_amount,2) }}</td>
                            <td class="border px-4 py-2">{{ $inst->pay_date }}</td>
                            <td class="border px-4 py-2">{{ $inst->next_date }}</td>
                            <td class="border px-4 py-2">
                                <select class="status-dropdown border rounded px-2 py-1 text-white"
                                    data-id="{{ $inst->id }}">
                                    <option value="unpaid" class="bg-red-600 text-white"
                                        {{ $inst->status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="paid" class="bg-green-600 text-white"
                                        {{ $inst->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </td>
                            <td class="border px-4 py-2 text-center">
                                <button type="button"
                                    class="update-status px-3 py-1 rounded text-white bg-green-600 hover:bg-green-700"
                                    data-id="{{ $inst->id }}">
                                    Update Status
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No installments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function applyDropdownColor(dropdown) {
            if (dropdown.value === 'paid') {
                dropdown.classList.remove('bg-red-600');
                dropdown.classList.add('bg-green-600');
            } else {
                dropdown.classList.remove('bg-green-600');
                dropdown.classList.add('bg-red-600');
            }
        }

        // Initialize colors on load
        document.querySelectorAll('.status-dropdown').forEach(dd => {
            applyDropdownColor(dd);
        });

        // Handle button click
        document.querySelectorAll('.update-status').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id;
                let dropdown = document.querySelector(`.status-dropdown[data-id="${id}"]`);

                fetch(`/installments/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        dropdown.value = data.status;
                        applyDropdownColor(dropdown);
                    }
                })
                .catch(err => console.error(err));
            });
        });
    });
</script>


@endsection
