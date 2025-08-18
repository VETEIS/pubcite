<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'USeP Publication Unit') }}</title>

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
            <!-- Waves Main Background Overlay (behind everything) -->
            <div class="pointer-events-none absolute inset-0 z-0 select-none bg-white">
                <img src="/images/waves.png" alt="Waves" class="w-full h-full object-contain mx-auto my-auto" style="position:absolute; top:0; left:0; right:0; bottom:0; margin:auto;" draggable="false" />
            </div>

            <!-- Simple Maroon Navbar for Consistency (overlay is behind this) -->
            <nav class="bg-maroon-800 border-b border-maroon-900 fixed top-0 left-0 w-full z-50 shadow-lg">
                <div class="px-6">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center gap-3">
                            <img src="/images/usep.png" alt="USEP Logo" class="h-10 w-10 object-contain rounded-full" />
                            <span class="text-white text-lg font-semibold tracking-wide whitespace-nowrap">USeP Publication Unit</span>
                            @if (View::exists('components.breadcrumbs'))
                                <span class="flex items-center justify-center ml-6">
                                    @include('components.breadcrumbs', ['crumbs' => $breadcrumbs ?? null, 'inline' => true])
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 sm:gap-4">
                            <!-- <img src="/images/usep.png" alt="USEP Logo" class="h-10 w-10 object-contain" /> -->
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="relative z-10 pt-16">
                <main>
                    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center">
                        <!-- Floating Side Cards (hidden on small screens) -->
                        <div class="hidden lg:block absolute left-0 z-20 pl-6" id="left-floating-card-container">
                            <div class="w-64 h-full flex flex-col justify-center">
                                <div class="side-floating-card h-full bg-white/40 backdrop-blur-md border border-white/40 rounded-xl shadow-2xl p-6 flex flex-col items-center justify-center opacity-0">
                                    <!-- Icon -->
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-8 h-8 text-maroon-800 drop-shadow-lg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 11-12 0 6 6 0 0112 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-1a4 4 0 00-4-4H7" />
                                        </svg>
                                        <h2 class="text-xl font-bold text-maroon-800">Quick Links</h2>
                                    </div>
                                    <div class="flex flex-col gap-2 w-full flex-1 justify-center">
                                        <div class="flex items-center gap-2 bg-white/60 backdrop-blur rounded-2xl px-3 py-1 shadow group hover:scale-105 hover:shadow-lg transition-all cursor-pointer">
                                            <svg class="w-5 h-5 text-maroon-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7" /><path stroke-linecap="round" stroke-linejoin="round" d="M16 3v4M8 3v4M4 11h16" /></svg>
                                            <span class="font-semibold text-maroon-800">SPJRD</span>
                                            <span class="ml-auto text-xs text-gray-700 font-bold">12</span>
                                        </div>
                                        <div class="flex items-center gap-2 bg-white/60 backdrop-blur rounded-2xl px-3 py-1 shadow group hover:scale-105 hover:shadow-lg transition-all cursor-pointer">
                                            <svg class="w-5 h-5 text-maroon-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7" /><path stroke-linecap="round" stroke-linejoin="round" d="M16 3v4M8 3v4M4 11h16" /></svg>
                                            <span class="font-semibold text-maroon-800">Scopus</span>
                                            <span class="ml-auto text-xs text-gray-700 font-bold">12</span>
                                        </div>
                                        <div class="flex items-center gap-2 bg-white/60 backdrop-blur rounded-2xl px-3 py-1 shadow group hover:scale-105 hover:shadow-lg transition-all cursor-pointer">
                                            <svg class="w-5 h-5 text-maroon-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20l9-5-9-5-9 5 9 5zm0 0V4" /></svg>
                                            <span class="font-semibold text-maroon-800">Web of Science</span>
                                            <span class="ml-auto text-xs text-gray-700 font-bold">8</span>
                                        </div>
                                        <div class="flex items-center gap-2 bg-white/60 backdrop-blur rounded-2xl px-3 py-1 shadow group hover:scale-105 hover:shadow-lg transition-all cursor-pointer">
                                            <svg class="w-5 h-5 text-maroon-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8" /></svg>
                                            <span class="font-semibold text-maroon-800">ACI</span>
                                            <span class="ml-auto text-xs text-gray-700 font-bold">5</span>
                                        </div>
                                        <div class="flex items-center gap-2 bg-white/60 backdrop-blur rounded-2xl px-3 py-1 shadow group hover:scale-105 hover:shadow-lg transition-all cursor-pointer">
                                            <svg class="w-5 h-5 text-maroon-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2" /><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8" /></svg>
                                            <span class="font-semibold text-maroon-800">Peer Review</span>
                                            <span class="ml-auto text-xs text-gray-700 font-bold">2</span>
                                        </div>
                                    </div>
                                    <a href="#" class="mt-4 inline-flex items-center gap-1 px-3 py-1 bg-maroon-800 text-white rounded-2xl shadow hover:bg-maroon-900 transition-all text-xs font-semibold group">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                        Expand
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="hidden lg:block absolute right-0 z-20 pr-6" id="right-floating-card-container">
                            <div class="w-64 h-full flex flex-col justify-center">
                                <div class="side-floating-card h-full bg-white/40 backdrop-blur-md border border-white/40 rounded-xl shadow-2xl p-6 flex flex-col justify-between overflow-x-hidden overflow-visible opacity-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-8 h-8 text-maroon-800 drop-shadow-lg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 11-12 0 6 6 0 0112 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-1a4 4 0 00-4-4H7" />
                                        </svg>
                                        <h2 class="text-xl font-bold text-maroon-800">Announcements</h2>
                                    </div>
                                    <div class="flex-1 overflow-y-auto w-full flex flex-col gap-0.5 relative px-0" style="overflow: visible;">
                                         <div class="flex items-center gap-2 bg-white/80 rounded-xl p-2 w-full border-b border-maroon-800/10 last:border-b-0 group hover:bg-white/90 transition-all cursor-pointer min-h-[3.5rem]">
                                             <svg class="w-5 h-5 text-maroon-800 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                             <div class="flex flex-col w-full">
                                                 <span class="text-maroon-800 text-xs font-semibold leading-tight">Jul 15</span>
                                                 <span class="text-xs text-gray-700 leading-snug line-clamp-3">Incentive Application Deadline extended to July 31.</span>
                                             </div>
                                         </div>
                                         <div class="flex items-center gap-2 bg-white/80 rounded-xl p-2 w-full border-b border-maroon-800/10 last:border-b-0 group hover:bg-white/90 transition-all cursor-pointer min-h-[3.5rem]">
                                             <svg class="w-5 h-5 text-maroon-800 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                             <div class="flex flex-col w-full">
                                                 <span class="text-maroon-800 text-xs font-semibold leading-tight">Jul 10</span>
                                                 <span class="text-xs text-gray-700 leading-snug line-clamp-3">New journal indexed in Scopus!</span>
                                             </div>
                                         </div>
                                         <div class="flex items-center gap-2 bg-white/80 rounded-xl p-2 w-full border-b border-maroon-800/10 last:border-b-0 group hover:bg-white/90 transition-all cursor-pointer min-h-[3.5rem]">
                                             <svg class="w-5 h-5 text-maroon-800 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                             <div class="flex flex-col w-full">
                                                 <span class="text-maroon-800 text-xs font-semibold leading-tight">Jul 05</span>
                                                 <span class="text-xs text-gray-700 leading-snug line-clamp-3">Workshop: Research Publishing 101, July 20.</span>
                                             </div>
                                         </div>
                                         <div class="flex items-center gap-2 bg-white/80 rounded-xl p-2 w-full border-b border-maroon-800/10 last:border-b-0 group hover:bg-white/90 transition-all cursor-pointer min-h-[3.5rem]">
                                             <svg class="w-5 h-5 text-maroon-800 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                             <div class="flex flex-col w-full">
                                                 <span class="text-maroon-800 text-xs font-semibold leading-tight">Jul 01</span>
                                                 <span class="text-xs text-gray-700 leading-snug line-clamp-3">Welcome to the new Publication Portal.</span>
                                             </div>
                                         </div>
                                    </div>
                                    <a href="#" class="mt-4 inline-flex items-center gap-1 px-3 py-1 bg-maroon-800 text-white rounded-2xl shadow hover:bg-maroon-900 transition-all text-xs font-semibold group w-auto mx-auto">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                        Expand
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- Main Card -->
                        <div id="main-welcome-card" class="w-full sm:max-w-4xl mx-auto px-6 py-6 bg-white/30 backdrop-blur-md border border-white/40 rounded-xl shadow-xl relative">
                    <!-- Welcome Header -->
                    <div class="text-center mb-6">
                        <div class="flex justify-center mb-4">
                            <img src="/images/usep.png" alt="USeP Logo" class="rounded-full object-cover" style="width: 60px; height: 60px;">
                        </div>
                        <h1 class="text-3xl font-extrabold text-maroon-900 mb-2">Welcome to USeP Publication Unit</h1>
                        <div class="w-40 h-1 bg-maroon-800 mx-auto"></div>
                    </div>

                    <!-- Indexing Logos Row -->
                    <!--
                    <div class="w-full sm:max-w-4xl mx-auto">
                        <div class="bg-white/30 backdrop-blur-md border border-white/40 rounded-xl shadow-xl px-6 py-3 mb-8 mt-6 flex justify-center items-center gap-8">
                            <a href="https://docs.google.com/spreadsheets/d/1bwf9eZvtI5HO7w0HdMRDujQULfdwKJNU4Ieb535sUdk/edit?gid=451510018#gid=451510018" target="_blank" rel="noopener noreferrer" class="group flex flex-col items-center justify-center">
                                <img src="/images/scopus.png" alt="Scopus" class="h-16 w-auto object-contain transition-transform group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-maroon-800/40" />
                                <div class="w-20 h-1 bg-maroon-800 mx-auto mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                            </a>
                            <a href="https://docs.google.com/spreadsheets/d/1_54NTUdRE4y9QVB01p9SHF_cEPllajyyM3siyBFWfRs/edit?gid=451510018#gid=451510018" target="_blank" rel="noopener noreferrer" class="group flex flex-col items-center justify-center">
                                <img src="/images/wos.png" alt="Web of Science" class="h-16 w-auto object-contain transition-transform group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-maroon-800/40" />
                                <div class="w-20 h-1 bg-maroon-800 mx-auto mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                            </a>
                            <a href="https://docs.google.com/spreadsheets/d/1XT-2QD6ZYK4Vl5JPWGoDAFFGu0j6SYXhxQbcvidIrAI/edit?gid=572855311#gid=572855311" target="_blank" rel="noopener noreferrer" class="group flex flex-col items-center justify-center">
                                <img src="/images/aci.png" alt="ACI" class="h-16 w-auto object-contain transition-transform group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-maroon-800/40" />
                                <div class="w-20 h-1 bg-maroon-800 mx-auto mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                            </a>
                            <a href="https://docs.google.com/spreadsheets/d/1qeRfbWQVB2fodnirzIK5Znql5nliLAPVtK4xXRS5xSY/edit?gid=451510018#gid=451510018" target="_blank" rel="noopener noreferrer" class="group flex flex-col items-center justify-center">
                                <img src="/images/peer.png" alt="Peer Review" class="h-16 w-auto object-contain rounded transition-transform group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-maroon-800/40" />
                                <div class="w-20 h-1 bg-maroon-800 mx-auto mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                            </a>
                        </div>
                    </div>
                    -->

                    <!-- Features Grid - More Compact -->
                    <div class="grid md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <a href="{{ route('login') }}" class="block text-center p-4 bg-white/30 backdrop-blur-md border border-white/40 rounded-2xl shadow-xl
                                hover:bg-white/40 hover:backdrop-blur-lg hover:scale-105 hover:shadow-2xl hover:-translate-y-2 transition-all duration-200 group">
                                <div class="w-10 h-10 bg-maroon-800 rounded-full flex items-center justify-center mx-auto mb-3
                                    group-hover:-translate-y-1 group-hover:scale-110 transition-transform duration-200">
                                    <svg class="w-5 h-5 text-white group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 9c-2.21 0-4-1.79-4-4h2a2 2 0 104 0h2c0 2.21-1.79 4-4 4zm0-16C6.477 1 2 5.477 2 11c0 5.523 4.477 10 10 10s10-4.477 10-10c0-5.523-4.477-10-10-10z" />
                                </svg>
                            </div>
                                <h3 class="text-base font-semibold text-maroon-800 mb-1">Application for Incentives</h3>
                                <p class="text-gray-600 text-xs">Apply for incentives and track your application status.</p>
                            </a>
                        </div>

                        <div>
                            <a href="#" class="block text-center p-4 bg-white/30 backdrop-blur-md border border-white/40 rounded-2xl shadow-xl
                                hover:bg-white/40 hover:backdrop-blur-lg hover:scale-105 hover:shadow-2xl hover:-translate-y-2 transition-all duration-200 group">
                                <div class="w-10 h-10 bg-maroon-800 rounded-full flex items-center justify-center mx-auto mb-3
                                    group-hover:-translate-y-1 group-hover:scale-110 transition-transform duration-200">
                                    <svg class="w-5 h-5 text-white group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                                <h3 class="text-base font-semibold text-maroon-800 mb-1">List of Suggested Journals</h3>
                                <p class="text-gray-600 text-xs">Description of Service 1 Lorem ipsum dolor sit amet</p>
                            </a>
                        </div>

                        <div>
                            <a href="#" class="block text-center p-4 bg-white/30 backdrop-blur-md border border-white/40 rounded-2xl shadow-xl
                                hover:bg-white/40 hover:backdrop-blur-lg hover:scale-105 hover:shadow-2xl hover:-translate-y-2 transition-all duration-200 group">
                                <div class="w-10 h-10 bg-maroon-800 rounded-full flex items-center justify-center mx-auto mb-3
                                    group-hover:-translate-y-1 group-hover:scale-110 transition-transform duration-200">
                                    <svg class="w-5 h-5 text-white group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                                <h3 class="text-base font-semibold text-maroon-800 mb-1">USeP Researchers</h3>
                                <p class="text-gray-600 text-xs">Description of Service 2 Lorem ipsum dolor sit amet</p>
                            </a>
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
                                    <div class="text-base text-gray-900 font-medium">
                                        New to the platform? 
                                        <a href="{{ route('register') }}" class="text-maroon-800 hover:text-maroon-800 underline">Create an account</a>
                                    </div>
                                </div>
                    @endauth
            @endif
                    </div>
                </div>
                </div>
        </main>
        </div>

        <div class="relative z-30">
            <x-footer />
        </div>

        @livewireScripts
    </body>
</html>

<script>
// Debounce utility
function debounce(fn, delay) {
    let timer = null;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}
// Ensure side cards match the main card's height and vertical position
function syncSideCardHeightsAndPosition() {
    const mainCard = document.getElementById('main-welcome-card');
    const leftContainer = document.getElementById('left-floating-card-container');
    const rightContainer = document.getElementById('right-floating-card-container');
    const sideCards = document.querySelectorAll('.side-floating-card');
    if (!mainCard || !leftContainer || !rightContainer) return;
    const rect = mainCard.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const top = rect.top + scrollTop;
    const height = rect.height;
    leftContainer.style.top = top + 'px';
    rightContainer.style.top = top + 'px';
    sideCards.forEach(card => {
        card.style.height = height + 'px';
    });
}
const debouncedSync = debounce(syncSideCardHeightsAndPosition, 30);

function revealSideCards() {
    syncSideCardHeightsAndPosition();
    document.querySelectorAll('.side-floating-card').forEach(card => card.classList.remove('opacity-0'));
}

// Run early to reduce initial flash, and again when everything is fully loaded
document.addEventListener('DOMContentLoaded', syncSideCardHeightsAndPosition);
window.addEventListener('load', revealSideCards);
window.addEventListener('resize', debouncedSync);
window.addEventListener('scroll', debouncedSync);

// Turbo lifecycle support (Hotwire)
document.addEventListener('turbo:render', syncSideCardHeightsAndPosition);
document.addEventListener('turbo:load', revealSideCards);
// Before Turbo caches the page, clear inline styles to avoid stale heights
document.addEventListener('turbo:before-cache', () => {
    const leftContainer = document.getElementById('left-floating-card-container');
    const rightContainer = document.getElementById('right-floating-card-container');
    const sideCards = document.querySelectorAll('.side-floating-card');
    if (leftContainer) leftContainer.style.top = '';
    if (rightContainer) rightContainer.style.top = '';
    sideCards.forEach(card => {
        card.style.height = '';
        card.classList.add('opacity-0');
    });
});
</script> 