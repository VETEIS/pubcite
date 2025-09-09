<aside class="w-56 h-screen flex-shrink-0 bg-gradient-to-b from-maroon-800 to-maroon-900 text-white flex flex-col shadow-2xl border-r border-maroon-700/50">
    <!-- Brand Section -->
    <div class="p-6 border-b border-maroon-700/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <img src="/images/spjrd.png" alt="SPJRD Logo" class="w-8 h-8 object-contain rounded-full">
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">PubCite</h1>
                <p class="text-xs text-maroon-200">Administration Panel</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2">
        <!-- Dashboard Link -->
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group relative overflow-hidden {{ request()->routeIs('admin.dashboard') && !request()->is('admin/users*') ? 'bg-white/20 text-white shadow-lg' : 'text-maroon-100 hover:bg-maroon-700/50 hover:text-white hover:shadow-md' }}">
            <div class="relative z-10">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" 
                     fill="{{ request()->routeIs('admin.dashboard') && !request()->is('admin/users*') ? 'currentColor' : 'none' }}" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
                </svg>
            </div>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Dashboard</span>
            @if(request()->routeIs('admin.dashboard') && !request()->is('admin/users*'))
                <div class="absolute right-3 w-2 h-2 bg-white rounded-full animate-pulse"></div>
            @endif
        </a>

        <!-- Users Link -->
        <a href="{{ route('admin.users.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group relative overflow-hidden {{ request()->is('admin/users*') ? 'bg-white/20 text-white shadow-lg' : 'text-maroon-100 hover:bg-maroon-700/50 hover:text-white hover:shadow-md' }}">
            <div class="relative z-10">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" 
                     fill="{{ request()->is('admin/users*') ? 'currentColor' : 'none' }}" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Users</span>
            @if(request()->is('admin/users*'))
                <div class="absolute right-3 w-2 h-2 bg-white rounded-full animate-pulse"></div>
            @endif
        </a>

        <!-- Settings Link -->
        <a href="{{ route('admin.settings') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group relative overflow-hidden {{ request()->routeIs('admin.settings') ? 'bg-white/20 text-white shadow-lg' : 'text-maroon-100 hover:bg-maroon-700/50 hover:text-white hover:shadow-md' }}">
            <div class="relative z-10">
                <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" 
                     fill="{{ request()->routeIs('admin.settings') ? 'currentColor' : 'none' }}" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Settings</span>
            @if(request()->routeIs('admin.settings'))
                <div class="absolute right-3 w-2 h-2 bg-white rounded-full animate-pulse"></div>
            @endif
        </a>
    </nav>

    <!-- User Profile Section -->
    <div class="p-4 border-t border-maroon-700/50 bg-maroon-800/30">
        <div class="flex items-center gap-3 p-3 rounded-xl bg-white/10 backdrop-blur-sm">
            @if(Auth::user()->profile_photo_path)
                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-white/20">
            @else
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-maroon-600 to-maroon-700 flex items-center justify-center text-white font-bold shadow-lg">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div class="text-xs text-maroon-200 truncate">Administrator</div>
            </div>
            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
        </div>
    </div>
</aside> 