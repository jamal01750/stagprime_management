@extends('layouts.app')

@section('title', 'Return Product | Product Management')
@section('heading', 'Return Product | Product Management')

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

    {{-- Return Form --}}
    <form method="POST" action="{{ route('product.return.store') }}" class="space-y-4">
        @csrf
        <h2 class="font-bold">Return Entry</h2>

        <select name="product_category_id" class="border p-2 w-full" required>
            <option value="">-- Select Category --</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>

        <input type="number" name="quantity" placeholder="Return Quantity" class="border p-2 w-full" required>
        <div class="flex space-x-2">
            <select name="amount_type" class="mt-1 block rounded border-2 bg-white text-gray-900">
                <option value="dollar">$ Dollar</option>
                <option value="taka">৳ Taka</option>
            </select>
            <input type="number" name="amount" step="0.01" placeholder="Return Amount" class="mt-1 block w-full border p-2" required>
        </div>
        <textarea name="description" placeholder="Comment" class="border p-2 w-full"></textarea>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">
            Record Return
        </button>
    </form>

    {{-- Pending Return Table --}}
    <div class="mt-8">
        <h2 class="text-lg font-semibold mb-4">Pending Return Entries</h2>
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">Category</th>
                    <th class="px-4 py-2 border">Quantity</th>
                    <th class="px-4 py-2 border">Amount</th>
                    <th class="px-4 py-2 border">Comment</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($returns as $return)
                <tr>
                    <td class="px-4 py-2 border">{{ $return->category->name }}</td>
                    <td class="px-4 py-2 border text-center">{{ $return->quantity }}</td>
                    <td class="px-4 py-2 border text-right">{{ $return->amount_type == 'taka' ? '৳' : '$' }}{{ number_format($return->amount,2) }}</td>
                    <td class="px-4 py-2 border text-center">{{ $return->description }}</td>
                    <td class="px-4 py-2 border flex gap-2">
                        {{-- Edit --}}
                        <button type="button" onclick="openEditReturn({{ $return->id }})" class="text-blue-600 hover:underline">Edit</button>

                        {{-- Approve only admin --}}
                        @if(auth()->check() && auth()->user()->role === 'admin')
                        <form action="{{ route('product.return.approve',$return->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="text-green-600 hover:underline">Approve</button>
                        </form>
                        @endif

                        {{-- Delete --}}
                        <form action="{{ route('product.return.destroy',$return->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this return entry?')" class="text-red-600 hover:underline">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- Edit Modal --}}
<div id="editReturnModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
        <h2 class="text-lg font-bold mb-4">Edit Return</h2>
        <form id="editReturnForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium">Quantity</label>
                <input type="number" name="quantity" id="editReturnQuantity" class="border p-2 w-full" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Return Amount</label>
                <div class="flex gap-2">
                    <select name="amount_type" id="editReturnAmountType" class="border p-2 w-full">
                        <option value="dollar">$ Dollar</option>
                        <option value="taka">৳ Taka</option>
                    </select>
                    <input type="number" name="amount" step="0.01" id="editReturnAmount" class="border p-2 w-full" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Comment</label>
                <textarea name="description" id="editReturnDescription" class="border p-2 w-full"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditReturn()" class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditReturn(id) {
    fetch(`/products/return/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('editReturnQuantity').value = data.quantity;
            document.getElementById('editReturnAmountType').value = data.amount_type;
            document.getElementById('editReturnAmount').value = data.amount;
            document.getElementById('editReturnDescription').value = data.description ?? '';
            document.getElementById('editReturnForm').action = `/products/return/${id}`;
            document.getElementById('editReturnModal').classList.remove('hidden');
        });
}
function closeEditReturn() {
    document.getElementById('editReturnModal').classList.add('hidden');
}
</script>
@endsection
