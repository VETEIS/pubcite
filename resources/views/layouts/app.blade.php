<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Security Headers -->
        <meta http-equiv="X-Content-Type-Options" content="nosniff">
        <meta http-equiv="X-XSS-Protection" content="1; mode=block">
        <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
        
        <!-- Content Security Policy handled by SecurityHeaders middleware -->

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Preload critical images with proper attributes -->
        <link rel="preload" as="image" href="/images/spjrd.png" type="image/png">

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

        <!-- Livewire Styles -->
        @livewireStyles
        
        <!-- Alpine.js x-cloak styles -->
        <style>[x-cloak]{display:none!important}</style>
    </head>
    <body class="font-sans antialiased bg-white">
        <div class="min-h-screen bg-white">
            <!-- Page Content -->
            <main class="bg-white">
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        <!-- Global Notification System -->
        <x-global-notifications />

        <!-- Livewire Scripts - Single Instance -->
        @livewireScripts
    </body>
</html>
