
@php
    // Determine the border color based on the notification level
    $borderColorClass = [
        'red' => 'border-red-500',
        'blue' => 'border-blue-500',
        'green' => 'border-green-500',
    ][$item->level] ?? 'border-gray-300';
@endphp

<div class="bg-white p-4 rounded-lg shadow-sm border-l-4 {{ $borderColorClass }} flex justify-between items-center mb-3">
    <div>
        <p class="text-sm text-gray-800 font-semibold">{{ $item->message }}</p>
        <p class="text-xs text-gray-500 mt-1">
            Due Date: {{ \Carbon\Carbon::parse($item->due_date)->format('M d, Y') }}
            
            @if($item->days_left < 0)
                <span class="font-bold text-red-600"> ({{ abs($item->days_left) }} days overdue)</span>
            @elseif($item->days_left == 0)
                <span class="font-bold text-blue-600"> (Due Today)</span>
            @else
                <span class="font-bold text-green-700"> ({{ $item->days_left }} days left)</span>
            @endif
        </p>
    </div>
    
    @if($item->action_route && Route::has($item->action_route))
        {{-- 
            --- FIX IS HERE ---
            We use the null coalescing operator (?? []) to ensure that if json_decode returns null,
            we pass an empty array [] to the route() helper, which is safe.
        --}}
        @php
            $routeParams = json_decode($item->action_params, true) ?? [];
        @endphp

        <a href="{{ route($item->action_route, $routeParams) }}" 
           class="ml-4 px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-md hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Take Action
        </a>
    @endif
</div>


