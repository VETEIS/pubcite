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
        
        <!-- Mobile-Only Portrait Orientation Lock -->
        <style>
            /* Mobile-only orientation lock - desktop completely unaffected */
            @media screen and (max-width: 768px) and (orientation: landscape) {
                body {
                    transform: rotate(90deg);
                    transform-origin: left top;
                    width: 100vh;
                    height: 100vw;
                    overflow-x: hidden;
                    position: absolute;
                    top: 100%;
                    left: 0;
                }
                
                .mobile-orientation-message {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 100vh;
                    width: 100vw;
                    background: linear-gradient(135deg, #8B1538, #A91B47);
                    color: white;
                    font-family: 'Figtree', sans-serif;
                    text-align: center;
                    position: fixed;
                    top: 0;
                    left: 0;
                    z-index: 9999;
                }
                
                .mobile-orientation-message h2 {
                    font-size: 1.5rem;
                    font-weight: 600;
                    margin-bottom: 1rem;
                }
                
                .mobile-orientation-message p {
                    font-size: 1rem;
                    opacity: 0.9;
                }
                
                .mobile-orientation-message svg {
                    width: 4rem;
                    height: 4rem;
                    margin-bottom: 1rem;
                    animation: mobile-rotate 2s linear infinite;
                }
                
                @keyframes mobile-rotate {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
            }
            
            /* Hide message on desktop and mobile portrait */
            @media screen and (min-width: 769px), screen and (max-width: 768px) and (orientation: portrait) {
                .mobile-orientation-message {
                    display: none;
                }
            }
        </style>
        


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
        
        <!-- Clear sessionStorage on logout -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Clear sessionStorage when any logout form is submitted
                document.addEventListener('submit', function(e) {
                    if (e.target.action && e.target.action.includes('logout')) {
                        sessionStorage.removeItem('privacy_accepted');
                    }
                });
            });
        </script>
    </head>
    <body class="font-sans antialiased bg-white">
        <!-- Mobile-Only Portrait Orientation Message -->
        <div class="mobile-orientation-message">
            <div>
                <svg class="mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <h2>Please Rotate Your Device</h2>
                <p>This site is optimized for portrait mode only.<br>Please rotate your device to continue.</p>
            </div>
        </div>
        
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
        
        <!-- Loading System - Load after DOM is ready -->
        <script src="{{ asset('js/loading.js') }}"></script>
    </body>
</html>
