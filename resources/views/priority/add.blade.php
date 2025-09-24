@extends('layouts.app')

@section('title', 'Add New | Priority Product/Project')
@section('heading', 'Add New | Priority Product/Project')

@section('content')
<div 
    x-data="transactionsHandler()" 
    class="w-full flex flex-col space-y-6 md:space-y-8">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="w-full mb-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Create Form --}}
    <div class="bg-white rounded shadow p-4 md:p-6">
        <h2 class="text-lg md:text-xl font-semibold mb-4">Create New Priority Product/Project</h2>
        <form method="post" action="{{ route('priority.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Product/Project Name</label>
                <input type="text" name="name" class="w-full border-2 border-blue-600 rounded px-3 py-1" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" name="quantity" class="w-full border-2 border-blue-600 rounded pl-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                <input type="number" name="amount" step="0.01" class="w-full border-2 border-blue-600 rounded pl-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                <textarea name="description" rows="3" 
                    class="w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           text-gray-900">
                </textarea>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Create</button>
        </form>
    </div>

    
</div>

@endsection
