@extends('layouts.app')

@section('title', 'Sell Product | Product Management')
@section('heading', 'Sell Product | Product Management')

@section('content')
<div class="p-6 bg-white shadow rounded space-y-6">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Sell Form --}}
    <form method="POST" action="{{ route('product.sell.store') }}" class="space-y-4">
        @csrf
        <h2 class="font-bold">Sell Product</h2>
        <select name="product_category_id" class="border p-2 w-full" required>
            <option value="">-- Select Category --</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        <input type="number" name="quantity" placeholder="Sell Quantity" class="border p-2 w-full" required>
        <div class="flex space-x-2">
            <select name="amount_type" class="mt-1 block rounded border-2 bg-white text-gray-900">
                <option value="dollar">$ Dollar</option>
                <option value="taka">৳ Taka</option>
            </select>
            <input type="number" name="amount" step="0.01" placeholder="Total Sell Amount" class="mt-1 block w-full border p-2" required>
        </div>
        <textarea name="description" placeholder="Comment" class="border p-2 w-full"></textarea>

        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">
            Save Sale
        </button>
    </form>

    {{-- Pending Sales Table --}}
    <div class="mt-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Pending Sales</h2>
            <a href="{{ route('product.sales.download.pdf') }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Download PDF</a>
        </div>
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">Sl.</th>
                    <th class="px-4 py-2 border">Category</th>
                    <th class="px-4 py-2 border">Quantity</th>
                    <th class="px-4 py-2 border">Amount</th>
                    <th class="px-4 py-2 border">Comment</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $key => $sale)
                <tr>
                    <td class="px-4 py-2 border">{{$key+1}}</td>
                    <td class="px-4 py-2 border">{{ $sale->category->name }}</td>
                    <td class="px-4 py-2 border text-center">{{ $sale->quantity }}</td>
                    <td class="px-4 py-2 border text-right">{{ $sale->amount_type == 'taka' ? '৳' : '$' }}{{ number_format($sale->amount,2) }}</td>
                    <td class="px-4 py-2 border text-center">{{ $sale->description }}</td>
                    <td class="px-4 py-2 border flex gap-2">
                        {{-- Edit --}}
                        <button type="button" onclick="openEditSale({{ $sale->id }})" class="text-blue-600 hover:underline">Edit</button>

                        {{-- Approve only for admin --}}
                        @if(auth()->check() && auth()->user()->role === 'admin')
                        <form action="{{ route('product.sell.approve',$sale->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="text-green-600 hover:underline">Approve</button>
                        </form>
                        @endif
                        
                        {{-- Delete --}}
                        <form action="{{ route('product.sell.destroy',$sale->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this sale?')" class="text-red-600 hover:underline">
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
            {{ $sales->links() }}
        </div>
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
