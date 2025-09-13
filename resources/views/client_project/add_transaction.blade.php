@extends('layouts.app')

@section('title', 'Add Transaction | Client Project')
@section('heading', 'Add Transaction | Client Project')

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

    

    {{-- Project Transaction Form --}}
    <div class="bg-white rounded shadow p-4 md:p-6">
        <h2 class="text-lg md:text-xl font-semibold mb-4">Project Transactions</h2>
        <form method="POST" action="{{ route('client.project.transaction.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Project Name</label>
                <select name="project_id" class="w-full border-2 border-blue-600 rounded">
                    <option>Select Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Transaction Date</label>
                <input type="date" name="date" class="w-full border-2 border-blue-600 rounded" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <div class="flex space-x-4 mt-1">
                    <label class="inline-flex items-center">
                        <input type="radio" name="type" value="invest" class="form-radio text-green-600" checked>
                        <span class="ml-2">Invest</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="type" value="profit" class="form-radio text-red-600">
                        <span class="ml-2">Profit</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="type" value="loss" class="form-radio text-red-600">
                        <span class="ml-2">Loss</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Amount</label>
                <input type="number" name="amount" step="0.01" class="w-full border-2 border-blue-600 rounded pl-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                <textarea name="description" rows="3" 
                    class="w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           text-gray-900">
                </textarea>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Submit</button>
        </form>
    </div>

    


</div>

@endsection
