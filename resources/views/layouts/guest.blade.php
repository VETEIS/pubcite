<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Security: Force HTTPS and prevent mixed content -->
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
        <meta http-equiv="X-Content-Type-Options" content="nosniff">
        <meta http-equiv="X-XSS-Protection" content="1; mode=block">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body>
        <div class="min-h-screen bg-white relative font-sans text-gray-900 antialiased">
            <!-- Eagle Main Background Overlay (behind everything) -->
            <div class="pointer-events-none absolute inset-0 z-0 select-none">
                <img src="/images/eagle.jpg" alt="Eagle" class="w-full h-full object-cover opacity-30" draggable="false" />
            </div>

            <!-- Simple Maroon Navbar for Consistency (overlay is behind this) -->
            <nav class="bg-maroon-800 border-b border-maroon-900 fixed top-0 left-0 w-full z-50 shadow-lg">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <a href="{{ url('/') }}" class="text-white text-lg font-semibold tracking-wide whitespace-nowrap hover:text-maroon-200 transition duration-150">
                                USeP Publications Unit
                            </a>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-4">
                            <img src="/images/usep.png" alt="USEP Logo" class="h-10 w-10 object-contain" />
                            <img src="/images/spjrd.png" alt="SPJRD Logo" class="h-10 w-10 object-contain rounded-full" />
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Main Content Area - fills viewport, footer is below -->
            <div class="relative z-10 pt-16 min-h-screen flex items-center justify-center py-6 sm:py-12">
                {{ $slot }}
            </div>
            
            <!-- Footer - positioned below viewport -->
            <div class="relative z-10">
                <x-footer />
            </div>
        </div>

        <script>window.livewireScriptConfig = { alpine: false }</script>
        @livewireScripts
    </body>
</html>
