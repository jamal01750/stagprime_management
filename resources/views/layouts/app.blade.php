<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Stag Prime')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Defer ensures the script executes after the document is parsed --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- This style hides Alpine elements until they are fully initialized to prevent "flashing" --}}
    <style>[x-cloak] { display: none !important; }</style>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    {{-- This directive handles loading your compiled CSS and JS assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="w-full min-h-screen flex">
        {{-- Side Navigation --}}
        @include('layouts.navigation')

        {{-- Main Content Area --}}
        <main class="w-full p-0 flex flex-col min-h-screen">
            <header class="flex items-center justify-between px-4 md:px-8 py-4 border-b border-gray-200 bg-white shadow-sm">
                {{-- Page Title and Heading --}}
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Stag Prime Cost Management</h1>
                    <p class="mt-1 text-sm text-gray-600">@yield('heading')</p>
                </div>

                <div class="flex items-center space-x-6">
                    <div class="relative" x-data="{ open: false }">
                        <a href="{{ route('notifications.index') }}" 
                           class="relative flex items-center" 
                           @mouseenter="open = true" 
                           @mouseleave="open = false"
                           aria-label="View notifications">
                            
                            {{-- IMPROVED: Correctly formatted Heroicon SVG for the bell --}}
                            <svg class="h-6 w-6 text-gray-600 hover:text-green-600 transition-colors duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>

                            @php
                                // Best practice: Check if user is authenticated before querying relationships
                                if (Auth::check()) {
                                    $allCount = \App\Models\Notification::where('status', 'active')->count();
                                    $priorityCount = \App\Models\PriorityNotification::where('is_active', true)->count();
                                    $totalNotifications = $allCount + $priorityCount;
                                } else {
                                    $totalNotifications = 0;
                                }
                            @endphp

                            @if($totalNotifications >= 0)
                                <span class="absolute -top-2 -right-2 flex h-5 w-5 items-center justify-center bg-red-600 text-white text-xs font-bold rounded-full">
                                    {{ $totalNotifications }}
                                </span>
                            @endif
                        </a>
                        
                        <div x-show="open" 
                             x-cloak 
                             x-transition
                             class="absolute bottom-[-35px] left-1/2 -translate-x-1/2 px-2 py-1 text-xs text-white bg-gray-800 rounded-md whitespace-nowrap pointer-events-none">
                            Notifications
                        </div>
                    </div>

                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                                    aria-haspopup="true" 
                                    :aria-expanded="open"
                                    aria-label="User menu">
                                {{-- IMPROVED: Correctly formatted Heroicon SVG for the user avatar --}}
                                <svg class="h-8 w-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>

                            {{-- Dropdown Panel --}}
                            <div x-show="open"
                                 x-cloak
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                
                                <div class="px-4 py-3 border-b">
                                    <p class="text-sm text-gray-800 font-semibold truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </header>

            <section class="flex-1 px-4 md:px-8 py-6 bg-gray-50">
                @yield('content')
            </section>

            <footer class="px-4 md:px-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-white">
                &copy; {{ date('Y') }} Stag Prime Cost Management. All rights reserved.
            </footer>
        </main>
    </div>

    
</body>
</html>