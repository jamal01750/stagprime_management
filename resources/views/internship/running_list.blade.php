@extends('layouts.app')

@section('title', 'Interns List | Office Interns')
@section('heading', 'Interns List | Office Interns')

@section('content')
<div class="bg-white p-6 rounded shadow-md">

    {{-- ✅ Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- 🔎 Filter & Search --}}
    <form method="GET" action="{{ route('internship.list') }}" id="filterForm" class="flex flex-wrap gap-3 items-end mb-6">
        <div>
            <label class="block text-sm">Payment Status</label>
            <select name="payment_status" class="border rounded px-2 py-1">
                <option value="">All</option>
                <option value="Full paid" {{ request('payment_status')=='Full paid'?'selected':'' }}>Paid</option>
                <option value="Partial" {{ request('payment_status')=='Partial'?'selected':'' }}>Unpaid</option>
            </select>
        </div>
        <div>
            <label class="block text-sm">Active Status</label>
            <select name="active_status" class="border rounded px-2 py-1">
                <option value="">All</option>
                <option value="Running" {{ request('active_status')=='Running'?'selected':'' }}>Running</option>
                <option value="Expired" {{ request('active_status')=='Expired'?'selected':'' }}>Expired</option>
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
            <label class="block text-sm">Admission Start Date</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="border rounded px-2 py-1">
        </div>
        <div>
            <label class="block text-sm">Admission End Date</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="border rounded px-2 py-1">
        </div>

        {{-- 🔍 Search --}}
        <div>
            <label class="block text-sm">Search by ID</label>
            <input type="text" id="searchInput" class="border rounded px-2 py-1" placeholder="Enter Intern ID">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="{{ route('internship.list') }}" class="bg-gray-400 text-white px-4 py-2 rounded">Clear</a>
            <a href="{{ route('internship.list.download.pdf', request()->query()) }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded">Download List PDF</a>
        </div>
    </form>

    <div class="bg-white rounded shadow p-4 md:p-6 mt-6 overflow-x-auto">
        <h2 class="text-lg md:text-xl font-semibold mb-4">Interns List</h2>
        <table class="min-w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-3 py-2">Intern ID</th>
                    <th class="border border-gray-300 px-3 py-2">Name</th>
                    <th class="border border-gray-300 px-3 py-2">Contract Amount</th>
                    <th class="border border-gray-300 px-3 py-2">Total Paid</th>
                    <th class="border border-gray-300 px-3 py-2">Upcoming Amount</th>
                    <th class="border border-gray-300 px-3 py-2">Upcoming Date</th>
                    <th class="border border-gray-300 px-3 py-2">Active Status</th>
                    <th class="border border-gray-300 px-3 py-2">Approve Status</th>
                    <th class="border border-gray-300 px-3 py-2">Actions</th>
                </tr>
            </thead>
            <tbody id="studentTable">
                @forelse($students as $student)
                <tr>
                    <td class="border px-3 py-2 text-center">{{ $student->intern_id }}</td>
                    <td class="border px-3 py-2">{{ $student->internee_name }}</td>
                    <td class="border px-3 py-2">{{ $student->pay_amount }}</td>
                    <td class="border px-3 py-2">
                        {{ $student->total_paid }}
                        @if(auth()->user()->role === 'admin')
                        <form action="{{ route('internship.payment.update', $student->id) }}" method="POST">
                            @csrf
                            @if($student->total_paid < $student->pay_amount)
                                <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                    Update
                                </button> 
                            @else
                                <button class="py-1 bg-gray-400 text-white rounded cursor-not-allowed" disabled>
                                    Full Paid
                                </button>
                            @endif  
                        </form>
                        @endif
                    </td>
                    <td class="border px-3 py-2">{{ $student->upcoming_amount }}</td>
                    <td class="border px-3 py-2">{{ $student->upcoming_date }}</td>
                    {{-- Active Status --}}
                    <td class="border px-3 py-2 text-center">
                        <form action="{{ route('internship.active.status.update', $student->id) }}" method="POST">
                            @csrf
                            <select name="active_status" class="border rounded py-1">
                                <option value="Running" {{ $student->active_status=='Running'?'selected':'' }}>Running</option>
                                <option value="Expired" {{ $student->active_status=='Expired'?'selected':'' }}>Expired</option>
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
                        <form action="{{ route('internship.approve.status.update', $student->id) }}" method="POST">
                            @csrf
                            <select name="approve_status" class="border rounded py-1">
                                <option value="approved" {{ $student->approve_status=='approved'?'selected':'' }}>Approved</option>
                                <option value="pending" {{ $student->approve_status=='pending'?'selected':'' }}>Pending</option>
                            </select>
                            @if(auth()->user()->role === 'admin')
                            <button type="submit" class="mt-2 px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium"> 
                                Update 
                            </button>
                            @endif
                        </form>
                    </td>
                    <td class="border px-3 py-2 text-center">
                        <div class="flex gap-2">
                            <a href="{{ route('internship.individual', $student->id) }}" 
                            class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-medium">
                            View
                            </a>
                            @if(auth()->user()->role === 'admin')
                            <a href="{{ route('internship.edit', $student->id) }}" 
                            class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs font-medium">
                            Edit
                            </a>
                            <form action="{{ route('internship.delete', $student->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this Intern?')">
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
                    <td colspan="8" class="text-center p-4 text-gray-500">No Interns found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $students->links() }}
    </div>
</div>

{{-- 🔍 JS Search Filter (client-side) --}}
<script>
document.getElementById('searchInput').addEventListener('keyup', function () {
    const filter = this.value.toLowerCase();
    document.querySelectorAll('#studentTable tr').forEach(row => {
        const id = row.cells[0]?.innerText.toLowerCase();
        row.style.display = id.includes(filter) ? '' : 'none';
    });
});
</script>
@endsection
