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

        <!-- Scripts and Styles -->
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

        <!-- Quicklink prefetch for instant navigation -->
        <script src="https://unpkg.com/quicklink@2.2.0/dist/quicklink.umd.js"></script>
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            quicklink.listen({
              ignores: [
                url => url.includes('logout'),
                url => url.startsWith('mailto:')
              ]
            });
          });
        </script>

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <!-- Eagle Main Background Overlay (only on white background) -->
        <div class="min-h-screen bg-white relative">
            <div class="pointer-events-none absolute inset-0 z-0 select-none bg-white">
                <img src="/images/waves.png" alt="Waves" class="w-full h-full object-contain mx-auto my-auto" style="position:absolute; top:0; left:0; right:0; bottom:0; margin:auto;" draggable="false" loading="eager" fetchpriority="high" />
            </div>

            <x-banner />

            <div class="relative z-10 pt-16">
                @livewire('navigation-menu')

                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            </div>
            
            <!-- Footer - positioned below viewport -->
            <div class="relative">
                <x-footer />
            </div>
        </div>

        @stack('modals')

        <!-- Global Notification System -->
        <x-global-notifications />

        <script>window.livewireScriptConfig = { alpine: true }</script>
        @livewireScripts
    </body>
</html>
