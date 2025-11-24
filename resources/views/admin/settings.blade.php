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

                    
                    <form id="settings-form" method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" onsubmit="return false;">
                        @csrf
                        @method('PUT')
                        
                        <!-- Landing Page Settings Section -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6 mt-6 relative" x-data="{ activeTab: 'calendar' }">
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
                                        <!-- Add Event Button (Calendar Tab) -->
                                        <button type="button" 
                                                x-show="activeTab === 'calendar'"
                                                onclick="addMarkRow()" 
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 font-medium text-sm shadow-sm transition-colors" 
                                                title="Add Event"
                                                style="display: none;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Add Event
                                        </button>
                                        <!-- Add Announcement Button (Announcements Tab) -->
                                        <button type="button" 
                                                x-show="activeTab === 'announcements'"
                                                onclick="addAnnouncementRow()" 
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-sm shadow-sm transition-colors" 
                                                title="Add Announcement"
                                                style="display: none;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Add Announcement
                                        </button>
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
                                    <button type="button" @click="activeTab = 'counters'" 
                                            :class="activeTab === 'counters' ? 'border-red-500 text-red-600 bg-red-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-100/50'"
                                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 border-b-2 font-medium text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        Publication Counters
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
                                <div x-show="activeTab === 'counters'" style="display: none; height: 100%;" class="flex flex-col justify-center">
                                    <div class="flex flex-row gap-4 justify-center items-stretch w-full">
                                        <div class="flex-1 rounded-lg p-4 border-2 border-gray-300 shadow-md">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm overflow-hidden">
                                                    <img src="{{ asset('images/scopus.webp') }}" alt="Scopus" class="w-full h-full object-contain">
                                                </div>
                                                <label class="block text-sm font-semibold text-gray-900 uppercase tracking-wide">Scopus</label>
                                            </div>
                                            <input type="number" name="scopus_publications_count" min="0" step="1" value="{{ old('scopus_publications_count', $scopus_publications_count) }}" 
                                                   class="w-full border-2 border-gray-400 rounded-lg px-4 py-2.5 text-xl font-bold bg-white focus:border-gray-600 focus:ring-2 focus:ring-gray-500/30 text-center"
                                                   placeholder="0">
                                        </div>
                                        <div class="flex-1 rounded-lg p-4 border-2 border-gray-300 shadow-md">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm overflow-hidden">
                                                    <img src="{{ asset('images/wos.webp') }}" alt="Web of Science" class="w-full h-full object-contain">
                                                </div>
                                                <label class="block text-sm font-semibold text-gray-900 uppercase tracking-wide">Web of Science</label>
                                            </div>
                                            <input type="number" name="wos_publications_count" min="0" step="1" value="{{ old('wos_publications_count', $wos_publications_count) }}" 
                                                   class="w-full border-2 border-gray-400 rounded-lg px-4 py-2.5 text-xl font-bold bg-white focus:border-gray-600 focus:ring-2 focus:ring-gray-500/30 text-center"
                                                   placeholder="0">
                                        </div>
                                        <div class="flex-1 rounded-lg p-4 border-2 border-gray-300 shadow-md">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm overflow-hidden">
                                                    <img src="{{ asset('images/aci.webp') }}" alt="ACI" class="w-full h-full object-contain">
                                                </div>
                                                <label class="block text-sm font-semibold text-gray-900 uppercase tracking-wide">ACI</label>
                                            </div>
                                            <input type="number" name="aci_publications_count" min="0" step="1" value="{{ old('aci_publications_count', $aci_publications_count) }}" 
                                                   class="w-full border-2 border-gray-400 rounded-lg px-4 py-2.5 text-xl font-bold bg-white focus:border-gray-600 focus:ring-2 focus:ring-gray-500/30 text-center"
                                                   placeholder="0">
                                        </div>
                                        <div class="flex-1 rounded-lg p-4 border-2 border-gray-300 shadow-md">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm overflow-hidden">
                                                    <img src="{{ asset('images/peer.webp') }}" alt="PEER" class="w-full h-full object-contain">
                                                </div>
                                                <label class="block text-sm font-semibold text-gray-900 uppercase tracking-wide">PEER</label>
                                            </div>
                                            <input type="number" name="peer_publications_count" min="0" step="1" value="{{ old('peer_publications_count', $peer_publications_count) }}" 
                                                   class="w-full border-2 border-gray-400 rounded-lg px-4 py-2.5 text-xl font-bold bg-white focus:border-gray-600 focus:ring-2 focus:ring-gray-500/30 text-center"
                                                   placeholder="0">
                                        </div>
                                    </div>
                                </div>

                                <!-- Calendar Events Tab -->
                                <div x-show="activeTab === 'calendar'" style="display: none; height: 100%; overflow-y-auto;">
                                    <div id="marksRepeater" class="space-y-3">
                                        @php($marks = old('calendar_marks', $calendar_marks ?? []))
                                        @if(empty($marks))
                                            @php($marks = [[ 'date' => '', 'note' => '' ]])
                                        @endif
                                        @foreach($marks as $idx => $mark)
                                        <div class="group relative bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-amber-300 transition-all duration-200 p-4">
                                            <div class="flex items-center gap-4">
                                                <!-- Counter -->
                                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow-sm">
                                                    <span class="text-white font-bold text-lg">{{ $idx + 1 }}</span>
                                                </div>
                                                
                                                <!-- Form Fields -->
                                                <div class="flex-1 flex gap-4">
                                                    <div class="w-48">
                                                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Event Date</label>
                                                        <input type="date" name="calendar_marks[{{ $idx }}][date]" value="{{ $mark['date'] ?? '' }}" 
                                                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                                                    </div>
                                                    <div class="flex-1">
                                                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Description</label>
                                                        <input type="text" name="calendar_marks[{{ $idx }}][note]" value="{{ $mark['note'] ?? '' }}" 
                                                               placeholder="Enter event description..." 
                                                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                                                    </div>
                                                </div>
                                                
                                                <!-- Delete Button -->
                                                <div class="flex-shrink-0">
                                                    <button type="button" onclick="removeMarkRow(this)" 
                                                            class="flex items-center justify-center w-10 h-10 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200" 
                                                            title="Remove Event">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Announcements Tab -->
                                <div x-show="activeTab === 'announcements'" style="display: none; height: 100%; overflow-y-auto;">
                                    <div id="announcementsRepeater" class="space-y-3">
                                        @php($announcements = old('announcements', $announcements ?? []))
                                        @if(empty($announcements))
                                            @php($announcements = [['title' => '', 'description' => '']])
                                        @endif
                                        @foreach($announcements as $idx => $announcement)
                                        <div class="group relative bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all duration-200 p-4">
                                            <div class="flex items-center gap-4">
                                                <!-- Counter -->
                                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-indigo-400 to-blue-500 rounded-xl flex items-center justify-center shadow-sm">
                                                    <span class="text-white font-bold text-lg">{{ $idx + 1 }}</span>
                                                </div>
                                                
                                                <!-- Form Fields -->
                                                <div class="flex-1 flex gap-4">
                                                    <div class="w-1/3">
                                                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Title</label>
                                                        <input type="text" name="announcements[{{ $idx }}][title]" value="{{ $announcement['title'] ?? '' }}" 
                                                               placeholder="Enter announcement title..." 
                                                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                                                    </div>
                                                    <div class="flex-1">
                                                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Description</label>
                                                        <input type="text" name="announcements[{{ $idx }}][description]" value="{{ $announcement['description'] ?? '' }}" 
                                                               placeholder="Enter announcement description..." 
                                                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                                                    </div>
                                                </div>
                                                
                                                <!-- Delete Button -->
                                                <div class="flex-shrink-0">
                                                    <button type="button" onclick="removeAnnouncementRow(this)" 
                                                            class="flex items-center justify-center w-10 h-10 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200" 
                                                            title="Remove Announcement">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <!-- USEP Researchers Management Section - Livewire Component -->
                        @livewire('researcher-manager')
                        
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
                                <div class="p-3 sm:p-4 md:p-6">
                                    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 md:gap-6">
                                    <!-- Academic Ranks -->
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 sm:p-4 md:p-5 w-full">
                                            <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h4 class="text-sm sm:text-base font-semibold text-gray-900">Academic Ranks</h4>
                                                    <span id="ranksCount" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 whitespace-nowrap">
                                                        {{ count(array_filter(old('academic_ranks', $academic_ranks ?? []))) }}
                                                    </span>
                                        </div>
                                            </div>
                                            <div id="academicRanksContainer" class="flex flex-wrap gap-2 mb-3 min-h-[60px]">
                                            @php($ranks = old('academic_ranks', $academic_ranks ?? []))
                                                @php($ranks = array_filter($ranks))
                                            @foreach($ranks as $idx => $rank)
                                                @if(!empty(trim($rank)))
                                                <div class="group relative inline-flex items-center gap-1.5 px-2 sm:px-3 py-1 sm:py-1.5 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-700 hover:border-purple-400 hover:bg-purple-50 transition-all max-w-full">
                                                    <input type="hidden" name="academic_ranks[]" value="{{ $rank }}">
                                                    <span class="text-xs sm:text-sm truncate">{{ $rank }}</span>
                                                    <button type="button" onclick="window.showDeleteModal && window.showDeleteModal(this, '{{ addslashes($rank) }}', 'Academic Ranks')" 
                                                            class="opacity-0 group-hover:opacity-100 transition-opacity ml-1 text-gray-400 hover:text-red-600 focus:outline-none flex-shrink-0">
                                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                                @endif
                                            @endforeach
                                        </div>
                                            <div class="flex flex-col sm:flex-row gap-2">
                                                <input type="text" id="rankInput" 
                                                       placeholder="Add rank..." 
                                                       class="flex-1 text-xs sm:text-sm border border-gray-300 rounded-lg px-2 sm:px-3 py-2 bg-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500/20 transition-all w-full"
                                                       onkeypress="if(event.key === 'Enter') { event.preventDefault(); addRankTag(); }">
                                                <button type="button" onclick="addRankTag()" 
                                                        class="px-3 sm:px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-xs sm:text-sm font-medium flex items-center justify-center gap-1.5 whitespace-nowrap w-full sm:w-auto">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                    <span class="hidden sm:inline">Add</span>
                                                    <span class="sm:hidden">Add Rank</span>
                                            </button>
                                        </div>
                                        </div>
                                        
                                        <!-- Colleges -->
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 sm:p-4 md:p-5 w-full">
                                            <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h4 class="text-sm sm:text-base font-semibold text-gray-900">Colleges</h4>
                                                    <span id="collegesCount" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 whitespace-nowrap">
                                                        {{ count(array_filter(old('colleges', $colleges ?? []))) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div id="collegesContainer" class="flex flex-wrap gap-2 mb-3 min-h-[60px]">
                                            @php($colleges = old('colleges', $colleges ?? []))
                                                @php($colleges = array_filter($colleges))
                                            @foreach($colleges as $idx => $college)
                                                @if(!empty(trim($college)))
                                                <div class="group relative inline-flex items-center gap-1.5 px-2 sm:px-3 py-1 sm:py-1.5 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-700 hover:border-indigo-400 hover:bg-indigo-50 transition-all max-w-full">
                                                    <input type="hidden" name="colleges[]" value="{{ $college }}">
                                                    <span class="text-xs sm:text-sm truncate">{{ $college }}</span>
                                                    <button type="button" onclick="window.showDeleteModal && window.showDeleteModal(this, '{{ addslashes($college) }}', 'Colleges')" 
                                                            class="opacity-0 group-hover:opacity-100 transition-opacity ml-1 text-gray-400 hover:text-red-600 focus:outline-none flex-shrink-0">
                                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                                @endif
                                            @endforeach
                                        </div>
                                            <div class="flex flex-col sm:flex-row gap-2">
                                                <input type="text" id="collegeInput" 
                                                       placeholder="Add college..." 
                                                       class="flex-1 text-xs sm:text-sm border border-gray-300 rounded-lg px-2 sm:px-3 py-2 bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 transition-all w-full"
                                                       onkeypress="if(event.key === 'Enter') { event.preventDefault(); addCollegeTag(); }">
                                                <button type="button" onclick="addCollegeTag()" 
                                                        class="px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-xs sm:text-sm font-medium flex items-center justify-center gap-1.5 whitespace-nowrap w-full sm:w-auto">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                    <span class="hidden sm:inline">Add</span>
                                                    <span class="sm:hidden">Add College</span>
                                            </button>
                                        </div>
                                        </div>
                                        
                                        <!-- Others Indexing Options -->
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 sm:p-4 md:p-5 w-full">
                                            <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h4 class="text-sm sm:text-base font-semibold text-gray-900">Indexing Options</h4>
                                                    <span id="othersCount" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 whitespace-nowrap">
                                                        {{ count(array_filter(old('others_indexing_options', $others_indexing_options ?? []))) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div id="othersIndexingContainer" class="flex flex-wrap gap-2 mb-3 min-h-[60px]">
                                            @php($othersIndexing = old('others_indexing_options', $others_indexing_options ?? []))
                                                @php($othersIndexing = array_filter($othersIndexing))
                                            @foreach($othersIndexing as $idx => $option)
                                                @if(!empty(trim($option)))
                                                <div class="group relative inline-flex items-center gap-1.5 px-2 sm:px-3 py-1 sm:py-1.5 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-700 hover:border-pink-400 hover:bg-pink-50 transition-all max-w-full">
                                                    <input type="hidden" name="others_indexing_options[]" value="{{ $option }}">
                                                    <span class="text-xs sm:text-sm truncate">{{ $option }}</span>
                                                    <button type="button" onclick="window.showDeleteModal && window.showDeleteModal(this, '{{ addslashes($option) }}', 'Indexing Options')" 
                                                            class="opacity-0 group-hover:opacity-100 transition-opacity ml-1 text-gray-400 hover:text-red-600 focus:outline-none flex-shrink-0">
                                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                                @endif
                                            @endforeach
                                            </div>
                                            <div class="flex flex-col sm:flex-row gap-2">
                                                <input type="text" id="othersInput" 
                                                       placeholder="Add option..." 
                                                       class="flex-1 text-xs sm:text-sm border border-gray-300 rounded-lg px-2 sm:px-3 py-2 bg-white focus:border-pink-500 focus:ring-1 focus:ring-pink-500/20 transition-all w-full"
                                                       onkeypress="if(event.key === 'Enter') { event.preventDefault(); addOthersIndexingTag(); }">
                                                <button type="button" onclick="addOthersIndexingTag()" 
                                                        class="px-3 sm:px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors text-xs sm:text-sm font-medium flex items-center justify-center gap-1.5 whitespace-nowrap w-full sm:w-auto">
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    <span class="hidden sm:inline">Add</span>
                                                    <span class="sm:hidden">Add Option</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        
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
                        
                        <!-- Removed standalone Calendar Settings Section (merged into Landing Page card) -->
                        
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
            
            // OLD CODE REMOVED: checkResearcherChanges function
            // Researchers are now managed via Livewire component
            
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
                // OLD CODE REMOVED: researcherInputs - researchers now managed via Livewire
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
                    // OLD CODE REMOVED: researchers tracking - now managed via Livewire
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
                
                // OLD CODE REMOVED: researcher input listeners - now managed via Livewire
                
                // Make functions globally available
                window.checkOfficialChanges = checkOfficialChanges;
                window.checkFeaturesChanges = checkFeaturesChanges;
                window.checkCalendarChanges = checkCalendarChanges;
                window.checkAnnouncementsChanges = checkAnnouncementsChanges;
                // OLD CODE REMOVED: checkResearcherChanges - now managed via Livewire
                
                // Check initial state
                checkOfficialChanges();
                checkFeaturesChanges();
                checkCalendarChanges();
        checkAnnouncementsChanges();
        
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
            // OLD CODE REMOVED: save_researchers form submit handler
            // Researchers are now managed via Livewire component
            } else if (false && e.submitter && e.submitter.name === 'save_researchers') {
                console.log('[DEBUG] Saving researchers - Form submit event');
                
                // OLD CODE REMOVED: researcher form submission logging
                // Researchers are now managed via Livewire component
                
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
            const index = container.querySelectorAll('div.group').length;
            const row = document.createElement('div');
            row.className = 'group relative bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-amber-300 transition-all duration-200 p-4';
            row.innerHTML = `
                <div class="flex items-center gap-4">
                    <!-- Counter -->
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow-sm">
                        <span class="text-white font-bold text-lg">${index + 1}</span>
                    </div>
                    
                    <!-- Form Fields -->
                    <div class="flex-1 flex gap-4">
                        <div class="w-48">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Event Date</label>
                            <input type="date" name="calendar_marks[${index}][date]" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Description</label>
                            <input type="text" name="calendar_marks[${index}][note]" 
                                   placeholder="Enter event description..." 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                        </div>
                    </div>
                    
                    <!-- Delete Button -->
                    <div class="flex-shrink-0">
                        <button type="button" onclick="removeMarkRow(this)" 
                                class="flex items-center justify-center w-10 h-10 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200" 
                                title="Remove Event">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            // Append at the bottom
            container.appendChild(row);
            
            // Update all counter numbers
            updateCalendarCounters();
            
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
        
        function updateCalendarCounters() {
            const container = document.getElementById('marksRepeater');
            if (!container) return;
            const rows = container.querySelectorAll('div.group');
            rows.forEach((row, index) => {
                const counterSpan = row.querySelector('div.flex-shrink-0 span');
                if (counterSpan) {
                    counterSpan.textContent = index + 1;
                }
            });
        }

        function removeMarkRow(btn) {
            const row = btn.closest('div.group');
            const container = document.getElementById('marksRepeater');
            if (row && container) {
                row.remove();
                
                // Update all counter numbers after removal
                updateCalendarCounters();
                
                // If no rows left, add an empty entry for consistency
                if (container.querySelectorAll('div.group').length === 0) {
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
            
            const index = container.querySelectorAll('div.group').length;
            const row = document.createElement('div');
            row.className = 'group relative bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all duration-200 p-4';
            row.innerHTML = `
                <div class="flex items-center gap-4">
                    <!-- Counter -->
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-indigo-400 to-blue-500 rounded-xl flex items-center justify-center shadow-sm">
                        <span class="text-white font-bold text-lg">${index + 1}</span>
                    </div>
                    
                    <!-- Form Fields -->
                    <div class="flex-1 flex gap-4">
                        <div class="w-1/3">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Title</label>
                            <input type="text" name="announcements[${index}][title]" 
                                   placeholder="Enter announcement title..." 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Description</label>
                            <input type="text" name="announcements[${index}][description]" 
                                   placeholder="Enter announcement description..." 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                        </div>
                    </div>
                    
                    <!-- Delete Button -->
                    <div class="flex-shrink-0">
                        <button type="button" onclick="removeAnnouncementRow(this)" 
                                class="flex items-center justify-center w-10 h-10 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200" 
                                title="Remove Announcement">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(row);
            
            // Update all counter numbers
            updateAnnouncementCounters();
            
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
        
        function updateAnnouncementCounters() {
            const container = document.getElementById('announcementsRepeater');
            if (!container) return;
            const rows = container.querySelectorAll('div.group');
            rows.forEach((row, index) => {
                const counterSpan = row.querySelector('div.flex-shrink-0 span');
                if (counterSpan) {
                    counterSpan.textContent = index + 1;
                }
            });
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
            const row = btn.closest('div.group');
            const container = document.getElementById('announcementsRepeater');
            if (row && container) {
                row.remove();
                
                // Update all counter numbers after removal
                updateAnnouncementCounters();
                
                // If no rows left, add an empty entry for consistency
                if (container.querySelectorAll('div.group').length === 0) {
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