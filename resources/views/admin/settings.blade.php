<x-app-layout>
    <div x-data="{ 
        searchOpen: false,
        userMenuOpen: false
    }" class="h-screen bg-gray-50 flex overflow-hidden" style="scrollbar-gutter: stable;">
        
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
                        <button class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors flex items-center justify-center group">
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-800 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                            </svg>
                        </button>
                        
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
                
                <div class="max-w-6xl mx-auto space-y-8">
                    
                    <form id="settings-form" method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
                        @csrf
                        @method('PUT')
                        
                        <!-- Official Information Section -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
                                <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-3">
                                    <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    Official Information
                                </h3>
                                <p class="text-gray-600 mt-1">Configure official names and titles for document generation</p>
                            </div>
                            <div class="p-8">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Deputy Director Row -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">Deputy Director Name</label>
                                        <input type="text" name="official_deputy_director_name" value="{{ old('official_deputy_director_name', $official_deputy_director_name) }}" 
                                               class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" required>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">Deputy Director Title</label>
                                        <input type="text" name="official_deputy_director_title" value="{{ old('official_deputy_director_title', $official_deputy_director_title) }}" 
                                               class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" required>
                                    </div>
                                    
                                    <!-- RDD Director Row -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">RDD Director Name</label>
                                        <input type="text" name="official_rdd_director_name" value="{{ old('official_rdd_director_name', $official_rdd_director_name) }}" 
                                               class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" required>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">RDD Director Title</label>
                                        <input type="text" name="official_rdd_director_title" value="{{ old('official_rdd_director_title', $official_rdd_director_title) }}" 
                                               class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" required>
                                    </div>
                                </div>
                                
                                <!-- Official Information Save Button -->
                                <div class="flex items-center justify-end pt-6 border-t border-gray-200 mt-6">
                                    <button type="submit" name="save_official_info" class="inline-flex items-center gap-2 px-6 py-3 bg-maroon-600 text-white rounded-lg hover:bg-maroon-700 transition-colors font-semibold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Save Official Information
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Welcome Page Calendar Section -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
                                <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-3">
                                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    Welcome Page Calendar
                                </h3>
                                <p class="text-gray-600 mt-1">Add multiple marked dates with notes to show on the welcome page</p>
                            </div>
                            <div class="p-8">
                                <div id="marksRepeater" class="space-y-4">
                                    @php($marks = old('calendar_marks', $calendar_marks ?? []))
                                    @if(empty($marks))
                                        @php($marks = [[ 'date' => '', 'note' => '' ]])
                                    @endif
                                    @foreach($marks as $idx => $mark)
                                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
                                            <input type="date" name="calendar_marks[{{ $idx }}][date]" value="{{ $mark['date'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all">
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Note</label>
                                            <input type="text" name="calendar_marks[{{ $idx }}][note]" value="{{ $mark['note'] ?? '' }}" placeholder="e.g., Call for Papers deadline" class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all">
                                        </div>
                                        <div class="md:col-span-1 flex gap-2">
                                            <button type="button" class="px-3 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors" onclick="removeMarkRow(this)" title="Remove">
                                                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-6 flex items-center justify-between">
                                    <button type="button" onclick="addMarkRow()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-600 text-white hover:bg-amber-700 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Mark
                                    </button>
                                    
                                    <!-- Calendar Save Button -->
                                    <button type="submit" name="save_calendar" class="inline-flex items-center gap-2 px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors font-semibold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Save Calendar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Application Controls Section -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
                                <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    Application Controls
                                </h3>
                                <p class="text-gray-600 mt-1">Manage feature availability and system behavior</p>
                            </div>
                            <div class="p-8 space-y-6">
                                
                                <!-- Citations Request Toggle -->
                                <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h4 class="text-lg font-semibold text-gray-900">Citations Request Feature</h4>
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
                                <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h4 class="text-lg font-semibold text-gray-500">Publications Request Feature</h4>
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
                </div>
            </main>
        </div>
    </div>
    
    <script>
        (function() {
            'use strict';
            
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
                    statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
                } else {
                    toggleText.textContent = 'Disabled';
                    statusBadge.textContent = 'Currently Disabled';
                    statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
                }
            }
            
            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCheckbox);
            } else {
                initCheckbox();
            }
        })();

        // Simple repeater for calendar marks
        function addMarkRow() {
            const container = document.getElementById('marksRepeater');
            if (!container) return;
            const index = container.querySelectorAll('.grid').length;
            const wrapper = document.createElement('div');
            wrapper.className = 'grid grid-cols-1 md:grid-cols-6 gap-4 items-end';
            wrapper.innerHTML = `
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
                    <input type="date" name="calendar_marks[${index}][date]" class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Note</label>
                    <input type="text" name="calendar_marks[${index}][note]" placeholder="e.g., Call for Papers deadline" class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all">
                </div>
                <div class="md:col-span-1 flex gap-2">
                    <button type="button" class="px-3 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors" onclick="removeMarkRow(this)" title="Remove">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            `;
            container.appendChild(wrapper);
        }

        function removeMarkRow(btn) {
            const row = btn.closest('.grid');
            const container = document.getElementById('marksRepeater');
            if (row && container && container.children.length > 1) {
                row.remove();
            }
        }
    </script>
</x-app-layout> 