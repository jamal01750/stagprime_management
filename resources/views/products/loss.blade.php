@extends('layouts.app')

@section('title', 'Loss Product | Product Management')
@section('heading', 'Loss Product | Product Management')

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

    <form method="POST" action="{{ route('product.loss.store') }}" class="space-y-4">
        @csrf
        <h2 class="font-bold">Loss Entry</h2>

        <select name="product_category_id" class="border p-2 w-full">
            <option value="">-- Select Category --</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>

        <input type="number" name="quantity" placeholder="Loss Quantity" class="border p-2 w-full">
        <input type="number" step="0.01" name="loss_amount" placeholder="Loss Amount" class="border p-2 w-full">
        <textarea name="description" placeholder="Comment" class="border p-2 w-full"></textarea>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Record Loss</button>
        
    </form>
</div>


@endsection
