@extends('layouts.app')

@section('title', 'Credit & Debit Report')
@section('heading', 'Credit & Debit Report')

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-green-100 border border-green-300 text-green-800 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif
<div class="w-full flex flex-col space-y-6">

    <!-- 🔹 Filter Form -->
    <div class="bg-white rounded shadow p-4">
        <form method="GET" action="{{ route('credit.debit.report') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="border rounded px-3 py-1">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="border rounded px-3 py-1">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="border rounded px-3 py-1">
                    <option value="">All</option>
                    <option value="pending" {{ $selected_status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $selected_status == 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">
                Filter
            </button>
            @if(request()->filled('start_date') || request()->filled('end_date') || request()->filled('status'))
                <a href="{{ route('credit.debit.report') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 font-medium">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- 🔹 Transactions Table -->
    <div class="bg-white shadow rounded p-4">

        <!-- Separate form ONLY for PDF download -->
        <form action="{{ route('credit.debit.download.pdf') }}" method="POST" target="_blank" id="downloadForm">
            @csrf
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-lg font-semibold text-gray-800">Transactions</h3>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Download PDF
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th id="toggleCheck" class="px-3 py-2 cursor-pointer">Mark All</th>
                            <th class="px-3 py-2 text-left">Date</th>
                            <th class="px-3 py-2 text-left">Credit (৳)</th>
                            <th class="px-3 py-2 text-left">Debit (৳)</th>
                            <th class="px-3 py-2 text-left">Comment</th>
                            <th class="px-3 py-2 text-left">Status</th>
                            <th class="px-3 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <input type="checkbox" name="transaction_ids[]" value="{{ $t->id }}" class="transaction-checkbox">
                                </td>
                                <td class="px-3 py-2">{{ $t->date }}</td>
                                <td class="px-3 py-2 text-green-600">{{ $t->type == 'credit' ? number_format($t->amount, 2) : '0.00' }}</td>
                                <td class="px-3 py-2 text-red-600">{{ $t->type == 'debit' ? number_format($t->amount, 2) : '0.00' }}</td>
                                <td class="px-3 py-2">{{ $t->description ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    <select data-id="{{ $t->id }}" class="status-dropdown border rounded px-2 py-1">
                                        <option value="pending" {{ $t->approve_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $t->approve_status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    </select>
                                    @if(auth()->user()->role === 'admin')
                                        <button type="button" data-id="{{ $t->id }}"
                                            class="update-status-btn mt-1 px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                            Update
                                        </button>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex gap-2">
                                    @if(auth()->user()->role === 'admin')
                                        <a href="{{ route('credit.debit.transaction.edit', $t->id) }}"
                                            class="px-3 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600">Edit</a>

                                        <!-- Delete button will trigger standalone form via JS -->
                                        <button type="button" data-id="{{ $t->id }}"
                                            class="delete-btn px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                                            Delete
                                        </button>
                                    @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-gray-500">No transactions found.</td></tr>
                        @endforelse

                        <tr class="bg-gray-50 font-semibold border-t">
                            <td></td>
                            <td>Total</td>
                            <td class="text-green-600">{{ $totalCredits }}</td>
                            <td class="text-red-600">{{ $totalDebits }}</td>
                            <td class="text-blue-600">Balance: {{ $balance }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>

        <div class="mt-3">{{ $transactions->links() }}</div>
    </div>
</div>

<!-- Standalone Delete Form -->
<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<!-- 🔹 JS Section -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleCheckBtn = document.getElementById('toggleCheck');
    const checkboxes = document.querySelectorAll('.transaction-checkbox');
    let allChecked = false;

    toggleCheckBtn.addEventListener('click', () => {
        allChecked = !allChecked;
        checkboxes.forEach(cb => cb.checked = allChecked);
        toggleCheckBtn.textContent = allChecked ? 'Unmark All' : 'Mark All';
    });

    // ✅ AJAX Update Status
    document.querySelectorAll('.update-status-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const select = document.querySelector(`.status-dropdown[data-id="${id}"]`);
            const status = select.value;

            fetch("{{ route('credit.debit.status.update') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ id, status }),
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
            })
            .catch(err => alert("Error updating status!"));
        });
    });

    // ✅ Delete Button Handler
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            if (confirm('Are you sure! you want to Delete this transaction?')) {
                const id = this.dataset.id;
                const form = document.getElementById('deleteForm');
                form.action = `/credit-debit/${id}/delete`;
                form.submit();
            }
        });
    });
});
</script>
@endsection
