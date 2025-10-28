@extends('layouts.app')

@section('title', 'Report | Office Offline Cost')
@section('heading', 'Report | Office Offline Cost')

@section('content')
<div class="w-full flex flex-col space-y-6 md:space-y-8">

    {{-- ✅ Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- 🔍 Filter Form --}}
    <form method="GET" action="{{ route('offline.cost.report') }}" id="filterForm" class="flex flex-wrap gap-3 items-end mb-6">
        <div>
            <label class="block text-sm">Payment Status</label>
            <select name="status" class="border rounded px-2 py-1">
                <option value="">All</option>
                <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Paid</option>
                <option value="unpaid" {{ request('status')=='unpaid'?'selected':'' }}>Unpaid</option>
            </select>
        </div>

        <div>
            <label class="block text-sm">Approval Status</label>
            <select name="approve_status" class="border rounded px-2 py-1">
                <option value="">All</option>
                <option value="approved" {{ request('approve_status')=='approved'?'selected':'' }}>Approved</option>
                <option value="pending" {{ request('approve_status')=='pending'?'selected':'' }}>Pending</option>
            </select>
        </div>

        <div>
            <label class="block text-sm">Year</label>
            <select name="year" class="border rounded px-2 py-1">
                <option value="">All</option>
                @for($y = now('Asia/Dhaka')->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <div>
            <label class="block text-sm">Start Month</label>
            <select name="start_month" class="border rounded px-2 py-1">
                <option value="">All</option>
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ request('start_month') == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm">End Month</label>
            <select name="end_month" class="border rounded px-2 py-1">
                <option value="">All</option>
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ request('end_month') == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="{{ route('offline.cost.report') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Clear</a>
            <a href="{{ route('offline.cost.download.pdf', request()->query()) }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded">Download PDF</a>
        </div>
    </form>

    {{-- 📊 Report Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border border-gray-200 rounded-lg">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-3 py-2 text-left">Month / Year</th>
                    <th class="px-3 py-2 text-left">Category</th>
                    <th class="px-3 py-2 text-left">Amount (৳)</th>
                    <th class="px-3 py-2 text-left">Last Payment Date</th>
                    <th class="px-3 py-2 text-left">Note</th>
                    <th class="px-3 py-2 text-left">Payment Status</th>
                    <th class="px-3 py-2 text-left">Approval Status</th>
                    <th class="px-3 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-3 py-2">{{ date('F', mktime(0, 0, 0, $transaction->month, 1)) }} / {{ $transaction->year }}</td>
                    <td class="px-3 py-2">{{ $transaction->category->category ?? 'N/A' }}</td>
                    <td class="px-3 py-2 text-red-600">{{ number_format($transaction->amount, 2) }}</td>
                    <td class="px-3 py-2">{{ $transaction->last_date ?? '—' }}</td>
                    <td class="px-3 py-2">{{ $transaction->description ?? '—' }}</td>
                    {{-- Payment Status --}}
                    <td class="border px-3 py-2 text-center">
                        <form action="{{ route('offline.cost.payment.update', $transaction->id) }}" method="POST">
                            @csrf
                            <select name="status" class="border rounded py-1">
                                <option value="paid" {{ $transaction->status=='paid'?'selected':'' }}>Paid</option>
                                <option value="unpaid" {{ $transaction->status=='unpaid'?'selected':'' }}>Unpaid</option>
                            </select>
                            @if(auth()->user()->role === 'admin')
                            <button type="submit" class="mt-2 px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium"> 
                                Update 
                            </button>
                            @endif
                        </form>
                    </td>
                    {{-- Approval Status --}}
                    <td class="border px-3 py-2 text-center">
                        <form action="{{ route('offline.approve.status.update', $transaction->id) }}" method="POST">
                            @csrf
                            <select name="approve_status" class="border rounded py-1">
                                <option value="approved" {{ $transaction->approve_status=='approved'?'selected':'' }}>Approved</option>
                                <option value="pending" {{ $transaction->approve_status=='pending'?'selected':'' }}>Pending</option>
                            </select>
                            @if(auth()->user()->role === 'admin')
                            <button type="submit" class="mt-2 px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium"> 
                                Update 
                            </button>
                            @endif
                        </form>
                    </td>
                    <td class="px-3 py-2">
                        <div class="flex gap-2">
                            @if(auth()->user()->role === 'admin')
                            <a href="{{ route('offline.cost.transaction.edit', $transaction->id) }}"
                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs font-medium">
                                Edit
                            </a>
                            <form action="{{ route('offline.cost.transaction.destroy', $transaction->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this transaction?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium">
                                    Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-gray-500">No records found</td>
                </tr>
                @endforelse

                @if($transactions->count())
                <tr class="bg-gray-50 font-semibold border-t">
                    <td colspan="2" class="px-3 py-2 text-right">Total:</td>
                    <td class="px-3 py-2 text-blue-600">{{ $total }}</td>
                    <td colspan="4"></td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection


