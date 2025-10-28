@extends('layouts.app')

@section('title', 'Students List | Office Student')
@section('heading', 'Students List | Office Student')

@section('content')
<div class="bg-white p-6 rounded shadow-md">

    {{-- ✅ Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- 🔎 Filter & Search --}}
    <form method="GET" action="{{ route('students.list') }}" id="filterForm" class="flex flex-wrap gap-3 items-end mb-6">
        <div>
            <label class="block text-sm">Payment Status</label>
            <select name="payment_status" class="border rounded px-2 py-1">
                <option value="">All</option>
                <option value="Paid" {{ request('payment_status')=='Paid'?'selected':'' }}>Paid</option>
                <option value="Unpaid" {{ request('payment_status')=='Unpaid'?'selected':'' }}>Unpaid</option>
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
            <input type="text" id="searchInput" class="border rounded px-2 py-1" placeholder="Enter Student ID">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="{{ route('students.list') }}" class="bg-gray-400 text-white px-4 py-2 rounded">Clear</a>
            <a href="{{ route('students.list.download.pdf', request()->query()) }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded">Download List PDF</a>
        </div>
    </form>

    {{-- 🧮 Table --}}
    <div class="overflow-x-auto">
        <table class="w-full border border-gray-300 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">ID</th>
                    <th class="border px-3 py-2">Name</th>
                    <th class="border px-3 py-2">Payment Status</th>
                    <th class="border px-3 py-2">Active Status</th>
                    <th class="border px-3 py-2">Approval</th>
                    <th class="border px-3 py-2">Actions</th>
                </tr>
            </thead>
            <tbody id="studentTable">
                @forelse($students as $student)
                    <tr>
                        <td class="border px-3 py-2 text-center">{{ $student->student_id }}</td>
                        <td class="border px-3 py-2">{{ $student->student_name }}</td>
                        
                        {{-- Payment Status --}}
                        <td class="border px-3 py-2 text-center">
                            <form action="{{ route('student.payment.update', $student->id) }}" method="POST">
                                @csrf
                                <select name="payment_status" class="border rounded py-1">
                                    <option value="Paid" {{ $student->payment_status=='Paid'?'selected':'' }}>Paid</option>
                                    <option  value="Unpaid" {{ $student->payment_status=='Unpaid'?'selected':'' }}>Unpaid</option>
                                </select>
                                @if(auth()->user()->role === 'admin')
                                <button type="submit" class="mt-2 px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium"> 
                                    Update 
                                </button>
                                @endif
                            </form>
                        </td>

                        {{-- Active Status --}}
                        <td class="border px-3 py-2 text-center">
                            <form action="{{ route('student.active.status.update', $student->id) }}" method="POST">
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
                            <form action="{{ route('student.approve.status.update', $student->id) }}" method="POST">
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
                    
                        <td class="border text-center px-3 py-2">
                            <div class="flex gap-2"> 
                                <a href="{{ route('student.individual', $student->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-medium"> View </a> 
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('student.edit', $student->id) }}" class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs font-medium"> Edit </a> 
                                    <form action="{{ route('student.delete', $student->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Student?')"> 
                                        @csrf @method('DELETE') 
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium"> 
                                            Delete 
                                        </button> 
                                    </form> 
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-3 text-gray-500">No students found</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $students->links() }}</div>
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
