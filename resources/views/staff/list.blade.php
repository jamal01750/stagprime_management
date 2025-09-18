@extends('layouts.app')

@section('title', 'Staff Salary List | Stagprime Management')
@section('heading', 'Staff Salary List | Stagprime Management')

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-md" x-data="salaryManager()">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Staff Salary List</h2>
    </div>

    {{-- Success Message --}}
    <template x-if="successMessage">
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700 border border-green-300" 
             x-text="successMessage"></div>
    </template>

    <div class="overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-lg shadow-sm">
            <thead>
                <tr class="bg-gray-100 text-gray-700 text-sm uppercase">
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Category</th>
                    <th class="px-4 py-2 text-left">Monthly Salary</th>
                    <th class="px-4 py-2 text-left">Salary Date</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($salaries as $salary)
                    <tr class="hover:bg-gray-50 transition" id="row-{{ $salary->id }}">
                        <td class="px-4 py-2 font-medium text-gray-800">{{ $salary->staff->name }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $salary->staff->staffcategory->category ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-800">৳{{ number_format($salary->amount, 2) }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ \Carbon\Carbon::parse($salary->salary_date)->format('d M, Y') }}</td>
                        <td class="px-4 py-2">
                            <span id="status-{{ $salary->id }}" 
                                  class="px-2 py-1 text-xs font-semibold rounded 
                                  {{ $salary->status === 'Unpaid' ? 'text-red-600 bg-red-100' : 'text-green-600 bg-green-100' }}">
                                {{ $salary->status }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center">
                            @if($salary->status === 'Unpaid')
                                <div class="flex items-center gap-2 justify-center">
                                    <button 
                                        @click="openPayModal({ 
                                            id: {{ $salary->id }}, 
                                            name: '{{ $salary->staff->name }}', 
                                            amount: '{{ $salary->amount }}' 
                                        })" 
                                        class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                                        Pay Now
                                    </button>
                                    <button 
                                        @click="markPaid({{ $salary->id }})" 
                                        class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                                        Mark Paid
                                    </button>
                                </div>
                            @else
                                <button disabled class="px-3 py-1 text-sm bg-gray-300 text-gray-600 rounded cursor-not-allowed">
                                    Paid
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $salaries->links() }}
    </div>

    <!-- Pay Now Modal -->
    <div x-show="showModal" 
         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
         x-transition>
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md" @click.away="closeModal">
            <h3 class="text-lg font-semibold mb-4">Pay Salary</h3>

            <form @submit.prevent="submitPayment">
                <input type="hidden" x-model="form.id">

                <div class="mb-3">
                    <label class="block text-sm font-medium">Staff Name</label>
                    <input type="text" x-model="form.name" disabled class="w-full border rounded p-2 bg-gray-100">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Monthly Salary</label>
                    <input type="text" x-model="form.amount" disabled class="w-full border rounded p-2 bg-gray-100">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Payment Date</label>
                    <input type="date" x-model="form.payment_date" class="w-full border rounded p-2" required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Payment Method</label>
                    <select x-model="form.payment_method" class="w-full border rounded p-2">
                        <option value="Cash">Cash</option>
                        <option value="Bank">Bank</option>
                        <option value="Mobile Banking">Mobile Banking</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Note</label>
                    <textarea x-model="form.note" class="w-full border rounded p-2"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="closeModal" 
                        class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                        Pay Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function salaryManager() {
    return {
        showModal: false,
        successMessage: '',
        form: { id: '', name: '', amount: '', payment_date: '', payment_method: 'Cash', note: '' },

        openPayModal(data) {
            this.form = { ...this.form, ...data };
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.form = { id: '', name: '', amount: '', payment_date: '', payment_method: 'Cash', note: '' };
        },
        async submitPayment() {
            try {
                let response = await fetch(`/staff/salaries/pay/${this.form.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                let result = await response.json();
                if (result.success) {
                    let rowId = this.form.id;
                    document.getElementById(`status-${rowId}`).innerText = "Paid";
                    document.getElementById(`status-${rowId}`).className = "px-2 py-1 text-xs font-semibold text-green-600 bg-green-100 rounded";

                    document.querySelector(`#row-${rowId} td:last-child`).innerHTML = 
                        `<button disabled class="px-3 py-1 text-sm bg-gray-300 text-gray-600 rounded cursor-not-allowed">Paid</button>`;

                    this.successMessage = result.message;
                    this.closeModal();
                }
            } catch (error) {
                alert("Error processing payment!");
                console.error(error);
            }
        },
        async markPaid(id) {
            try {
                let response = await fetch(`/staff/salaries/mark-paid/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                let result = await response.json();
                if (result.success) {
                    document.getElementById(`status-${id}`).innerText = "Paid";
                    document.getElementById(`status-${id}`).className =
                        "px-2 py-1 text-xs font-semibold text-green-600 bg-green-100 rounded";

                    document.querySelector(`#row-${id} td:last-child`).innerHTML =
                        `<button disabled class="px-3 py-1 text-sm bg-gray-300 text-gray-600 rounded cursor-not-allowed">Paid</button>`;

                    this.successMessage = result.message;
                }
            } catch (error) {
                alert("Error marking salary as paid!");
                console.error(error);
            }
        }

    }
}
</script>
@endsection



