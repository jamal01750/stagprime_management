@extends('layouts.app')

@section('title', 'Offline Cost Category')
@section('heading', 'Offline Cost Category')

@section('content')
<div x-data="categoryManager()" class="w-full flex flex-col space-y-6 md:space-y-8">

    @if(session('success'))
        <div class="w-full mb-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif 

    <!-- 🔹 Create Category -->
    <div class="bg-white rounded shadow p-4 md:p-6">
        <h2 class="text-lg md:text-xl font-semibold mb-4">Create Category</h2>
        <div class="flex flex-col md:flex-row gap-4">
            <form method="POST" action="{{ route('offline.category.create') }}" class="flex items-center gap-2">
                @csrf
                <input type="text" name="category" placeholder="Type Category Name" class="border-2 border-blue-600 rounded px-3 py-1 focus:border-blue-700 focus:ring-blue-700" required>
                <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Create Category</button>
            </form>
        </div>
    </div>

    <!-- 🔹 Category List -->
    <div class="bg-white rounded shadow p-4 md:p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Category List</h2>
            <a href="{{ route('category.download.pdf') }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Download PDF</a>
        </div>

        <table class="min-w-full border">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="px-3 py-2 text-left">SL</th>
                    <th class="px-3 py-2 text-left">Category Name</th>
                    <th class="px-3 py-2 text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $key => $category)
                    <tr class="border-b">
                        <td class="px-3 py-2">{{ $key + 1 }}</td>
                        <td class="px-3 py-2">{{ $category->category }}</td>
                        <td class="px-3 py-2">
                            <button @click="openEditModal({{ $category->id }}, '{{ $category->category }}')" class="text-blue-600 hover:underline mr-2">Edit</button>
                            @if(auth()->check() && auth()->user()->role === 'admin')
                            <button @click="deleteCategory({{ $category->id }})" class="text-red-600 hover:underline">Delete</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- 🔹 Edit Modal -->
    <div x-show="showModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-lg font-semibold mb-4">Edit Category</h3>
            <input type="text" x-model="editName" class="w-full border rounded px-3 py-2 mb-4" />
            <div class="flex justify-end gap-3">
                <button @click="showModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <button @click="updateCategory()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
            </div>
        </div>
    </div>

</div>

<!-- ✅ Alpine.js Script -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('categoryManager', () => ({
        showModal: false,
        editId: null,
        editName: '',

        openEditModal(id, name) {
            this.editId = id;
            this.editName = name;
            this.showModal = true;
        },

        updateCategory() {
            fetch(`/offline/category/update/${this.editId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name: this.editName })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Category updated successfully');
                    window.location.reload();
                } else {
                    alert('Update failed');
                }
            })
            .catch(err => console.error(err));
            this.showModal = false;
        },

        deleteCategory(id) {
            if (!confirm('Are you sure you want to delete this category?')) return;
            fetch(`/offline/category/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Category deleted successfully');
                    window.location.reload();
                } else {
                    alert('Delete failed');
                }
            })
            .catch(err => console.error(err));
        }
    }))
});
</script>
@endsection
