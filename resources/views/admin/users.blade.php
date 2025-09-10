<x-app-layout>
    <div x-data="{ 
        searchOpen: false,
        userMenuOpen: false
    }" 
    x-init="
        // Initialize notification bell
        window.notificationBell = function() {
            return {
                showDropdown: false,
                notifications: [],
                unreadCount: 0,
                loading: false,
                
                init() {
                    this.loadNotifications();
                    // Poll for new notifications every 10 seconds
                    setInterval(() => this.checkForNewNotifications(), 10000);
                },
                
                toggleNotifications() {
                    this.showDropdown = !this.showDropdown;
                    if (this.showDropdown) {
                        this.loadNotifications();
                    }
                },
                
                async loadNotifications() {
                    this.loading = true;
                    try {
                        const response = await fetch('/admin/notifications');
                        const data = await response.json();
                        this.notifications = data.items || [];
                        this.unreadCount = data.unread || 0;
                    } catch (error) {
                        console.error('Failed to load notifications:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                async checkForNewNotifications() {
                    try {
                        const response = await fetch('/admin/notifications');
                        const data = await response.json();
                        const currentUnreadCount = data.unread || 0;
                        
                        // If there are new unread notifications, reload the page
                        if (currentUnreadCount > this.unreadCount) {
                            console.log('New notifications detected, reloading page...');
                            window.location.reload();
                        }
                        
                        // Update the count silently
                        this.unreadCount = currentUnreadCount;
                    } catch (error) {
                        console.error('Failed to check for new notifications:', error);
                    }
                },
                
                async markAsRead(notificationId) {
                    try {
                        await fetch(`/admin/notifications/${notificationId}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            }
                        });
                        
                        // Update local state
                        const notification = this.notifications.find(n => n.id === notificationId);
                        if (notification) {
                            notification.read_at = new Date().toISOString();
                        }
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    } catch (error) {
                        console.error('Failed to mark notification as read:', error);
                    }
                },
                
                async markAllAsRead() {
                    try {
                        await fetch('/admin/notifications/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            }
                        });
                        
                        // Update local state
                        this.notifications.forEach(n => n.read_at = new Date().toISOString());
                        this.unreadCount = 0;
                    } catch (error) {
                        console.error('Failed to mark all notifications as read:', error);
                    }
                },
                
                formatTime(dateString) {
                    const date = new Date(dateString);
                    const now = new Date();
                    const diffInMinutes = Math.floor((now - date) / (1000 * 60));
                    
                    if (diffInMinutes < 1) return 'Just now';
                    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
                    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
                    return date.toLocaleDateString();
                }
            }
        }
    " class="h-screen bg-gray-50 flex overflow-hidden" style="scrollbar-gutter: stable;">
        
        <!-- Hidden notification divs for global notification system -->
        @if(session('success'))
            <div id="success-notification" class="hidden">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div id="error-notification" class="hidden">{{ session('error') }}</div>
        @endif

        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 ml-4 h-screen overflow-y-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
            <style>
                .flex-1::-webkit-scrollbar {
                    display: none;
                }
            </style>
            <!-- Content Area -->
            <main class="p-4 rounded-bl-lg">
                <!-- Dashboard Header with Modern Compact Filters -->
                <div class="relative flex items-center justify-between mb-4">
                    <!-- Page Title -->
                    <div class="flex items-center gap-4">
                        <!-- User Management Title -->
                        <div class="flex items-center gap-2 text-md font-semibold text-gray-600 bg-gray-50 px-3 py-2.5 rounded-lg h-10">
                            <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>User Management</span>
                        </div>
                    </div>
                    
                    <!-- Enhanced Search and User Controls -->
                    <div class="flex items-center gap-4">
                        <!-- Modern Compact Filters -->
                        <div class="flex items-center gap-2">
                            @php
                                $currentRole = request('role');
                                $currentSearch = request('search');
                            @endphp
                            
                            <!-- Compact Role Filter -->
                            <div class="relative group">
                                <button class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 h-8 w-32 justify-between">
                                    <svg class="w-3.5 h-3.5 text-maroon-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                                    <span class="min-w-[60px] max-w-[80px] truncate">{{ $currentRole ? ucfirst($currentRole) : 'All Roles' }}</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div class="absolute top-full left-0 mt-1 bg-white text-md font-semibold border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 min-w-[120px]">
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => null])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ !$currentRole ? 'bg-maroon-50 text-maroon-700' : '' }}">All Roles</a>
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => 'user'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentRole === 'user' ? 'bg-maroon-50 text-maroon-700' : '' }}">Users</a>
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => 'admin'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentRole === 'admin' ? 'bg-maroon-50 text-maroon-700' : '' }}">Admins</a>
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => 'signatory'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentRole === 'signatory' ? 'bg-maroon-50 text-maroon-700' : '' }}">Signatories</a>
                                </div>
                            </div>
                            
                            <!-- Clear Filters Button -->
                            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium {{ $currentRole || $currentSearch ? 'text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 hover:border-red-300' : 'text-gray-400 bg-gray-50 border border-gray-200 cursor-not-allowed' }} rounded-lg transition-all duration-200 h-8 {{ $currentRole || $currentSearch ? 'hover:scale-105' : '' }}">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span>Clear</span>
                            </a>
                        </div>

                        <!-- Subtle Separator -->
                        <div class="w-px h-8 bg-gray-200"></div>
                        
                        <!-- Notification Bell (like user dashboard) -->
                        <div class="relative" x-data="notificationBell()">
                            <button @click="toggleNotifications" class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors flex items-center justify-center group relative">
                                <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-800 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                                </svg>
                                <!-- Notification Badge -->
                                <div x-show="unreadCount > 0" 
                                     x-text="unreadCount" 
                                     class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse"></div>
                            </button>
                            
                            <!-- Notification Dropdown -->
                            <div x-show="showDropdown" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 @click.away="showDropdown = false"
                                 class="absolute right-0 top-12 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-hidden">
                                
                                <!-- Header -->
                                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                                        <button @click="markAllAsRead" 
                                                x-show="unreadCount > 0"
                                                class="text-xs text-maroon-600 hover:text-maroon-800 font-medium">
                                            Mark all as read
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Notifications List -->
                                <div class="max-h-80 overflow-y-auto relative">
                                    <!-- Loading Overlay -->
                                    <div x-show="loading" 
                                         class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center z-10">
                                        <div class="text-center text-gray-500">
                                            <div class="animate-spin w-5 h-5 border-2 border-maroon-600 border-t-transparent rounded-full mx-auto mb-2"></div>
                                            Loading notifications...
                                        </div>
                                    </div>
                                    
                                    <!-- Empty State -->
                                    <div x-show="!loading && notifications.length === 0" class="p-4 text-center text-gray-500">
                                        No notifications yet
                                    </div>
                                    
                                    <!-- Notifications List -->
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer"
                                             :class="{ 'bg-blue-50': !notification.read_at }"
                                             @click="markAsRead(notification.id)">
                                            <div class="flex items-start gap-3">
                                                <div class="w-2 h-2 bg-maroon-500 rounded-full mt-2 flex-shrink-0" 
                                                     x-show="!notification.read_at"></div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                                    <p class="text-xs text-gray-600 mt-1" x-text="notification.message"></p>
                                                    <p class="text-xs text-gray-400 mt-1" x-text="formatTime(notification.created_at)"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Enhanced Search Button (like user dashboard) -->
                        <button class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors flex items-center justify-center group">
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-800 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                         
                        <!-- Enhanced User Avatar Dropdown -->
                        <div class="relative">
                            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 hover:bg-gray-100 rounded-xl p-2 transition-all duration-300">
                                @if(Auth::user()->profile_photo_path)
                                    <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-maroon-600 flex items-center justify-center text-white font-bold shadow-sm">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                    </div>
                                @endif
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-300" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <!-- Enhanced User Dropdown Menu -->
                            <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                                <div class="py-2">
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? 'Admin' }}</div>
                                        <div class="text-xs text-gray-500">Administrator</div>
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
                </div>

                <!-- Users Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <!-- Users Table -->
                    <div class="table-container stable-layout">
                        <div class="overflow-x-auto table-scroll-area always-scroll scrollbar-gutter-stable">
                            <div class="table-content">
                                <table class="w-full divide-y divide-gray-200 requests-table">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                            @php
                                                $currentSort = request('sort', 'name');
                                                $currentOrder = request('order', 'asc');
                                            @endphp
                                            
                                            <th class="w-20 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                <a href="{{ route('admin.users.index', array_merge(request()->except(['sort', 'order', 'page']), ['sort' => 'id', 'order' => $currentSort === 'id' && $currentOrder === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center justify-center gap-1">
                                                    ID
                                                    @if($currentSort === 'id')
                                                        @if($currentOrder === 'asc')
                                                            <svg class="w-3 h-3 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-3 h-3 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                            </svg>
                                                        @endif
                                                    @else
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    @endif
                                                </a>
                                            </th>
                                            <th class="w-48 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                <a href="{{ route('admin.users.index', array_merge(request()->except(['sort', 'order', 'page']), ['sort' => 'name', 'order' => $currentSort === 'name' && $currentOrder === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1">
                                                    Name
                                                    @if($currentSort === 'name')
                                                        @if($currentOrder === 'asc')
                                                            <svg class="w-3 h-3 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-3 h-3 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                            </svg>
                                                        @endif
                                                    @else
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    @endif
                                                </a>
                                            </th>
                                            <th class="w-64 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                <a href="{{ route('admin.users.index', array_merge(request()->except(['sort', 'order', 'page']), ['sort' => 'email', 'order' => $currentSort === 'email' && $currentOrder === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1">
                                                    Email
                                                    @if($currentSort === 'email')
                                                        @if($currentOrder === 'asc')
                                                            <svg class="w-3 h-3 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-3 h-3 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                            </svg>
                                                        @endif
                                                    @else
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    @endif
                                                </a>
                                            </th>
                                            <th class="w-32 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                <a href="{{ route('admin.users.index', array_merge(request()->except(['sort', 'order', 'page']), ['sort' => 'role', 'order' => $currentSort === 'role' && $currentOrder === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center justify-center gap-1">
                                                    Role
                                                    @if($currentSort === 'role')
                                                        @if($currentOrder === 'asc')
                                                            <svg class="w-3 h-3 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-3 h-3 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                            </svg>
                                                        @endif
                                                    @else
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    @endif
                                                </a>
                                            </th>
                                            <th class="w-40 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <div class="flex items-center justify-center gap-1">
                                                    Signatory Type
                                                </div>
                                            </th>
                                            <th class="w-32 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                            </tr>
                                        </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                        @if($users->isEmpty())
                                            <tr>
                                                <td class="w-20 px-6 py-4"></td>
                                                <td class="w-48 px-6 py-4"></td>
                                                <td class="w-64 px-6 py-4"></td>
                                                <td class="w-32 px-6 py-4"></td>
                                                <td class="w-40 px-6 py-4"></td>
                                                <td class="w-32 px-6 py-4"></td>
                                            </tr>
                                            <tr>
                                                <td class="w-20 px-6 py-4"></td>
                                                <td class="w-48 px-6 py-4"></td>
                                                <td class="w-64 px-6 py-4"></td>
                                                <td class="w-32 px-6 py-4"></td>
                                                <td class="w-40 px-6 py-4">
                                                    <div class="flex flex-col items-center justify-center gap-3">
                                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20v-6m0 0l-3 3m3-3l3 3M4 6h16M4 10h16M4 14h16"/>
                                                            </svg>
                                                        </div>
                                                        <div class="text-center">
                                                            <h4 class="text-lg font-semibold text-gray-900">No users found</h4>
                                                            <p class="text-gray-500">No users match your current filters.</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="w-32 px-6 py-4"></td>
                                            </tr>
                                        @else
                                        @foreach($users as $user)
                                                <tr class="hover:bg-white hover:shadow-md transition-all duration-300 cursor-pointer group">
                                                    <td class="w-20 px-6 py-3 text-center">
                                                        <div class="text-sm font-medium text-gray-900">{{ $user->id }}</div>
                                                    </td>
                                                    <td class="w-48 px-6 py-3 overflow-hidden">
                                                        <div class="flex items-center w-full">
                                                            @if($user->profile_photo_path)
                                                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover mr-3 flex-shrink-0">
                                                            @else
                                                                <div class="w-8 h-8 rounded-full bg-maroon-100 flex items-center justify-center mr-3 flex-shrink-0">
                                                                    <span class="text-sm font-medium text-maroon-700">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span>
                                                                </div>
                                                            @endif
                                                            <div class="min-w-0 flex-1 overflow-hidden">
                                                                <div class="text-sm font-medium text-gray-900 truncate">{{ $user->name ?? 'N/A' }}</div>
                                                            </div>
                                                        </div>
                                    </td>
                                                    <td class="w-64 px-6 py-3 overflow-hidden">
                                                        <div class="text-sm text-gray-500 truncate">{{ $user->email ?? 'No email' }}</div>
                                    </td>
                                                    <td class="w-32 px-6 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-maroon-100 text-maroon-800' : ($user->role === 'signatory' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                        {{ ucfirst($user->role) }}
                                                    </span>
                                                </td>
                                                    <td class="w-40 px-6 py-3 text-center text-sm text-gray-500">
                                                    @if($user->role === 'signatory')
                                                        {{ str_replace('_',' ', ucfirst($user->signatory_type ?? '')) }}
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                    <td class="w-32 px-6 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 hover:shadow-md transition-all duration-300 text-sm font-medium" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6 6M3 21h6v-6H3v6z" />
                                                </svg>
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 hover:shadow-md transition-all duration-300 text-sm font-medium" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                            </div>
                    
                    <!-- Pagination -->
                    <div class="bg-white px-6 py-3 border-t border-gray-200 pagination-container">
                        {{ $users->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
    


</x-app-layout> 