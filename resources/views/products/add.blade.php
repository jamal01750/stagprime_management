@extends('layouts.app')

@section('title', 'Add Product | Product Management')
@section('heading', 'Add Product | Product Management')

@section('content')
<div class="p-6 bg-white shadow rounded space-y-6">

    {{-- Success message --}}
    @if(session('success'))
        <div class="w-full mb-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif 

    {{-- Product Form --}}
    <div class="bg-white rounded shadow p-4 md:p-6">
        <h2 class="text-lg md:text-xl font-semibold mb-4">Add Product</h2>
        <form method="post" action="{{ route('product.store') }}" class="space-y-4">
            @csrf
            <div class="mb-4">
                <select name="product_category_id" class="border p-2 w-full" required>
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" name="quantity" step="1" class="w-full border-2 border-blue-600 rounded pl-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Total Amount (Optional)</label>
                <div class="flex gap-2">
                    <select name="amount_type" class="mt-1 block rounded border-2 bg-white text-gray-900">
                        <option value="dollar">$ Dollar</option>
                        <option value="taka">৳ Taka</option>
                    </select>
                    <input type="number" name="amount" step="0.01" class="w-full border-2 border-blue-600 rounded pl-2" >
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">
                Save Product
            </button>
        </form>
    </div>

    {{-- Products List --}}
    <div class="mt-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Pending Products</h2>
            <a href="{{ route('product.download.pdf') }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Download PDF</a>
        </div>
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">Sl.</th>
                    <th class="px-4 py-2 border">Category</th>
                    <th class="px-4 py-2 border">Quantity</th>
                    <th class="px-4 py-2 border">Amount</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $key => $product)
                <tr>
                    <td class="px-4 py-2 border">{{$key+1}}</td>
                    <td class="px-4 py-2 border">{{ $product->category->name }}</td>
                    <td class="px-4 py-2 border text-center">{{ $product->quantity }}</td>
                    <td class="px-4 py-2 border text-center">{{ $product->amount_type == 'taka' ? '৳' : '$' }}{{ number_format($product->amount, 2) }}</td>
                    <td class="px-4 py-2 border flex gap-2">
                        
                        {{-- Edit --}}
                        <button type="button" onclick="openEditModal({{ $product->id }})" class="text-blue-600 hover:underline">Edit</button>
                        
                        {{-- Approve (only for admin) --}}
                        @if(auth()->check() && auth()->user()->role === 'admin')
                            <form action="{{ route('product.approve',$product->id) }}" method="POST" class="inline-block">
                                @csrf

                                @if($product->status === 'pending')
                                    <button type="submit" class="text-green-600 hover:underline">Approve</button>
                                @endif
                            </form>
                        @endif

                        {{-- Delete --}}
                        <form action="{{ route('product.destroy',$product->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this product?')" class="text-red-600 hover:underline">
                                Delete
                            </button>
                        </form>
                        
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td class="px-4 py-2 border font-bold"></td>
                    <td class="px-4 py-2 border font-bold">Total</td>
                    <td class="px-4 py-2 border text-center font-bold">{{ $totalQuantity }}</td>
                    <td class="px-4 py-2 border text-center font-bold">
                        ৳ {{ number_format($totalAmount, 2) }}
                    </td>
                    <td class="px-4 py-2 border"></td>
                </tr>
            </tbody>
        </table>
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>

</div>

{{-- Edit Modal --}}
<div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
        <h2 class="text-lg font-bold mb-4">Edit Product</h2>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium">Quantity</label>
                <input type="number" name="quantity" id="editQuantity" class="border p-2 w-full" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Total Amount (Optional)</label>
                <div class="flex gap-2">
                    <select name="amount_type" id="editAmountType" class="mt-1 block rounded border-2 bg-white text-gray-900">
                        <option value="dollar">$ Dollar</option>
                        <option value="taka">৳ Taka</option>
                    </select>
                    <input type="number" name="amount" id="editAmount" step="0.01" class="w-full border-2 border-blue-600 rounded pl-2" >
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id) {
    fetch(`/products/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('editQuantity').value = data.quantity;
            document.getElementById('editAmountType').value = data.amount_type;
            document.getElementById('editAmount').value = data.amount;
            document.getElementById('editForm').action = `/products/${id}`;
            document.getElementById('editModal').classList.remove('hidden');
        });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection
