@extends('layouts.app')

@section('title', 'Notifications')
@section('heading', 'Payment Notifications')

@section('content')
<div class="space-y-6">

    {{-- RED (urgent) --}}
    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-bold text-red-700 mb-3">Urgent (Red)</h2>

        @forelse($red as $n)
            <div class="border rounded p-3 mb-3">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-semibold">{{ $n->monthlyOfflineCost->category->category ?? 'Unknown Category' }}</div>
                        <div class="text-sm text-gray-600">
                            Amount: {{ $n->monthlyOfflineCost->amount ?? 'N/A' }} |
                            Last date: {{ $n->monthlyOfflineCost->last_date ?? 'N/A' }} |
                            Days left: {{ $n->days_left }}
                        </div>
                        <div class="mt-1 text-sm text-gray-700">{{ $n->monthlyOfflineCost->description }}</div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-600">No red notifications.</p>
        @endforelse

        <div class="mt-2">
            {{ $red->appends(request()->except('red_page'))->links() }}
        </div>
    </div>

    {{-- YELLOW (warning) --}}
    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-bold text-yellow-700 mb-3">Warning (Yellow)</h2>

        @forelse($yellow as $n)
            <div class="border rounded p-3 mb-3">
                <div class="flex justify-between">
                    <div>
                        <div class="font-semibold">{{ $n->monthlyOfflineCost->category->category ?? 'Unknown Category' }}</div>
                        <div class="text-sm text-gray-600">
                            Amount: {{ $n->monthlyOfflineCost->amount ?? 'N/A' }} |
                            Last date: {{ $n->monthlyOfflineCost->last_date ?? 'N/A' }} |
                            Days left: {{ $n->days_left }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-600">No yellow notifications.</p>
        @endforelse

        <div class="mt-2">
            {{ $yellow->appends(request()->except('yellow_page'))->links() }}
        </div>
    </div>

    {{-- GREEN (info) --}}
    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-bold text-green-700 mb-3">Upcoming (Green)</h2>

        @forelse($green as $n)
            <div class="border rounded p-3 mb-3">
                <div class="flex justify-between">
                    <div>
                        <div class="font-semibold">{{ $n->monthlyOfflineCost->category->category ?? 'Unknown Category' }}</div>
                        <div class="text-sm text-gray-600">
                            Amount: {{ $n->monthlyOfflineCost->amount ?? 'N/A' }} |
                            Last date: {{ $n->monthlyOfflineCost->last_date ?? 'N/A' }} |
                            Days left: {{ $n->days_left }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-600">No green notifications.</p>
        @endforelse

        <div class="mt-2">
            {{ $green->appends(request()->except('green_page'))->links() }}
        </div>
    </div>

</div>
@endsection
