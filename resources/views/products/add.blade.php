@extends('layouts.app')

@section('title', 'Add Product | Product Management')
@section('heading', 'Add Product | Product Management')

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

    {{-- Product Form --}}

    <div class="bg-white rounded shadow p-4 md:p-6">
        <h2 class="text-lg md:text-xl font-semibold mb-4">Add Product</h2>
        <form method="post" action="{{ route('product.store') }}" class="space-y-4">
            @csrf
            <div class="mb-4">
                <select name="product_category_id" class="border p-2 w-full">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Product Name</label>
                <input type="text" name="product_name" class="w-full border-2 border-blue-600 rounded px-3 py-1" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Product Quantity</label>
                <input type="number" name="quantity" step="0.01" class="w-full border-2 border-blue-600 rounded pl-2" required>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Save Product</button>
        </form>
    </div>

</div>
@endsection
