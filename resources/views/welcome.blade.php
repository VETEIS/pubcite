<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'USeP Publications Unit') }}</title>

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
                            <span class="text-white text-lg font-semibold tracking-wide whitespace-nowrap">USeP Publications Unit</span>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-4">
                            <img src="/images/usep.png" alt="USEP Logo" class="h-10 w-10 object-contain" />
                            <img src="/images/spjrd.png" alt="SPJRD Logo" class="h-10 w-10 object-contain rounded-full" />
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="relative z-10 pt-16 min-h-screen flex items-center justify-center">
                <div class="w-full sm:max-w-4xl mx-auto px-6 py-6 bg-white shadow-md overflow-hidden sm:rounded-lg">
                    <!-- Welcome Header -->
                    <div class="text-center mb-6">
                        <div class="flex justify-center mb-4">
                            <img src="/images/spjrd.png" alt="SPJRD Logo" class="rounded-full object-cover" style="width: 60px; height: 60px;">
                        </div>
                        <h1 class="text-3xl font-bold text-maroon-800 mb-2">Welcome to USeP Publications Unit</h1>
                        <p class="text-lg text-gray-600 mb-3">Southeastern Philippines Journal of Research and Development</p>
                        <div class="w-20 h-1 bg-maroon-600 mx-auto"></div>
                    </div>

                    <!-- Welcome Message -->
                    <div class="prose prose-sm mx-auto text-center mb-6">
                        <p class="text-gray-700 leading-relaxed">
                            Welcome to the University of Southeastern Philippines Publications Unit. 
                            We are dedicated to fostering academic excellence through the publication 
                            of high-quality research and scholarly works.
                        </p>
                    </div>

                    <!-- Features Grid - More Compact -->
                    <div class="grid md:grid-cols-3 gap-4 mb-6">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-maroon-800 mb-1">Research Publications</h3>
                            <p class="text-gray-600 text-xs">Submit and manage your research publications.</p>
                        </div>

                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-maroon-800 mb-1">Academic Excellence</h3>
                            <p class="text-gray-600 text-xs">Maintain high standards of academic rigor.</p>
                        </div>

                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-maroon-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-maroon-800 mb-1">Community Impact</h3>
                            <p class="text-gray-600 text-xs">Contribute to local and global development.</p>
                        </div>
                    </div>

                    <!-- Call to Action -->
                    <div class="text-center">
            @if (Route::has('login'))
                    @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-6 py-3 bg-maroon-700 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-maroon-800 focus:bg-maroon-800 active:bg-maroon-900 focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150">
                                    Go to Dashboard
                        </a>
                    @else
                                <div class="space-y-3">
                                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-maroon-700 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-maroon-800 focus:bg-maroon-800 active:bg-maroon-900 focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150">
                            Request Submission
                        </a>
                                    <div class="text-sm text-gray-500">
                                        New to the platform? 
                                        <a href="{{ route('register') }}" class="text-maroon-600 hover:text-maroon-800 underline">Create an account</a>
                                    </div>
                                </div>
                    @endauth
            @endif
                    </div>
                </div>
                </div>
        </div>

        @livewireScripts
    </body>
</html> 