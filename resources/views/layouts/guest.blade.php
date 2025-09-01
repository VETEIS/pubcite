<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Force HTTPS -->
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

        <!-- Preload -->
        <link rel="preload" as="image" href="/images/spjrd.png" type="image/png">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Fallback when Vite assets are not available -->
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

        <!-- Styles -->
        @livewireStyles
        <style>[x-cloak]{display:none!important}</style>
    </head>
    <body class="font-sans antialiased bg-white text-gray-900">
        <div class="min-h-screen bg-white relative">
            <!-- SVG Waves Background Overlay -->
            <div class="pointer-events-none absolute inset-0 z-0 select-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 1367 768" preserveAspectRatio="xMidYMid slice">
                    <defs>
                        <!-- Background gradient -->
                        <linearGradient id="bg_grad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#ffffff"/>
                            <stop offset="100%" stop-color="#fff5f5"/>
                        </linearGradient>

                        <!-- Wave gradients -->
                        <linearGradient id="grad0" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#f8e6e6"/>
                            <stop offset="100%" stop-color="#f3d1d1"/>
                        </linearGradient>
                        <linearGradient id="grad1" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#e7a8a8"/>
                            <stop offset="100%" stop-color="#de8c8c"/>
                        </linearGradient>
                        <linearGradient id="grad2" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#d16c6c"/>
                            <stop offset="100%" stop-color="#c05050"/>
                        </linearGradient>
                        <linearGradient id="grad3" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#b22222"/>
                            <stop offset="100%" stop-color="#8b0000"/>
                        </linearGradient>
                    </defs>

                    <!-- Background -->
                    <rect width="1367" height="768" fill="url(#bg_grad)"/>

                    <!-- Non-parallel, asymmetric waves -->
                    <path d="M0,100 C200,20 900,250 1367,140 L1367,768 L0,768 Z" fill="url(#grad0)"/>
                    <path d="M0,300 C500,500 1000,150 1367,280 L1367,768 L0,768 Z" fill="url(#grad1)"/>
                    <path d="M0,480 C250,400 1100,600 1367,500 L1367,768 L0,768 Z" fill="url(#grad2)"/>
                    <path d="M0,680 C600,800 1200,650 1367,700 L1367,768 L0,768 Z" fill="url(#grad3)"/>
                </svg>
            </div>

            <!-- Navbar -->
            <nav class="bg-maroon-800 border-b border-maroon-900 fixed top-0 left-0 w-full z-50 shadow-lg">
                <div class="px-6">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center gap-3">
                        <img src="/images/usep.png" alt="USEP Logo" class="h-10 w-10 object-contain rounded-full" loading="eager" />
                            <a href="{{ url('/') }}" class="text-white text-lg font-semibold tracking-wide whitespace-nowrap hover:text-maroon-200 transition duration-150">
                                PubCite
                            </a>
                            @if (View::exists('components.breadcrumbs'))
                            <span class="flex items-center justify-center ml-2">
                                    @include('components.breadcrumbs', ['crumbs' => $breadcrumbs ?? null, 'inline' => true])
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 sm:gap-4">
                        @auth
                            
                        @else
                            
                        @endauth

                        <!-- Quick Links Dropdown -->
                        <div class="relative" x-data="{ open: false }" x-init="open=false" @keydown.escape.window="open=false" @click.away="open=false">
                            <button @click="open = !open" type="button" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg font-semibold text-sm text-white/90 hover:text-white hover:bg-white/10 transition" x-cloak>
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-1.414 1.414a4 4 0 01-5.656 0l-1.414-1.414a4 4 0 010-5.656M10.172 13.828a4 4 0 010-5.656l1.414-1.414a4 4 0 015.656 0l1.414 1.414a4 4 0 010 5.656" /></svg>
                                Quick Links
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute right-0 mt-2 w-64 bg-white/95 backdrop-blur border border-white/40 rounded-lg shadow-xl overflow-hidden z-50" x-cloak>
                                <div class="py-1">
                                    <a href="https://journal.usep.edu.ph/index.php/Southeastern_Philippines_Journal/index" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 px-3 py-2 text-sm text-maroon-900 hover:bg-maroon-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7" /><path stroke-linecap="round" stroke-linejoin="round" d="M16 3v4M8 3v4M4 11h16" /></svg>
                                        <span>SPJRD</span>
                                    </a>
                                    <a href="https://docs.google.com/spreadsheets/d/1bwf9eZvtI5HO7w0HdMRDujQULfdwKJNU4Ieb535sUdk/edit?gid=451510018#gid=451510018" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 px-3 py-2 text-sm text-maroon-900 hover:bg-maroon-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7" /><path stroke-linecap="round" stroke-linejoin="round" d="M16 3v4M8 3v4M4 11h16" /></svg>
                                        <span>Scopus (Suggested Journals)</span>
                                    </a>
                                    <a href="https://docs.google.com/spreadsheets/d/1_54NTUdRE4y9QVB01p9SHF_cEPllajyyM3siyBFWfRs/edit?gid=451510018#gid=451510018" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 px-3 py-2 text-sm text-maroon-900 hover:bg-maroon-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20l9-5-9-5-9 5 9 5zm0 0V4" /></svg>
                                        <span>Web of Science (Suggested)</span>
                                    </a>
                                    <a href="https://docs.google.com/spreadsheets/d/1XT-2QD6ZYK4Vl5JPWGoDAFFGu0j6SYXhxQbcvidIrAI/edit?gid=572855311#gid=572855311" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 px-3 py-2 text-sm text-maroon-900 hover:bg-maroon-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8" /></svg>
                                        <span>ACI (Suggested)</span>
                                    </a>
                                    <a href="https://docs.google.com/spreadsheets/d/1qeRfbWQVB2fodnirzIK5Znql5nliLAPVtK4xXRS5xSY/edit?gid=451510018#gid=451510018" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 px-3 py-2 text-sm text-maroon-900 hover:bg-maroon-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2" /><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8" /></svg>
                                        <span>Peer Review</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Announcements Dropdown -->
                        <div class="relative" x-data="{ open: false }" x-init="open=false" @keydown.escape.window="open=false" @click.away="open=false">
                            <button @click="open = !open" type="button" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg font-semibold text-sm text-white/90 hover:text-white hover:bg-white/10 transition" x-cloak>
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                Announcements
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute right-0 mt-2 w-80 bg-white/95 backdrop-blur border border-white/40 rounded-lg shadow-xl overflow-hidden z-50" x-cloak>
                                <div class="py-1">
                                    <div class="px-3 py-2 border-b border-gray-100">
                                        <h4 class="text-sm font-semibold text-maroon-900">Latest Updates</h4>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto">
                                        <div class="px-3 py-2 hover:bg-maroon-50">
                                            <div class="text-sm font-medium text-maroon-900">New Publication Incentive Guidelines</div>
                                            <div class="text-xs text-gray-600 mt-1">Updated guidelines for 2024 publication incentives are now available.</div>
                                            <div class="text-xs text-gray-500 mt-1">2 hours ago</div>
                                        </div>
                                        <div class="px-3 py-2 hover:bg-maroon-50">
                                            <div class="text-sm font-medium text-maroon-900">Research Workshop Series</div>
                                            <div class="text-xs text-gray-600 mt-1">Join our upcoming workshop series on research methodology and academic writing techniques.</div>
                                            <div class="text-xs text-gray-500 mt-1">1 day ago</div>
                                        </div>
                                        <div class="px-3 py-2 hover:bg-maroon-50">
                                            <div class="text-sm font-medium text-maroon-900">Journal Indexing Updates</div>
                                            <div class="text-xs text-gray-600 mt-1">Latest updates on Scopus and Web of Science indexing for USeP publications.</div>
                                            <div class="text-xs text-gray-500 mt-1">3 days ago</div>
                                        </div>
                                        <div class="px-3 py-2 hover:bg-maroon-50">
                                            <div class="text-sm font-medium text-maroon-900">Call for Papers</div>
                                            <div class="text-xs text-gray-600 mt-1">Submit your research papers for the upcoming special issue on sustainable development.</div>
                                            <div class="text-xs text-gray-500 mt-1">1 week ago</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg font-semibold text-sm text-white bg-white/10 border border-white/20 hover:bg-white/20 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75L12 3l9 6.75V20a1.5 1.5 0 01-1.5 1.5H15a1.5 1.5 0 01-1.5-1.5v-4.5H10.5V20A1.5 1.5 0 019 21.5H4.5A1.5 1.5 0 013 20V9.75z" />
                                    </svg>
                                    Dashboard
                                </a>
                            @endauth
                        @endif
                        </div>
                    </div>
                </div>
            </nav>
            
            <div class="relative z-10 pt-16">
                <main>
                    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center">
                {{ $slot }}
                    </div>
                </main>
            </div>
            
            <!-- Footer below viewport -->
            <div class="relative z-10">
                <x-footer />
            </div>
        </div>

        <!-- Global Notification System -->
        <x-global-notifications />

        <script>window.livewireScriptConfig = { alpine: false }</script>
        @livewireScripts
    </body>
</html>
