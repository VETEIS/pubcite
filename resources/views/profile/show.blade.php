<x-app-layout>
    {{-- Privacy Enforcer --}}
    <x-privacy-enforcer />
    
    <div x-data="{ 
        loading: false,
        errorMessage: null,
        errorTimer: null,
        userMenuOpen: false,
        showError(message) {
            this.errorMessage = message;
            if (this.errorTimer) clearTimeout(this.errorTimer);
            this.errorTimer = setTimeout(() => {
                this.errorMessage = null;
            }, 3000);
        }
    }" class="h-screen bg-gray-50 flex overflow-hidden" style="scrollbar-gutter: stable;">
        
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

        <!-- Sidebar -->
        @if(Auth::user()->role === 'admin')
            @include('admin.partials.sidebar')
        @else
            @include('components.user-sidebar')
        @endif

        <!-- Main Content -->
        <div class="flex-1 ml-4 h-screen overflow-y-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
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
                                
                                <!-- Quick Actions -->
                                <div class="flex flex-col gap-3">
                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-6 py-3 bg-white/20 hover:bg-white/30 text-white rounded-lg transition-colors text-sm font-medium flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
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
                                
                                <!-- Password Update -->
                                <div class="border-b border-gray-200 pb-8">
                                    @php
                                        $isGoogleUser = Auth::user()->auth_provider === 'google';
                                    @endphp
                                    @if($isGoogleUser)
                                        <!-- Disabled Password Update Card for Google Users -->
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                                            <div class="flex items-start gap-4">
                                                <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="text-lg font-medium text-gray-900 mb-2">Password Management</h4>
                                                    <p class="text-gray-600 mb-4">Password management is handled by Google for your account.</p>
                                                    <div class="flex items-center gap-2 text-sm text-gray-500">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        Password changes must be made through your Google account
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div>
                                            <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                                </svg>
                                                Update Password
                                            </h4>
                                            @livewire('profile.update-password-form')
                                        </div>
                                    @endif
                                </div>

                                <!-- Two-Factor Authentication -->
                                <div class="border-b border-gray-200 pb-8">
                                    <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Two-Factor Authentication
                                    </h4>
                                    @livewire('profile.two-factor-authentication-form')
                                </div>

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
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // Update the profile photo display
                    const profilePhoto = document.querySelector('.group img, .group div');
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
                    
                    // Here you would typically upload the file to the server
                    // For now, we'll just show a success message
                    alert('Profile photo updated successfully!');
                };
                
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>
