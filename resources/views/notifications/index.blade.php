@extends('layouts.app')

@section('title', 'All Notifications')
@section('heading', 'All Notifications')

@section('content')

<div class="container mx-auto p-4">

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Red Notifications -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-red-600 mb-4 border-b-2 border-red-500 pb-2">Urgent (Due in 3 days or Overdue)</h2>
        @if($red->count() > 0)
            <div class="space-y-4">
                @foreach($red as $notification)
                    @include('notifications._notification_item', ['item' => $notification, 'borderColor' => 'border-red-500'])
                @endforeach
            </div>
            <div class="mt-4">
                {{ $red->links() }}
            </div>
        @else
            <p class="text-gray-500">No urgent notifications.</p>
        @endif
    </div>

    <!-- blue Notifications -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-blue-600 mb-4 border-b-2 border-blue-500 pb-2">Warning (Due in 4-7 days)</h2>
        @if($blue->count() > 0)
            <div class="space-y-4">
                @foreach($blue as $notification)
                     @include('notifications._notification_item', ['item' => $notification, 'borderColor' => 'border-blue-500'])
                @endforeach
            </div>
             <div class="mt-4">
                {{ $blue->links() }}
            </div>
        @else
            <p class="text-gray-500">No warnings.</p>
        @endif
    </div>

    <!-- Green Notifications -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-green-600 mb-4 border-b-2 border-green-500 pb-2">Notice (Due in 8-10 days)</h2>
        @if($green->count() > 0)
            <div class="space-y-4">
                @foreach($green as $notification)
                     @include('notifications._notification_item', ['item' => $notification, 'borderColor' => 'border-green-500'])
                @endforeach
            </div>
             <div class="mt-4">
                {{ $green->links() }}
            </div>
        @else
            <p class="text-gray-500">No notices at the moment.</p>
        @endif
    </div>

    {{-- ✅ Priority Notifications --}}
    <h3 class="mt-4 mb-2 text-green-700 font-bold">Priority Products/Projects</h3>
    
    {{-- Priority --}}
    @foreach($priority as $item)
        <div class="alert alert-success">[Priority] <strong>{{ $item->product->name }}</strong> is ready for purchase/start. 
                (Amount: {{ number_format($item->product->amount,2) }})
        </div>
    @endforeach
    {{ $priority->links() }}

</div>
@endsection


