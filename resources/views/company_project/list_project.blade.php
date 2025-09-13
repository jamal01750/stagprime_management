@extends('layouts.app')

@section('title', 'Project List | Company Own Project')
@section('heading', 'Project List | Company Own Project')

@section('content')
<div 
    x-data="transactionsHandler()" 
    class="w-full flex flex-col space-y-6 md:space-y-8">

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
        <h2 class="text-lg md:text-xl font-semibold mb-4">Project List</h2>
        <table class="w-full bg-white border text-left">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Project Name</th>
                    <th class="py-2 px-4 border-b">Start Date</th>
                    <th class="py-2 px-4 border-b">Initial Invest/Balance</th>
                    <th class="py-2 px-4 border-b">Total Invest</th>
                    <th class="py-2 px-4 border-b">Total Profit</th>
                    <th class="py-2 px-4 border-b">Total Loss</th>
                    <th class="py-2 px-4 border-b">Remaining Balance</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $project)
                    <tr>
                        <td class="py-2 px-4 border-b">{{ $project->project_name }}</td>
                        <td class="py-2 px-4 border-b">{{ $project->start_date }}</td>
                        <td class="py-2 px-4 border-b">{{ $project->initial_invest }}</td>
                        <td class="py-2 px-4 border-b">{{ $project->total_invest }}</td>
                        <td class="py-2 px-4 border-b">{{ $project->total_profit }}</td>
                        <td class="py-2 px-4 border-b">{{ $project->total_loss }}</td>
                        <td class="py-2 px-4 border-b">{{ $project->remaining_balance }}</td>
                        <td class="py-2 px-4 border-b space-x-2">
                            {{-- View Transactions --}}
                            <button 
                                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium"
                                @click="loadTransactions({{ $project->id }})">
                                View Transactions
                            </button>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('company.project.delete', ['id' => $project->id]) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this project?')">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Transactions Section --}}
    <div x-show="selectedProject" class="bg-white rounded shadow p-4 md:p-6">
        <h2 class="text-lg md:text-xl font-semibold mb-4">
            Transactions of <span x-text="projectName"></span>
        </h2>

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
                    <td class="py-2 px-4 border-b"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    function transactionsHandler() {
        return {
            selectedProject: null,
            projectName: '',
            transactions: [],
            filters: {
                startDate: '',
                endDate: '',
                type: 'all'
            },
            async loadTransactions(projectId) {
                let res = await fetch(`/company-projects/${projectId}/transactions`);
                let data = await res.json();
                this.selectedProject = projectId;
                this.projectName = data.project_name;
                this.transactions = data.transactions;
            },
            get filteredTransactions() {
                return this.transactions.filter(t => {
                    // Date filter
                    let tDate = new Date(t.date);
                    if (this.filters.startDate && tDate < new Date(this.filters.startDate)) return false;
                    if (this.filters.endDate && tDate > new Date(this.filters.endDate)) return false;
                    // Type filter
                    if (this.filters.type !== 'all' && t.type !== this.filters.type) return false;
                    return true;
                });
            },
            get totals() {
                let invest = 0, profit = 0, loss = 0;
                this.filteredTransactions.forEach(t => {
                    if (t.type === 'invest') invest += parseFloat(t.amount);
                    if (t.type === 'profit') profit += parseFloat(t.amount);
                    if (t.type === 'loss') loss += parseFloat(t.amount);
                });
                return { invest, profit, loss };
            }
        }
    }
</script>
@endsection
