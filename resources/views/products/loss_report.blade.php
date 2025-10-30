@extends('layouts.app')

@section('title', 'Product Loss Report')
@section('heading', 'Product Loss Report')

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-green-100 border border-green-300 text-green-800 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif

<div class="w-full flex flex-col space-y-6">

    <!-- 🔹 Filter Form -->
    <div class="bg-white rounded shadow p-4">
        <form method="GET" action="{{ route('product.loss.report') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="border rounded px-3 py-1">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="border rounded px-3 py-1">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category_id" class="border rounded px-3 py-1 min-w-[150px]">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $selected_category == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
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

            @if(request()->filled('start_date') || request()->filled('end_date') || request()->filled('status') || request()->filled('category_id'))
                <a href="{{ route('product.loss.report') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 font-medium">
                    Clear
                </a>
            @endif
            <div class="flex gap-2">
                <a href="{{ route('product.loss.filter.pdf', request()->query()) }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded">Download PDF</a>
            </div>
        </form>
    </div>

    <!-- 🔹 Products Table -->
    <div class="bg-white shadow rounded p-4">
        <div class="overflow-x-auto">
            <h3 class="text-lg font-semibold text-gray-800">Product Loss</h3>
            <table class="min-w-full text-sm border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-left">Date</th>
                        <th class="px-3 py-2 text-left">Category</th>
                        <th class="px-3 py-2 text-left">Quantity</th>
                        <th class="px-3 py-2 text-left">Amount</th>
                        <th class="px-3 py-2 text-left">Comment</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $p->created_at->format('Y-m-d') }}</td>
                            <td class="px-3 py-2">{{ $p->category->name ?? '-' }}</td>
                            <td class="px-3 py-2 text-center">{{ $p->quantity }}</td>
                            <td class="px-3 py-2 text-center">
                                {{ $p->amount_type == 'taka' ? '৳' : '$' }}{{ number_format($p->loss_amount, 2) }}
                            </td>
                            <td class="px-3 py-2 text-center">{{ $p->description }}</td>
                            <td class="px-3 py-2">
                                <select data-id="{{ $p->id }}" class="status-dropdown border rounded px-2 py-1">
                                    <option value="pending" {{ $p->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $p->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                </select>
                                @if(auth()->user()->role === 'admin')
                                    <button type="button" data-id="{{ $p->id }}"
                                        class="update-status-btn mt-1 px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                        Update
                                    </button>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex gap-2">
                                @if(auth()->user()->role === 'admin')
                                    @if($p->status === 'pending')
                                        {{-- Edit --}}
                                        <button type="button" onclick="openEditLoss({{ $p->id }})" class="text-blue-600 hover:underline">Edit</button>
                                        {{-- Delete --}}
                                        <form action="{{ route('product.loss.destroy',$p->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete this sale?')" class="text-red-600 hover:underline">
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="text-gray-400 cursor-not-allowed">Edit</button>
                                        <button type="button" class="text-gray-400 cursor-not-allowed">Delete</button>
                                    @endif
                                @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-gray-500">No products found.</td></tr>
                    @endforelse

                    <tr class="bg-gray-50 font-semibold border-t">
                        <td colspan="2">Total</td>
                        <td class="text-center">{{ $totalQuantity }}</td>
                        <td class="text-center">৳ {{ number_format($totalAmount, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $products->links() }}</div>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editLossModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
        <h2 class="text-lg font-bold mb-4">Edit Loss</h2>
        <form id="editLossForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium">Quantity</label>
                <input type="number" name="quantity" id="editLossQuantity" class="border p-2 w-full" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Loss Amount</label>
                <div class="flex gap-2">
                    <select name="amount_type" id="editLossAmountType" class="border p-2 w-full">
                        <option value="dollar">$ Dollar</option>
                        <option value="taka">৳ Taka</option>
                    </select>
                    <input type="number" name="loss_amount" step="0.01" id="editLossAmount" class="border p-2 w-full" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Comment</label>
                <textarea name="description" id="editLossDescription" class="border p-2 w-full"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditLoss()" class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            </div>
        </form>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
    // ✅ AJAX Status Update
    document.querySelectorAll('.update-status-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const select = document.querySelector(`.status-dropdown[data-id="${id}"]`);
            const status = select.value;

            fetch("{{ route('product.loss.status.update') }}", {
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

});

function openEditLoss(id) {
    fetch(`/products/loss/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('editLossQuantity').value = data.quantity;
            document.getElementById('editLossAmountType').value = data.amount_type;
            document.getElementById('editLossAmount').value = data.loss_amount;
            document.getElementById('editLossDescription').value = data.description ?? '';
            document.getElementById('editLossForm').action = `/products/loss/${id}`;
            document.getElementById('editLossModal').classList.remove('hidden');
        });
}
function closeEditLoss() {
    document.getElementById('editLossModal').classList.add('hidden');
}
</script>
@endsection
