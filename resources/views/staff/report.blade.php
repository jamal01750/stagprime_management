@extends('layouts.app')

@section('title', 'Staff Salary Report | Stagprime Management')
@section('heading', 'Staff Salary Report | Stagprime Management')

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-md">

    {{-- Report Filters --}}
    <form method="GET" action="{{ route('staff.salary.report', ['type' => request('type', 'monthly')]) }}" class="mb-6">
        <div class="flex flex-wrap items-end gap-4">
            
            {{-- Report Type --}}
            <div>
                <label class="block text-sm font-medium">Report Type</label>
                <select name="type" onchange="this.form.submit()" class="border rounded p-2">
                    <option value="monthly" {{ request('type')=='monthly' ? 'selected' : '' }}>Monthly Report</option>
                    <option value="yearly" {{ request('type')=='yearly' ? 'selected' : '' }}>Yearly Report</option>
                    <option value="individual" {{ request('type')=='individual' ? 'selected' : '' }}>Individual Report</option>
                    <option value="service" {{ request('type')=='service' ? 'selected' : '' }}>Service Time Report</option>
                </select>
            </div>

            {{-- Month & Year (Monthly Report) --}}
            @if(request('type','monthly') == 'monthly')
                <div>
                    <label class="block text-sm font-medium">Month</label>
                    <select name="month" class="border rounded p-2">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0,0,0,$m,1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Year</label>
                    <input type="number" name="year" value="{{ request('year', now()->year) }}" class="border rounded p-2 w-28">
                </div>
            @endif

            {{-- Year (Yearly Report) --}}
            @if(request('type') == 'yearly')
                <div>
                    <label class="block text-sm font-medium">Year</label>
                    <input type="number" name="year" value="{{ request('year', now()->year) }}" class="border rounded p-2 w-28">
                </div>
            @endif

            {{-- Individual Staff --}}
            @if(request('type') == 'individual')
                <div>
                    <label class="block text-sm font-medium">Select Staff</label>
                    <select name="staff_id" class="border rounded p-2">
                        @foreach(\App\Models\Staff::all() as $s)
                            <option value="{{ $s->id }}" {{ request('staff_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Show Report
                </button>
            </div>
        </div>
    </form>

    {{-- Report Title --}}
    <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $reportData['title'] ?? 'Reports' }}</h2>

    {{-- Monthly Report --}}
    @if($type === 'monthly')
        <div class="mb-4">
            <p><strong>Total Paid:</strong> ৳{{ number_format($reportData['totalPaid'], 2) }}</p>
            <p><strong>Total Unpaid:</strong> ৳{{ number_format($reportData['totalUnpaid'], 2) }}</p>
        </div>
        <table class="w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Staff Name</th>
                    <th class="px-4 py-2">Amount</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['salaries'] as $salary)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $salary->staff->name }}</td>
                        <td class="px-4 py-2">৳{{ number_format($salary->amount, 2) }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $salary->status == 'Paid' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                {{ $salary->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Yearly Report --}}
    @if($type === 'yearly')
        @foreach($reportData['salaries'] as $staffId => $salaries)
            <h3 class="mt-6 font-semibold text-gray-800">{{ $salaries->first()->staff->name }}</h3>
            <p>Total Paid: ৳{{ number_format($salaries->where('status', 'Paid')->sum('amount'), 2) }}</p>
            <p>Total Unpaid: ৳{{ number_format($salaries->where('status', 'Unpaid')->sum('amount'), 2) }}</p>
        @endforeach
    @endif

    {{-- Individual Report --}}
    @if($type === 'individual')
        <h3 class="text-lg font-semibold">{{ $reportData['staff']->name }}</h3>
        <p>Paid Months: {{ $reportData['paidMonths'] }}</p>
        <p>Total Paid: ৳{{ number_format($reportData['totalPaid'], 2) }}</p>
        <p>Total Unpaid: ৳{{ number_format($reportData['totalUnpaid'], 2) }}</p>
    @endif

    {{-- Service Time Report --}}
    @if($type === 'service')
        <table class="w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Staff Name</th>
                    <th class="px-4 py-2">Join Date</th>
                    <th class="px-4 py-2">Total Paid</th>
                    <th class="px-4 py-2">Total Unpaid</th>
                    <th class="px-4 py-2">Duration</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['staff'] as $s)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $s['name'] }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($s['join_date'])->format('d M, Y') }}</td>
                        <td class="px-4 py-2">৳{{ number_format($s['total_paid'], 2) }}</td>
                        <td class="px-4 py-2">৳{{ number_format($s['total_unpaid'], 2) }}</td>
                        <td class="px-4 py-2">{{ $s['duration'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
