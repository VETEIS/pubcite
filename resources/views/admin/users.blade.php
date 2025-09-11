<x-app-layout>
    <div x-data="{ 
        searchOpen: false
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
                        // Silent fail for notifications
                    } finally {
                        this.loading = false;
                    }
                },

                async checkForNewNotifications() {
                    try {
                        const response = await fetch('/admin/notifications');
                        const data = await response.json();
                        const currentUnreadCount = data.unread || 0;
                        
                        // Update the count silently without reloading
                        if (currentUnreadCount !== this.unreadCount) {
                            this.unreadCount = currentUnreadCount;
                            // Optionally update notifications list if dropdown is open
                            if (this.showDropdown) {
                                this.notifications = data.items || [];
                            }
                        }
                    } catch (error) {
                        // Silent fail for notification check
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
                        // Silent fail for mark as read
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
                        // Silent fail for mark all as read
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
        <div class="flex-1 ml-4 h-screen overflow-y-auto force-scrollbar">
            <!-- Content Area -->
            <main class="p-4 rounded-bl-lg h-full flex flex-col main-content">
                <!-- Dashboard Header with Modern Compact Filters -->
                <div class="relative flex items-center justify-between mb-4 flex-shrink-0">
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

                        <!-- Add User Button -->
                        <a href="{{ route('admin.users.create') }}" 
                           class="inline-flex items-center gap-2 px-3 py-2 bg-maroon-600 text-white text-xs font-medium rounded-lg hover:bg-maroon-700 hover:shadow-md transition-all duration-200 h-8">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add User
                        </a>
                        
                        <!-- Subtle Separator -->
                        <div class="w-px h-8 bg-gray-200"></div>
                        
                        <!-- Notification Bell (like user dashboard) -->
                        <div class="relative" x-data="notificationBell()" x-cloak>
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
                                 x-cloak
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
                        <div class="relative" x-data="{ searchOpen: false }">
                            <button @click="searchOpen = !searchOpen" class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors flex items-center justify-center group">
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-800 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                         
                            <!-- Search Dropdown -->
                            <div x-show="searchOpen" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 @click.away="searchOpen = false"
                                 class="absolute right-0 top-12 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                                
                                <!-- Search Header -->
                                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                                    <h3 class="text-sm font-semibold text-gray-900">Search Users</h3>
                                </div>
                                
                                <!-- Search Form -->
                                <div class="p-4">
                                    <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
                                        <!-- Search Input -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Term</label>
                                            <input type="text" 
                                                   name="search" 
                                                   value="{{ request('search') }}"
                                                   placeholder="Search by name, email, or role..."
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500">
                                        </div>
                                        
                                        <!-- Filter Options -->
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500">
                                                    <option value="">All Roles</option>
                                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                                                </select>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Signatory Type</label>
                                                <select name="signatory_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500">
                                                    <option value="">All Types</option>
                                                    <option value="faculty" {{ request('signatory_type') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                                                    <option value="staff" {{ request('signatory_type') == 'staff' ? 'selected' : '' }}>Staff</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="flex items-center justify-between pt-4">
                                            <button type="button" 
                                                    @click="searchOpen = false"
                                                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition-colors">
                                                Cancel
                                            </button>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.users.index') }}" 
                                                   class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition-colors">
                                                    Clear
                                                </a>
                                                <button type="submit" 
                                                        class="px-4 py-2 bg-maroon-600 text-white text-sm rounded-md hover:bg-maroon-700 transition-colors">
                                                    Search
                                        </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                         
                        <!-- User Profile Button -->
                        <a href="{{ route('profile.show') }}" class="flex items-center gap-2 hover:bg-gray-100 rounded-xl p-2 transition-all duration-300">
                            @if(Auth::user()->profile_photo_path)
                                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-200">
                            @else
                                <div class="w-10 h-10 rounded-full bg-maroon-600 flex items-center justify-center text-white font-bold shadow-sm">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                </div>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Users Table Container -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex-1 flex flex-col">
                    <!-- Table Header (Fixed) -->
                    <div class="bg-gray-50 border-b border-gray-200 flex-shrink-0">
                        <div class="overflow-x-auto">
                            <table class="w-full table-fixed">
                                <thead>
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
                                </table>
                            </div>
                        </div>
                        
                        <!-- Table Body (Scrollable) -->
                        <div class="flex-1 overflow-y-auto table-scroll-area">
                                        @if($users->isEmpty())
                                <!-- Empty State (Centered) -->
                                <div class="h-full flex items-center justify-center">
                                    <div class="flex flex-col items-center justify-center gap-3 text-center">
                                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20v-6m0 0l-3 3m3-3l3 3M4 6h16M4 10h16M4 14h16"/>
                                                            </svg>
                                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900">No users yet</h4>
                                            <p class="text-gray-500">No users have been created yet.</p>
                                        </div>
                                                        </div>
                                                    </div>
                                        @else
                                <div class="overflow-x-auto">
                                    <table class="w-full table-fixed divide-y divide-gray-200">
                                        <tbody class="bg-white divide-y divide-gray-200">
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
                                    </tbody>
                                </table>
                            </div>
                        @endif
                            </div>
                    
                    <!-- Pagination (Fixed at bottom) -->
                    <div class="bg-white px-6 py-3 border-t border-gray-200 flex-shrink-0">
                        {{ $users->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <style>
        /* Force scrollbar to always be visible to prevent layout shifts */
        .force-scrollbar {
            scrollbar-gutter: stable;
            overflow-y: scroll !important;
        }
        
        /* Ensure table column alignment */
        .table-fixed {
            table-layout: fixed;
        }
        
        .table-fixed th,
        .table-fixed td {
            box-sizing: border-box;
        }
        
        /* Ensure scrollbar is always visible even on short content */
        .force-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        
        .force-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .force-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        
        .force-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* For Firefox */
        .force-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        
        /* Table scrollbar styling */
        .table-scroll-area::-webkit-scrollbar {
            width: 8px;
        }
        
        .table-scroll-area::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .table-scroll-area::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        
        .table-scroll-area::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* For Firefox table scrollbar */
        .table-scroll-area {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        
        /* Ensure main content has proper bottom spacing */
        .main-content {
            padding-bottom: 1rem; /* 16px bottom padding */
        }
        
        /* Ensure proper flex layout */
        .flex-1 {
            flex: 1 1 0%;
        }
        
        .flex-shrink-0 {
            flex-shrink: 0;
        }
    </style>
</x-app-layout> 