@extends('layouts.app')

@section('title', 'Product Sell Report')
@section('heading', 'Product Sell Report')

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-green-100 border border-green-300 text-green-800 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif

<div class="w-full flex flex-col space-y-6">

    <!-- 🔹 Filter Form -->
    <div class="bg-white rounded shadow p-4">
        <form method="GET" action="{{ route('product.sell.report') }}" class="flex flex-wrap gap-4 items-end">
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
                    <option value="unpaid" {{ $selected_status == 'unpaid' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ $selected_status == 'paid' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">
                Filter
            </button>

            @if(request()->filled('start_date') || request()->filled('end_date') || request()->filled('status') || request()->filled('category_id'))
                <a href="{{ route('product.sell.report') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 font-medium">
                    Clear
                </a>
            @endif
            <div class="flex gap-2">
                <a href="{{ route('product.sell.filter.pdf', request()->query()) }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded">Download PDF</a>
            </div>
        </form>
    </div>

    <!-- 🔹 Products Table -->
    <div class="bg-white shadow rounded p-4">
        <div class="overflow-x-auto">
            <h3 class="text-lg font-semibold text-gray-800">Product Sales</h3>
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
                                {{ $p->amount_type == 'taka' ? '৳' : '$' }}{{ number_format($p->amount, 2) }}
                            </td>
                            <td class="px-3 py-2 text-center">{{ $p->description }}</td>
                            <td class="px-3 py-2">
                                <select data-id="{{ $p->id }}" class="status-dropdown border rounded px-2 py-1">
                                    <option value="unpaid" {{ $p->status == 'unpaid' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ $p->status == 'paid' ? 'selected' : '' }}>Approved</option>
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
                                    @if($p->status === 'unpaid')
                                        {{-- Edit --}}
                                        <button type="button" onclick="openEditSale({{ $p->id }})" class="text-blue-600 hover:underline">Edit</button>
                                        {{-- Delete --}}
                                        <form action="{{ route('product.sell.destroy',$p->id) }}" method="POST" class="inline-block">
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
<div id="editSaleModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
        <h2 class="text-lg font-bold mb-4">Edit Sale</h2>
        <form id="editSaleForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium">Quantity</label>
                <input type="number" name="quantity" id="editSaleQuantity" class="border p-2 w-full" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Total Amount</label>
                <div class="flex gap-2">
                    <select name="amount_type" id="editAmountType" class="mt-1 block rounded border-2 bg-white text-gray-900">
                        <option value="dollar">$ Dollar</option>
                        <option value="taka">৳ Taka</option>
                    </select>
                    <input type="number" name="amount" id="editSaleAmount" step="0.01" class="w-full border-2 border-blue-600 rounded pl-2" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Comment</label>
                <textarea name="description" id="editSaleDescription" class="border p-2 w-full"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditSale()" class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
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

            fetch("{{ route('product.sell.status.update') }}", {
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

function openEditSale(id) {
    fetch(`/products/sell/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('editSaleQuantity').value = data.quantity;
            document.getElementById('editAmountType').value = data.amount_type;
            document.getElementById('editSaleAmount').value = data.amount;
            document.getElementById('editSaleDescription').value = data.description ?? '';
            document.getElementById('editSaleForm').action = `/products/sell/${id}`;
            document.getElementById('editSaleModal').classList.remove('hidden');
        });
}
function closeEditSale() {
    document.getElementById('editSaleModal').classList.add('hidden');
}
</script>
@endsection
