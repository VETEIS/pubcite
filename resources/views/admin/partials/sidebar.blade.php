<aside class="w-20 h-full flex-shrink-0 bg-white/30 backdrop-blur border border-white/40 text-maroon-900 flex flex-col items-center shadow-lg rounded-full">
    <nav class="flex flex-col gap-8 w-full items-center flex-1 justify-center">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 group focus:outline-none {{ request()->routeIs('dashboard') && !request()->is('admin/users*') ? '' : 'hover:scale-110 hover:-translate-y-1 transition-all duration-200' }}">
            <svg class="w-7 h-7 {{ request()->routeIs('dashboard') && !request()->is('admin/users*') ? 'text-maroon-800 fill-maroon-800' : 'text-maroon-800' }} group-hover:text-maroon-800 transition" fill="{{ request()->routeIs('dashboard') && !request()->is('admin/users*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6" /></svg>
            <span class="text-xs mt-1 pl-2 {{ request()->routeIs('dashboard') && !request()->is('admin/users*') ? 'font-extrabold text-maroon-800' : 'font-bold' }} group-hover:text-maroon-800">Overview</span>
            @if(request()->routeIs('dashboard') && !request()->is('admin/users*'))
                <div class="h-0.5 bg-maroon-800 rounded-full w-8 mx-auto mt-1"></div>
            @endif
        </a>
        <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center gap-1 group focus:outline-none {{ request()->is('admin/users*') ? '' : 'hover:scale-110 hover:-translate-y-1 transition-all duration-200' }}">
            <svg class="w-7 h-7 {{ request()->is('admin/users*') ? 'text-maroon-800 fill-maroon-800' : 'text-maroon-800' }} group-hover:text-maroon-800 transition" fill="{{ request()->is('admin/users*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            <span class="text-xs mt-1 {{ request()->is('admin/users*') ? 'font-extrabold text-maroon-800' : 'font-bold' }} group-hover:text-maroon-800">Users</span>
            @if(request()->is('admin/users*'))
                <div class="h-0.5 bg-maroon-800 rounded-full w-8 mx-auto mt-1"></div>
            @endif
        </a>
        <a href="{{ route('admin.settings') }}" class="flex flex-col items-center gap-1 group focus:outline-none {{ request()->routeIs('admin.settings') ? '' : 'hover:scale-110 hover:-translate-y-1 transition-all duration-200' }}">
            <svg class="w-7 h-7 {{ request()->routeIs('admin.settings') ? 'text-maroon-800 fill-maroon-800' : 'text-maroon-800' }} group-hover:text-maroon-800 transition" fill="{{ request()->routeIs('admin.settings') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.93 4.93l2.83 2.83M19.07 4.93l-2.83 2.83M4.93 19.07l2.83-2.83M19.07 19.07l-2.83-2.83" /></svg>
            <span class="text-xs mt-1 {{ request()->routeIs('admin.settings') ? 'font-extrabold text-maroon-800' : 'font-bold' }} group-hover:text-maroon-800">Settings</span>
            @if(request()->routeIs('admin.settings'))
                <div class="h-0.5 bg-maroon-800 rounded-full w-8 mx-auto mt-1"></div>
            @endif
        </a>
    </nav>
</aside> 