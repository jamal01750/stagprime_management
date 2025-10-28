@extends('layouts.app')

@section('title', 'Loss Product | Product Management')
@section('heading', 'Loss Product | Product Management')

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

    {{-- Loss Form --}}
    <form method="POST" action="{{ route('product.loss.store') }}" class="space-y-4">
        @csrf
        <h2 class="font-bold">Loss Entry</h2>

        <select name="product_category_id" class="border p-2 w-full" required>
            <option value="">-- Select Category --</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>

        <input type="number" name="quantity" placeholder="Loss Quantity" class="border p-2 w-full" required>
        <div class="flex space-x-2">
            <select name="amount_type" class="mt-1 block rounded border-2 bg-white text-gray-900">
                <option value="dollar">$ Dollar</option>
                <option value="taka">৳ Taka</option>
            </select>
            <input type="number" name="loss_amount" step="0.01" placeholder="Loss Amount" class="mt-1 block w-full border p-2" required>
        </div>
        <textarea name="description" placeholder="Comment" class="border p-2 w-full"></textarea>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">
            Record Loss
        </button>
    </form>

    {{-- Pending Loss Table --}}
    <div class="mt-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Pending Loss Entries</h2>
            <a href="{{ route('product.loss.download.pdf') }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Download PDF</a>
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
                @foreach($losses as $key => $loss)
                <tr>
                    <td class="px-4 py-2 border">{{$key+1}}</td>
                    <td class="px-4 py-2 border">{{ $loss->category->name }}</td>
                    <td class="px-4 py-2 border text-center">{{ $loss->quantity }}</td>
                    <td class="px-4 py-2 border text-right">{{ $loss->amount_type == 'taka' ? '৳' : '$' }}{{ number_format($loss->loss_amount,2) }}</td>
                    <td class="px-4 py-2 border text-center">{{ $loss->description }}</td>
                    <td class="px-4 py-2 border flex gap-2">
                        {{-- Edit --}}
                        <button type="button" onclick="openEditLoss({{ $loss->id }})" class="text-blue-600 hover:underline">Edit</button>

                        {{-- Approve only admin --}}
                        @if(auth()->check() && auth()->user()->role === 'admin')
                        <form action="{{ route('product.loss.approve',$loss->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="text-green-600 hover:underline">Approve</button>
                        </form>
                        @endif

                        {{-- Delete --}}
                        <form action="{{ route('product.loss.destroy',$loss->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this loss entry?')" class="text-red-600 hover:underline">
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
            {{ $losses->links() }}
        </div>
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
