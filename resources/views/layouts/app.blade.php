<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">


        <!-- Fonts -->
        <!-- <link rel="preload" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> -->

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>[x-cloak]{ display:none !important; }</style>
        
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="w-full min-h-screen flex">
            @include('layouts.navigation')

            <main class="w-4/5 p-0 flex flex-col min-h-screen">
            
                <header class="flex items-center justify-between px-8 py-6 border-b border-gray-200 bg-white">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Stag Prime Cost Management</h1>
                        <p class="mt-2 text-gray-600">@yield('heading')</p>
                    </div>

                    <div class="flex items-center gap-4 text-white bg-green-600 border border-gray-200 rounded-full px-4 py-2">
                        <p><a href="{{route('notifications.index')}}">Notification</a></p>
                    </div>

                    @auth
                        <div class="relative">
                            <button onclick="toggleDropdown()" 
                                class="px-4 py-2 bg-white border rounded text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                {{ Auth::user()->name }}
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div id="dropdownMenu" 
                                class="hidden absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg z-50">
                                <!-- <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    Profile
                                </a> -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </header>

                <section class="flex-1 px-4 md:px-8 py-4 md:py-6 bg-gray-50">
                    @yield('content')
                </section>

                <footer class="px-8 py-4 border-t border-gray-200 bg-white text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} Stag Prime Cost Management. All rights reserved.
                </footer>

            </main>
        </div>
    
        <script>
            function toggleDropdown() {
                document.getElementById('dropdownMenu').classList.toggle('hidden');
            }
        </script>
    </body>
</html>



