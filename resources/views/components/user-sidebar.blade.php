<aside class="w-56 h-screen flex-shrink-0 bg-gradient-to-b from-maroon-800 to-maroon-900 text-white flex flex-col shadow-2xl border-r border-maroon-700/50">
    <!-- Brand Section -->
    <div class="p-6 border-b border-maroon-700/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <img src="/images/spjrd.png" alt="SPJRD Logo" class="w-7 h-7 object-contain rounded-full">
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">PubCite</h1>
                <p class="text-xs text-maroon-200">User Dashboard</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2">
        <!-- Dashboard Link -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group relative overflow-hidden {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white shadow-lg' : 'text-maroon-100 hover:bg-maroon-700/50 hover:text-white hover:shadow-md' }}">
            <div class="relative z-10">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" 
                     fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"/>
                </svg>
            </div>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Dashboard</span>
            @if(request()->routeIs('dashboard'))
                <div class="absolute right-3 w-2 h-2 bg-white rounded-full animate-pulse"></div>
            @endif
        </a>

        @if(Auth::user() && Auth::user()->isSignatory())
            <!-- Signing Link (for signatories) -->
            <a href="{{ route('signing.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group relative overflow-hidden {{ request()->routeIs('signing.*') ? 'bg-white/20 text-white shadow-lg' : 'text-maroon-100 hover:bg-maroon-700/50 hover:text-white hover:shadow-md' }}">
                <div class="relative z-10">
                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" 
                         fill="{{ request()->routeIs('signing.*') ? 'currentColor' : 'none' }}" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20v-6m0 0l-3 3m3-3l3 3M5 8l7-3 7 3-7 3-7-3z"/>
                    </svg>
                </div>
                <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Signatures</span>
                @if(request()->routeIs('signing.*'))
                    <div class="absolute right-3 w-2 h-2 bg-white rounded-full animate-pulse"></div>
                @endif
            </a>

            <!-- Separator -->
            <div class="my-4 border-t border-maroon-700/50"></div>
        @else
            <!-- Separator -->
            <div class="my-4 border-t border-maroon-700/50"></div>
        @endif

        <!-- Publications Link -->
        <a href="{{ route('publications.request') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group relative overflow-hidden {{ request()->routeIs('publications.*') ? 'bg-white/20 text-white shadow-lg' : 'text-maroon-100 hover:bg-maroon-700/50 hover:text-white hover:shadow-md' }}">
            <div class="relative z-10">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" 
                     fill="{{ request()->routeIs('publications.*') ? 'currentColor' : 'none' }}" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Publications</span>
            @if(request()->routeIs('publications.*'))
                <div class="absolute right-3 w-2 h-2 bg-white rounded-full animate-pulse"></div>
            @endif
        </a>

        <!-- Citations Link -->
        @if(\App\Models\Setting::get('citations_request_enabled', '1') == '1')
        <a href="{{ route('citations.request') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group relative overflow-hidden {{ request()->routeIs('citations.*') ? 'bg-white/20 text-white shadow-lg' : 'text-maroon-100 hover:bg-maroon-700/50 hover:text-white hover:shadow-md' }}">
            <div class="relative z-10">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" 
                     fill="{{ request()->routeIs('citations.*') ? 'currentColor' : 'none' }}" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Citations</span>
            @if(request()->routeIs('citations.*'))
                <div class="absolute right-3 w-2 h-2 bg-white rounded-full animate-pulse"></div>
            @endif
        </a>
        @else
        <div class="relative px-4 py-3 rounded-xl text-maroon-300 cursor-not-allowed opacity-50" title="Citations requests are currently disabled">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="font-medium">Citations</span>
            </div>
            <span class="absolute -top-1 -right-1 text-xs bg-red-500 text-white px-1.5 py-0.5 rounded-full text-[10px]">Disabled</span>
        </div>
        @endif
    </nav>

    <!-- Logout Button -->
    <div class="p-4 border-t border-maroon-700/50">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group text-maroon-100 hover:bg-red-600/20 hover:text-red-200 hover:shadow-md">
                <div class="relative z-10">
                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Logout</span>
            </button>
        </form>
    </div>

</aside>
