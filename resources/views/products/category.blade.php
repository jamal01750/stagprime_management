@extends('layouts.app')

@section('title', 'Product Category | Product Management')
@section('heading', 'Product Category | Product Management')

@section('content')
<div class="p-6 bg-white shadow rounded space-y-6">
    @if(session('success'))
        <div class="w-full mb-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif 

    {{-- Category Form --}}
    <form method="POST" action="{{ route('product.category.store') }}" class="space-y-4">
        @csrf
        <h2 class="font-bold">Add Product Category</h2>
        <input type="text" name="name" placeholder="Category Name" class="border p-2 w-full" required>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Save Category</button>
    </form>

    <!-- Category List -->
    <div class="mt-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Product Categories</h2>
            <a href="{{ route('product.category.download.pdf') }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Download PDF</a>
        </div>
        <table class="min-w-full bg-white border border-gray-200 text-center">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b">Category Name</th>
                    <th class="px-6 py-3 border-b">Total Stock</th>
                    <th class="px-6 py-3 border-b">Current Stock</th>
                    <th class="px-6 py-3 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td class="px-6 py-4 border-b">{{ $category->name }}</td>
                        <td class="px-6 py-4 border-b">{{ $category->total_quantity }}</td>
                        <td class="px-6 py-4 border-b">{{ $category->quantity }}</td>
                        <td class="px-6 py-4 border-b">
                            <button type="button" 
                                    onclick="openEditModal({{ $category->id }})"
                                    class="text-blue-600 hover:underline">
                                Edit
                            </button>
                            {{-- (only for admin) --}}
                            @if(auth()->check() && auth()->user()->role === 'admin')
                            <form action="{{ route('product.category.destroy', $category->id) }}" method="POST" class="inline-block ml-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline"
                                    onclick="return confirm('Are you sure to delete this category?')">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
        
</div>

{{-- Edit Modal --}}
<div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
        <h2 class="text-lg font-bold mb-4">Edit Category</h2>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <input type="text" name="name" id="editName" class="border p-2 w-full mb-4" required>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id) {
    fetch(`/categories/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('editName').value = data.name;
            document.getElementById('editForm').action = `/categories/${id}`;
            document.getElementById('editModal').classList.remove('hidden');
        });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection
