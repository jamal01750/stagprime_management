@extends('layouts.app')

@section('title', 'Edit Form| Priority Product/Project')
@section('heading', 'Edit Form | Priority Product/Project')

@section('content')
<div class="container">
    <h2>Edit Priority Product/Project</h2>

    <form action="{{ route('priority.update', $priority->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $priority->name) }}" required>
        </div>
        <div class="mb-3">
            <label>Quantity *</label>
            <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $priority->quantity) }}" required>
        </div>
        <div class="mb-3">
            <label>Amount *</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $priority->amount) }}" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ old('description', $priority->description) }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('priority.list') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
