@extends('layouts.app')

@section('title', 'List | Priority Product/Project')
@section('heading', 'List | Priority Product/Project')

@section('content')
<div class="container">
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded shadow p-4 md:p-6 mt-6 overflow-x-auto">
        <h2 class="text-lg font-semibold mb-4">Priority Products/Projects List</h2>
        <table class="w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-4 py-2 text-left">#</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Quantity</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Amount</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Description</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($priorities as $priority)
                    <tr>
                        <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                        <td class="border px-4 py-2">{{ $priority->name }}</td>
                        <td class="border px-4 py-2">{{ $priority->quantity }}</td>
                        <td class="border px-4 py-2">{{ number_format($priority->amount,2) }}</td>
                        <td class="border px-4 py-2">{{ $priority->description }}</td>
                        <td class="border px-4 py-2">
                            @if($priority->is_purchased)
                                <span class="badge bg-success">Purchased/Started</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2">
                            <a href="{{ route('priority.edit', $priority->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            @if(!$priority->is_purchased)
                                <form action="{{ route('priority.purchase', $priority->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success">Purchased/Started</button>
                                </form>
                            @endif
                            <form action="{{ route('priority.delete', $priority->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure! you want to delete this?')" class="btn btn-sm btn-danger">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            
        </table>
    </div>
    
   
    {{ $priorities->links() }}
</div>
@endsection
