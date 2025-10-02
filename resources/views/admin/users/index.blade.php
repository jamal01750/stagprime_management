@extends('layouts.app')
@section('title', 'User Management | StagPrime Cost Management')
@section('heading', 'User Management')

@section('content')
<!-- New User Role creation  -->
<!-- <div class="mb-5 flex justify-between items-center bg-gray-300 p-4 rounded">
    
    <form method="POST" action="{{ route('admin.addrole') }}" class="space-y-3 md:space-y-4 w-full md:w-auto">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Add New User Role</label>
            <input type="text" name="role_name" class="mt-1 px-2 block w/3 border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-700" placeholder="Enter role name">
            @if ($errors->has('role_name'))
                <span class="text-red-600 text-xs">{{ $errors->first('role_name') }}</span>
            @endif
        </div>
        <button type="submit" class="px-1 py-1 bg-green-600 text-white rounded hover:bg-green-700 font-normal">Add New Role</button>
    </form>
</div> -->

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<div class="mt-5 mb-3">
    <button class="px-1 py-1 bg-green-600 text-white rounded hover:bg-green-700 font-normal">
        <a href="{{ route('admin.users.create') }}">+ Create New User</a>
    </button>
</div>

<table class="w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200 items-center text-left mt-5">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>
    @foreach($users as $user)
    <tr>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ ucfirst($user->role) }}</td>
        <td>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                @csrf
                @method('DELETE')
                <button type="submit">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@endsection
