<x-app-layout>
    <div x-data="{ 
        loading: false,
        errorMessage: null,
        errorTimer: null,
        showError(message) {
            this.errorMessage = message;
            if (this.errorTimer) clearTimeout(this.errorTimer);
            this.errorTimer = setTimeout(() => {
                this.errorMessage = null;
            }, 3000);
        }
    }" class="h-screen bg-gray-50 flex overflow-hidden" style="scrollbar-gutter: stable;" data-turbo="false">
        
        <!-- Hidden notification divs for global notification system -->
        @if(session('success'))
            <div id="success-notification" class="hidden">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div id="error-notification" class="hidden">{{ session('error') }}</div>
        @endif

        <!-- Error message overlay -->
        <div x-show="errorMessage" x-transition class="fixed top-20 right-4 z-[60] bg-red-600 text-white px-4 py-2 rounded shadow" style="display:none;">
            <span x-text="errorMessage"></span>
        </div>
        <!-- Loading overlay -->
        <div x-show="loading" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center" style="display:none;">
            <div class="bg-white rounded-lg shadow-xl px-6 py-5 flex items-center gap-3">
                <svg class="animate-spin h-6 w-6 text-maroon-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span class="text-maroon-900 font-semibold">Processing…</span>
            </div>
        </div>

        <!-- Sidebar - Hidden on mobile, visible on desktop -->
        <div class="hidden lg:block">
            @if(Auth::user()->role === 'admin')
                @include('admin.partials.sidebar')
            @else
                @include('components.user-sidebar')
            @endif
        </div>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-4 h-screen overflow-y-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
            <style>
                .flex-1::-webkit-scrollbar {
                    display: none;
                }
            </style>

            <!-- Main Content Area -->
            <main class="flex-1 p-6">
                <div class="max-w-4xl mx-auto space-y-8">
                    <!-- Profile Overview Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-maroon-600 to-maroon-700 px-8 py-10">
                            <div class="flex items-center gap-8">
                                <!-- Profile Photo Section -->
                                <div class="relative group">
                                    @if(Auth::user()->profile_photo_path)
                                        <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-28 h-28 rounded-full object-cover border-4 border-white shadow-lg">
                                    @else
                                        <div class="w-28 h-28 rounded-full bg-white/20 border-4 border-white shadow-lg flex items-center justify-center text-white text-4xl font-bold">
                                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <!-- Edit Photo Overlay -->
                                    <div class="absolute inset-0 bg-black/50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center cursor-pointer" onclick="document.getElementById('profile-photo-input').click()">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <input type="file" id="profile-photo-input" class="hidden" accept="image/*" onchange="handleProfilePhotoChange(this)">
                                </div>
                                
                                <!-- User Info -->
                                <div class="flex-1">
                                    <h2 class="text-3xl font-bold text-white mb-3">{{ Auth::user()->name ?? 'User' }}</h2>
                                    <p class="text-maroon-100 text-lg mb-3">{{ Auth::user()->email ?? 'No email' }}</p>
                                    <div class="flex items-center gap-4">
                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white/20 text-white">
                                            @if(Auth::user()->role === 'admin')
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                </svg>
                                                Administrator
                                            @else
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                User
                                            @endif
                                        </span>
                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-500/20 text-green-100">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                            Active
                                        </span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <!-- Profile Settings Sections -->
                    <div class="space-y-8">
                        
                        <!-- Personal Information Section -->
                        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
                                <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-3">
                                    <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    Personal Information
                                </h3>
                                <p class="text-gray-600 mt-1">Update your personal details and contact information</p>
                            </div>
                            <div class="p-8">
                                @livewire('profile.update-profile-information-form')
                            </div>
                        </div>
                        @endif

                        <!-- Security Section -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
                                <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    </div>
                                    Security Settings
                                </h3>
                                <p class="text-gray-600 mt-1">Manage your password and account security</p>
                            </div>
                            <div class="p-8 space-y-8">
                                
                                <!-- Browser Sessions -->
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        Browser Sessions
                                    </h4>
                                    @livewire('profile.logout-other-browser-sessions-form')
                                </div>
                            </div>
                        </div>

                        <!-- Danger Zone Section -->
                        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-8 py-6 border-b border-gray-200 bg-red-50">
                                <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-3">
                                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </div>
                                    Danger Zone
                                </h3>
                                <p class="text-gray-600 mt-1">Permanent actions that cannot be undone</p>
                            </div>
                            <div class="p-8">
                                @livewire('profile.delete-user-form')
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function handleProfilePhotoChange(input) {
            if (!input.files || !input.files[0]) {
                return;
            }
            
            const file = input.files[0];
            
            // Validate file type
            if (!file.type.match(/^image\/(jpeg|jpg|png)$/)) {
                if (window.notificationManager) {
                    window.notificationManager.error('Please select a valid image file (JPG, PNG)');
                }
                input.value = '';
                return;
            }
            
            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                if (window.notificationManager) {
                    window.notificationManager.error('Image size must be less than 10MB');
                }
                input.value = '';
                return;
            }
            
            // Show preview immediately
            const reader = new FileReader();
            reader.onload = function(e) {
                // Find the specific profile photo container by finding the input's parent group
                const profileContainer = input.closest('.group');
                if (!profileContainer) {
                    console.error('Profile photo container not found');
                    return;
                }
                
                // Find the img or div within this specific container
                const profilePhoto = profileContainer.querySelector('img, div.w-28');
                if (!profilePhoto) {
                    console.error('Profile photo element not found');
                    return;
                }
                
                if (profilePhoto.tagName === 'IMG') {
                    profilePhoto.src = e.target.result;
                } else {
                    // Replace the div with an img
                    const newImg = document.createElement('img');
                    newImg.src = e.target.result;
                    newImg.alt = '{{ Auth::user()->name }}';
                    newImg.className = 'w-28 h-28 rounded-full object-cover border-4 border-white shadow-lg';
                    profilePhoto.parentNode.replaceChild(newImg, profilePhoto);
                }
            };
            reader.readAsDataURL(file);
            
            // Upload the photo to the server
            const formData = new FormData();
            formData.append('photo', file);
            formData.append('name', '{{ Auth::user()->name }}');
            formData.append('email', '{{ Auth::user()->email }}');
            formData.append('_method', 'PUT');
            
            // Show loading state
            if (window.Alpine && window.Alpine.store) {
                const store = window.Alpine.store('app');
                if (store) store.loading = true;
            }
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                || document.querySelector('input[name="_token"]')?.value 
                || '';
            
            if (!csrfToken) {
                console.error('CSRF token not found');
                if (window.notificationManager) {
                    window.notificationManager.error('Security token missing. Please refresh the page and try again.');
                }
                return;
            }
            
            fetch('/user/profile-information', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
                credentials: 'same-origin'
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                const isJson = contentType && contentType.includes('application/json');
                
                if (response.ok) {
                    // Success - reload the page to get the updated photo URL
                    if (window.notificationManager) {
                        window.notificationManager.success('Profile photo updated successfully!');
                    }
                    // Reload after a short delay to show the notification
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                    return;
                }
                
                // Handle error response
                let errorMessage = 'Failed to update profile photo. Please try again.';
                if (isJson) {
                    try {
                        const data = await response.json();
                        errorMessage = data.message || data.errors?.photo?.[0] || errorMessage;
                    } catch (e) {
                        // Use default error message
                    }
                } else {
                    // Try to get error from response text
                    try {
                        const text = await response.text();
                        if (text.includes('validation')) {
                            errorMessage = 'Invalid image file. Please select a JPG or PNG image under 10MB.';
                        }
                    } catch (e) {
                        // Use default error message
                    }
                }
                
                throw new Error(errorMessage);
            })
            .catch(error => {
                console.error('Error updating profile photo:', error);
                if (window.notificationManager) {
                    window.notificationManager.error(error.message || 'Failed to update profile photo. Please try again.');
                }
                // Reset the input
                input.value = '';
            })
            .finally(() => {
                // Hide loading state
                if (window.Alpine && window.Alpine.store) {
                    const store = window.Alpine.store('app');
                    if (store) store.loading = false;
                }
            });
        }
    </script>
</x-app-layout>
