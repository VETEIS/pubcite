<x-app-layout>
    <div x-data="{ 
        searchOpen: false
    }" 
    x-init="
    " class="h-screen bg-gray-50 flex overflow-hidden" style="scrollbar-gutter: stable;">
        
        <!-- Hidden notification divs for global notification system -->
        @if(session('success'))
            <div id="success-notification" class="hidden">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div id="error-notification" class="hidden">{{ session('error') }}</div>
        @endif

        <!-- Sidebar - Hidden on mobile, visible on desktop -->
        <div class="hidden lg:block">
            @include('admin.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-4 h-screen overflow-y-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
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

                    
                    <form id="settings-form" method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
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
                                                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm {{ ($deputy_director_account_exists ?? false) ? 'bg-gray-100' : 'bg-white' }} focus:border-maroon-500 focus:ring-1 focus:ring-maroon-500/20 transition-all" 
                                                                   placeholder="deputy.director@example.com"
                                                                   {{ ($deputy_director_account_exists ?? false) ? 'readonly' : '' }}>
                                                        </div>
                                                        <div id="deputy-password-field" style="{{ ($deputy_director_account_exists ?? false) ? 'display: none;' : '' }}" class="{{ ($deputy_director_account_exists ?? false) ? 'hidden' : '' }}">
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                                                            <input type="password" name="deputy_director_password" 
                                                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-1 focus:ring-maroon-500/20 transition-all" 
                                                                   placeholder="Enter password for account creation">
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" id="deputy-account-button" onclick="createDeputyDirectorAccount()" 
                                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-maroon-600 hover:bg-maroon-700 text-white text-xs font-medium rounded-md transition-colors {{ ($deputy_director_account_exists ?? false) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                                    {{ ($deputy_director_account_exists ?? false) ? 'disabled' : '' }}>
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                                </svg>
                                                                Create Account
                                                            </button>
                                                            <span class="text-xs text-gray-500" id="deputy-account-status">
                                                                @if($deputy_director_account_exists ?? false)
                                                                    <span class="text-green-600">Account exists</span>
                                                                @endif
                                                            </span>
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
                                                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm {{ ($rdd_director_account_exists ?? false) ? 'bg-gray-100' : 'bg-white' }} focus:border-maroon-500 focus:ring-1 focus:ring-maroon-500/20 transition-all" 
                                                                   placeholder="rdd.director@example.com"
                                                                   {{ ($rdd_director_account_exists ?? false) ? 'readonly' : '' }}>
                                                        </div>
                                                        <div id="rdd-password-field" style="{{ ($rdd_director_account_exists ?? false) ? 'display: none;' : '' }}" class="{{ ($rdd_director_account_exists ?? false) ? 'hidden' : '' }}">
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                                                            <input type="password" name="rdd_director_password" 
                                                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-1 focus:ring-maroon-500/20 transition-all" 
                                                                   placeholder="Enter password for account creation">
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" id="rdd-account-button" onclick="createRddDirectorAccount()" 
                                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors {{ ($rdd_director_account_exists ?? false) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                                    {{ ($rdd_director_account_exists ?? false) ? 'disabled' : '' }}>
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                                </svg>
                                                                Create Account
                                                            </button>
                                                            <span class="text-xs text-gray-500" id="rdd-account-status">
                                                                @if($rdd_director_account_exists ?? false)
                                                                    <span class="text-green-600">Account exists</span>
                                                                @endif
                                                            </span>
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
                        
                        <!-- Form Dropdowns Management Section -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6 mt-6">
                                @csrf
                                @method('PUT')
                                
                                <!-- Header -->
                                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-gray-200">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">Form Dropdown Options</h3>
                                            <p class="text-sm text-gray-600 mt-1">Manage academic ranks and colleges for incentive application forms • Auto-saved</p>
                                            </div>
                                        </div>
                                </div>
                                
                                <!-- Confirmation Modal -->
                                <div id="deleteConfirmModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden backdrop-blur-sm" style="display: none;">
                                    <div class="relative top-20 mx-auto p-5 w-full max-w-md" onclick="event.stopPropagation()">
                                        <div class="relative bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
                                            <div class="p-6">
                                                <div class="flex items-center gap-4 mb-4">
                                                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h3 class="text-lg font-semibold text-gray-900">Confirm Removal</h3>
                                                        <p class="text-sm text-gray-600 mt-1" id="deleteConfirmMessage">Are you sure you want to remove this item?</p>
                                                    </div>
                                                </div>
                                                <div class="flex gap-3 justify-end">
                                                    <button type="button" onclick="window.closeDeleteModal && window.closeDeleteModal()" 
                                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                                        Cancel
                                                    </button>
                                                    <button type="button" id="confirmDeleteBtn" onclick="window.confirmDelete && window.confirmDelete()" 
                                                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                                        Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Content -->
                                <div class="p-6">
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    <!-- Academic Ranks -->
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="text-sm font-semibold text-gray-900">Academic Ranks</h4>
                                                    <span id="ranksCount" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        {{ count(array_filter(old('academic_ranks', $academic_ranks ?? []))) }}
                                                    </span>
                                        </div>
                                            </div>
                                            <div id="academicRanksContainer" class="flex flex-wrap gap-2 mb-3 min-h-[60px]">
                                            @php($ranks = old('academic_ranks', $academic_ranks ?? []))
                                                @php($ranks = array_filter($ranks))
                                            @foreach($ranks as $idx => $rank)
                                                @if(!empty(trim($rank)))
                                                <div class="group relative inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:border-purple-400 hover:bg-purple-50 transition-all">
                                                    <input type="hidden" name="academic_ranks[]" value="{{ $rank }}">
                                                    <span class="text-sm">{{ $rank }}</span>
                                                    <button type="button" onclick="window.showDeleteModal && window.showDeleteModal(this, '{{ addslashes($rank) }}', 'Academic Ranks')" 
                                                            class="opacity-0 group-hover:opacity-100 transition-opacity ml-1 text-gray-400 hover:text-red-600 focus:outline-none">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                                @endif
                                            @endforeach
                                        </div>
                                            <div class="flex gap-2">
                                                <input type="text" id="rankInput" 
                                                       placeholder="Add rank..." 
                                                       class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500/20 transition-all"
                                                       onkeypress="if(event.key === 'Enter') { event.preventDefault(); addRankTag(); }">
                                                <button type="button" onclick="addRankTag()" 
                                                        class="px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                    Add
                                            </button>
                                        </div>
                                        </div>
                                        
                                        <!-- Colleges -->
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="text-sm font-semibold text-gray-900">Colleges</h4>
                                                    <span id="collegesCount" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        {{ count(array_filter(old('colleges', $colleges ?? []))) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div id="collegesContainer" class="flex flex-wrap gap-2 mb-3 min-h-[60px]">
                                            @php($colleges = old('colleges', $colleges ?? []))
                                                @php($colleges = array_filter($colleges))
                                            @foreach($colleges as $idx => $college)
                                                @if(!empty(trim($college)))
                                                <div class="group relative inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:border-indigo-400 hover:bg-indigo-50 transition-all">
                                                    <input type="hidden" name="colleges[]" value="{{ $college }}">
                                                    <span class="text-sm">{{ $college }}</span>
                                                    <button type="button" onclick="window.showDeleteModal && window.showDeleteModal(this, '{{ addslashes($college) }}', 'Colleges')" 
                                                            class="opacity-0 group-hover:opacity-100 transition-opacity ml-1 text-gray-400 hover:text-red-600 focus:outline-none">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                                @endif
                                            @endforeach
                                        </div>
                                            <div class="flex gap-2">
                                                <input type="text" id="collegeInput" 
                                                       placeholder="Add college..." 
                                                       class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 transition-all"
                                                       onkeypress="if(event.key === 'Enter') { event.preventDefault(); addCollegeTag(); }">
                                                <button type="button" onclick="addCollegeTag()" 
                                                        class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                    Add
                                            </button>
                                        </div>
                                        </div>
                                        
                                        <!-- Others Indexing Options -->
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="text-sm font-semibold text-gray-900">Indexing Options</h4>
                                                    <span id="othersCount" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                                        {{ count(array_filter(old('others_indexing_options', $others_indexing_options ?? []))) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div id="othersIndexingContainer" class="flex flex-wrap gap-2 mb-3 min-h-[60px]">
                                            @php($othersIndexing = old('others_indexing_options', $others_indexing_options ?? []))
                                                @php($othersIndexing = array_filter($othersIndexing))
                                            @foreach($othersIndexing as $idx => $option)
                                                @if(!empty(trim($option)))
                                                <div class="group relative inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:border-pink-400 hover:bg-pink-50 transition-all">
                                                    <input type="hidden" name="others_indexing_options[]" value="{{ $option }}">
                                                    <span class="text-sm">{{ $option }}</span>
                                                    <button type="button" onclick="window.showDeleteModal && window.showDeleteModal(this, '{{ addslashes($option) }}', 'Indexing Options')" 
                                                            class="opacity-0 group-hover:opacity-100 transition-opacity ml-1 text-gray-400 hover:text-red-600 focus:outline-none">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                                @endif
                                            @endforeach
                                            </div>
                                            <div class="flex gap-2">
                                                <input type="text" id="othersInput" 
                                                       placeholder="Add option..." 
                                                       class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white focus:border-pink-500 focus:ring-1 focus:ring-pink-500/20 transition-all"
                                                       onkeypress="if(event.key === 'Enter') { event.preventDefault(); addOthersIndexingTag(); }">
                                                <button type="button" onclick="addOthersIndexingTag()" 
                                                        class="px-3 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors text-sm font-medium flex items-center gap-1.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        
                        <!-- Removed standalone Calendar Settings Section (merged into Landing Page card) -->
                        </div>

                        <!-- Landing Page Settings Section -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6 mt-6 relative" x-data="{ activeTab: 'counters' }">
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
                                            <h3 class="text-lg font-semibold text-gray-900">Landing Page Settings</h3>
                                            <p class="text-sm text-gray-600 mt-1">Manage publication counters, calendar events, and announcements</p>
                                            </div>
                                        </div>
                                    <div class="flex items-center gap-3">
                                        <button type="submit" name="save_announcements" value="1"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed font-medium text-sm shadow-sm"
                                                    disabled>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                            <!-- Tabs Navigation -->
                            <div class="border-b border-gray-200 bg-gray-50">
                                <nav class="flex -mb-px" aria-label="Tabs">
                                    <button type="button" @click="activeTab = 'counters'" 
                                            :class="activeTab === 'counters' ? 'border-blue-500 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-100/50'"
                                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 border-b-2 font-medium text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        Publication Counters
                                    </button>
                                    <button type="button" @click="activeTab = 'calendar'" 
                                            :class="activeTab === 'calendar' ? 'border-amber-500 text-amber-600 bg-amber-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-100/50'"
                                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 border-b-2 font-medium text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Calendar Events
                                    </button>
                                    <button type="button" @click="activeTab = 'announcements'" 
                                            :class="activeTab === 'announcements' ? 'border-indigo-500 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-100/50'"
                                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 border-b-2 font-medium text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Announcements
                                    </button>
                                </nav>
                                        </div>
                            
                            <!-- Tab Content -->
                            <div class="p-6 overflow-y-auto" style="height: 400px;">
                                @if($errors->any())
                                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 rounded-md">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                        </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                                    @foreach($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                        </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Publication Counters Tab -->
                                <div x-show="activeTab === 'counters'" style="display: none;" class="h-full flex flex-col justify-center">
                                    <div class="mb-3 text-center">
                                        <p class="text-sm text-gray-600">Update the publication counts displayed on the landing page hero section</p>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl mx-auto w-full">
                                        <div class="bg-gradient-to-br from-maroon-50 via-red-50 to-red-100 rounded-lg p-4 border-2 border-maroon-300 shadow-md">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-8 h-8 bg-maroon-600 rounded-lg flex items-center justify-center shadow-sm">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                    </div>
                                                <label class="block text-sm font-semibold text-maroon-900 uppercase tracking-wide">Scopus</label>
                                            </div>
                                            <input type="number" name="scopus_publications_count" min="0" step="1" value="{{ old('scopus_publications_count', $scopus_publications_count) }}" 
                                                   class="w-full border-2 border-maroon-400 rounded-lg px-4 py-2.5 text-xl font-bold bg-white focus:border-maroon-600 focus:ring-2 focus:ring-maroon-500/30 text-center"
                                                   placeholder="0">
                                        </div>
                                        <div class="bg-gradient-to-br from-maroon-50 via-red-50 to-red-100 rounded-lg p-4 border-2 border-maroon-300 shadow-md">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-8 h-8 bg-maroon-600 rounded-lg flex items-center justify-center shadow-sm">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <label class="block text-sm font-semibold text-maroon-900 uppercase tracking-wide">Web of Science</label>
                                            </div>
                                            <input type="number" name="wos_publications_count" min="0" step="1" value="{{ old('wos_publications_count', $wos_publications_count) }}" 
                                                   class="w-full border-2 border-maroon-400 rounded-lg px-4 py-2.5 text-xl font-bold bg-white focus:border-maroon-600 focus:ring-2 focus:ring-maroon-500/30 text-center"
                                                   placeholder="0">
                                        </div>
                                        <div class="bg-gradient-to-br from-maroon-50 via-red-50 to-red-100 rounded-lg p-4 border-2 border-maroon-300 shadow-md">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-8 h-8 bg-maroon-600 rounded-lg flex items-center justify-center shadow-sm">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <label class="block text-sm font-semibold text-maroon-900 uppercase tracking-wide">ACI</label>
                                            </div>
                                            <input type="number" name="aci_publications_count" min="0" step="1" value="{{ old('aci_publications_count', $aci_publications_count) }}" 
                                                   class="w-full border-2 border-maroon-400 rounded-lg px-4 py-2.5 text-xl font-bold bg-white focus:border-maroon-600 focus:ring-2 focus:ring-maroon-500/30 text-center"
                                                   placeholder="0">
                                        </div>
                                        <div class="bg-gradient-to-br from-maroon-50 via-red-50 to-red-100 rounded-lg p-4 border-2 border-maroon-300 shadow-md">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-8 h-8 bg-maroon-600 rounded-lg flex items-center justify-center shadow-sm">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <label class="block text-sm font-semibold text-maroon-900 uppercase tracking-wide">PEER</label>
                                            </div>
                                            <input type="number" name="peer_publications_count" min="0" step="1" value="{{ old('peer_publications_count', $peer_publications_count) }}" 
                                                   class="w-full border-2 border-maroon-400 rounded-lg px-4 py-2.5 text-xl font-bold bg-white focus:border-maroon-600 focus:ring-2 focus:ring-maroon-500/30 text-center"
                                                   placeholder="0">
                                        </div>
                                    </div>
                                </div>

                                <!-- Calendar Events Tab -->
                                <div x-show="activeTab === 'calendar'" style="display: none;">
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                                            <div class="overflow-x-auto">
                                                <table class="w-full">
                                                <thead class="bg-white border-b border-gray-200">
                                                    <tr>
                                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-16"></th>
                                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-48">Event Date</th>
                                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-24">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="marksRepeater" class="bg-white divide-y divide-gray-200">
                                                        @php($marks = old('calendar_marks', $calendar_marks ?? []))
                                                        @if(empty($marks))
                                                            @php($marks = [[ 'date' => '', 'note' => '' ]])
                                                        @endif
                                                        @foreach($marks as $idx => $mark)
                                                    <tr class="hover:bg-amber-50/50 transition-colors duration-150">
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center shadow-sm">
                                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                    </svg>
                                                                </div>
                                                            </td>
                                                        <td class="px-4 py-4 whitespace-nowrap w-48">
                                                                <input type="date" name="calendar_marks[{{ $idx }}][date]" value="{{ $mark['date'] ?? '' }}" 
                                                                   class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                                                            </td>
                                                        <td class="px-4 py-4">
                                                                <input type="text" name="calendar_marks[{{ $idx }}][note]" value="{{ $mark['note'] ?? '' }}" 
                                                                   placeholder="Enter event description..." 
                                                                   class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                                                            </td>
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                                <button type="button" onclick="removeMarkRow(this)" 
                                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-colors" 
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

                                <!-- Announcements Tab -->
                                <div x-show="activeTab === 'announcements'" style="display: none;">
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                                        <div class="overflow-x-auto">
                                            <table class="w-full">
                                                <thead class="bg-white border-b border-gray-200">
                                                    <tr>
                                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-16"></th>
                                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Title</th>
                                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-24">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="announcementsRepeater" class="bg-white divide-y divide-gray-200">
                                                    @php($announcements = old('announcements', $announcements ?? []))
                                                    @if(empty($announcements))
                                                        @php($announcements = [['title' => '', 'description' => '']])
                                                    @endif
                                                    @foreach($announcements as $idx => $announcement)
                                                    <tr class="hover:bg-indigo-50/50 transition-colors duration-150">
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-blue-500 rounded-lg flex items-center justify-center shadow-sm">
                                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                                </svg>
                            </div>
                                                        </td>
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                            <input type="text" name="announcements[{{ $idx }}][title]" value="{{ $announcement['title'] ?? '' }}" 
                                                                   placeholder="Enter announcement title..." 
                                                                   class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                                                        </td>
                                                        <td class="px-4 py-4">
                                                            <input type="text" name="announcements[{{ $idx }}][description]" value="{{ $announcement['description'] ?? '' }}" 
                                                                   placeholder="Enter announcement description..." 
                                                                   class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                                                        </td>
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                            <button type="button" onclick="removeAnnouncementRow(this)" 
                                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-colors" 
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
                            
                            <!-- Floating Action Buttons -->
                            <button type="button" 
                                    x-show="activeTab === 'calendar'"
                                    onclick="addMarkRow()" 
                                    class="absolute bottom-6 right-6 w-14 h-14 bg-amber-600 text-white rounded-full hover:bg-amber-700 shadow-lg hover:shadow-xl flex items-center justify-center z-10" 
                                    title="Add Event"
                                    style="display: none;">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                            <button type="button" 
                                    x-show="activeTab === 'announcements'"
                                    onclick="addAnnouncementRow()" 
                                    class="absolute bottom-6 right-6 w-14 h-14 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 shadow-lg hover:shadow-xl flex items-center justify-center z-10" 
                                    title="Add Announcement"
                                    style="display: none;">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                        </div>

                        <!-- USEP Researchers Management Section -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6 relative">
                            <!-- Header -->
                            <div class="px-6 py-4 bg-gradient-to-r from-maroon-50 to-red-50 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-maroon-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">USEP Researchers</h3>
                                            <p class="text-sm text-gray-600 mt-1">Manage researcher profiles displayed on the landing page</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button type="submit" name="save_researchers" value="1"
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed font-medium text-sm shadow-sm"
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
                            <div class="p-6 overflow-y-auto" style="height: 500px;">
                                @if($errors->any() && (old('save_researchers') || request()->has('save_researchers')))
                                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 rounded-md">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                                    @foreach($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div id="researchersRepeater" class="space-y-4">
                                        @php($researchers = old('researchers', $researchers ?? []))
                                        @if(empty($researchers))
                                            @php($researchers = [['name' => '', 'title' => '', 'research_areas' => '', 'bio' => '', 'status_badge' => 'Active', 'background_color' => 'maroon', 'profile_link' => '']])
                                        @endif
                                        @foreach($researchers as $idx => $researcher)
                                        <div class="researcher-card bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden" x-data="{ isExpanded: false }">
                                            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100/50 border-b border-gray-200">
                                                <div class="flex items-center justify-between gap-4">
                                                    <button type="button" @click="isExpanded = !isExpanded" class="flex items-center gap-4 flex-1 text-left group hover:opacity-90 transition-opacity">
                                                        <!-- Profile Picture or Icon -->
                                                        <div class="relative flex-shrink-0">
                                                            @if(!empty($researcher['photo_path']))
                                                                <div class="w-14 h-14 rounded-xl overflow-hidden ring-2 ring-white shadow-md">
                                                                    <img src="/storage/{{ $researcher['photo_path'] }}" alt="Profile" class="w-full h-full object-cover">
                                                                </div>
                                                            @else
                                                                <div class="w-14 h-14 bg-gradient-to-br from-maroon-500 to-red-600 rounded-xl flex items-center justify-center shadow-md ring-2 ring-white">
                                                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                        </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <!-- Name and Title -->
                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <h4 class="text-lg font-bold text-gray-900 group-hover:text-maroon-600 transition-colors">
                                                                {{ !empty($researcher['name']) ? $researcher['name'] : 'New Researcher' }}
                                                            </h4>
                                                        </div>
                                                            @if(!empty($researcher['title']))
                                                                <p class="text-sm text-gray-600 font-medium truncate">{{ $researcher['title'] }}</p>
                                                            @else
                                                                <p class="text-xs text-gray-400 italic">No title set</p>
                                                            @endif
                                                            @if(!empty($researcher['research_areas']))
                                                                <div class="flex items-center gap-1.5 mt-1.5">
                                                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                                    </svg>
                                                                    <p class="text-xs text-gray-500 truncate">{{ is_array($researcher['research_areas'] ?? []) ? implode(', ', $researcher['research_areas']) : ($researcher['research_areas'] ?? '') }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <!-- Chevron Icon -->
                                                        <div class="flex items-center gap-3 flex-shrink-0">
                                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-maroon-600 transition-all duration-200 flex-shrink-0" :class="{ 'rotate-180': isExpanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                        </div>
                                                    </button>
                                                    
                                                    <!-- Remove Button -->
                                                    <button type="button" onclick="removeResearcherRow(this)" 
                                                            class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 flex-shrink-0" 
                                                            title="Remove Researcher">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div x-show="isExpanded" style="display: none;" class="px-5 pt-5 pb-5 space-y-5 border-t-2 border-gray-200">
                                                <!-- Row 1: Profile Picture | Biography -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                                                        <div class="flex items-center gap-4">
                                                            <div class="relative">
                                                                <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden ring-2 ring-gray-200 relative">
                                                                    <img id="preview-{{ $idx }}" src="{{ !empty($researcher['photo_path']) ? '/storage/' . $researcher['photo_path'] : '' }}" alt="Profile preview" class="w-full h-full object-cover {{ !empty($researcher['photo_path']) ? '' : 'hidden' }}">
                                                                    <svg class="w-8 h-8 text-gray-400 absolute inset-0 m-auto {{ !empty($researcher['photo_path']) ? 'hidden' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="placeholder-{{ $idx }}">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 0 18 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                                    </svg>
                                                                </div>
                                                                <label for="photo-{{ $idx }}" class="absolute inset-0 rounded-lg bg-black/30 opacity-0 hover:opacity-100 flex items-center justify-center cursor-pointer">
                                                                    <span class="text-xs text-white font-medium px-2 py-1 bg-black/40 rounded">Change</span>
                                                                </label>
                                                            </div>
                                                            <div class="flex-1">
                                                                @if(!empty($researcher['photo_path']))
                                                                    <input type="hidden" name="researchers[{{ $idx }}][photo_path]" value="{{ $researcher['photo_path'] }}">
                                                                @endif
                                                                <input type="file" name="researchers[{{ $idx }}][photo]" id="photo-{{ $idx }}" 
                                                                       accept="image/*" onchange="previewImage(this, {{ $idx }})"
                                                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-maroon-50 file:text-maroon-700 hover:file:bg-maroon-100">
                                                                <p class="text-xs text-gray-500 mt-1">JPG, PNG up to 10MB (will be converted to WebP)</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Biography</label>
                                                        <textarea name="researchers[{{ $idx }}][bio]" rows="4" 
                                                                  placeholder="Brief description of research focus and achievements..." 
                                                                  class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 resize-none">{{ $researcher['bio'] ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                                
                                                <!-- Row 2: Full Name | Email Address -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                                        <input type="text" name="researchers[{{ $idx }}][name]" value="{{ $researcher['name'] ?? '' }}" 
                                                               placeholder="Dr. John Doe" 
                                                               class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                                        <input type="email" name="researchers[{{ $idx }}][profile_link]" value="{{ $researcher['profile_link'] ?? '' }}" 
                                                               placeholder="researcher@example.com" 
                                                               class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                                                    </div>
                                                </div>
                                                
                                                <!-- Row 3: Title/Position | Status Badge -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Title/Position *</label>
                                                        <input type="text" name="researchers[{{ $idx }}][title]" value="{{ $researcher['title'] ?? '' }}" 
                                                               placeholder="Professor, College of Engineering" 
                                                               class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Badge</label>
                                                        <select name="researchers[{{ $idx }}][status_badge]" 
                                                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                                                            <option value="Active" {{ ($researcher['status_badge'] ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                                            <option value="Research" {{ ($researcher['status_badge'] ?? '') == 'Research' ? 'selected' : '' }}>Research</option>
                                                            <option value="Innovation" {{ ($researcher['status_badge'] ?? '') == 'Innovation' ? 'selected' : '' }}>Innovation</option>
                                                            <option value="Leadership" {{ ($researcher['status_badge'] ?? '') == 'Leadership' ? 'selected' : '' }}>Leadership</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <!-- Row 4: Research Areas | Card Background Color -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Research Areas</label>
                                                        <input type="text" name="researchers[{{ $idx }}][research_areas]" value="{{ is_array($researcher['research_areas'] ?? []) ? implode(', ', $researcher['research_areas']) : ($researcher['research_areas'] ?? '') }}" 
                                                               placeholder="AI, Machine Learning, Data Science" 
                                                               class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Card Background Color</label>
                                                        <select name="researchers[{{ $idx }}][background_color]" 
                                                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                                                            <option value="maroon" {{ ($researcher['background_color'] ?? '') == 'maroon' ? 'selected' : '' }}>Maroon</option>
                                                            <option value="blue" {{ ($researcher['background_color'] ?? '') == 'blue' ? 'selected' : '' }}>Blue</option>
                                                            <option value="green" {{ ($researcher['background_color'] ?? '') == 'green' ? 'selected' : '' }}>Green</option>
                                                            <option value="purple" {{ ($researcher['background_color'] ?? '') == 'purple' ? 'selected' : '' }}>Purple</option>
                                                            <option value="orange" {{ ($researcher['background_color'] ?? '') == 'orange' ? 'selected' : '' }}>Orange</option>
                                                            <option value="teal" {{ ($researcher['background_color'] ?? '') == 'teal' ? 'selected' : '' }}>Teal</option>
                                                            <option value="rose" {{ ($researcher['background_color'] ?? '') == 'rose' ? 'selected' : '' }}>Rose</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <!-- Row 5: Research Profile Links (at bottom) -->
                                                <div class="mt-5 pt-5 border-t border-gray-200">
                                                    <h4 class="text-sm font-semibold text-gray-700 mb-4">Research Profile Links</h4>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-2">SCOPUS Link</label>
                                                            <input type="url" name="researchers[{{ $idx }}][scopus_link]" value="{{ $researcher['scopus_link'] ?? '' }}" 
                                                                   placeholder="https://www.scopus.com/authid/detail.uri?authorId=..." 
                                                                   class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                                                        </div>
                                                        
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-2">ORCID Link</label>
                                                            <input type="url" name="researchers[{{ $idx }}][orcid_link]" value="{{ $researcher['orcid_link'] ?? '' }}" 
                                                                   placeholder="https://orcid.org/0000-0000-0000-0000" 
                                                                   class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                                                        </div>
                                                        
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-2">WOS (Web of Science) Link</label>
                                                            <input type="url" name="researchers[{{ $idx }}][wos_link]" value="{{ $researcher['wos_link'] ?? '' }}" 
                                                                   placeholder="https://www.webofscience.com/wos/author/record/..." 
                                                                   class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                                                        </div>
                                                        
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-2">Google Scholar Link</label>
                                                            <input type="url" name="researchers[{{ $idx }}][google_scholar_link]" value="{{ $researcher['google_scholar_link'] ?? '' }}" 
                                                                   placeholder="https://scholar.google.com/citations?user=..." 
                                                                   class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            
                            <!-- Floating Action Button -->
                            <button type="button" onclick="addResearcherRow()" 
                                    class="absolute bottom-6 right-6 w-14 h-14 bg-maroon-600 text-white rounded-full hover:bg-maroon-700 shadow-lg hover:shadow-xl flex items-center justify-center z-10" 
                                    title="Add Researcher">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                </div>
                        
                    </form>
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
                if (!button) return;
                
                if (hasChanges) {
                    button.disabled = false;
                    button.className = 'inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-maroon-600 to-red-600 text-white rounded-lg hover:from-maroon-700 hover:to-red-700 transition-all duration-200 font-medium text-sm shadow-md hover:shadow-lg';
                } else {
                    button.disabled = true;
                    button.className = 'inline-flex items-center gap-2 px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed transition-all duration-200 font-medium text-sm';
                }
            }
            
            // Make updateSaveButton globally available
            window.updateSaveButton = updateSaveButton;
            
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
            // Note: Calendar marks share the same save button with announcements, so we just call checkAnnouncementsChanges
            function checkCalendarChanges() {
                // Calendar marks share the save button with announcements, so trigger that check
                if (window.checkAnnouncementsChanges) {
                    checkAnnouncementsChanges();
                }
            }
            
            function checkAnnouncementsChanges() {
                // Build announcements as nested array structure
                const announcementInputs = document.querySelectorAll('input[name^="announcements"]');
                const currentAnnouncements = [];
                announcementInputs.forEach(input => {
                    const name = input.name;
                    const match = name.match(/announcements\[(\d+)\]\[(title|description)\]/);
                    if (match) {
                        const index = parseInt(match[1]);
                        const field = match[2];
                        if (!currentAnnouncements[index]) {
                            currentAnnouncements[index] = { title: '', description: '' };
                        }
                        currentAnnouncements[index][field] = input.value;
                    }
                });
                const originalAnnouncements = window.originalValues.announcements?.announcements || [];
                const hasAnnouncementChanges = JSON.stringify(currentAnnouncements) !== JSON.stringify(originalAnnouncements);
                
                // Also check calendar changes since they share the same save button
                const calendarInputs = document.querySelectorAll('input[name^="calendar_marks"]');
                const currentMarks = [];
                calendarInputs.forEach(input => {
                    const name = input.name;
                    const match = name.match(/calendar_marks\[(\d+)\]\[(date|note)\]/);
                    if (match) {
                        const index = parseInt(match[1]);
                        const field = match[2];
                        if (!currentMarks[index]) {
                            currentMarks[index] = { date: '', note: '' };
                        }
                        currentMarks[index][field] = input.value;
                    }
                });
                const originalMarks = window.originalValues.calendar?.marks || [];
                const hasCalendarChanges = JSON.stringify(currentMarks) !== JSON.stringify(originalMarks);
                
                // Check publication counts changes
                const scopusCount = document.querySelector('input[name="scopus_publications_count"]');
                const wosCount = document.querySelector('input[name="wos_publications_count"]');
                const aciCount = document.querySelector('input[name="aci_publications_count"]');
                const peerCount = document.querySelector('input[name="peer_publications_count"]');
                
                const currentCounts = {
                    scopus: scopusCount ? scopusCount.value : '',
                    wos: wosCount ? wosCount.value : '',
                    aci: aciCount ? aciCount.value : '',
                    peer: peerCount ? peerCount.value : ''
                };
                const originalCounts = window.originalValues.publicationCounts || {};
                const hasPublicationCountChanges = JSON.stringify(currentCounts) !== JSON.stringify(originalCounts);
                
                // Enable button if any section has changes
                const hasChanges = hasAnnouncementChanges || hasCalendarChanges || hasPublicationCountChanges;
                
                const saveBtn = document.querySelector('button[name="save_announcements"]');
                if (saveBtn) {
                    updateSaveButton(saveBtn, hasChanges);
                }
            }
            
            // Check for changes in researchers section
            function checkResearcherChanges() {
                // Build researchers as nested array structure
                const researcherInputs = document.querySelectorAll('input[name^="researchers"]:not([type="file"]), select[name^="researchers"], textarea[name^="researchers"]');
                const researcherFileInputs = document.querySelectorAll('input[type="file"][name^="researchers"]');
                const currentResearchers = {};
                
                // Process all non-file inputs
                researcherInputs.forEach(input => {
                    const name = input.name;
                    const match = name.match(/researchers\[(\d+)\]\[(.+)\]/);
                    if (match) {
                        const index = parseInt(match[1]);
                        const field = match[2];
                        if (!currentResearchers[index]) {
                            currentResearchers[index] = {};
                        }
                        // Normalize values: trim strings, convert empty to empty string
                        const value = input.value || '';
                        currentResearchers[index][field] = typeof value === 'string' ? value.trim() : value;
                    }
                });
                
                // Check for file input changes (new photo uploads)
                let hasFileChanges = false;
                researcherFileInputs.forEach(input => {
                    if (input.files && input.files.length > 0) {
                        hasFileChanges = true;
                    }
                });
                
                // Normalize original researchers for comparison
                const originalResearchers = window.originalValues?.researchers?.researchers || [];
                const normalizedOriginal = {};
                originalResearchers.forEach((researcher, idx) => {
                    if (researcher) {
                        normalizedOriginal[idx] = {};
                        Object.keys(researcher).forEach(key => {
                            const value = researcher[key];
                            normalizedOriginal[idx][key] = typeof value === 'string' ? value.trim() : (value || '');
                        });
                    }
                });
                
                // Compare current with original
                const currentKeys = Object.keys(currentResearchers).map(k => parseInt(k)).sort((a, b) => a - b);
                const originalKeys = Object.keys(normalizedOriginal).map(k => parseInt(k)).sort((a, b) => a - b);
                
                // Check if arrays have different lengths
                if (currentKeys.length !== originalKeys.length) {
                    const saveBtn = document.querySelector('button[name="save_researchers"]');
                    if (saveBtn) {
                        updateSaveButton(saveBtn, true);
                    }
                    return;
                }
                
                // Check if any researcher has changed
                let hasChanges = hasFileChanges;
                
                // Check each researcher
                for (const key of currentKeys) {
                    const current = currentResearchers[key] || {};
                    const original = normalizedOriginal[key] || {};
                    
                    // Get all unique keys from both objects
                    const allKeys = new Set([...Object.keys(current), ...Object.keys(original)]);
                    
                    for (const field of allKeys) {
                        const currentValue = (current[field] || '').toString().trim();
                        const originalValue = (original[field] || '').toString().trim();
                        
                        if (currentValue !== originalValue) {
                            hasChanges = true;
                            break;
                        }
                    }
                    
                    if (hasChanges) break;
                }
                
                // Also check if any original researcher is missing
                for (const key of originalKeys) {
                    if (!currentResearchers[key]) {
                        hasChanges = true;
                        break;
                    }
                }
                
                // Fallback: If there's any non-empty researcher data, enable the button
                // This ensures the button is enabled even if change detection didn't work properly
                if (!hasChanges) {
                    for (const key of currentKeys) {
                        const researcher = currentResearchers[key] || {};
                        // Check if researcher has any meaningful data (name or title are required fields)
                        const hasName = researcher.name && researcher.name.trim() !== '';
                        const hasTitle = researcher.title && researcher.title.trim() !== '';
                        const hasBio = researcher.bio && researcher.bio.trim() !== '';
                        const hasResearchAreas = researcher.research_areas && researcher.research_areas.trim() !== '';
                        
                        if (hasName || hasTitle || hasBio || hasResearchAreas) {
                            hasChanges = true;
                            break;
                        }
                    }
                }
                
                const saveBtn = document.querySelector('button[name="save_researchers"]');
                if (saveBtn) {
                    updateSaveButton(saveBtn, hasChanges);
                }
            }
            
            // Make checkResearcherChanges available globally immediately
            window.checkResearcherChanges = checkResearcherChanges;
            
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
                const researcherInputs = document.querySelectorAll('input[name^="researchers"], select[name^="researchers"], textarea[name^="researchers"]');
                const rankInputs = document.querySelectorAll('input[name="academic_ranks[]"]');
                const collegeInputs = document.querySelectorAll('input[name="colleges[]"]');
                const othersInputs = document.querySelectorAll('input[name="others_indexing_options[]"]');
                
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
                        marks: (function() {
                            // Initialize calendar marks as array of objects (date, note pairs)
                            const marks = [];
                            calendarInputs.forEach(input => {
                                const name = input.name;
                                const match = name.match(/calendar_marks\[(\d+)\]\[(date|note)\]/);
                                if (match) {
                                    const index = parseInt(match[1]);
                                    const field = match[2];
                                    if (!marks[index]) {
                                        marks[index] = { date: '', note: '' };
                                    }
                                    marks[index][field] = input.value;
                                }
                            });
                            return marks;
                        })()
                    },
                    announcements: {
                        announcements: (function() {
                            // Build announcements as nested array structure
                            const announcements = [];
                            announcementInputs.forEach(input => {
                                const name = input.name;
                                const match = name.match(/announcements\[(\d+)\]\[(title|description)\]/);
                                if (match) {
                                    const index = parseInt(match[1]);
                                    const field = match[2];
                                    if (!announcements[index]) {
                                        announcements[index] = { title: '', description: '' };
                                    }
                                    announcements[index][field] = input.value;
                                }
                            });
                            return announcements;
                        })()
                    },
                    publicationCounts: {
                        scopus: document.querySelector('input[name="scopus_publications_count"]')?.value || '',
                        wos: document.querySelector('input[name="wos_publications_count"]')?.value || '',
                        aci: document.querySelector('input[name="aci_publications_count"]')?.value || '',
                        peer: document.querySelector('input[name="peer_publications_count"]')?.value || ''
                    },
                    researchers: {
                        researchers: (function() {
                            // Build researchers as nested array structure
                            const researchers = [];
                            
                            // Process all non-file inputs
                            researcherInputs.forEach(input => {
                                if (input.type === 'file') return; // Skip file inputs
                                const name = input.name;
                                const match = name.match(/researchers\[(\d+)\]\[(.+)\]/);
                                if (match) {
                                    const index = parseInt(match[1]);
                                    const field = match[2];
                                    if (!researchers[index]) {
                                        researchers[index] = {};
                                    }
                                    researchers[index][field] = input.value || '';
                                }
                            });
                            
                            // Also include photo_path from hidden inputs
                            const photoPathInputs = document.querySelectorAll('input[type="hidden"][name^="researchers"][name*="[photo_path]"]');
                            photoPathInputs.forEach(input => {
                                const name = input.name;
                                const match = name.match(/researchers\[(\d+)\]\[photo_path\]/);
                                if (match) {
                                    const index = parseInt(match[1]);
                                    if (!researchers[index]) {
                                        researchers[index] = {};
                                    }
                                    researchers[index]['photo_path'] = input.value || '';
                                }
                            });
                            
                            return researchers;
                        })()
                    },
                    formDropdowns: {
                        academic_ranks: Array.from(rankInputs).map(input => input.value.trim()),
                        colleges: Array.from(collegeInputs).map(input => input.value.trim()),
                        others_indexing_options: Array.from(othersInputs).map(input => input.value.trim())
                    }
                };
                
                
                // Add event listeners
                if (deputyName) deputyName.addEventListener('input', checkOfficialChanges);
                if (deputyTitle) deputyTitle.addEventListener('input', checkOfficialChanges);
                if (rddName) rddName.addEventListener('input', checkOfficialChanges);
                if (rddTitle) rddTitle.addEventListener('input', checkOfficialChanges);
                
                // Citations checkbox change is handled by updateCheckboxUI function
                
                calendarInputs.forEach(input => {
                    input.addEventListener('input', checkAnnouncementsChanges); // Calendar shares save button with announcements
                    input.addEventListener('change', checkAnnouncementsChanges); // Also listen to change for date inputs
                });
                
                announcementInputs.forEach(input => {
                    input.addEventListener('input', checkAnnouncementsChanges);
                });
                
                // Add event listeners for publication counters
                const scopusCount = document.querySelector('input[name="scopus_publications_count"]');
                const wosCount = document.querySelector('input[name="wos_publications_count"]');
                const aciCount = document.querySelector('input[name="aci_publications_count"]');
                const peerCount = document.querySelector('input[name="peer_publications_count"]');
                if (scopusCount) scopusCount.addEventListener('input', checkAnnouncementsChanges);
                if (wosCount) wosCount.addEventListener('input', checkAnnouncementsChanges);
                if (aciCount) aciCount.addEventListener('input', checkAnnouncementsChanges);
                if (peerCount) peerCount.addEventListener('input', checkAnnouncementsChanges);
                
                researcherInputs.forEach(input => {
                    if (input.type !== 'file') { // Skip file inputs for input event
                        input.addEventListener('input', checkResearcherChanges);
                        input.addEventListener('change', checkResearcherChanges); // Also listen to change for select elements
                    }
                });
                
                // Add change listeners for file inputs (photo uploads)
                const researcherFileInputs = document.querySelectorAll('input[type="file"][name^="researchers"]');
                researcherFileInputs.forEach(input => {
                    input.addEventListener('change', checkResearcherChanges);
                });
                
                // Make functions globally available
                window.checkOfficialChanges = checkOfficialChanges;
                window.checkFeaturesChanges = checkFeaturesChanges;
                window.checkCalendarChanges = checkCalendarChanges;
                window.checkAnnouncementsChanges = checkAnnouncementsChanges;
                window.checkResearcherChanges = checkResearcherChanges;
                
                // Check initial state
                checkOfficialChanges();
                checkFeaturesChanges();
                checkCalendarChanges();
        checkAnnouncementsChanges();
        checkResearcherChanges();
        
        // Initialize tag counts on page load
        const ranksContainer = document.getElementById('academicRanksContainer');
        const collegesContainer = document.getElementById('collegesContainer');
        const othersContainer = document.getElementById('othersIndexingContainer');
        if (ranksContainer) updateCount('ranksCount', ranksContainer);
        if (collegesContainer) updateCount('collegesCount', collegesContainer);
        if (othersContainer) updateCount('othersCount', othersContainer);
        
        // Initialize name header updates for existing researchers
        function initResearcherNameDisplays() {
            document.querySelectorAll('.researcher-card').forEach(card => {
                const nameInput = card.querySelector('input[name*="[name]"]');
                const titleInput = card.querySelector('input[name*="[title]"]');
                const nameHeader = card.querySelector('h4');
                
                // Create title element if it doesn't exist
                let titleElement = card.querySelector('.researcher-title-text');
                if (!titleElement && nameHeader) {
                    titleElement = document.createElement('p');
                    titleElement.className = 'researcher-title-text text-sm text-gray-500 mt-1';
                    titleElement.style.display = 'none';
                    nameHeader.parentElement.appendChild(titleElement);
                }
                
                if (nameInput && nameHeader) {
                    // Update on input
                    nameInput.addEventListener('input', function() {
                        nameHeader.textContent = this.value.trim() || 'New Researcher';
                    });
                }
                
                if (titleInput && titleElement) {
                    // Update title display
                    const updateTitle = function() {
                        const titleValue = titleInput.value.trim();
                        if (titleValue) {
                            titleElement.textContent = titleValue;
                            titleElement.style.display = 'block';
                        } else {
                            titleElement.style.display = 'none';
                        }
                    };
                    titleInput.addEventListener('input', updateTitle);
                    // Set initial state
                    updateTitle();
                }
            });
        }
        
        // Initialize name displays
        initResearcherNameDisplays();
        
        // Re-initialize on Turbo navigation and after successful save
        function reinitializeFormDetection() {
            if (typeof initFormChangeDetection === 'function') {
                initFormChangeDetection();
            }
        }
        
        document.addEventListener('turbo:load', function() {
            reinitializeFormDetection();
            initResearcherNameDisplays();
        });
        
        // Initialize announcements state tracking
        initializeAnnouncementsState();
        
        // Add click event listeners to save buttons that are outside the form
        document.addEventListener('click', function(e) {
            if (e.target.closest('button[name="save_announcements"]')) {
                const btn = e.target.closest('button[name="save_announcements"]');
                console.log('[DEBUG] save_announcements button clicked');
                console.log('[DEBUG] Button disabled?', btn.disabled);
                
                // Prevent default button behavior
                e.preventDefault();
                e.stopPropagation();
                
                // If button is disabled, don't submit
                if (btn.disabled) {
                    console.log('[DEBUG] Button is disabled, not submitting');
                    return;
                }
                
                // Find the main settings form
                const settingsForm = document.getElementById('settings-form');
                if (!settingsForm) {
                    console.error('[DEBUG] Main settings form not found!');
                    return;
                }
                
                console.log('[DEBUG] Found form, submitting...');
                
                // Create a hidden input to indicate which button was clicked
                let submitterInput = settingsForm.querySelector('input[name="save_announcements"]');
                if (!submitterInput) {
                    submitterInput = document.createElement('input');
                    submitterInput.type = 'hidden';
                    submitterInput.name = 'save_announcements';
                    submitterInput.value = '1';
                    settingsForm.appendChild(submitterInput);
                }
                
                // Submit the form
                settingsForm.submit();
                
            } else if (e.target.closest('button[name="save_researchers"]')) {
                const btn = e.target.closest('button[name="save_researchers"]');
                console.log('[DEBUG] save_researchers button clicked');
                console.log('[DEBUG] Button disabled?', btn.disabled);
                
                // Prevent default button behavior
                e.preventDefault();
                e.stopPropagation();
                
                // Find the main settings form
                const settingsForm = document.getElementById('settings-form');
                if (!settingsForm) {
                    console.error('[DEBUG] Main settings form not found!');
                    return;
                }
                
                // Check if there's any researcher data in the form
                const researcherInputs = settingsForm.querySelectorAll('input[name^="researchers"], select[name^="researchers"], textarea[name^="researchers"]');
                let hasResearcherData = false;
                researcherInputs.forEach(input => {
                    if (input.type === 'file') {
                        if (input.files && input.files.length > 0) {
                            hasResearcherData = true;
                        }
                    } else if (input.value && input.value.trim() !== '') {
                        hasResearcherData = true;
                    }
                });
                
                // If button is disabled and there's no data, don't submit
                if (btn.disabled && !hasResearcherData) {
                    console.log('[DEBUG] Button is disabled and no researcher data found, not submitting');
                    return;
                }
                
                console.log('[DEBUG] Found form, submitting researchers...', { hasResearcherData, buttonDisabled: btn.disabled });
                
                // Debug: Log all researcher fields before submission
                const allResearcherFields = settingsForm.querySelectorAll('input[name^="researchers"], select[name^="researchers"], textarea[name^="researchers"]');
                console.log('[DEBUG] Total researcher fields found:', allResearcherFields.length);
                allResearcherFields.forEach((field, idx) => {
                    if (field.type === 'file') {
                        console.log(`[DEBUG] Field ${idx}: ${field.name} = FILE (${field.files?.length || 0} files)`);
                    } else {
                        console.log(`[DEBUG] Field ${idx}: ${field.name} = "${field.value}"`);
                    }
                });
                
                // Always create a hidden input to indicate which button was clicked
                // This ensures the value is submitted even if the button appears disabled
                let submitterInput = settingsForm.querySelector('input[name="save_researchers"]');
                if (!submitterInput) {
                    submitterInput = document.createElement('input');
                    submitterInput.type = 'hidden';
                    submitterInput.name = 'save_researchers';
                    submitterInput.value = '1';
                    settingsForm.appendChild(submitterInput);
                } else {
                    // Ensure the value is set
                    submitterInput.value = '1';
                }
                
                // Ensure all researcher fields are properly included in the form
                // Check if fields are actually in the form
                const formData = new FormData(settingsForm);
                const researcherKeys = [];
                for (let [key, value] of formData.entries()) {
                    if (key.startsWith('researchers')) {
                        researcherKeys.push(key);
                    }
                }
                console.log('[DEBUG] Researcher keys in FormData:', researcherKeys.length, researcherKeys);
                
                // Submit the form
                settingsForm.submit();
            }
        }, true); // Use capture phase to catch before any preventDefault
        
        // Form submission handling with Turbo integration
        document.addEventListener('submit', function(e) {
            console.log('[DEBUG] Form submit event triggered');
            console.log('[DEBUG] Submitter:', e.submitter);
            console.log('[DEBUG] Submitter name:', e.submitter?.name);
            console.log('[DEBUG] Submitter value:', e.submitter?.value);
            console.log('[DEBUG] Form action:', e.target.action);
            console.log('[DEBUG] Form method:', e.target.method);
            
            if (e.submitter && e.submitter.name === 'save_announcements') {
                console.log('[DEBUG] Saving announcements/calendar/publication counts');
                
                // Collect form data for debugging
                const formData = new FormData(e.target);
                const data = {};
                for (let [key, value] of formData.entries()) {
                    if (data[key]) {
                        if (Array.isArray(data[key])) {
                            data[key].push(value);
                        } else {
                            data[key] = [data[key], value];
                        }
                    } else {
                        data[key] = value;
                    }
                }
                console.log('[DEBUG] Form data being submitted:', data);
                console.log('[DEBUG] Announcements data:', formData.getAll('announcements[]'));
                console.log('[DEBUG] Calendar marks:', Array.from(formData.entries()).filter(([k]) => k.startsWith('calendar_marks')));
                console.log('[DEBUG] Publication counts:', {
                    scopus: formData.get('scopus_publications_count'),
                    wos: formData.get('wos_publications_count'),
                    aci: formData.get('aci_publications_count'),
                    peer: formData.get('peer_publications_count')
                });
                
                // Let the form submit normally - don't prevent default
                // The server will handle the submission and redirect back
            } else if (e.submitter && e.submitter.name === 'save_researchers') {
                console.log('[DEBUG] Saving researchers - Form submit event');
                
                // Log form data for debugging
                const formData = new FormData(e.target);
                const researchersData = [];
                for (let [key, value] of formData.entries()) {
                    if (key.startsWith('researchers[')) {
                        researchersData.push({ key, value: value instanceof File ? value.name : value });
                    }
                }
                console.log('[DEBUG] Researchers data being submitted:', researchersData);
                console.log('[DEBUG] Total researcher fields:', researchersData.length);
                
                // Don't prevent default - let the form submit normally
                // The form already contains all the researcher fields, so normal submission should work
                // The save_researchers flag is already set in the button's value attribute
                
                // After successful save, originalValues will be recalculated on page reload
                // This happens automatically when the page loads via initFormChangeDetection()
            } else if (e.submitter) {
                console.log('[DEBUG] Other save button clicked:', e.submitter.name);
            } else {
                console.warn('[DEBUG] Form submitted but no submitter found!');
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
            row.className = 'hover:bg-amber-50/50 transition-colors duration-150';
            row.innerHTML = `
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap w-48">
                    <input type="date" name="calendar_marks[${index}][date]" 
                           class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                </td>
                <td class="px-4 py-4">
                    <input type="text" name="calendar_marks[${index}][note]" 
                           placeholder="Enter event description..." 
                           class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <button type="button" onclick="removeMarkRow(this)" 
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-colors" 
                            title="Remove Event">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </td>
            `;
            
            // Append at the bottom
                container.appendChild(row);
            
            // Add event listeners to new inputs
            row.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', function() {
                    // Trigger announcements change detection (calendar shares the same save button)
                    if (window.checkAnnouncementsChanges) {
                        window.checkAnnouncementsChanges();
                    }
                });
                input.addEventListener('change', function() {
                    // Also trigger on change for date inputs
                    if (window.checkAnnouncementsChanges) {
                        window.checkAnnouncementsChanges();
                    }
                });
            });
            
            // Trigger change detection immediately since we added a new row
            if (window.checkAnnouncementsChanges) {
                // Use setTimeout to ensure DOM is updated
                setTimeout(() => {
                    window.checkAnnouncementsChanges();
                }, 0);
            }
        }

        function removeMarkRow(btn) {
            const row = btn.closest('tr');
            const container = document.getElementById('marksRepeater');
            if (row && container) {
                row.remove();
                
                // If no rows left, add an empty entry for consistency
                if (container.querySelectorAll('tr').length === 0) {
                    addMarkRow();
                }
                
                // Trigger announcements change detection (calendar shares the same save button)
                if (window.checkAnnouncementsChanges) {
                    window.checkAnnouncementsChanges();
                }
            }
        }

        // Simple announcements management
        function addAnnouncementRow() {
            const container = document.getElementById('announcementsRepeater');
            if (!container) return;
            
            const index = container.querySelectorAll('tr').length;
            const row = document.createElement('tr');
            row.className = 'hover:bg-indigo-50/50 transition-colors duration-150';
            row.innerHTML = `
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-blue-500 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <input type="text" name="announcements[${index}][title]" 
                           placeholder="Enter announcement title..." 
                           class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                </td>
                <td class="px-4 py-4">
                    <input type="text" name="announcements[${index}][description]" 
                           placeholder="Enter announcement description..." 
                           class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <button type="button" onclick="removeAnnouncementRow(this)" 
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-colors" 
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

        // Auto-save state
        // Use window object to avoid redeclaration errors with Turbo
        if (typeof window.pendingDelete === 'undefined') {
            window.pendingDelete = null;
        }
        if (typeof window.isSaving === 'undefined') {
            window.isSaving = false;
        }
        
        // Auto-save function for form dropdowns
        async function autoSaveFormDropdowns() {
            if (window.isSaving) return;
            window.isSaving = true;
            
            const form = document.getElementById('settings-form') || document.querySelector('form[method="POST"]');
            if (!form) {
                window.isSaving = false;
                return;
            }
            
            const formData = new FormData();
            formData.append('save_form_dropdowns', '1');
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('_method', 'PUT');
            
            // Collect all dropdown values
            const ranks = Array.from(document.querySelectorAll('#academicRanksContainer input[type="hidden"]')).map(inp => inp.value.trim()).filter(v => v);
            const colleges = Array.from(document.querySelectorAll('#collegesContainer input[type="hidden"]')).map(inp => inp.value.trim()).filter(v => v);
            const others = Array.from(document.querySelectorAll('#othersIndexingContainer input[type="hidden"]')).map(inp => inp.value.trim()).filter(v => v);
            
            // Clear and add fresh data
            formData.delete('academic_ranks[]');
            formData.delete('colleges[]');
            formData.delete('others_indexing_options[]');
            
            ranks.forEach(rank => formData.append('academic_ranks[]', rank));
            colleges.forEach(college => formData.append('colleges[]', college));
            others.forEach(option => formData.append('others_indexing_options[]', option));
            
            try {
                const actionUrl = form.action || '{{ route("admin.settings.update") }}';
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                
                if (response.ok) {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        if (data.success) {
                            showAutoSaveNotification('Changes saved successfully');
                        } else {
                            showAutoSaveNotification('Failed to save changes', 'error');
                        }
                    } else {
                        // Handle redirect response
                        showAutoSaveNotification('Changes saved successfully');
                    }
                } else {
                    showAutoSaveNotification('Failed to save changes', 'error');
                }
            } catch (error) {
                console.error('Auto-save error:', error);
                showAutoSaveNotification('Failed to save changes', 'error');
            } finally {
                window.isSaving = false;
            }
        }
        
        // Show auto-save notification
        function showAutoSaveNotification(message, type = 'success') {
            // Remove existing notification if any
            const existing = document.getElementById('autoSaveNotification');
            if (existing) existing.remove();
            
            const notification = document.createElement('div');
            notification.id = 'autoSaveNotification';
            notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 2000);
        }
        
        // Delete confirmation modal functions
        function showDeleteModal(btn, itemName, category) {
            if (!btn || !itemName || !category) {
                console.error('showDeleteModal: Missing required parameters', { btn, itemName, category });
                return;
            }
            
            try {
                window.pendingDelete = { btn, itemName, category };
                const modal = document.getElementById('deleteConfirmModal');
                const message = document.getElementById('deleteConfirmMessage');
                
                if (!modal) {
                    console.error('Delete confirmation modal not found');
                    return;
                }
                
                if (!message) {
                    console.error('Delete confirmation message element not found');
                    return;
                }
                
                message.textContent = `Are you sure you want to remove "${itemName}" from ${category}?`;
                modal.classList.remove('hidden');
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            } catch (error) {
                console.error('Error showing delete modal:', error);
            }
        }
        
        function closeDeleteModal() {
            try {
                const modal = document.getElementById('deleteConfirmModal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                }
                document.body.style.overflow = '';
                window.pendingDelete = null;
            } catch (error) {
                console.error('Error closing delete modal:', error);
            }
        }
        
        function confirmDelete() {
            if (!window.pendingDelete) {
                console.warn('No pending delete operation');
                return;
            }
            
            try {
                const { btn, category } = window.pendingDelete;
                if (!btn || !category) {
                    console.error('Invalid pending delete data');
                    closeDeleteModal();
                    return;
                }
                
                let container;
                let countId;
                
                if (category === 'Academic Ranks') {
                    container = document.getElementById('academicRanksContainer');
                    countId = 'ranksCount';
                } else if (category === 'Colleges') {
                    container = document.getElementById('collegesContainer');
                    countId = 'collegesCount';
                } else if (category === 'Indexing Options') {
                    container = document.getElementById('othersIndexingContainer');
                    countId = 'othersCount';
                } else {
                    console.error('Unknown category:', category);
                    closeDeleteModal();
                    return;
                }
                
                if (!container) {
                    console.error('Container not found for category:', category);
                    closeDeleteModal();
                    return;
                }
                
                // Find and remove the tag element
                const tagElement = btn.closest('div.group');
                if (tagElement) {
                    tagElement.remove();
                    updateCount(countId, container);
                } else {
                    console.error('Tag element not found');
                }
                
                closeDeleteModal();
                autoSaveFormDropdowns();
            } catch (error) {
                console.error('Error confirming delete:', error);
                closeDeleteModal();
            }
        }
        
        // Modern tag-based functions for Form Dropdowns
        function addRankTag() {
            const input = document.getElementById('rankInput');
            const value = input.value.trim();
            if (!value) return;
            
            const container = document.getElementById('academicRanksContainer');
            const existingValues = Array.from(container.querySelectorAll('input[type="hidden"]')).map(inp => inp.value.trim());
            if (existingValues.includes(value)) {
                input.value = '';
                return;
            }
            
            const tag = document.createElement('div');
            tag.className = 'group relative inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:border-purple-400 hover:bg-purple-50 transition-all';
            tag.innerHTML = `
                <input type="hidden" name="academic_ranks[]" value="${escapeHtml(value)}">
                <span class="text-sm">${escapeHtml(value)}</span>
                <button type="button" onclick="window.showDeleteModal && window.showDeleteModal(this, '${escapeHtml(value)}', 'Academic Ranks')" 
                        class="opacity-0 group-hover:opacity-100 transition-opacity ml-1 text-gray-400 hover:text-red-600 focus:outline-none">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            container.appendChild(tag);
            input.value = '';
            updateCount('ranksCount', container);
            autoSaveFormDropdowns();
        }
        
        function addCollegeTag() {
            const input = document.getElementById('collegeInput');
            const value = input.value.trim();
            if (!value) return;
            
            const container = document.getElementById('collegesContainer');
            const existingValues = Array.from(container.querySelectorAll('input[type="hidden"]')).map(inp => inp.value.trim());
            if (existingValues.includes(value)) {
                input.value = '';
                return;
            }
            
            const tag = document.createElement('div');
            tag.className = 'group relative inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:border-indigo-400 hover:bg-indigo-50 transition-all';
            tag.innerHTML = `
                <input type="hidden" name="colleges[]" value="${escapeHtml(value)}">
                <span class="text-sm">${escapeHtml(value)}</span>
                <button type="button" onclick="window.showDeleteModal && window.showDeleteModal(this, '${escapeHtml(value)}', 'Colleges')" 
                        class="opacity-0 group-hover:opacity-100 transition-opacity ml-1 text-gray-400 hover:text-red-600 focus:outline-none">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            container.appendChild(tag);
            input.value = '';
            updateCount('collegesCount', container);
            autoSaveFormDropdowns();
        }
        
        function addOthersIndexingTag() {
            const input = document.getElementById('othersInput');
            const value = input.value.trim();
            if (!value) return;
            
            const container = document.getElementById('othersIndexingContainer');
            const existingValues = Array.from(container.querySelectorAll('input[type="hidden"]')).map(inp => inp.value.trim());
            if (existingValues.includes(value)) {
                input.value = '';
                return;
            }
            
            const tag = document.createElement('div');
            tag.className = 'group relative inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:border-pink-400 hover:bg-pink-50 transition-all';
            tag.innerHTML = `
                <input type="hidden" name="others_indexing_options[]" value="${escapeHtml(value)}">
                <span class="text-sm">${escapeHtml(value)}</span>
                <button type="button" onclick="window.showDeleteModal && window.showDeleteModal(this, '${escapeHtml(value)}', 'Indexing Options')" 
                        class="opacity-0 group-hover:opacity-100 transition-opacity ml-1 text-gray-400 hover:text-red-600 focus:outline-none">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            container.appendChild(tag);
            input.value = '';
            updateCount('othersCount', container);
            autoSaveFormDropdowns();
        }
        
        function updateCount(countId, container) {
            const count = container.querySelectorAll('input[type="hidden"]').length;
            const countElement = document.getElementById(countId);
            if (countElement) {
                countElement.textContent = count;
            }
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Make functions globally available immediately
        window.addRankTag = addRankTag;
        window.addCollegeTag = addCollegeTag;
        window.addOthersIndexingTag = addOthersIndexingTag;
        window.showDeleteModal = showDeleteModal;
        window.closeDeleteModal = closeDeleteModal;
        window.confirmDelete = confirmDelete;
        window.updateCount = updateCount;
        window.autoSaveFormDropdowns = autoSaveFormDropdowns;
        
        // Initialize modal event listeners after DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Close modal when clicking outside (on backdrop)
            const modal = document.getElementById('deleteConfirmModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    // Only close if clicking directly on the backdrop (not on modal content)
                    if (e.target === modal) {
                        closeDeleteModal();
                    }
                });
            }
            
            // Close modal on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    const modal = document.getElementById('deleteConfirmModal');
                    if (modal && !modal.classList.contains('hidden')) {
                        closeDeleteModal();
                    }
                }
            });
        });
        
        function removeAnnouncementRow(btn) {
            const row = btn.closest('tr');
            const container = document.getElementById('announcementsRepeater');
            if (row && container) {
                row.remove();
                
                // If no rows left, add an empty entry for consistency
                if (container.querySelectorAll('tr').length === 0) {
                    addAnnouncementRow();
                }
                
                // Trigger announcements change detection
                if (window.checkAnnouncementsChanges) {
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
                    // Update UI to show delete button
                    updateDeputyAccountUI(true);
                } else {
                    statusElement.textContent = data.message || 'Failed to create account';
                    statusElement.className = 'text-xs text-red-500';
                }
            } catch (error) {
                statusElement.textContent = 'Error creating account';
                statusElement.className = 'text-xs text-red-500';
            }
        }

        // Update UI for deputy director account
        function updateDeputyAccountUI(accountExists) {
            const button = document.getElementById('deputy-account-button');
            const passwordField = document.getElementById('deputy-password-field');
            const emailInput = document.querySelector('input[name="deputy_director_email"]');
            const statusElement = document.getElementById('deputy-account-status');
            
            if (accountExists) {
                // Disable create button and show status
                button.disabled = true;
                button.classList.add('opacity-50', 'cursor-not-allowed');
                if (passwordField) {
                    passwordField.style.display = 'none';
                    passwordField.classList.add('hidden');
                }
                if (emailInput) {
                    emailInput.readOnly = true;
                    emailInput.classList.remove('bg-white');
                    emailInput.classList.add('bg-gray-100');
                }
                if (statusElement) {
                    statusElement.innerHTML = '<span class="text-green-600">Account exists</span>';
                }
            } else {
                // Enable create button
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
                if (passwordField) {
                    passwordField.style.display = 'block';
                    passwordField.classList.remove('hidden');
                }
                if (emailInput) {
                    emailInput.readOnly = false;
                    emailInput.classList.remove('bg-gray-100');
                    emailInput.classList.add('bg-white');
                }
                if (statusElement) {
                    statusElement.innerHTML = '';
                }
            }
        }

        // Update UI for RDD director account
        function updateRddAccountUI(accountExists) {
            const button = document.getElementById('rdd-account-button');
            const passwordField = document.getElementById('rdd-password-field');
            const emailInput = document.querySelector('input[name="rdd_director_email"]');
            const statusElement = document.getElementById('rdd-account-status');
            
            if (accountExists) {
                // Disable create button and show status
                button.disabled = true;
                button.classList.add('opacity-50', 'cursor-not-allowed');
                if (passwordField) {
                    passwordField.style.display = 'none';
                    passwordField.classList.add('hidden');
                }
                if (emailInput) {
                    emailInput.readOnly = true;
                    emailInput.classList.remove('bg-white');
                    emailInput.classList.add('bg-gray-100');
                }
                if (statusElement) {
                    statusElement.innerHTML = '<span class="text-green-600">Account exists</span>';
                }
            } else {
                // Enable create button
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
                if (passwordField) {
                    passwordField.style.display = 'block';
                    passwordField.classList.remove('hidden');
                }
                if (emailInput) {
                    emailInput.readOnly = false;
                    emailInput.classList.remove('bg-gray-100');
                    emailInput.classList.add('bg-white');
                }
                if (statusElement) {
                    statusElement.innerHTML = '';
                }
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
                    // Update UI to show delete button
                    updateRddAccountUI(true);
                } else {
                    statusElement.textContent = data.message || 'Failed to create account';
                    statusElement.className = 'text-xs text-red-500';
                }
            } catch (error) {
                statusElement.textContent = 'Error creating account';
                statusElement.className = 'text-xs text-red-500';
            }
        }

        // Simple researchers management - Refactored approach
        // Define function in global scope so it's accessible from onclick handlers
        window.addResearcherRow = function addResearcherRow() {
            const container = document.getElementById('researchersRepeater');
            if (!container) {
                console.error('[DEBUG] researchersRepeater container not found!');
                return;
            }
            
            // Calculate next index based on existing cards - use sequential indices
            const existingCards = container.querySelectorAll('.researcher-card');
            const indices = Array.from(existingCards).map(card => {
                const input = card.querySelector('input[name*="[name]"], input[name*="[title]"], textarea[name*="[bio]"]');
                if (input) {
                    const match = input.name.match(/researchers\[(\d+)\]/);
                    return match ? parseInt(match[1]) : -1;
                }
                return -1;
            }).filter(i => i >= 0).sort((a, b) => a - b);
            
            // Get the next sequential index
            const index = indices.length > 0 ? Math.max(...indices) + 1 : 0;
            
            console.log('[DEBUG] Adding researcher at index', index, 'Existing indices:', indices);
            
            // Create card element
            const card = document.createElement('div');
            card.className = 'researcher-card bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden';
            card.setAttribute('data-researcher-index', index);
            
            // Use a simpler approach without Alpine.js for dynamic rows
            // We'll use plain JavaScript for expand/collapse
            let isExpanded = true;
            
            card.innerHTML = `
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100/50 border-b border-gray-200">
                    <div class="flex items-center justify-between gap-4">
                        <button type="button" class="toggle-researcher-btn flex items-center gap-4 flex-1 text-left group hover:opacity-90 transition-opacity" data-index="${index}">
                            <!-- Profile Picture or Icon -->
                            <div class="relative flex-shrink-0">
                                <div class="w-14 h-14 bg-gradient-to-br from-maroon-500 to-red-600 rounded-xl flex items-center justify-center shadow-md ring-2 ring-white">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            </div>
                            
                            <!-- Name and Title -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="text-lg font-bold text-gray-900 group-hover:text-maroon-600 transition-colors researcher-name-header" data-index="${index}">New Researcher</h4>
                            </div>
                                <p class="text-xs text-gray-400 italic researcher-title-text" style="display: none;">No title set</p>
                                <div class="flex items-center gap-1.5 mt-1.5 researcher-areas-text" style="display: none;">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <p class="text-xs text-gray-500 truncate"></p>
                                </div>
                            </div>
                            
                            <!-- Chevron Icon -->
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-maroon-600 transition-all duration-200 flex-shrink-0 toggle-arrow" style="transform: rotate(180deg);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            </div>
                        </button>
                        
                        <!-- Remove Button -->
                        <button type="button" onclick="removeResearcherRow(this)" 
                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 flex-shrink-0" 
                                title="Remove Researcher">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="researcher-fields px-5 pt-5 pb-5 space-y-5 border-t border-gray-200" 
                     style="display: block;">
                     <!-- Row 1: Profile Picture | Biography -->
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                         <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                             <div class="flex items-center gap-4">
                             <div class="relative">
                                 <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden ring-2 ring-gray-200 relative">
                                     <img id="preview-${index}" src="" alt="Profile preview" class="w-full h-full object-cover hidden">
                                     <svg class="w-8 h-8 text-gray-400 absolute inset-0 m-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="placeholder-${index}">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 0 18 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                            </div>
                                 <label for="photo-${index}" class="absolute inset-0 rounded-lg bg-black/30 opacity-0 hover:opacity-100 flex items-center justify-center cursor-pointer">
                                     <span class="text-xs text-white font-medium px-2 py-1 bg-black/40 rounded">Change</span>
                                 </label>
                         </div>
                                 <div class="flex-1">
                                     <input type="file" name="researchers[${index}][photo]" id="photo-${index}" 
                                            accept="image/*" onchange="previewImage(this, ${index})"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-maroon-50 file:text-maroon-700 hover:file:bg-maroon-100">
                                     <p class="text-xs text-gray-500 mt-1">JPG, PNG up to 10MB (will be converted to WebP)</p>
                    </div>
                </div>
                         </div>
                         
                         <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Biography</label>
                             <textarea name="researchers[${index}][bio]" rows="4" 
                                       placeholder="Brief description of research focus and achievements..." 
                                       class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 resize-none"></textarea>
                         </div>
                     </div>
                     
                     <!-- Row 2: Full Name | Email Address -->
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                         <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                             <input type="text" name="researchers[${index}][name]" 
                                    placeholder="Dr. John Doe" 
                                    class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                         </div>
                         
                         <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                             <input type="email" name="researchers[${index}][profile_link]" 
                                    placeholder="researcher@example.com" 
                                    class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                         </div>
                     </div>
                     
                     <!-- Row 3: Title/Position | Status Badge -->
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                         <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Title/Position *</label>
                             <input type="text" name="researchers[${index}][title]" 
                                    placeholder="Professor, College of Engineering" 
                                    class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                         </div>
                         
                         <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Status Badge</label>
                             <select name="researchers[${index}][status_badge]" 
                                     class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                                 <option value="Active">Active</option>
                                 <option value="Research">Research</option>
                                 <option value="Innovation">Innovation</option>
                                 <option value="Leadership">Leadership</option>
                             </select>
                         </div>
                     </div>
                     
                     <!-- Row 4: Research Areas | Card Background Color -->
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                         <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Research Areas</label>
                             <input type="text" name="researchers[${index}][research_areas]" 
                                    placeholder="AI, Machine Learning, Data Science" 
                                    class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                         </div>
                         
                         <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Card Background Color</label>
                             <select name="researchers[${index}][background_color]" 
                                     class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                                 <option value="maroon">Maroon</option>
                                 <option value="blue">Blue</option>
                                 <option value="green">Green</option>
                                 <option value="purple">Purple</option>
                                 <option value="orange">Orange</option>
                                 <option value="teal">Teal</option>
                                 <option value="rose">Rose</option>
                             </select>
                         </div>
                     </div>
                     
                     <!-- Row 5: Research Profile Links (at bottom) -->
                     <div class="mt-5 pt-5 border-t border-gray-200">
                         <h4 class="text-sm font-semibold text-gray-700 mb-4">Research Profile Links</h4>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                             <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-2">SCOPUS Link</label>
                                 <input type="url" name="researchers[${index}][scopus_link]" 
                                        placeholder="https://www.scopus.com/authid/detail.uri?authorId=..." 
                                        class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                             </div>
                             
                             <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-2">ORCID Link</label>
                                 <input type="url" name="researchers[${index}][orcid_link]" 
                                        placeholder="https://orcid.org/0000-0000-0000-0000" 
                                        class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                             </div>
                             
                             <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-2">WOS (Web of Science) Link</label>
                                 <input type="url" name="researchers[${index}][wos_link]" 
                                        placeholder="https://www.webofscience.com/wos/author/record/..." 
                                        class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                             </div>
                             
                             <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-2">Google Scholar Link</label>
                                 <input type="url" name="researchers[${index}][google_scholar_link]" 
                                        placeholder="https://scholar.google.com/citations?user=..." 
                                        class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20">
                             </div>
                         </div>
                     </div>
                 </div>
            `;
            
            // Append to container
            container.appendChild(card);
            console.log('[DEBUG] ✓ Card added to container at index', index);
            
            // Note: Even if we can't detect the form programmatically, the browser will
            // serialize all form fields with name attributes when the form is submitted.
            // As long as the container is in the DOM where it should be, it will work.
            
            // Set up toggle functionality
            const toggleBtn = card.querySelector('.toggle-researcher-btn');
            const fieldsDiv = card.querySelector('.researcher-fields');
            const arrow = card.querySelector('.toggle-arrow');
            
            if (toggleBtn && fieldsDiv) {
                toggleBtn.addEventListener('click', function() {
                    const isCurrentlyExpanded = fieldsDiv.style.display !== 'none';
                    fieldsDiv.style.display = isCurrentlyExpanded ? 'none' : 'block';
                    if (arrow) {
                        arrow.style.transform = isCurrentlyExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
                    }
                });
            }
            
            // Add event listeners to new inputs (excluding file inputs)
            const newInputs = card.querySelectorAll('input:not([type="file"]), select, textarea');
            newInputs.forEach(input => {
                if (window.checkResearcherChanges) {
                    input.addEventListener('input', window.checkResearcherChanges);
                }
                
                // Update name header when name field changes
                if (input.name && input.name.includes('[name]')) {
                    const nameHeader = card.querySelector('.researcher-name-header');
                    input.addEventListener('input', function() {
                        if (nameHeader) {
                            nameHeader.textContent = this.value.trim() || 'New Researcher';
                        }
                    });
                }
                
                // Update title text when title field changes
                if (input.name && input.name.includes('[title]')) {
                    const titleText = card.querySelector('.researcher-title-text');
                    input.addEventListener('input', function() {
                        if (titleText) {
                            const titleValue = this.value.trim();
                            if (titleValue) {
                                titleText.textContent = titleValue;
                                titleText.className = 'text-sm text-gray-600 font-medium truncate researcher-title-text';
                                titleText.style.display = 'block';
                            } else {
                                titleText.textContent = 'No title set';
                                titleText.className = 'text-xs text-gray-400 italic researcher-title-text';
                                titleText.style.display = 'block';
                            }
                        }
                    });
                }
                
                // Update research areas when research_areas field changes
                if (input.name && input.name.includes('[research_areas]')) {
                    const areasText = card.querySelector('.researcher-areas-text');
                    const areasValue = card.querySelector('.researcher-areas-text p');
                    input.addEventListener('input', function() {
                        if (areasText && areasValue) {
                            const areas = this.value.trim();
                            if (areas) {
                                areasValue.textContent = areas;
                                areasText.style.display = 'flex';
                            } else {
                                areasText.style.display = 'none';
                            }
                        }
                    });
                }
            });
            
            // Update profile picture preview when photo is selected
            const photoInput = card.querySelector('input[type="file"][name*="[photo]"]');
            if (photoInput) {
                photoInput.addEventListener('change', function() {
                    // Trigger change detection when photo is selected
                    if (window.checkResearcherChanges) {
                        window.checkResearcherChanges();
                    }
                    
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewContainer = card.querySelector('.relative.flex-shrink-0 .w-14');
                            if (previewContainer) {
                                // Replace icon with image
                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.alt = 'Profile';
                                img.className = 'w-full h-full object-cover';
                                const currentContent = previewContainer.querySelector('svg, img');
                                if (currentContent) {
                                    currentContent.replaceWith(img);
                                }
                                previewContainer.className = 'w-14 h-14 rounded-xl overflow-hidden ring-2 ring-white shadow-md';
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
            
            // Trigger change detection
            if (window.checkResearcherChanges) {
                window.checkResearcherChanges();
            }
        }

        window.removeResearcherRow = function removeResearcherRow(btn) {
            const card = btn.closest('.researcher-card');
            const container = document.getElementById('researchersRepeater');
            if (card && container) {
                card.remove();
                
                // If no cards left, add an empty one
                const remainingCards = container.querySelectorAll('.researcher-card');
                if (remainingCards.length === 0) {
                    addResearcherRow();
                }
                
                // Trigger researcher change detection
                if (window.checkResearcherChanges) {
                    window.checkResearcherChanges();
                }
            }
        }

        // Compress image before upload to avoid PHP upload_max_filesize issues
        function compressImage(file, maxSizeMB = 1.8, maxWidth = 1920, maxHeight = 1920, quality = 0.85) {
            return new Promise((resolve, reject) => {
                // If file is already small enough, return as-is
                if (file.size <= maxSizeMB * 1024 * 1024) {
                    resolve(file);
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;
                        
                        // Calculate new dimensions
                        if (width > maxWidth || height > maxHeight) {
                            if (width > height) {
                                height = (height / width) * maxWidth;
                                width = maxWidth;
                            } else {
                                width = (width / height) * maxHeight;
                                height = maxHeight;
                            }
                        }
                        
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Convert to blob with compression
                        canvas.toBlob(function(blob) {
                            if (!blob) {
                                reject(new Error('Compression failed'));
                                return;
                            }
                            
                            // If still too large, reduce quality and try again
                            if (blob.size > maxSizeMB * 1024 * 1024 && quality > 0.5) {
                                canvas.toBlob(function(blob2) {
                                    if (blob2) {
                                        const compressedFile = new File([blob2], file.name, {
                                            type: 'image/jpeg',
                                            lastModified: Date.now()
                                        });
                                        resolve(compressedFile);
                                    } else {
                                        resolve(file); // Fallback to original
                                    }
                                }, 'image/jpeg', quality * 0.7);
                            } else {
                                const compressedFile = new File([blob], file.name, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile);
                            }
                        }, 'image/jpeg', quality);
                    };
                    img.onerror = () => reject(new Error('Failed to load image'));
                    img.src = e.target.result;
                };
                reader.onerror = () => reject(new Error('Failed to read file'));
                reader.readAsDataURL(file);
            });
        }

        // Image preview functionality with automatic compression
        window.previewImage = function previewImage(input, index) {
            console.log('[DEBUG] previewImage called', { index, hasFile: !!input.files[0] });
            
            // Trigger change detection when photo is selected
            if (window.checkResearcherChanges) {
                setTimeout(() => window.checkResearcherChanges(), 100);
            }
            
            const file = input.files[0];
            if (!file) {
                console.log('[DEBUG] No file selected');
                // Reset if no file selected
                const preview = document.getElementById(`preview-${index}`);
                const placeholder = document.getElementById(`placeholder-${index}`);
                if (preview) {
                    preview.classList.add('hidden');
                    preview.style.display = 'none';
                }
                if (placeholder) {
                    placeholder.classList.remove('hidden');
                    placeholder.style.display = 'block';
                }
                return;
            }
            
            console.log('[DEBUG] File selected', { 
                name: file.name, 
                size: file.size, 
                sizeMB: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                type: file.type 
            });
            
            // Show preview immediately with original file (for better UX)
            const showPreview = (fileToPreview) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Update the form preview (in expanded section)
                    const preview = document.getElementById(`preview-${index}`);
                    const placeholder = document.getElementById(`placeholder-${index}`);
                    
                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        preview.style.display = 'block';
                    }
                    if (placeholder) {
                        placeholder.classList.add('hidden');
                        placeholder.style.display = 'none';
                    }
                    
                    // Update the header preview (in collapsed section)
                    const card = input.closest('.researcher-card');
                    if (card) {
                        const headerPreview = card.querySelector('.relative.flex-shrink-0 .w-14');
                        if (headerPreview) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = 'Profile';
                            img.className = 'w-full h-full object-cover';
                            const currentContent = headerPreview.querySelector('svg, img');
                            if (currentContent) {
                                currentContent.replaceWith(img);
                            }
                            headerPreview.className = 'w-14 h-14 rounded-xl overflow-hidden ring-2 ring-white shadow-md';
                        }
                    }
                };
                reader.onerror = (error) => {
                    console.error('[DEBUG] FileReader error:', error);
                };
                reader.readAsDataURL(fileToPreview);
            };
            
            // Show preview immediately
            showPreview(file);
            
            // Compress image if it's too large (over 1.8MB to stay under 2MB PHP limit)
            if (file.size > 1.8 * 1024 * 1024) {
                console.log('[DEBUG] File is large, compressing...');
                compressImage(file, 1.8).then(compressedFile => {
                    console.log('[DEBUG] Compression complete', {
                        originalSize: file.size,
                        compressedSize: compressedFile.size,
                        originalMB: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                        compressedMB: (compressedFile.size / 1024 / 1024).toFixed(2) + ' MB',
                    });
                    
                    // Replace the file in the input with the compressed version
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFile);
                    input.files = dataTransfer.files;
                    
                    // Update preview with compressed version
                    showPreview(compressedFile);
                }).catch(error => {
                    console.error('[DEBUG] Image compression failed:', error);
                    // Keep original file if compression fails
                });
            } else {
                console.log('[DEBUG] File is small enough, no compression needed');
            }
        }

        // Functions are already assigned to window above
        
        // Re-initialize form detection after successful save
        // Check for success notification and reinitialize originalValues
        function checkAndReinitializeAfterSave() {
            const successNotification = document.getElementById('success-notification');
            if (successNotification && successNotification.textContent.trim()) {
                // Success notification exists, reinitialize form detection
                if (typeof initFormChangeDetection === 'function') {
                    setTimeout(() => {
                        initFormChangeDetection();
                        console.log('[DEBUG] Reinitialized form detection after successful save');
                    }, 100);
                }
            }
        }
        
        // Check on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', checkAndReinitializeAfterSave);
        } else {
            checkAndReinitializeAfterSave();
        }
        
        // Also check after Turbo navigation
        document.addEventListener('turbo:load', checkAndReinitializeAfterSave);

    </script>
</x-app-layout> 