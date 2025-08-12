<nav x-data="{ open: false, showNotif: false, notif: { items: [], unread: 0 }, async fetchNotifs() { try { const res = await fetch('{{ route('admin.notifications.list') }}'); if (!res.ok) return; const data = await res.json(); this.notif = data; } catch(e) {} }, async markRead() { try { await fetch('{{ route('admin.notifications.read') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } }); this.notif.unread = 0; } catch(e) {} } }" class="bg-maroon-800 border-b border-maroon-900 fixed top-0 left-0 w-full z-50 shadow-lg">
    <!-- Primary Navigation Menu -->
    <div class="px-6">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-7 w-auto" />
                    </a>
                </div>
                <!-- Dashboard Title -->
                <div class="flex items-center ml-4">
                    <h1 class="text-white text-lg font-semibold">
                        {{ Auth::user() && Auth::user()->role === 'admin' ? 'Admin Dashboard' : 'User Dashboard' }}
                    </h1>
                </div>
            </div>
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                @if(Auth::user() && Auth::user()->role === 'admin')
                <!-- Notification Bell Icon with dropdown -->
                <div class="relative" @click.outside="showNotif = false">
                    <button id="notification-bell" class="relative focus:outline-none" @click.prevent="showNotif = !showNotif; if (showNotif) { fetchNotifs(); markRead(); }">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        <span x-show="notif.unread > 0" class="absolute -top-1 -right-1 inline-flex items-center justify-center text-[10px] font-bold text-white bg-red-600 rounded-full w-4 h-4" x-text="notif.unread"></span>
                    </button>
                    <!-- Dropdown -->
                    <div x-show="showNotif" style="display:none;" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-2xl border border-gray-200 overflow-hidden">
                        <div class="px-4 py-2 bg-gray-50 border-b">
                            <span class="text-sm font-semibold text-gray-800">Notifications</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            <template x-if="notif.items.length === 0">
                                <div class="p-4 text-sm text-gray-500">No notifications</div>
                            </template>
                            <template x-for="item in notif.items" :key="item.id">
                                <a :href="item.data && item.data.request_code ? ('{{ url('/admin/requests/manage') }}') : '#'" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition">
                                    <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold text-gray-800" x-text="item.title"></div>
                                        <div class="text-xs text-gray-600" x-text="item.message"></div>
                                        <div class="text-[10px] text-gray-400 mt-0.5" x-text="new Date(item.created_at).toLocaleString()"></div>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
                @endif
                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="60" contentClasses="py-1 bg-maroon-700 text-white">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-maroon-700 hover:text-maroon-200 focus:outline-none focus:bg-maroon-900 active:bg-maroon-900 transition ease-in-out duration-150">
                                        {{ Auth::user()->currentTeam->name }}

                                        <svg class="ms-2 -me-0.5 size-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <div class="block px-4 py-2 text-xs text-maroon-200">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <!-- Team Settings -->
                                    <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" class="text-white hover:bg-maroon-900">
                                        {{ __('Team Settings') }}
                                    </x-dropdown-link>

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}" class="text-white hover:bg-maroon-900">
                                            {{ __('Create New Team') }}
                                        </x-dropdown-link>
                                    @endcan

                                    <!-- Team Switcher -->
                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-maroon-900"></div>

                                        <div class="block px-4 py-2 text-xs text-maroon-200">
                                            {{ __('Switch Teams') }}
                                        </div>

                                        @foreach (Auth::user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48" contentClasses="py-1 bg-maroon-700 text-white">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 bg-maroon-900 rounded px-3 py-1 focus:outline-none">
                                <img class="w-8 h-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                <div class="flex flex-col items-start">
                                    <span class="text-white text-sm font-semibold">{{ Auth::user()->name }}</span>
                                    <span class="text-maroon-200 text-xs">{{ ucfirst(Auth::user()->role) }}</span>
                                </div>
                                <svg class="w-4 h-4 text-white ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-maroon-200">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}" class="text-white hover:bg-maroon-900">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}" class="text-white hover:bg-maroon-900">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-maroon-900"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}" class="text-white hover:bg-maroon-900"
                                         @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-maroon-200 hover:bg-maroon-900 focus:outline-none focus:bg-maroon-900 focus:text-maroon-200 transition duration-150 ease-in-out">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-maroon-800">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="text-white hover:text-maroon-200">
                {{ Auth::user() && Auth::user()->isAdmin() ? __('Admin Dashboard') : __('User Dashboard') }}
            </x-responsive-nav-link>
            @if(Auth::user() && Auth::user()->role === 'admin')
                <x-responsive-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')" class="text-white hover:text-maroon-200">
                    Manage Users
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-maroon-900">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-maroon-200">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')" class="text-white hover:text-maroon-200">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')" class="text-white hover:text-maroon-200">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}" class="text-white hover:text-maroon-200"
                                   @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>

                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-maroon-100"></div>

                    <div class="block px-4 py-2 text-xs text-maroon-200">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')" class="text-white hover:text-maroon-200">
                        {{ __('Team Settings') }}
                    </x-responsive-nav-link>

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')" class="text-white hover:text-maroon-200">
                            {{ __('Create New Team') }}
                        </x-responsive-nav-link>
                    @endcan
                @endif
            </div>
        </div>
    </div>
</nav>
