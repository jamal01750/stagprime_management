@extends('layouts.app')

@section('title', 'Sell Product | Product Management')
@section('heading', 'Sell Product | Product Management')


@section('content')
<div class="p-6 bg-white shadow rounded space-y-6">

    @if(session('success'))
        <div class="w-full mb-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif 
    @if($errors->any())
        <div class="w-full mb-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('product.sell.store') }}" class="space-y-4">
        @csrf
        <h2 class="font-bold">Sell Product</h2>
        <select name="product_category_id" class="border p-2 w-full">
            <option value="">-- Select Category --</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        <input type="number" name="quantity" placeholder="Sell Quantity" class="border p-2 w-full" required>
        <input type="number" step="0.01" name="amount" placeholder="Total Sell Amount" class="border p-2 w-full" required>
        <textarea name="description" placeholder="Comment" class="border p-2 w-full"></textarea>

        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Save Sales</button>
    </form>
</div>

@endsection
