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
        <div class="flex-1 ml-60 h-screen overflow-y-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
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
                        <!-- System Settings Title -->
                        <div class="flex items-center gap-2 text-md font-semibold text-gray-600 bg-gray-50 px-3 py-2.5 rounded-lg h-10">
                            <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>System Settings</span>
                        </div>
                    </div>
                    
                    <!-- Enhanced Search and User Controls -->
                    <div class="flex items-center gap-4">
                        
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
                        <button class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors flex items-center justify-center group">
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-800 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                         
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
                
                <!-- Modern Settings Layout -->
                <div class="max-w-7xl mx-auto">

                    
                    <form id="settings-form" method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- General Settings Section -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                                <!-- Header -->
                                <div class="px-6 py-4 bg-gradient-to-r from-maroon-50 to-red-50 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-maroon-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">Official Information</h3>
                                                <p class="text-sm text-gray-600 mt-1">Configure official names and titles for document generation</p>
                                            </div>
                            </div>
                                        <button type="submit" name="save_official_info" value="1"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed transition-all duration-200 font-medium text-sm"
                                                disabled>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Save Changes
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Content -->
                                <div class="p-6">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <!-- Deputy Director Card -->
                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <div class="flex items-center gap-2 mb-3">
                                                <div class="w-6 h-6 bg-maroon-100 rounded flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                </div>
                                                <h4 class="text-sm font-semibold text-gray-900">Deputy Director</h4>
                                            </div>
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Full Name</label>
                                                    <input type="text" name="official_deputy_director_name" value="{{ old('official_deputy_director_name', $official_deputy_director_name) }}" 
                                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-1 focus:ring-maroon-500/20 transition-all" 
                                                           placeholder="Enter deputy director's full name" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Official Title</label>
                                                    <input type="text" name="official_deputy_director_title" value="{{ old('official_deputy_director_title', $official_deputy_director_title) }}" 
                                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-1 focus:ring-maroon-500/20 transition-all" 
                                                           placeholder="Enter deputy director's title" required>
                                                </div>
                                                
                                                <!-- Account Creation Section -->
                                                <div class="border-t border-gray-200 pt-3 mt-4">
                                                    <div class="space-y-3">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Email Address</label>
                                                            <input type="email" name="deputy_director_email" value="{{ old('deputy_director_email', $deputy_director_email ?? '') }}" 
                                                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-1 focus:ring-maroon-500/20 transition-all" 
                                                                   placeholder="deputy.director@example.com">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                                                            <input type="password" name="deputy_director_password" 
                                                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-1 focus:ring-maroon-500/20 transition-all" 
                                                                   placeholder="Enter password for account creation">
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" onclick="createDeputyDirectorAccount()" 
                                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-maroon-600 text-white text-xs font-medium rounded-md hover:bg-maroon-700 transition-colors">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                                </svg>
                                                                Create Account
                                                            </button>
                                                            <span class="text-xs text-gray-500" id="deputy-account-status"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    
                                        <!-- RDD Director Card -->
                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <div class="flex items-center gap-2 mb-3">
                                                <div class="w-6 h-6 bg-blue-100 rounded flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                </div>
                                                <h4 class="text-sm font-semibold text-gray-900">RDD Director</h4>
                                            </div>
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Full Name</label>
                                                    <input type="text" name="official_rdd_director_name" value="{{ old('official_rdd_director_name', $official_rdd_director_name) }}" 
                                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-1 focus:ring-maroon-500/20 transition-all" 
                                                           placeholder="Enter RDD director's full name" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Official Title</label>
                                                    <input type="text" name="official_rdd_director_title" value="{{ old('official_rdd_director_title', $official_rdd_director_title) }}" 
                                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-1 focus:ring-maroon-500/20 transition-all" 
                                                           placeholder="Enter RDD director's title" required>
                                                </div>
                                                
                                                <!-- Account Creation Section -->
                                                <div class="border-t border-gray-200 pt-3 mt-4">
                                                    <div class="space-y-3">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Email Address</label>
                                                            <input type="email" name="rdd_director_email" value="{{ old('rdd_director_email', $rdd_director_email ?? '') }}" 
                                                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-1 focus:ring-maroon-500/20 transition-all" 
                                                                   placeholder="rdd.director@example.com">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                                                            <input type="password" name="rdd_director_password" 
                                                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-1 focus:ring-maroon-500/20 transition-all" 
                                                                   placeholder="Enter password for account creation">
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" onclick="createRddDirectorAccount()" 
                                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 transition-colors">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                                </svg>
                                                                Create Account
                                                            </button>
                                                            <span class="text-xs text-gray-500" id="rdd-account-status"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Features Settings Section -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                                <!-- Header -->
                                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">Feature Controls</h3>
                                                <p class="text-sm text-gray-600 mt-1">Manage feature availability and system behavior</p>
                            </div>
                                        </div>
                                        <button type="submit" name="save_application_controls" value="1"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed transition-all duration-200 font-medium text-sm"
                                                disabled>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Save Changes
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Content -->
                                <div class="p-6 space-y-6">
                                    <!-- Citations Request Feature -->
                                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6 border border-gray-200">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-3">
                                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-base font-semibold text-gray-900">Citations Request Feature</h4>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ old('citations_request_enabled', $citations_request_enabled) == '1' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}" id="status-badge">
                                                                {{ old('citations_request_enabled', $citations_request_enabled) == '1' ? 'Currently Enabled' : 'Currently Disabled' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                        </div>
                                                <p class="text-sm text-gray-600 mb-3">Control whether users and signatories can access the "Apply for Citations" feature</p>
                                                <div class="bg-white rounded-md p-3 border border-gray-200">
                                                    <div class="flex items-start gap-2">
                                                        <div class="w-4 h-4 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                                            <svg class="w-2 h-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs font-medium text-gray-900 mb-1">When disabled:</p>
                                                            <p class="text-xs text-gray-600">The citations button will be grayed out and users will be redirected with an error message if they try to access the feature.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-4 ml-6">
                                                <div class="text-center">
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="hidden" name="citations_request_enabled" value="0">
                                                        <input type="checkbox" name="citations_request_enabled" value="1" class="sr-only peer" {{ old('citations_request_enabled', $citations_request_enabled) == '1' ? 'checked' : '' }}>
                                                        <div class="w-12 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                                                    </label>
                                                    <span class="text-xs font-medium text-gray-900 mt-1 block" id="toggle-text">
                                                        {{ old('citations_request_enabled', $citations_request_enabled) == '1' ? 'Enabled' : 'Disabled' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Publications Request Feature (Coming Soon) -->
                                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6 border border-gray-200 opacity-60">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-3">
                                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-base font-semibold text-gray-500">Publications Request Feature</h4>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                                                Coming Soon
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="text-sm text-gray-500 mb-3">Control access to the publications request feature</p>
                                            </div>
                                            <div class="flex items-center gap-4 ml-6">
                                                <div class="text-center">
                                                    <div class="w-12 h-6 bg-gray-200 rounded-full cursor-not-allowed"></div>
                                                    <span class="text-xs font-medium text-gray-400 mt-1 block">Disabled</span>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Calendar Settings Section -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <!-- Header -->
                                <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">Welcome Page Calendar</h3>
                                                <p class="text-sm text-gray-600 mt-1">Add multiple marked dates with notes to show on the welcome page</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button" onclick="addMarkRow()" 
                                                    class="w-8 h-8 rounded-md bg-amber-600 text-white hover:bg-amber-700 transition-colors flex items-center justify-center" 
                                                    title="Add Event">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                            </button>
                                            <button type="submit" name="save_calendar" value="1"
                                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed transition-all duration-200 font-medium text-sm"
                                                    disabled>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Content -->
                                <div class="p-6">
                                    <div class="mb-4">                                        
                                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
                                            <div class="overflow-x-auto">
                                                <table class="w-full">
                                                    <thead class="bg-gray-50 border-b border-gray-200">
                                                        <tr>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                            </th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Date</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="marksRepeater" class="bg-white divide-y divide-gray-200">
                                                        @php($marks = old('calendar_marks', $calendar_marks ?? []))
                                                        @if(empty($marks))
                                                            @php($marks = [[ 'date' => '', 'note' => '' ]])
                                                        @endif
                                                        @foreach($marks as $idx => $mark)
                                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="w-8 h-8 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-sm">
                                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                    </svg>
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <input type="date" name="calendar_marks[{{ $idx }}][date]" value="{{ $mark['date'] ?? '' }}" 
                                                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all">
                                                            </td>
                                                            <td class="px-6 py-4">
                                                                <input type="text" name="calendar_marks[{{ $idx }}][note]" value="{{ $mark['note'] ?? '' }}" 
                                                                       placeholder="Enter event description" 
                                                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all">
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <button type="button" onclick="removeMarkRow(this)" 
                                                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" 
                                                                        title="Remove Event">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                    </svg>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Announcements Management Section -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6 mt-6">
                            <!-- Header -->
                            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Landing Page Announcements</h3>
                                            <p class="text-sm text-gray-600 mt-1">Manage announcements displayed on the landing page dropdown</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button type="button" onclick="addAnnouncementRow()" 
                                                class="w-8 h-8 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition-colors flex items-center justify-center" 
                                                title="Add Announcement">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        </button>
                                        <button type="submit" name="save_announcements" value="1"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed transition-all duration-200 font-medium text-sm"
                                                disabled>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Save Changes
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="p-6">
                                @if($errors->any())
                                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                        <ul class="list-disc list-inside">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                <div class="mb-4">                                        
                                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
                                        <div class="overflow-x-auto">
                                            <table class="w-full">
                                                <thead class="bg-gray-50 border-b border-gray-200">
                                                    <tr>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                            </svg>
                                                        </th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="announcementsRepeater" class="bg-white divide-y divide-gray-200">
                                                    @php($announcements = old('announcements', $announcements ?? []))
                                                    @if(empty($announcements))
                                                        @php($announcements = [['title' => '', 'description' => '']])
                                                    @endif
                                                    @foreach($announcements as $idx => $announcement)
                                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center shadow-sm">
                                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                                </svg>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <input type="text" name="announcements[{{ $idx }}][title]" value="{{ $announcement['title'] ?? '' }}" 
                                                                   placeholder="Enter announcement title" 
                                                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 transition-all">
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <input type="text" name="announcements[{{ $idx }}][description]" value="{{ $announcement['description'] ?? '' }}" 
                                                                   placeholder="Enter announcement description" 
                                                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 transition-all">
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <button type="button" onclick="removeAnnouncementRow(this)" 
                                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" 
                                                                    title="Remove Announcement">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteAnnouncementModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Delete Announcement</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="deleteAnnouncementMessage">
                        Are you sure you want to delete this announcement? This action cannot be undone.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirmDeleteAnnouncement" 
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Delete
                    </button>
                    <button id="cancelDeleteAnnouncement" 
                            class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-24 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        (function() {
            'use strict';
            
            // Global variables for change detection
            window.originalValues = {};
            
            // Update save button state
            function updateSaveButton(button, hasChanges) {
                console.log('updateSaveButton called:', { button: !!button, hasChanges });
                if (!button) return;
                
                if (hasChanges) {
                    console.log('Enabling save button');
                    button.disabled = false;
                    button.className = 'inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-maroon-600 to-red-600 text-white rounded-lg hover:from-maroon-700 hover:to-red-700 transition-all duration-200 font-medium text-sm shadow-md hover:shadow-lg';
                } else {
                    console.log('Disabling save button');
                    button.disabled = true;
                    button.className = 'inline-flex items-center gap-2 px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed transition-all duration-200 font-medium text-sm';
                }
            }
            
            // Check for changes in official information section
            function checkOfficialChanges() {
                const deputyName = document.querySelector('input[name="official_deputy_director_name"]');
                const deputyTitle = document.querySelector('input[name="official_deputy_director_title"]');
                const rddName = document.querySelector('input[name="official_rdd_director_name"]');
                const rddTitle = document.querySelector('input[name="official_rdd_director_title"]');
                
                if (!deputyName || !deputyTitle || !rddName || !rddTitle) return;
                
                const currentValues = {
                    deputy_name: deputyName.value,
                    deputy_title: deputyTitle.value,
                    rdd_name: rddName.value,
                    rdd_title: rddTitle.value
                };
                
                const hasChanges = Object.keys(currentValues).some(key => 
                    currentValues[key] !== window.originalValues.official[key]
                );
                
                const saveBtn = document.querySelector('button[name="save_official_info"]');
                updateSaveButton(saveBtn, hasChanges);
            }
            
            // Check for changes in features section
            function checkFeaturesChanges() {
                const checkbox = document.querySelector('input[name="citations_request_enabled"][type="checkbox"]');
                if (!checkbox) return;
                
                const currentValue = checkbox.checked;
                const hasChanges = currentValue !== (window.originalValues.features && window.originalValues.features.citations_enabled);
                
                const saveBtn = document.querySelector('button[name="save_application_controls"]');
                updateSaveButton(saveBtn, hasChanges);
            }
            
            // Check for changes in calendar section
            function checkCalendarChanges() {
                const calendarInputs = document.querySelectorAll('input[name^="calendar_marks"]');
                
                const currentMarks = calendarInputs.length > 0 
                    ? Array.from(calendarInputs).map(input => input.value)
                    : [];
                const originalMarks = window.originalValues.calendar?.marks || [];
                
                console.log('checkCalendarChanges - Current:', currentMarks);
                console.log('checkCalendarChanges - Original:', originalMarks);
                
                const hasChanges = JSON.stringify(currentMarks) !== JSON.stringify(originalMarks);
                console.log('checkCalendarChanges - Has changes:', hasChanges);
                
                const saveBtn = document.querySelector('button[name="save_calendar"]');
                updateSaveButton(saveBtn, hasChanges);
            }
            
            function checkAnnouncementsChanges() {
                const announcementInputs = document.querySelectorAll('input[name^="announcements"]');
                
                const currentAnnouncements = announcementInputs.length > 0 
                    ? Array.from(announcementInputs).map(input => input.value)
                    : [];
                const originalAnnouncements = window.originalValues.announcements?.announcements || [];
                
                console.log('checkAnnouncementsChanges - Current:', currentAnnouncements);
                console.log('checkAnnouncementsChanges - Original:', originalAnnouncements);
                
                const hasChanges = JSON.stringify(currentAnnouncements) !== JSON.stringify(originalAnnouncements);
                console.log('checkAnnouncementsChanges - Has changes:', hasChanges);
                
                const saveBtn = document.querySelector('button[name="save_announcements"]');
                updateSaveButton(saveBtn, hasChanges);
            }
            
            
            // Initialize announcements state tracking
            function initializeAnnouncementsState() {
                // Reset announcements state if needed
            }
            
            // Initialize form change detection
            function initFormChangeDetection() {
                // Store original values
                const deputyName = document.querySelector('input[name="official_deputy_director_name"]');
                const deputyTitle = document.querySelector('input[name="official_deputy_director_title"]');
                const rddName = document.querySelector('input[name="official_rdd_director_name"]');
                const rddTitle = document.querySelector('input[name="official_rdd_director_title"]');
                const citationsCheckbox = document.querySelector('input[name="citations_request_enabled"][type="checkbox"]');
                const calendarInputs = document.querySelectorAll('input[name^="calendar_marks"]');
                const announcementInputs = document.querySelectorAll('input[name^="announcements"]');
                
                window.originalValues = {
                    official: {
                        deputy_name: deputyName ? deputyName.value : '',
                        deputy_title: deputyTitle ? deputyTitle.value : '',
                        rdd_name: rddName ? rddName.value : '',
                        rdd_title: rddTitle ? rddTitle.value : ''
                    },
                    features: {
                        citations_enabled: citationsCheckbox ? citationsCheckbox.checked : false
                    },
                    calendar: {
                        marks: Array.from(calendarInputs).map(input => input.value)
                    },
                    announcements: {
                        announcements: Array.from(announcementInputs).map(input => input.value)
                    }
                };
                
                // Add event listeners
                if (deputyName) deputyName.addEventListener('input', checkOfficialChanges);
                if (deputyTitle) deputyTitle.addEventListener('input', checkOfficialChanges);
                if (rddName) rddName.addEventListener('input', checkOfficialChanges);
                if (rddTitle) rddTitle.addEventListener('input', checkOfficialChanges);
                
                // Citations checkbox change is handled by updateCheckboxUI function
                
                calendarInputs.forEach(input => {
                    input.addEventListener('input', checkCalendarChanges);
                });
                
                announcementInputs.forEach(input => {
                    input.addEventListener('input', checkAnnouncementsChanges);
                });
                
                // Make functions globally available
                window.checkOfficialChanges = checkOfficialChanges;
                window.checkFeaturesChanges = checkFeaturesChanges;
                window.checkCalendarChanges = checkCalendarChanges;
                window.checkAnnouncementsChanges = checkAnnouncementsChanges;
                
        // Check initial state
        checkOfficialChanges();
        checkFeaturesChanges();
        checkCalendarChanges();
        checkAnnouncementsChanges();
        
        // Initialize announcements state tracking
        initializeAnnouncementsState();
        
        // Debug: Log initial announcements state
        console.log('Initial announcements state:', {
            inputs: document.querySelectorAll('input[name^="announcements"]').length,
            rows: document.querySelectorAll('#announcementsRepeater tr').length,
            saveBtnDisabled: document.querySelector('button[name="save_announcements"]')?.disabled
        });
        
        // Form submission handling with Turbo integration
        document.addEventListener('submit', function(e) {
            if (e.submitter && e.submitter.name === 'save_announcements') {
                // Let the form submit normally - don't prevent default
                // The server will handle the submission and redirect back
                console.log('Announcements form submitting...');
                console.log('Form action:', e.target.action);
                console.log('Form method:', e.target.method);
                console.log('Submitter:', e.submitter.name, e.submitter.value);
            } else if (e.submitter && e.submitter.name === 'save_calendar') {
                console.log('Calendar form submitting...');
                console.log('Form action:', e.target.action);
                console.log('Form method:', e.target.method);
                console.log('Submitter:', e.submitter.name, e.submitter.value);
            }
        });
        
        // Handle successful form submission
        document.addEventListener('turbo:load', function() {
            // Check if we just came back from a successful save
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('saved') === 'announcements') {
                // Reset state after successful save
                // Reset announcements state if needed
            }
        });
            }
            
            // Initialize checkbox UI
            function initCheckbox() {
                const checkbox = document.querySelector('input[name="citations_request_enabled"][type="checkbox"]');
                if (checkbox) {
                    checkbox.addEventListener('change', updateCheckboxUI);
                    // Set initial UI state
                    updateCheckboxUI();
                }
            }
            
            // Update checkbox UI
            function updateCheckboxUI() {
                const checkbox = document.querySelector('input[name="citations_request_enabled"][type="checkbox"]');
                const toggleText = document.getElementById('toggle-text');
                const statusBadge = document.getElementById('status-badge');
                
                if (!toggleText || !statusBadge || !checkbox) return;
                
                if (checkbox.checked) {
                    toggleText.textContent = 'Enabled';
                    statusBadge.textContent = 'Currently Enabled';
                    statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
                } else {
                    toggleText.textContent = 'Disabled';
                    statusBadge.textContent = 'Currently Disabled';
                    statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800';
                }
                
                // Also trigger change detection when UI is updated
                if (window.checkFeaturesChanges) {
                    checkFeaturesChanges();
                }
            }
            
            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    initCheckbox();
                    initFormChangeDetection();
                });
            } else {
                initCheckbox();
                initFormChangeDetection();
            }
        })();

        // Enhanced repeater for calendar marks
        function addMarkRow() {
            const container = document.getElementById('marksRepeater');
            if (!container) return;
            const index = container.querySelectorAll('tr').length;
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition-colors duration-150';
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="w-8 h-8 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="date" name="calendar_marks[${index}][date]" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all">
                </td>
                <td class="px-6 py-4">
                    <input type="text" name="calendar_marks[${index}][note]" 
                           placeholder="Enter event description" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <button type="button" onclick="removeMarkRow(this)" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" 
                            title="Remove Event">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </td>
            `;
            
            // Insert at the top instead of bottom
            if (container.firstChild) {
                container.insertBefore(row, container.firstChild);
            } else {
                container.appendChild(row);
            }
            
            // Add event listeners to new inputs
            row.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', function() {
                    // Trigger calendar change detection
                    if (window.checkCalendarChanges) {
                        window.checkCalendarChanges();
                    }
                });
            });
            
            // Trigger change detection immediately since we added a new row
            if (window.checkCalendarChanges) {
                window.checkCalendarChanges();
            }
        }

        function removeMarkRow(btn) {
            const row = btn.closest('tr');
            const container = document.getElementById('marksRepeater');
            if (row && container) {
                row.remove();
                
                // If no rows left, add an empty entry for consistency
                if (container.querySelectorAll('tr').length === 0) {
                    console.log('No calendar rows left, adding empty entry');
                    addMarkRow();
                }
                
                // Trigger calendar change detection
                if (window.checkCalendarChanges) {
                    console.log('Calling checkCalendarChanges after deletion');
                    window.checkCalendarChanges();
                }
            }
        }

        // Simple announcements management
        function addAnnouncementRow() {
            const container = document.getElementById('announcementsRepeater');
            if (!container) return;
            
            const index = container.querySelectorAll('tr').length;
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition-colors duration-150';
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="text" name="announcements[${index}][title]" 
                           placeholder="Enter announcement title" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 transition-all"
>
                </td>
                <td class="px-6 py-4">
                    <input type="text" name="announcements[${index}][description]" 
                           placeholder="Enter announcement description" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 transition-all"
>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <button type="button" onclick="removeAnnouncementRow(this)" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" 
                            title="Remove Announcement">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </td>
            `;
            
            container.appendChild(row);
            
            // Add event listeners to new inputs
            const newInputs = row.querySelectorAll('input');
            newInputs.forEach(input => {
                input.addEventListener('input', checkAnnouncementsChanges);
            });
            
            // Trigger change detection
            if (window.checkAnnouncementsChanges) {
                window.checkAnnouncementsChanges();
            }
        }

        function removeAnnouncementRow(btn) {
            const row = btn.closest('tr');
            const container = document.getElementById('announcementsRepeater');
            if (row && container) {
                row.remove();
                
                // If no rows left, add an empty entry for consistency
                if (container.querySelectorAll('tr').length === 0) {
                    console.log('No rows left, adding empty entry');
                    addAnnouncementRow();
                }
                
                // Trigger announcements change detection
                if (window.checkAnnouncementsChanges) {
                    console.log('Calling checkAnnouncementsChanges after deletion');
                    window.checkAnnouncementsChanges();
                }
            }
        }


        // Account creation functions
        async function createDeputyDirectorAccount() {
            const email = document.querySelector('input[name="deputy_director_email"]').value;
            const password = document.querySelector('input[name="deputy_director_password"]').value;
            const name = document.querySelector('input[name="official_deputy_director_name"]').value;
            const statusElement = document.getElementById('deputy-account-status');
            
            if (!email || !password || !name) {
                statusElement.textContent = 'Please fill all required fields';
                statusElement.className = 'text-xs text-red-500';
                return;
            }
            
            statusElement.textContent = 'Creating account...';
            statusElement.className = 'text-xs text-blue-500';
            
            try {
                const response = await fetch('/admin/settings/create-account', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password,
                        name: name,
                        role: 'deputy_director'
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    statusElement.textContent = 'Account created successfully!';
                    statusElement.className = 'text-xs text-green-500';
                    // Clear password field
                    document.querySelector('input[name="deputy_director_password"]').value = '';
                } else {
                    statusElement.textContent = data.message || 'Failed to create account';
                    statusElement.className = 'text-xs text-red-500';
                }
            } catch (error) {
                statusElement.textContent = 'Error creating account';
                statusElement.className = 'text-xs text-red-500';
            }
        }

        async function createRddDirectorAccount() {
            const email = document.querySelector('input[name="rdd_director_email"]').value;
            const password = document.querySelector('input[name="rdd_director_password"]').value;
            const name = document.querySelector('input[name="official_rdd_director_name"]').value;
            const statusElement = document.getElementById('rdd-account-status');
            
            if (!email || !password || !name) {
                statusElement.textContent = 'Please fill all required fields';
                statusElement.className = 'text-xs text-red-500';
                return;
            }
            
            statusElement.textContent = 'Creating account...';
            statusElement.className = 'text-xs text-blue-500';
            
            try {
                const response = await fetch('/admin/settings/create-account', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password,
                        name: name,
                        role: 'rdd_director'
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    statusElement.textContent = 'Account created successfully!';
                    statusElement.className = 'text-xs text-green-500';
                    // Clear password field
                    document.querySelector('input[name="rdd_director_password"]').value = '';
                } else {
                    statusElement.textContent = data.message || 'Failed to create account';
                    statusElement.className = 'text-xs text-red-500';
                }
            } catch (error) {
                statusElement.textContent = 'Error creating account';
                statusElement.className = 'text-xs text-red-500';
            }
        }

    </script>
</x-app-layout> 