@extends('layouts.app')

@section('title', 'Project Details | Client Project')
@section('heading', 'Project Details | Client Project')

@section('content')
<div x-data="mainHandler()" class="w-full flex flex-col space-y-6 md:space-y-8">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="w-full mb-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Project List --}}
    <div class="bg-white rounded shadow p-4 md:p-6 overflow-x-auto">
        <h2 class="text-lg md:text-xl font-semibold mb-4">Client Projects List</h2>
        <table class="min-w-full bg-white border text-left">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-2 border-b">Project Name</th>
                    <th class="py-2 px-2 border-b">Start Date</th>
                    <th class="py-2 px-2 border-b">End Date</th>
                    <th class="py-2 px-2 border-b">Contract Amount</th>
                    <th class="py-2 px-2 border-b">Advance Amount</th>
                    <th class="py-2 px-2 border-b">Total Paid</th>
                    <th class="py-2 px-2 border-b">Due Amount</th>
                    <th class="py-2 px-2 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $project)
                    <tr>
                        <td class="py-2 px-2 border-b">{{ $project->project_name }}</td>
                        <td class="py-2 px-2 border-b">{{ $project->start_date }}</td>
                        <td class="py-2 px-2 border-b">{{ $project->end_date }}</td>
                        <td class="py-2 px-2 border-b">{{ $project->currency }} {{ $project->contract_amount }}</td>
                        <td class="py-2 px-2 border-b">{{ $project->currency }} {{ $project->advance_amount }}</td>
                        <td class="py-2 px-2 border-b">{{ $project->currency }} {{ $project->paid_amount }}</td>
                        <td class="py-2 px-2 border-b">{{ $project->currency }} {{ $project->due_amount }}</td>
                        <td class="py-2 px-4 border-b space-x-2">
                            {{-- View Debits --}}
                            <button
                                class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 font-medium"
                                @click="showDebits({{ $project->id }})">
                                View Debits
                            </button>

                            {{-- View Transactions --}}
                            <button
                                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium"
                                @click="showTransactions({{ $project->id }})">
                                View Transactions
                            </button>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('client.project.delete', ['id' => $project->id]) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this project?')">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Client Debits Section --}}
    <div x-show="activeTab === 'debits'" class="bg-white rounded shadow p-4 md:p-6 mt-6" x-cloak>
        <h2 class="text-lg md:text-xl font-semibold mb-4">
            Client Debits for <span x-text="projectName"></span>
        </h2>
        <template x-if="debits.length > 0">
        <table class="min-w-full bg-white border text-left">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 border">#</th>
                    <th class="px-4 py-2 border">Pay Amount</th>
                    <th class="px-4 py-2 border">Due Amount</th>
                    <th class="px-4 py-2 border">Pay Date</th>
                    <th class="px-4 py-2 border">Next Date</th>
                    <th class="px-4 py-2 border">Status</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(debit, i) in debits" :key="debit.id">
                    <tr>
                        <td class="border px-4 py-2" x-text="i+1"></td>
                        <td class="border px-4 py-2" x-text="debit.pay_amount"></td>
                        <td class="border px-4 py-2" x-text="debit.due_amount"></td>
                        <td class="border px-4 py-2" x-text="debit.pay_date"></td>
                        <td class="border px-4 py-2" x-text="debit.next_date ?? '-'"></td>
                        <td class="border px-4 py-2">
                            <select
                                class="status-dropdown border rounded px-2 py-1"
                                :class="debit.status === 'paid' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
                                x-model="debit.status"
                            >
                                <option value="unpaid" class="bg-red-600 text-white">Unpaid</option>
                                <option value="paid" class="bg-green-600 text-white">Paid</option>
                            </select>
                        </td>
                        <td class="border px-4 py-2 text-center">
                            <button type="button"
                                class="update-status px-3 py-1 rounded text-white bg-green-600 hover:bg-green-700"
                                @click="updateStatus(debit)">
                                Update Status
                            </button>
                        </td>
                    </tr>
                </template>
                <tr class="font-semibold bg-gray-100">
                    <td class="py-2 px-4 border-b">Total</td>
                    <td class="py-2 px-4 border-b" x-text="totalDebit"></td>
                    <td colspan="5"></td>
                </tr>
            </tbody>
        </table>
        </template>
        <template x-if="debits.length === 0">
            <div class="text-gray-500">No debits found for this project.</div>
        </template>
    </div>

    {{-- Transactions Section --}}
    <div x-show="activeTab === 'transactions'" class="bg-white rounded shadow p-4 md:p-6" x-cloak>
        <h2 class="text-lg md:text-xl font-semibold mb-4">
            Transactions of <span x-text="projectName"></span>
        </h2>
        <div>
            {{-- Filter --}}
            <div class="flex flex-col md:flex-row md:space-x-4 mb-4">
                <div>
                    <label class="block text-sm">Start Date</label>
                    <input type="date" x-model="filters.startDate" class="border rounded px-2 py-1">
                </div>
                <div>
                    <label class="block text-sm">End Date</label>
                    <input type="date" x-model="filters.endDate" class="border rounded px-2 py-1">
                </div>
                <div>
                    <label class="block text-sm">Type</label>
                    <select x-model="filters.type" class="border rounded px-2 py-1">
                        <option value="all">All</option>
                        <option value="invest">Invest</option>
                        <option value="profit">Profit</option>
                        <option value="loss">Loss</option>
                    </select>
                </div>
            </div>

            {{-- Transactions Table --}}
            <template x-if="transactions.length > 0">
            <table class="min-w-full bg-white border text-left">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Date</th>
                        <th class="py-2 px-4 border-b">Invest</th>
                        <th class="py-2 px-4 border-b">Profit</th>
                        <th class="py-2 px-4 border-b">Loss</th>
                        <th class="py-2 px-4 border-b">Comment</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="transaction in filteredTransactions" :key="transaction.id">
                        <tr>
                            <td class="py-2 px-4 border-b" x-text="transaction.date"></td>
                            <td class="py-2 px-4 border-b" x-text="transaction.type === 'invest' ? transaction.amount : '-'"></td>
                            <td class="py-2 px-4 border-b" x-text="transaction.type === 'profit' ? transaction.amount : '-'"></td>
                            <td class="py-2 px-4 border-b" x-text="transaction.type === 'loss' ? transaction.amount : '-'"></td>
                            <td class="py-2 px-4 border-b" x-text="transaction.description ?? ''"></td>
                        </tr>
                    </template>
                    <tr class="font-semibold bg-gray-100">
                        <td class="py-2 px-4 border-b">Total</td>
                        <td class="py-2 px-4 border-b" x-text="totals.invest"></td>
                        <td class="py-2 px-4 border-b" x-text="totals.profit"></td>
                        <td class="py-2 px-4 border-b" x-text="totals.loss"></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            </template>
            <template x-if="transactions.length === 0">
                <div class="text-gray-500">No transactions found for this project.</div>
            </template>
        </div>
    </div>

</div>

<script>
function mainHandler() {
    return {
        activeTab: null,
        projectName: '',
        debits: [],
        transactions: [],
        filters: { startDate: '', endDate: '', type: 'all' },

        async showDebits(projectId) {
            this.activeTab = 'debits';
            this.debits = [];
            this.projectName = '';
            try {
                const res = await fetch(`/client-projects/${projectId}/debits`);
                const data = await res.json();
                this.projectName = data.project_name;
                this.debits = data.debits;
            } catch (e) {
                this.projectName = '';
                this.debits = [];
            }
        },

        async showTransactions(projectId) {
            this.activeTab = 'transactions';
            this.transactions = [];
            this.projectName = '';
            try {
                const res = await fetch(`/client-projects/${projectId}/transactions`);
                const data = await res.json();
                this.projectName = data.project_name;
                this.transactions = data.transactions;
            } catch (e) {
                this.projectName = '';
                this.transactions = [];
            }
        },

        async updateStatus(debit) {
            try {
                const res = await fetch(`/client-debits/${debit.id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    debit.status = data.status;
                }
            } catch (e) {
                // Optionally show error
            }
        },

        get totalDebit() {
            return this.debits.reduce((sum, d) => sum + parseFloat(d.pay_amount || 0), 0);
        },

        get filteredTransactions() {
            return this.transactions.filter(t => {
                const tDate = new Date(t.date);
                if (this.filters.startDate && tDate < new Date(this.filters.startDate)) return false;
                if (this.filters.endDate && tDate > new Date(this.filters.endDate)) return false;
                if (this.filters.type !== 'all' && t.type !== this.filters.type) return false;
                return true;
            });
        },

        get totals() {
            let invest = 0, profit = 0, loss = 0;
            this.filteredTransactions.forEach(t => {
                const amt = parseFloat(t.amount || 0);
                if (t.type === 'invest') invest += amt;
                if (t.type === 'profit') profit += amt;
                if (t.type === 'loss')   loss   += amt;
            });
            return { invest, profit, loss };
        }
    }
}
</script>
@endsection