<!-- User Navbar Component -->
<div x-data="{ userMenuOpen: false }" class="flex items-center gap-4">
    <!-- Modern Compact Filters -->
    @if(isset($showFilters) && $showFilters)
        <div class="flex items-center gap-2">
            @php
                $currentStatus = request('status');
                $filteredRequests = $requests ?? collect();
                if ($currentStatus) {
                    $filteredRequests = $filteredRequests->where('status', $currentStatus);
                }
            @endphp
            
            <!-- Compact Status Filter -->
            <div class="relative group">
                <button class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 h-8 w-32 justify-between">
                    <svg class="w-3.5 h-3.5 text-maroon-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="min-w-[60px] max-w-[80px] truncate">{{ $currentStatus ? ucfirst($currentStatus) : 'All Status' }}</span>
                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="absolute top-full left-0 mt-1 bg-white text-md font-semibold border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 min-w-[120px]">
                    <a href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => null])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ !$currentStatus ? 'bg-maroon-50 text-maroon-700' : '' }}">All Status</a>
                    <a href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentStatus === 'pending' ? 'bg-maroon-50 text-maroon-700' : '' }}">Pending</a>
                    <a href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'endorsed'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentStatus === 'endorsed' ? 'bg-maroon-50 text-maroon-700' : '' }}">Endorsed</a>
                    <a href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'rejected'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentStatus === 'rejected' ? 'bg-maroon-50 text-maroon-700' : '' }}">Rejected</a>
                </div>
            </div>
        </div>
    @endif

    <!-- User Avatar Dropdown -->
    <div class="relative">
        <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 hover:bg-gray-100 rounded-lg p-1 transition-colors">
            @if(Auth::user()->profile_photo_path)
                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover">
            @else
                <div class="w-10 h-10 rounded-full bg-maroon-600 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
            @endif
            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <!-- User Dropdown Menu -->
        <div x-cloak x-show="userMenuOpen" @click.away="userMenuOpen = false" @keydown.escape.window="userMenuOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50" style="display: none;">
            <div class="py-2">
                <div class="px-4 py-2 border-b border-gray-100">
                    <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? 'User' }}</div>
                    <div class="text-xs text-gray-500">{{ Auth::user()->email ?? 'No email' }}</div>
                </div>
                <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
