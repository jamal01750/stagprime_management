@extends('layouts.app')

@section('title', 'Add Project | Client Project Management')
@section('heading', 'Add Project | Client Project Management')

@section('content')
    <div class="w-full flex flex-col space-y-6 md:space-y-8">

        {{-- Success Message --}}
        @if(session('success'))
            <div class="w-full mb-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        {{-- Create Project Form --}}
        <div class="bg-white rounded shadow p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold mb-4">Create Client Project</h2>
            <form method="post" action="{{ route('client.project.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Project Name</label>
                    <input type="text" name="project_name" class="w-full border-2 border-blue-600 rounded px-3 py-1" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Transaction Currency</label>
                    <div class="flex space-x-4 mt-1">
                        <label class="inline-flex items-center">
                            <input type="radio" name="currency" value="Dollar" class="form-radio text-green-600" checked>
                            <span class="ml-2">Dollar</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="currency" value="Taka" class="form-radio text-red-600">
                            <span class="ml-2">Taka</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="start_date" class="w-full border-2 border-blue-600 rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">End Date / Deadline</label>
                    <input type="date" name="end_date" class="w-full border-2 border-blue-600 rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Contract Amount</label>
                    <input type="number" name="contract_amount" step="0.01" class="w-full border-2 border-blue-600 rounded pl-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Advance Amount</label>
                    <input type="number" name="advance_amount" step="0.01" class="w-full border-2 border-blue-600 rounded pl-2" required>
                </div>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Create Project</button>
            </form>
        </div>

    </div>
@endsection