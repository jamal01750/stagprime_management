@extends('layouts.app')

@section('title', 'Notifications')
@section('heading', 'Notifications')

@section('content')
<div class="container">
    <h2>Notifications</h2>

    {{-- ✅ Offline Payment Notifications --}}
    <h3 class="mt-4 mb-2 text-red-700 font-bold">Offline Payment Notifications</h3>

    {{-- Red --}}
    @foreach($red as $item)
        <div class="alert alert-danger">[Red] {{ $item->monthlyOfflineCost->category->name }} - {{ $item->message }}</div>
    @endforeach
    {{ $red->links() }}

    {{-- Yellow --}}
    @foreach($yellow as $item)
        <div class="alert alert-warning">[Yellow] {{ $item->monthlyOfflineCost->category->name }} - {{ $item->message }}</div>
    @endforeach
    {{ $yellow->links() }}

    {{-- Green --}}
    @foreach($green as $item)
        <div class="alert alert-success">[Green] {{ $item->monthlyOfflineCost->category->name }} - {{ $item->message }}</div>
    @endforeach
    {{ $green->links() }}

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

