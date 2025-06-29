<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Security: Force HTTPS only in production -->
        @if (app()->environment('production'))
            <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https:;">
        @else
            <meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' 'unsafe-eval' http: https:; style-src 'self' 'unsafe-inline' http: https:; img-src 'self' data: http: https:;">
        @endif
        <meta http-equiv="X-Content-Type-Options" content="nosniff">
        <meta http-equiv="X-XSS-Protection" content="1; mode=block">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Preload critical images to reduce flickering -->
        <link rel="preload" as="image" href="/images/eagle.jpg" fetchpriority="high">
        <link rel="preload" as="image" href="/images/spjrd.png" fetchpriority="high">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Fallback for when Vite assets are not available -->
        <script>
            // Check if Vite assets loaded, if not, load built assets
            setTimeout(function() {
                if (!window.Alpine && !document.querySelector('link[href*="app.css"]')) {
                    console.log('Vite assets not loaded, loading built assets...');
                    
                    // Load built CSS
                    var link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = '/build/assets/app-Dw9KLaam.css';
                    document.head.appendChild(link);
                    
                    // Load built JS
                    var script = document.createElement('script');
                    script.src = '/build/assets/app-DaBYqt0m.js';
                    script.onload = function() {
                        console.log('Built assets loaded successfully');
                        if (window.Alpine) {
                            window.Alpine.start();
                        }
                    };
                    script.onerror = function() {
                        console.error('Built assets failed to load');
                        document.body.innerHTML = '<div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;"><h1>Application Loading Error</h1><p>The application assets failed to load. Please refresh the page or contact support.</p><p>Error: Assets not found</p><button onclick="location.reload()" style="padding: 10px 20px; background: #dc2626; color: white; border: none; border-radius: 5px; cursor: pointer;">Refresh Page</button></div>';
                    };
                    document.head.appendChild(script);
                }
            }, 2000);
        </script>

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="flex flex-col min-h-screen">
        <div class="flex-1 min-h-0 bg-white relative font-sans text-gray-900 antialiased">
            <!-- Eagle Main Background Overlay (behind everything) -->
            <div class="pointer-events-none absolute inset-0 z-0 select-none">
                <img src="/images/eagle.jpg" alt="Eagle" class="w-full h-full object-cover opacity-30" draggable="false" loading="eager" fetchpriority="high" />
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
                            <img src="/images/usep.png" alt="USEP Logo" class="h-10 w-10 object-contain" loading="eager" />
                            <img src="/images/spjrd.png" alt="SPJRD Logo" class="h-10 w-10 object-contain rounded-full" loading="eager" />
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Main Content Area - fills viewport, footer is below -->
            <div class="relative z-10 pt-16 flex-1 flex items-center justify-center py-6 sm:py-12">
                {{ $slot }}
            </div>
            
            <!-- Footer - positioned below viewport -->
            <div class="relative z-10 w-full">
                <x-footer />
            </div>
        </div>

        <script>window.livewireScriptConfig = { alpine: false }</script>
        @livewireScripts
    </body>
</html>
