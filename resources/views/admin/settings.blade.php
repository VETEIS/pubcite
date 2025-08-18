<x-app-layout>
    <div class="flex h-[calc(100vh-4rem)] p-4 gap-x-6">
        @include('admin.partials.sidebar')
        <div class="flex-1 flex items-center justify-center h-full m-0">
            <div class="w-full h-full flex-1 rounded-2xl shadow-xl bg-white/30 backdrop-blur border border-white/40 p-4 flex flex-col items-stretch overflow-y-auto">
                <!-- Settings Header -->
                <div class="relative flex items-center mb-6">
                    <h1 class="text-2xl font-bold text-maroon-900 flex items-center gap-2">
                        <svg class="w-7 h-7 text-maroon-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        System Settings
                    </h1>
                </div>
                
                <!-- Session Notifications - Will be converted to new system automatically -->
                @if(session('success'))
                <div id="success-notification" class="hidden">
                    {{ session('success') }}
                </div>
                @endif
                
                @if(session('error'))
                <div id="error-notification" class="hidden">
                    {{ session('error') }}
                </div>
                @endif
                
                <!-- Debug: Show session data -->
                <script>
                    console.log('Session data available:', {
                        success: '{{ session('success') }}',
                        error: '{{ session('error') }}',
                        hasSuccess: {{ session('success') ? 'true' : 'false' }},
                        hasError: {{ session('error') ? 'true' : 'false' }}
                    });
                </script>
                
                <form id="settings-form" method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Settings Cards Container -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Official Information Section -->
                        <div class="bg-white/40 backdrop-blur border border-white/50 rounded-xl p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-maroon-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-maroon-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-maroon-900">Official Information</h2>
                                    <p class="text-sm text-gray-600">Configure official names and titles for document generation</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Deputy Director Row -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">Deputy Director Name</label>
                                        <input type="text" name="official_deputy_director_name" value="{{ old('official_deputy_director_name', $official_deputy_director_name) }}" 
                                               class="w-full border border-gray-300/50 rounded-xl px-4 py-3 bg-white/50 backdrop-blur focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" required>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">Deputy Director Title</label>
                                        <input type="text" name="official_deputy_director_title" value="{{ old('official_deputy_director_title', $official_deputy_director_title) }}" 
                                               class="w-full border border-gray-300/50 rounded-xl px-4 py-3 bg-white/50 backdrop-blur focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" required>
                                    </div>
                                </div>
                                
                                <!-- RDD Director Row -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">RDD Director Name</label>
                                        <input type="text" name="official_rdd_director_name" value="{{ old('official_rdd_director_name', $official_rdd_director_name) }}" 
                                               class="w-full border border-gray-300/50 rounded-xl px-4 py-3 bg-white/50 backdrop-blur focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" required>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">RDD Director Title</label>
                                        <input type="text" name="official_rdd_director_title" value="{{ old('official_rdd_director_title', $official_rdd_director_title) }}" 
                                               class="w-full border border-gray-300/50 rounded-xl px-4 py-3 bg-white/50 backdrop-blur focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Application Controls Section -->
                        <div class="bg-white/40 backdrop-blur border border-white/50 rounded-xl p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-maroon-900">Application Controls</h2>
                                    <p class="text-sm text-gray-600">Manage feature availability and system behavior</p>
                                </div>
                            </div>
                            
                            <!-- Citations Request Toggle -->
                            <div class="bg-white/50 backdrop-blur border border-white/60 rounded-xl p-6 mb-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900">Citations Request Feature</h3>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ old('citations_request_enabled', $citations_request_enabled) == '1' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}" id="status-badge">
                                                {{ old('citations_request_enabled', $citations_request_enabled) == '1' ? 'Currently Enabled' : 'Currently Disabled' }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3">Control whether users and signatories can access the "Apply for Citations" feature</p>
                                        <div class="text-xs text-gray-500">
                                            <strong>When disabled:</strong> The citations button will be grayed out and users will be redirected with an error message if they try to access the feature.
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="hidden" name="citations_request_enabled" value="0">
                                            <input type="checkbox" name="citations_request_enabled" value="1" class="sr-only peer" {{ old('citations_request_enabled', $citations_request_enabled) == '1' ? 'checked' : '' }}>
                                            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                        <span class="text-sm font-medium text-gray-900" id="toggle-text">
                                            {{ old('citations_request_enabled', $citations_request_enabled) == '1' ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Placeholder for future settings -->
                            <div class="bg-gray-50/50 backdrop-blur border border-gray-200/50 rounded-xl p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-500">Publications Request Feature</h3>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                                Coming Soon
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mb-3">Control access to the publications request feature</p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-7 bg-gray-200 rounded-full cursor-not-allowed"></div>
                                        <span class="text-sm font-medium text-gray-400">Disabled</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- Floating Save Button Overlay -->
                <div id="floating-save-button" class="fixed bottom-6 right-6 z-50 opacity-0 pointer-events-none transition-all duration-300 transform translate-y-4">
                    <button type="submit" form="settings-form" class="inline-flex items-center gap-2 px-6 py-3 bg-maroon-700 text-white rounded-xl shadow-xl hover:bg-maroon-800 hover:shadow-2xl transition-all font-semibold backdrop-blur border border-white/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Settings
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        (function() {
            'use strict';
            
            // Private state - isolated from global scope
            let isVisible = false;
            let isSubmitting = false;
            
            // DOM elements
            let floatingSaveButton = null;
            let form = null;
            let checkbox = null;
            
            // Initialize the save button system
            function init() {
                floatingSaveButton = document.getElementById('floating-save-button');
                form = document.getElementById('settings-form');
                
                if (!floatingSaveButton || !form) {
                    console.error('Required elements not found');
                    return;
                }
                
                // Bind events
                bindEvents();
                
                // Initialize checkbox UI
                initCheckbox();
                
                // Ensure save button starts hidden
                hideSaveButton();
            }
            
            // Bind all event listeners
            function bindEvents() {
                // Form input events - show save button
                form.addEventListener('input', handleFormChange);
                form.addEventListener('change', handleFormChange);
                
                // Form submission - hide save button
                form.addEventListener('submit', handleFormSubmit);
                
                // Page visibility changes
                document.addEventListener('visibilitychange', handleVisibilityChange);
                
                // Navigation events
                window.addEventListener('popstate', handleNavigation);
                
                // Global events for SPA frameworks
                document.addEventListener('turbo:submit-end', handleFormComplete);
                document.addEventListener('livewire:load', handleFormComplete);
            }
            
            // Handle any form input change
            function handleFormChange(event) {
                if (isSubmitting) return; // Don't show during submission
                
                if (!isVisible) {
                    showSaveButton();
                }
            }
            
            // Handle form submission
            function handleFormSubmit(event) {
                isSubmitting = true;
                hideSaveButton();
                
                // Reset submission state after a delay
                setTimeout(() => {
                    isSubmitting = false;
                }, 1000);
            }
            
            // Handle form completion (for SPA frameworks)
            function handleFormComplete() {
                isSubmitting = false;
                hideSaveButton();
            }
            
            // Handle page visibility changes
            function handleVisibilityChange() {
                if (!document.hidden) {
                    hideSaveButton();
                }
            }
            
            // Handle navigation
            function handleNavigation() {
                hideSaveButton();
            }
            
            // Show save button
            function showSaveButton() {
                if (isVisible) return; // Already visible
                
                isVisible = true;
                floatingSaveButton.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
                floatingSaveButton.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
            }
            
            // Hide save button
            function hideSaveButton() {
                if (!isVisible) return; // Already hidden
                
                isVisible = false;
                floatingSaveButton.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
                floatingSaveButton.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
            }
            
            // Initialize checkbox UI
            function initCheckbox() {
                checkbox = document.querySelector('input[name="citations_request_enabled"][type="checkbox"]');
                if (checkbox) {
                    checkbox.addEventListener('change', updateCheckboxUI);
                    // Set initial UI state
                    updateCheckboxUI();
                }
            }
            
            // Update checkbox UI
            function updateCheckboxUI() {
                const toggleText = document.getElementById('toggle-text');
                const statusBadge = document.getElementById('status-badge');
                
                if (!toggleText || !statusBadge) return;
                
                if (checkbox.checked) {
                    toggleText.textContent = 'Enabled';
                    statusBadge.textContent = 'Currently Enabled';
                    statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
                } else {
                    toggleText.textContent = 'Disabled';
                    statusBadge.textContent = 'Currently Disabled';
                    statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
                }
            }
            
            // Public API for external use
            window.saveButtonAPI = {
                show: showSaveButton,
                hide: hideSaveButton,
                reset: function() {
                    isSubmitting = false;
                    hideSaveButton();
                }
            };
            
            // Debug: Add test notification button
            /*const debugContainer = document.createElement('div');
            debugContainer.className = 'fixed top-4 left-4 z-[70] bg-gray-800 text-white p-3 rounded shadow-lg';
            debugContainer.innerHTML = `
                <div class="text-xs mb-2">Debug Controls</div>
                <button onclick="window.notificationManager.success('Test success notification!')" class="bg-green-600 hover:bg-green-700 px-2 py-1 rounded text-xs mr-2">Test Success</button>
                <button onclick="window.notificationManager.error('Test error notification!')" class="bg-red-600 hover:bg-red-700 px-2 py-1 rounded text-xs">Test Error</button>
            `;
            document.body.appendChild(debugContainer);*/
            
            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
        
        // Global Notification Manager - Consistent across entire app
        (function() {
            'use strict';
            
            // Notification configuration
            const NOTIFICATION_CONFIG = {
                position: 'fixed top-20 right-4 z-[60]',
                baseClasses: 'px-4 py-3 rounded-lg shadow-xl backdrop-blur border transform transition-all duration-500 ease-out',
                success: {
                    bg: 'bg-green-600',
                    border: 'border-green-500/20',
                    icon: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />'
                },
                error: {
                    bg: 'bg-red-600',
                    border: 'border-red-500/20',
                    icon: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />'
                },
                warning: {
                    bg: 'bg-yellow-600',
                    border: 'border-yellow-500/20',
                    icon: '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />'
                },
                info: {
                    bg: 'bg-blue-600',
                    border: 'border-blue-500/20',
                    icon: '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />'
                }
            };
            
            // Active notifications tracking
            let activeNotifications = [];
            let notificationCounter = 0;
            
            // Create and show notification
            function createNotification(type, message, duration = 5000) {
                const id = `notification-${++notificationCounter}`;
                const config = NOTIFICATION_CONFIG[type] || NOTIFICATION_CONFIG.info;
                
                // Create notification element
                const notification = document.createElement('div');
                notification.id = id;
                notification.className = `${NOTIFICATION_CONFIG.position} ${NOTIFICATION_CONFIG.baseClasses} ${config.bg} ${config.border} text-white opacity-0 translate-x-full`;
                
                notification.innerHTML = `
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            ${config.icon}
                        </svg>
                        <span class="text-sm font-medium">${message}</span>
                        <button onclick="window.notificationManager.dismiss('${id}')" class="ml-2 text-white/80 hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                
                // Add to DOM
                document.body.appendChild(notification);
                activeNotifications.push({ id, element: notification, timer: null });
                
                // Animate in with staggered delay for multiple notifications
                const delay = activeNotifications.length * 100;
                setTimeout(() => {
                    notification.classList.remove('opacity-0', 'translate-x-full');
                    notification.classList.add('opacity-100', 'translate-x-0');
                }, delay);
                
                // Auto-dismiss
                if (duration > 0) {
                    const timer = setTimeout(() => {
                        dismissNotification(id);
                    }, duration);
                    
                    // Update timer reference
                    const notificationObj = activeNotifications.find(n => n.id === id);
                    if (notificationObj) {
                        notificationObj.timer = timer;
                    }
                }
                
                return id;
            }
            
            // Dismiss specific notification
            function dismissNotification(id) {
                const notificationObj = activeNotifications.find(n => n.id === id);
                if (!notificationObj) return;
                
                const { element, timer } = notificationObj;
                
                // Clear timer
                if (timer) {
                    clearTimeout(timer);
                }
                
                // Animate out
                element.classList.add('opacity-0', 'translate-x-full');
                
                // Remove after animation
                setTimeout(() => {
                    if (document.body.contains(element)) {
                        document.body.removeChild(element);
                    }
                    activeNotifications = activeNotifications.filter(n => n.id !== id);
                    
                    // Reposition remaining notifications
                    repositionNotifications();
                }, 500);
            }
            
            // Dismiss all notifications
            function dismissAll() {
                activeNotifications.forEach(notification => {
                    dismissNotification(notification.id);
                });
            }
            
            // Reposition notifications to prevent overlap
            function repositionNotifications() {
                activeNotifications.forEach((notification, index) => {
                    const topOffset = 20 + (index * 80); // 20px from top, 80px spacing between notifications
                    notification.element.style.top = `${topOffset}px`;
                });
            }
            
            // Public API
            window.notificationManager = {
                success: (message, duration) => createNotification('success', message, duration),
                error: (message, duration) => createNotification('error', message, duration),
                warning: (message, duration) => createNotification('warning', message, duration),
                info: (message, duration) => createNotification('info', message, duration),
                dismiss: dismissNotification,
                dismissAll: dismissAll
            };
            
            // Auto-dismiss existing session notifications
            document.addEventListener('DOMContentLoaded', function() {
                const existingNotifications = document.querySelectorAll('[id*="success-notification"], [id*="error-notification"]');
                existingNotifications.forEach(notification => {
                    // Convert existing notifications to new system
                    const isSuccess = notification.id.includes('success');
                    const message = notification.textContent.trim();
                    const type = isSuccess ? 'success' : 'error';
                    
                    // Remove old notification
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                    
                    // Show new notification
                    setTimeout(() => {
                        createNotification(type, message);
                    }, 100);
                });
            });
        })();
    </script>
</x-app-layout> 