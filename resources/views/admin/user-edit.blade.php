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
        <div class="flex-1 ml-60 h-screen overflow-y-auto force-scrollbar">
            <!-- Content Area -->
            <main class="p-4 rounded-bl-lg h-full flex flex-col main-content">
                <!-- Dashboard Header with Modern Compact Filters -->
                <div class="relative flex items-center justify-between mb-4 flex-shrink-0">
                    <!-- Page Title -->
                    <div class="flex items-center gap-4">
                        <!-- User Management Title -->
                        <div class="flex items-center gap-2 text-md font-semibold text-gray-600 bg-gray-50 px-3 py-2.5 rounded-lg h-10">
                            <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 11l6 6M3 21h6v-6H3v6z" />
                        </svg>
                            <span>Edit User</span>
                        </div>
                    </div>
                    
                    <!-- Enhanced Search and User Controls -->
                    <div class="flex items-center gap-4">
                        <!-- Add User Button (Back to List) -->
                        <a href="{{ route('admin.users.index') }}" 
                           class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 hover:shadow-md transition-all duration-200 h-8">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Users
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
                                             x-show="notification"
                                             :class="{ 'bg-blue-50': notification && !notification.read_at }"
                                             @click="markAsRead(notification.id)">
                                            <div class="flex items-start gap-3">
                                                <div class="w-2 h-2 bg-maroon-500 rounded-full mt-2 flex-shrink-0" 
                                                     x-show="notification && !notification.read_at"></div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm text-gray-900" x-text="notification?.message || ''"></p>
                                                    <p class="text-xs text-gray-500 mt-1" x-text="formatTime(notification?.created_at || '')"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
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

                <!-- Form Card -->
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @if($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-sm font-semibold text-red-800">Please fix the following errors:</h3>
                                </div>
                                <ul class="list-disc pl-5 text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

                        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')
                            <!-- Name Field -->
                <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                                <input type="text" name="name" id="name-input" value="{{ old('name', $user->name) }}" required 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" 
                                       placeholder="Enter full name" />
                </div>

                            <!-- Email Field -->
                <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" 
                                       placeholder="Enter email address" />
                </div>

                            <!-- Role Field -->
                <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">User Role</label>
                                <select name="role" id="role-select" required 
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all">
                                    <option value="">Select a role</option>
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="signatory" {{ old('role', $user->role) == 'signatory' ? 'selected' : '' }}>Signatory</option>
                    </select>
                </div>
                            <!-- Signatory Type Field (Conditional) -->
                <div id="signatory-type-group" class="{{ old('role', $user->role) == 'signatory' ? '' : 'hidden' }}">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Signatory Type</label>
                                <select name="signatory_type" 
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all">
                                    <option value="">Select signatory type</option>
                        <option value="faculty" {{ old('signatory_type', $user->signatory_type) == 'faculty' ? 'selected' : '' }}>Faculty</option>
                        <option value="center_manager" {{ old('signatory_type', $user->signatory_type) == 'center_manager' ? 'selected' : '' }}>Research Center Manager</option>
                        <option value="college_dean" {{ old('signatory_type', $user->signatory_type) == 'college_dean' ? 'selected' : '' }}>College Dean</option>
                    </select>
                </div>

                            <!-- Password Fields (Only for non-Google users) -->
                @if($user->auth_provider !== 'google')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        New Password
                                        <span class="text-xs font-normal text-gray-500">(leave blank to keep current)</span>
                                    </label>
                                    <input type="password" name="password" 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" 
                                           placeholder="Enter new password" />
                    </div>
                    <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" 
                                           placeholder="Confirm new password" />
                    </div>
                </div>
                @else
                <!-- Google User Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-800">Google Account</h4>
                            <p class="text-sm text-blue-700 mt-1">This user signed in with Google. Password changes are not available for Google accounts.</p>
                        </div>
                    </div>
                </div>
                @endif

                            <!-- Form Actions -->
                            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                                <a href="{{ route('admin.users.index') }}" 
                                   class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg shadow-sm hover:bg-gray-200 hover:shadow-md transition-all duration-300 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-maroon-700 text-white rounded-lg shadow-sm hover:bg-maroon-800 hover:shadow-md transition-all duration-300 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                        Update User
                    </button>
                </div>
            </form>
        </div>
                </div>
            </main>
    </div>
</div>

<script>
    // Function to toggle signatory type field visibility
    function toggleSignatoryTypeField() {
            const roleSelect = document.getElementById('role-select');
            const signatoryGroup = document.getElementById('signatory-type-group');
        
        if (!roleSelect || !signatoryGroup) {
            return;
        }
            
        // Show/hide based on role selection
                if (roleSelect.value === 'signatory') {
                    signatoryGroup.classList.remove('hidden');
            signatoryGroup.style.display = 'block';
                } else {
                    signatoryGroup.classList.add('hidden');
            signatoryGroup.style.display = 'none';
        }
    }
    
    // Function to convert name to uppercase for signatories
    function convertNameToUppercase() {
        const roleSelect = document.getElementById('role-select');
        const nameInput = document.getElementById('name-input');
        
            if (roleSelect && nameInput && roleSelect.value === 'signatory') {
                nameInput.value = nameInput.value.toUpperCase();
            }
        }
            
    // Initialize function - works with both regular page loads and Turbo
    function initializeUserForm() {
        const roleSelect = document.getElementById('role-select');
        const signatoryGroup = document.getElementById('signatory-type-group');
        const nameInput = document.getElementById('name-input');
        
        // Initial state check
        if (roleSelect && signatoryGroup) {
            toggleSignatoryTypeField();
        }
        
        // Add event listeners
        if (roleSelect) {
            // Remove existing listeners to prevent duplicates
            const newRoleSelect = roleSelect.cloneNode(true);
            roleSelect.parentNode.replaceChild(newRoleSelect, roleSelect);
            
            // Add event listeners to the new element
            newRoleSelect.addEventListener('change', function() {
                toggleSignatoryTypeField();
                convertNameToUppercase();
            });
            
            newRoleSelect.addEventListener('input', function() {
                toggleSignatoryTypeField();
                convertNameToUppercase();
            });
        }
        
        if (nameInput) {
            nameInput.addEventListener('input', convertNameToUppercase);
        }
        
        // Refresh signatory cache after successful form submission
        const form = document.querySelector('form[action*="users"]');
        if (form) {
            form.addEventListener('submit', function() {
                setTimeout(() => {
                    if (window.refreshSignatoryCache) {
                        window.refreshSignatoryCache();
                    }
                }, 1000);
            });
        }
    }
    
    // Initialize on both regular page loads and Turbo navigation
    document.addEventListener('DOMContentLoaded', initializeUserForm);
    document.addEventListener('turbo:load', initializeUserForm);
    document.addEventListener('turbo:render', initializeUserForm);
    
    // Also run immediately if DOM is already loaded
    if (document.readyState !== 'loading') {
        initializeUserForm();
    }
</script> 
</x-app-layout> 