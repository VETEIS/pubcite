<x-app-layout>
    <style>
        body { 
            overflow-x: hidden; 
            min-height: 100vh;
        }
        html { 
            overflow-x: hidden; 
            height: 100%;
        }
        .admin-dashboard-container {
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Fix scrollbar layout shift */
        .table-container {
            height: 600px;
            overflow: hidden;
        }
        
        .table-scroll-area {
            height: 500px;
            overflow-y: scroll;
            /* Always show scrollbar to prevent layout shift */
            overflow-y: overlay;
            /* For webkit browsers */
            scrollbar-width: thin;
            scrollbar-color: #d1d5db transparent;
        }
        
        /* Webkit scrollbar styling */
        .table-scroll-area::-webkit-scrollbar {
            width: 8px;
        }
        
        .table-scroll-area::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .table-scroll-area::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            border-radius: 4px;
        }
        
        .table-scroll-area::-webkit-scrollbar-thumb:hover {
            background-color: #9ca3af;
        }
        
        /* Ensure table layout doesn't shift */
        .requests-table {
            table-layout: fixed;
            width: 100%;
        }
        
        /* Prevent content from affecting layout */
        .table-content {
            min-height: 500px;
        }
        
        /* Ensure pagination stays in place */
        .pagination-container {
            position: relative;
            z-index: 1;
        }
        
        /* Prevent any layout shifts from content changes */
        .stable-layout {
            overflow: hidden;
        }
        
        /* Ensure table cells maintain consistent width */
        .table-cell {
            box-sizing: border-box;
        }
        
        /* Force scrollbar to always be present */
        .always-scroll {
            overflow-y: scroll !important;
        }
        
        /* Hide scrollbar when not needed but maintain space */
        .scrollbar-gutter-stable {
            scrollbar-gutter: stable;
        }
        
        /* Fallback for browsers that don't support scrollbar-gutter */
        @supports not (scrollbar-gutter: stable) {
            .table-scroll-area {
                padding-right: 8px; /* Approximate scrollbar width */
            }
        }
        
        /* Ensure consistent layout across all browsers */
        .table-container * {
            box-sizing: border-box;
        }
        
        /* Prevent any horizontal scrollbar from affecting layout */
        .table-scroll-area {
            overflow-x: auto;
            overflow-y: scroll;
        }
    </style>
    <div x-data="{ 
        loading: false,
        errorMessage: null,
        errorTimer: null,
        activeTab: 'dashboard',
        searchOpen: false,
        userMenuOpen: false,
        showError(message) {
            this.errorMessage = message;
            if (this.errorTimer) clearTimeout(this.errorTimer);
            this.errorTimer = setTimeout(() => {
                this.errorMessage = null;
            }, 3000);
        }
         }" class="h-screen bg-gray-50 flex overflow-hidden admin-dashboard-container" style="scrollbar-gutter: stable;">
        
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
                    <!-- Date Range Display -->
                    <div class="flex items-center gap-4">
                        <!-- Overview Header -->
                        <div class="flex items-center gap-2 text-md font-semibold text-gray-600 bg-gray-50 px-3 py-2.5 rounded-lg h-10">
                            <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Overview</span>
                        </div>
                        
                        <!-- Compact Date Range Display -->
                        <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 h-8 hover:border-gray-300 transition-colors">
                            <svg class="w-3.5 h-3.5 text-maroon-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-xs font-medium text-gray-700">
                                @if($rangeDescription)
                                    {{ $rangeDescription }}
                                @else
                                    All time
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <!-- Enhanced Search and User Controls -->
                    <div class="flex items-center gap-4">
                        <!-- Modern Compact Filters -->
                        <div class="flex items-center gap-2">
                            @php
                                $currentStatus = request('status');
                                $currentType = request('type');
                                $currentPeriod = request('period');
                                $filteredRequests = $requests;
                                if ($currentStatus) {
                                    $filteredRequests = $requests->where('status', $currentStatus);
                                }
                                @endphp
                            
                            <!-- Compact Status Filter -->
                            <div class="relative group">
                                <button class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 h-8 w-32 justify-between">
                                    <svg class="w-3.5 h-3.5 text-maroon-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="min-w-[60px] max-w-[80px] truncate">{{ $currentStatus ? ucfirst($currentStatus) : 'All Status' }}</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div class="absolute top-full left-0 mt-1 bg-white text-md font-semibold border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 min-w-[120px]">
                                    <a href="{{ route('admin.dashboard', array_merge(request()->except('status', 'page'), ['status' => null])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ !$currentStatus ? 'bg-maroon-50 text-maroon-700' : '' }}">All Status</a>
                                    <a href="{{ route('admin.dashboard', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentStatus === 'pending' ? 'bg-maroon-50 text-maroon-700' : '' }}">Pending</a>
                                    <a href="{{ route('admin.dashboard', array_merge(request()->except('status', 'page'), ['status' => 'endorsed'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentStatus === 'endorsed' ? 'bg-maroon-50 text-maroon-700' : '' }}">Endorsed</a>
                                    <a href="{{ route('admin.dashboard', array_merge(request()->except('status', 'page'), ['status' => 'rejected'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentStatus === 'rejected' ? 'bg-maroon-50 text-maroon-700' : '' }}">Rejected</a>
                </div>
            </div>

                            <!-- Compact Type Filter -->
                            <div class="relative group">
                                <button class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 h-8 w-32 justify-between">
                                    <svg class="w-3.5 h-3.5 text-maroon-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                                    <span class="min-w-[60px] max-w-[80px] truncate">{{ $currentType ? ($currentType === 'Publication' ? 'Publications' : ($currentType === 'Citation' ? 'Citations' : $currentType)) : 'All Types' }}</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div class="absolute top-full left-0 mt-1 bg-white text-md font-semibold border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 min-w-[120px]">
                                    <a href="{{ route('admin.dashboard', array_merge(request()->except('type', 'page'), ['type' => null])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ !$currentType ? 'bg-maroon-50 text-maroon-700' : '' }}">All Types</a>
                                    <a href="{{ route('admin.dashboard', array_merge(request()->except('type', 'page'), ['type' => 'Publications'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentType === 'Publications' ? 'bg-maroon-50 text-maroon-700' : '' }}">Publications</a>
                                    <a href="{{ route('admin.dashboard', array_merge(request()->except('type', 'page'), ['type' => 'Citations'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentType === 'Citations' ? 'bg-maroon-50 text-maroon-700' : '' }}">Citations</a>
                                </div>
                            </div>
                            
                            <!-- Compact Period Filter -->
                            <div class="relative group">
                                <button class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 h-8 w-32 justify-between">
                                    <svg class="w-3.5 h-3.5 text-maroon-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                                    <span class="min-w-[60px] max-w-[80px] truncate">{{ $currentPeriod ? ucfirst($currentPeriod) : 'All Time' }}</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div class="absolute top-full left-0 mt-1 bg-white text-md font-semibold border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 min-w-[120px]">
                                    <a href="{{ route('admin.dashboard', array_merge(request()->except('period', 'page'), ['period' => null])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ !$currentPeriod ? 'bg-maroon-50 text-maroon-700' : '' }}">All Time</a>
                                    <a href="{{ route('admin.dashboard', array_merge(request()->except('period', 'page'), ['period' => 'This Week'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentPeriod === 'This Week' ? 'bg-maroon-50 text-maroon-700' : '' }}">This Week</a>
                                    <a href="{{ route('admin.dashboard', array_merge(request()->except('period', 'page'), ['period' => 'This Month'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentPeriod === 'This Month' ? 'bg-maroon-50 text-maroon-700' : '' }}">This Month</a>
                                    <a href="{{ route('admin.dashboard', array_merge(request()->except('period', 'page'), ['period' => 'This Quarter'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentPeriod === 'This Quarter' ? 'bg-maroon-50 text-maroon-700' : '' }}">This Quarter</a>
                                    <a href="{{ route('admin.dashboard', array_merge(request()->except('period', 'page'), ['period' => 'This Year'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentPeriod === 'This Year' ? 'bg-maroon-50 text-maroon-700' : '' }}">This Year</a>
                                </div>
                            </div>
                            
                            <!-- Clear Filters Button -->
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium {{ $currentType || $currentStatus || $currentPeriod ? 'text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 hover:border-red-300' : 'text-gray-400 bg-gray-50 border border-gray-200 cursor-not-allowed' }} rounded-lg transition-all duration-200 h-8 {{ $currentType || $currentStatus || $currentPeriod ? 'hover:scale-105' : '' }}">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                                <span>Clear</span>
                            </a>
                        </div>

                        <!-- Subtle Separator -->
                        <div class="w-px h-8 bg-gray-200"></div>
                        
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

                <!-- Charts and Activity Log Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Charts Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow py-4 pl-4 flex flex-col md:flex-row items-stretch justify-center overflow-y-auto min-h-[220px] gap-4">
                        <!-- Request Stats (Line Chart) -->
                        <div class="flex-[3_3_0%] flex flex-col items-center justify-center min-w-0 overflow-hidden pl-3 pr-1 relative">
                            <h2 class="text-sm font-semibold mb-2 text-left w-full flex items-center gap-2">
                                <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span id="chartTitle">Request Stats (Last 12 Months)</span>
                            </h2>
                            <div class="w-full flex-1 flex items-center justify-center relative">
                                <!-- Loading Overlay for Line Chart -->
                                <div id="lineChartLoading" class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center z-50 transition-opacity duration-300" style="opacity: 1;">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-maroon-600"></div>
                                        <p class="text-sm text-gray-600 font-medium">Loading chart...</p>
                                    </div>
                                </div>
                                <canvas id="monthlyChart" class="w-full h-48 max-h-[200px] transition-opacity duration-500" style="max-height:200px;"></canvas>
                            </div>
                        </div>
                                                 <!-- Status Breakdown (Donut Chart + Legend) -->
                         <div class="flex-[1_1_0%] flex flex-col items-center justify-center min-w-0 overflow-hidden border-t md:border-t-0 md:border-l border-gray-200 p-4 relative">
                             <div class="w-full max-w-xs mx-auto flex flex-col items-center justify-center">
                                 <!-- Loading Overlay for Pie Chart -->
                                 <div id="pieChartLoading" class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center z-50 transition-opacity duration-300" style="opacity: 1;">
                                     <div class="flex flex-col items-center gap-3">
                                         <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-maroon-600"></div>
                                         <p class="text-sm text-gray-600 font-medium">Loading chart...</p>
                                     </div>
                                 </div>
                                 <canvas id="statusChart" class="w-28 h-28 max-w-[112px] max-h-[112px] transition-opacity duration-500" style="max-width:112px;max-height:112px;"></canvas>
                                 <div id="statusLegend" class="mt-2 w-full transition-all duration-500">
                                    <table class="w-full text-xs min-w-0 table-fixed">
                                        <thead>
                                            <tr class="border-b border-gray-200">
                                                <th class="text-left py-1 font-semibold text-gray-700 w-16">Status</th>
                                                <th class="text-center py-1 font-semibold text-gray-700 w-8">Count</th>
                                                <th class="text-right py-1 font-semibold text-gray-700 w-8">%</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $statusLabels = ['Pending', 'Endorsed', 'Rejected'];
                                                $statusColors = ['bg-yellow-400', 'bg-green-500', 'bg-red-500'];
                                                $total = array_sum(array_values($statusCounts));
                                            @endphp
                                            @foreach($statusLabels as $i => $label)
                                                <tr class="hover:bg-gray-50 transition-all duration-300">
                                                    <td class="py-1 flex items-center truncate">
                                                        <span class="inline-block w-2 h-2 rounded-full {{ $statusColors[$i] }} mr-1 flex-shrink-0"></span>
                                                        <span class="font-semibold truncate">{{ $label }}</span>
                                                    </td>
                                                    <td class="py-1 text-center font-semibold">{{ $statusCounts[strtolower($label)] ?? 0 }}</td>
                                                    <td class="py-1 text-right text-gray-500">@if($total > 0){{ number_format(100 * ($statusCounts[strtolower($label)] ?? 0) / $total, 1) }}%@else 0%@endif</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                                    </div>
                                </div>
                    
                        <!-- Activity Log Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow overflow-visible" style="overflow: visible;">
                        <div class="bg-gray-50 sticky top-0 left-0 right-0 z-10 px-4 py-3 rounded-t-lg">
                            <h2 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Activity Log
                            </h2>
                            </div>
                        <div class="overflow-y-auto max-h-[220px] p-4 table-scroll-area always-scroll scrollbar-gutter-stable" style="overflow-x: visible;">
                            <ul class="space-y-2">
                                @foreach($activityLogs as $log)
                                    <li class="grid grid-cols-[auto_1fr_auto_16px_80px] items-center gap-2 bg-gray-50 hover:bg-white hover:shadow-md rounded-lg p-2 transition-all duration-300 cursor-pointer group relative">
                                        @php
                                            $icon = match($log->action) {
                                                'created' => 'plus-circle',
                                                'status_changed' => 'refresh-cw',
                                                'deleted' => 'trash-2',
                                                default => 'activity',
                                            };
                                            $iconColor = match($log->action) {
                                                'created' => 'text-green-500',
                                                'status_changed' => 'text-blue-500',
                                                'deleted' => 'text-red-500',
                                                default => 'text-maroon-400',
                                            };
                                            $desc = '';
                                            if ($log->action === 'created') {
                                                $desc = 'Request&nbsp;<b>' . e($log->details['request_code'] ?? '') . '</b>&nbsp;submitted';
                                            } elseif ($log->action === 'status_changed') {
                                                $oldStatus = e($log->details['old_status'] ?? '');
                                                $newStatus = e($log->details['new_status'] ?? '');
                                                $requestCode = e($log->details['request_code'] ?? '');
                                                
                                                // Color mapping for status
                                                $oldColor = match($oldStatus) {
                                                    'pending' => 'text-yellow-600',
                                                    'endorsed' => 'text-green-600',
                                                    'rejected' => 'text-red-600',
                                                    default => 'text-gray-600'
                                                };
                                                $newColor = match($newStatus) {
                                                    'pending' => 'text-yellow-600',
                                                    'endorsed' => 'text-green-600',
                                                    'rejected' => 'text-red-600',
                                                    default => 'text-gray-600'
                                                };
                                                
                                                $desc = '<b>' . $requestCode . '</b>:&nbsp;<span class="' . $oldColor . ' font-semibold">' . ucfirst($oldStatus) . '</span>&nbsp;<span class="text-gray-400 mx-1">→</span>&nbsp;<span class="' . $newColor . ' font-semibold">' . ucfirst($newStatus) . '</span>';
                                            } elseif ($log->action === 'deleted') {
                                                $desc = 'Request&nbsp;<b>' . e($log->details['request_code'] ?? '') . '</b>&nbsp;deleted';
                                            } elseif ($log->action === 'nudged') {
                                                $desc = 'Nudge&nbsp;for&nbsp;<b>' . e($log->details['request_code'] ?? '') . '</b>';
                                            } else {
                                                $desc = ucfirst($log->action);
                                    }
                                @endphp
                                        <span class="flex items-center justify-center w-7 h-7 rounded-full 
    @if($icon === 'plus-circle' && ($log->details['type'] ?? null) === 'Publication') bg-maroon-800 @elseif($icon === 'plus-circle' && ($log->details['type'] ?? null) === 'Citation') bg-maroon-800 @elseif($icon === 'plus-circle') bg-green-500 @elseif($icon === 'refresh-cw') bg-blue-500 @elseif($icon === 'trash-2') bg-red-500 @else bg-maroon-400 @endif border">
    @if($icon === 'plus-circle' && ($log->details['type'] ?? null) === 'Publication')
        <svg class="w-5 h-5" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m0 0H3m9 0h9" /></svg>
    @elseif($icon === 'plus-circle' && ($log->details['type'] ?? null) === 'Citation')
        <svg class="w-5 h-5" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2z" /><path stroke-linecap="round" stroke-linejoin="round" d="M17 3v4M7 3v4" /></svg>
    @elseif($icon === 'plus-circle')
        <svg class="w-5 h-5" fill="white" stroke="white" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
    @elseif($icon === 'refresh-cw')
        <svg class="w-5 h-5" fill="none" stroke="white" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.13-3.36L23 10M1 14l5.37 5.36A9 9 0 0020.49 15"/></svg>
    @elseif($icon === 'trash-2')
        <svg class="w-5 h-5" fill="none" stroke="white" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m5 0V4a2 2 0 012-2h0a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
    @else
        <svg class="w-5 h-5" fill="none" stroke="white" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    @endif
</span>
                                        <span class="min-w-0 text-xs text-gray-900 font-medium group-hover:whitespace-normal group-hover:break-words group-hover:overflow-visible transition-all duration-200">
                                            {!! $desc !!}
                                        </span>
                                        <span class="text-xs text-right whitespace-nowrap min-w-[80px] pl-2 @if($log->user && $log->user->role === 'admin') text-maroon-900 font-extrabold @else text-gray-700 font-semibold @endif">
                                            @if($log->user)
                                                @php
                                                    $nameParts = preg_split('/\s+/', trim($log->user->name ?? ''));
                                                    if (count($nameParts) === 1) {
                                                        $shortName = $log->user->name;
                                                    } else {
                                                        $last = array_pop($nameParts);
                                                        $initials = '';
                                                        foreach ($nameParts as $p) {
                                                            if ($p !== '') $initials .= mb_substr($p, 0, 1) . '.';
                                                        }
                                                        $shortName = $initials . $last;
                                                    }
                                    @endphp
                                                {{ $shortName }}
                                                @if($log->user->role === 'admin')
                                                    <svg class="inline w-3 h-3 text-maroon-900 ml-1 align-baseline relative" style="top:1px;line-height:1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l7 4v5c0 5.25-3.5 9.25-7 11-3.5-1.75-7-5.75-7-11V7l7-4z" /><circle cx="12" cy="10" r="2" fill="currentColor"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17a3 3 0 00-6 0" /></svg>
                                    @endif
                                @else
                                                System
                                @endif
                                        </span>
                                        <span class="text-xs text-gray-400 text-center w-4 flex items-center justify-center">&middot;</span>
                                        <span class="text-xs text-gray-500 text-right whitespace-nowrap min-w-[60px] max-w-[80px] w-full block pr-1 font-semibold">
                                            <span title="{{ $log->created_at->setTimezone('Asia/Manila')->toDayDateTimeString() }}">{{ $log->created_at->setTimezone('Asia/Manila')->diffForHumans() }}</span>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            </div>
                        </div>
                                </div>

                <!-- Requests Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">

                                <!-- Requests Table -->
                    <div class="table-container stable-layout">
                    <div class="overflow-x-auto table-scroll-area always-scroll scrollbar-gutter-stable">
                        <div class="table-content">
                            <table class="w-full divide-y divide-gray-200 requests-table">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    @php
                                        $currentSort = request('sort', 'requested_at');
                                        $currentOrder = request('order', 'desc');
                                    @endphp
                                    
                                    <th class="w-32 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                        <a href="{{ route('admin.dashboard', array_merge(request()->except(['sort', 'order', 'page']), ['sort' => 'requested_at', 'order' => $currentSort === 'requested_at' && $currentOrder === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1">
                                            Date
                                            @if($currentSort === 'requested_at')
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
                                    <th class="w-32 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                        <a href="{{ route('admin.dashboard', array_merge(request()->except(['sort', 'order', 'page']), ['sort' => 'request_code', 'order' => $currentSort === 'request_code' && $currentOrder === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1">
                                            Request Code
                                            @if($currentSort === 'request_code')
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
                                    <th class="w-48 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center gap-1">
                                            Applicant
                                        </div>
                                    </th>
                                    <th class="w-24 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors" style="width: 96px; max-width: 96px; min-width: 96px;">
                                        <a href="{{ route('admin.dashboard', array_merge(request()->except(['sort', 'order', 'page']), ['sort' => 'type', 'order' => $currentSort === 'type' && $currentOrder === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center justify-center gap-1">
                                            Type
                                            @if($currentSort === 'type')
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
                                    <th class="w-28 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                        <a href="{{ route('admin.dashboard', array_merge(request()->except(['sort', 'order', 'page']), ['sort' => 'status', 'order' => $currentSort === 'status' && $currentOrder === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center justify-center gap-1">
                                            Status
                                            @if($currentSort === 'status')
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
                                    <th class="w-24 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if($filteredRequests->isEmpty())
                                    <tr>
                                        <td class="w-32 px-6 py-4"></td>
                                        <td class="w-32 px-6 py-4"></td>
                                        <td class="w-48 px-6 py-4"></td>
                                        <td class="w-24 px-6 py-4"></td>
                                        <td class="w-28 px-6 py-4"></td>
                                        <td class="w-24 px-6 py-4"></td>
                                    </tr>
                                    <tr>
                                        <td class="w-32 px-6 py-4"></td>
                                        <td class="w-32 px-6 py-4"></td>
                                        <td class="w-48 px-6 py-4"></td>
                                        <td class="w-24 px-6 py-4"></td>
                                        <td class="w-28 px-6 py-4"></td>
                                        <td class="w-24 px-6 py-4"></td>
                                    </tr>
                                    <tr>
                                        <td class="w-32 px-6 py-4"></td>
                                        <td class="w-32 px-6 py-4"></td>
                                        <td class="w-48 px-6 py-4">
                                            <div class="flex flex-col items-center justify-center gap-3">
                                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20v-6m0 0l-3 3m3-3l3 3M4 6h16M4 10h16M4 14h16"/>
                                                    </svg>
                                        </div>
                                                <div class="text-center">
                                                    <h4 class="text-lg font-semibold text-gray-900">No requests found</h4>
                                                    <p class="text-gray-500">No requests match your current filters.</p>
                                    </div>
                                            </div>
                                        </td>
                                        <td class="w-24 px-6 py-4"></td>
                                        <td class="w-28 px-6 py-4"></td>
                                        <td class="w-24 px-6 py-4"></td>
                                    </tr>
                                @else
                                    @foreach($filteredRequests as $index => $request)
                                    <tr class="hover:bg-white hover:shadow-md transition-all duration-300 cursor-pointer group">
                                        <td class="w-32 px-6 py-3 text-sm text-gray-900 overflow-hidden">
                                            <div class="truncate">{{ \Carbon\Carbon::parse($request->requested_at)->format('M d, Y H:i') }}</div>
                                        </td>
                                        <td class="w-32 px-6 py-3 overflow-hidden">
                                            <div class="text-sm font-medium text-gray-900 truncate">{{ $request->request_code }}</div>
                                        </td>
                                        <td class="w-48 px-6 py-3 overflow-hidden">
                                            <div class="flex items-center w-full">
                                                @if($request->user->profile_photo_path)
                                                    <img src="{{ $request->user->profile_photo_url }}" alt="{{ $request->user->name }}" class="w-8 h-8 rounded-full object-cover mr-3 flex-shrink-0">
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-maroon-100 flex items-center justify-center mr-3 flex-shrink-0">
                                                        <span class="text-sm font-medium text-maroon-700">{{ strtoupper(substr($request->user->name ?? 'U', 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                                <div class="min-w-0 flex-1 overflow-hidden">
                                                    <div class="text-sm font-medium text-gray-900 truncate">{{ $request->user->name ?? 'N/A' }}</div>
                                                    <div class="text-sm text-gray-500 truncate">{{ $request->user->email ?? 'No email' }}</div>
                                    </div>
                                                </div>
                                        </td>
                                        <td class="w-24 px-6 py-3 overflow-hidden" style="width: 96px; max-width: 96px; min-width: 96px;">
                                            <div class="w-full flex justify-center">
                                                <span class="inline-flex items-center px-1 py-0.5 rounded-full text-xs font-medium w-20 justify-center truncate overflow-hidden
                                                    {{ $request->type === 'Publication' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                    <span class="truncate">{{ $request->type }}</span>
                                                </span>
                                                </div>
                                        </td>
                                        <td class="w-28 px-6 py-3 overflow-hidden">
                                            <div class="w-full flex justify-center">
                                                @if($request->status === 'endorsed')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 w-20 justify-center">
                                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                                        Endorsed
                                                    </span>
                                                @elseif($request->status === 'rejected')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-20 justify-center">
                                                        <div class="w-2 h-2 bg-red-400 rounded-full mr-2"></div>
                                                        Rejected
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 w-20 justify-center">
                                                        <div class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></div>
                                                        Pending
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="w-24 px-6 py-3 text-center text-sm font-medium overflow-hidden">
                                            <div class="flex items-center justify-center gap-1 w-full">
                                                <!-- Review Button -->
                                                <button type="button" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 hover:shadow-md hover:scale-105 transition-all duration-300 text-xs font-medium group-hover:bg-blue-200" title="Review" onclick="openReviewModal({{ $request->id }})">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    Review
                                                </button>
                                                
                                                <!-- Delete Button -->
                                                <form action="{{ route('admin.requests.destroy', $request->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this request?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 hover:shadow-md hover:scale-105 transition-all duration-300 text-xs font-medium group-hover:bg-red-200" title="Delete">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
                    
                                        <!-- Pagination -->
                    <div class="bg-white px-4 py-2 border-t border-gray-200 flex items-center justify-between pagination-container">
                        <div class="text-sm text-gray-700 min-w-0">
                            <span class="whitespace-nowrap">Showing <span class="font-medium w-8 inline-block text-center">1</span> to <span class="font-medium w-8 inline-block text-center">{{ $filteredRequests->count() }}</span> of <span class="font-medium w-8 inline-block text-center">{{ $requests->count() }}</span> results</span>
                            </div>
                        <div class="flex items-center space-x-2 flex-shrink-0">
                            {{ $requests->links() }}
                                </div>
                            </div>
                        </div>
                    </div>


            </main>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
        let monthlyChartInstance = null;
        let statusChartInstance = null;
let eventSource;

function initializeRealTimeUpdates() {
    // Close existing connection if any
    if (eventSource) {
        eventSource.close();
    }
    
    // Create new SSE connection
    eventSource = new EventSource('{{ route("admin.dashboard.stream") }}');
    
    eventSource.onmessage = function(event) {
        try {
            const data = JSON.parse(event.data);
            updateDashboard(data);
        } catch (error) {
            console.error('Error parsing SSE data:', error);
        }
    };
    
    eventSource.onerror = function(error) {
        console.error('SSE connection error:', error);
        // Reconnect after 5 seconds
        setTimeout(initializeRealTimeUpdates, 5000);
    };
    
    eventSource.onopen = function() {
        console.log('Real-time updates connected');
    };
}

function updateDashboard(data) {
    // Only update if there are actual changes
    if (!data.hasChanges) return;
    
    // Update stats
    if (data.stats) {
        updatePeriodStats(data.stats);
    }
    
    // Update activity logs
    if (data.activityLogs) {
        updateActivityLogs(data.activityLogs);
    }
    
    // Show notification for new updates
    showUpdateNotification();
}

function updatePeriodStats(stats) {
    const type = '{{ request("type") }}';
    const periodStats = {
        'week': type === 'Citation' ? (stats.citation.week || 0)
            : (type === 'Publication' ? (stats.publication.week || 0)
            : ((stats.publication.week || 0) + (stats.citation.week || 0))),
        'month': type === 'Citation' ? (stats.citation.month || 0)
            : (type === 'Publication' ? (stats.publication.month || 0)
            : ((stats.publication.month || 0) + (stats.citation.month || 0))),
        'quarter': type === 'Citation' ? (stats.citation.quarter || 0)
            : (type === 'Publication' ? (stats.publication.quarter || 0)
            : ((stats.publication.quarter || 0) + (stats.citation.quarter || 0))),
    };
    
    // Update period stat cards
    Object.keys(periodStats).forEach(period => {
        const statElement = document.querySelector(`[data-period="${period}"]`);
        if (statElement) {
            statElement.textContent = periodStats[period];
        }
    });
}

function updateActivityLogs(activityLogs) {
    const activityLogContainer = document.querySelector('.activity-log-list');
    if (!activityLogContainer) return;
    
    // Clear existing logs
    activityLogContainer.innerHTML = '';
    
    // Add new logs
    activityLogs.forEach(log => {
        const logItem = createActivityLogItem(log);
        activityLogContainer.appendChild(logItem);
    });
}

function createActivityLogItem(log) {
    const li = document.createElement('li');
            li.className = 'grid grid-cols-[auto_1fr_auto_16px_80px] items-center gap-2 bg-gray-50 rounded-lg p-3';
    
    // Create icon
    const icon = document.createElement('span');
    icon.className = `flex items-center justify-center w-7 h-7 rounded-full bg-white border ${getIconColor(log.action)}`;
    icon.innerHTML = getIconSvg(log.action);
    
    // Create description
    const desc = document.createElement('span');
            desc.className = 'min-w-0 text-xs text-gray-900 font-medium truncate';
    desc.innerHTML = getLogDescription(log);
    
    // Create username
    const username = document.createElement('span');
    username.className = `text-xs text-right whitespace-nowrap min-w-[80px] pl-2 ${log.user && log.user.role === 'admin' ? 'text-maroon-900 font-bold' : 'text-gray-700'}`;
    username.textContent = log.user ? getShortName(log.user.name) : 'System';
    
    // Create separator
    const separator = document.createElement('span');
    separator.className = 'text-xs text-gray-400 text-center w-4 flex items-center justify-center';
    separator.textContent = '·';
    
    // Create timestamp
    const timestamp = document.createElement('span');
    timestamp.className = 'text-xs text-gray-500 text-right whitespace-nowrap min-w-[60px] max-w-[80px] w-full block pr-1';
    timestamp.innerHTML = `<span title="${new Date(log.created_at).toLocaleString()}">${getTimeAgo(log.created_at)}</span>`;
    
    li.appendChild(icon);
    li.appendChild(desc);
    li.appendChild(username);
    li.appendChild(separator);
    li.appendChild(timestamp);
    
    return li;
}

function getIconColor(action) {
    switch(action) {
        case 'created': return 'text-green-500';
        case 'status_changed': return 'text-blue-500';
        case 'deleted': return 'text-red-500';
        default: return 'text-maroon-400';
    }
}

function getIconSvg(action) {
    switch(action) {
        case 'created':
            return '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>';
        case 'status_changed':
            return '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.13-3.36L23 10M1 14l5.37 5.36A9 9 0 0020.49 15"/></svg>';
        case 'deleted':
            return '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m5 0V4a2 2 0 012-2h0a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>';
        default:
            return '<svg class="w-5 h-5 text-maroon-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>';
    }
}

function getLogDescription(log) {
    switch(log.action) {
        case 'created':
            return `Request <b>${log.details?.request_code || ''}</b> submitted (${log.details?.type || ''})`;
        case 'status_changed':
            return `Status changed <b>${log.details?.old_status || ''}</b> → <b>${log.details?.new_status || ''}</b> for <b>${log.details?.request_code || ''}</b>`;
        case 'deleted':
            return `Request <b>${log.details?.request_code || ''}</b> deleted`;
        default:
            return log.action.charAt(0).toUpperCase() + log.action.slice(1);
    }
}

function getShortName(name) {
    const nameParts = name.trim().split(/\s+/);
    if (nameParts.length === 1) {
        return name;
    }
    const last = nameParts.pop();
    const initials = nameParts.map(p => p.charAt(0) + '.').join('');
    return initials + last;
}

function getTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    
    // Convert to Asia/Manila timezone
    const manilaDate = new Date(date.toLocaleString("en-US", {timeZone: "Asia/Manila"}));
    const manilaNow = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Manila"}));
    
    const diffInSeconds = Math.floor((manilaNow - manilaDate) / 1000);
    
    if (diffInSeconds < 60) return `${diffInSeconds} seconds ago`;
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    return `${Math.floor(diffInSeconds / 86400)} days ago`;
}

function showUpdateNotification() {
            // Create notification that animates from the top
    const notification = document.createElement('div');
            notification.className = 'fixed top-20 right-4 z-50 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg text-sm font-medium transform transition-all duration-500';
    notification.textContent = 'Dashboard updated';
    
            // Start position: above viewport
            notification.style.transform = 'translateY(-100%)';
    
    document.body.appendChild(notification);
    
            // Animate in
    setTimeout(() => {
                notification.style.transform = 'translateY(0)';
    }, 100);
    
            // Animate out after 3 seconds
    setTimeout(() => {
                notification.style.transform = 'translateY(-100%)';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

        function showChartsLoading() {
            console.log('Showing charts loading...');
            const lineChartLoading = document.getElementById('lineChartLoading');
            const pieChartLoading = document.getElementById('pieChartLoading');
            
            if (lineChartLoading) {
                lineChartLoading.style.opacity = '1';
                lineChartLoading.style.pointerEvents = 'auto';
            }
            if (pieChartLoading) {
                pieChartLoading.style.opacity = '1';
                pieChartLoading.style.pointerEvents = 'auto';
            }
            
            console.log('Loading elements opacity set to 1');
            
            // Fade out charts
            const charts = document.querySelectorAll('#monthlyChart, #statusChart');
            charts.forEach(chart => {
                chart.style.opacity = '0.3';
            });
            console.log('Charts faded out');
        }

        function hideChartsLoading() {
            console.log('Hiding charts loading...');
            const lineChartLoading = document.getElementById('lineChartLoading');
            const pieChartLoading = document.getElementById('pieChartLoading');
            
            if (lineChartLoading) {
                lineChartLoading.style.opacity = '0';
                lineChartLoading.style.pointerEvents = 'none';
            }
            if (pieChartLoading) {
                pieChartLoading.style.opacity = '0';
                pieChartLoading.style.pointerEvents = 'none';
            }
            
            console.log('Loading elements opacity set to 0');
            
            // Fade in charts
            const charts = document.querySelectorAll('#monthlyChart, #statusChart');
            charts.forEach(chart => {
                chart.style.opacity = '1';
            });
            console.log('Charts faded in');
        }

        function updateChartTitle(period) {
            const titleElement = document.getElementById('chartTitle');
            if (!titleElement) return;
            
            let title = 'Request Stats';
            if (period) {
                switch(period) {
                    case 'This Week':
                        title = 'Request Stats (This Week)';
                        break;
                    case 'This Month':
                        title = 'Request Stats (This Month)';
                        break;
                    case 'This Quarter':
                        title = 'Request Stats (This Quarter)';
                        break;
                    case 'This Year':
                        title = 'Request Stats (This Year)';
                        break;
                    default:
                        title = 'Request Stats (Last 12 Months)';
                }
            } else {
                title = 'Request Stats (Last 12 Months)';
            }
            
            titleElement.textContent = title;
        }

        function updateStatusLegend(statusCounts) {
            const legendElement = document.getElementById('statusLegend');
            if (!legendElement) return;
            
            const statusLabels = ['Pending', 'Endorsed', 'Rejected'];
            const statusColors = ['bg-yellow-400', 'bg-green-500', 'bg-red-500'];
            const total = statusCounts.pending + statusCounts.endorsed + statusCounts.rejected;
            
            legendElement.innerHTML = `
                <table class="w-full text-xs min-w-0 table-fixed">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-1 font-semibold text-gray-700 w-16">Status</th>
                            <th class="text-center py-1 font-semibold text-gray-700 w-8">Count</th>
                            <th class="text-right py-1 font-semibold text-gray-700 w-8">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${statusLabels.map((label, i) => {
                            const count = statusCounts[label.toLowerCase()] || 0;
                            const percentage = total > 0 ? ((count / total) * 100).toFixed(1) : '0';
                            
                            return `
                                <tr class="hover:bg-gray-50 transition-all duration-300">
                                    <td class="py-1 flex items-center truncate">
                                        <span class="inline-block w-2 h-2 rounded-full ${statusColors[i]} mr-1 flex-shrink-0"></span>
                                        <span class="font-semibold truncate">${label}</span>
                                    </td>
                                    <td class="py-1 text-center font-semibold">${count}</td>
                                    <td class="py-1 text-right text-gray-500">${percentage}%</td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            `;
        }

        function updateChartsWithData(data) {
            // Debug logging
            console.log('updateChartsWithData called with:', data);
            console.log('Chart data received:', data);
            
            // Show loading state
            showChartsLoading();
            
            // Update chart title based on period
            updateChartTitle(data.period);
            
            // Update Monthly Chart with smooth transition
            const monthlyChartElement = document.getElementById('monthlyChart');
            console.log('Monthly chart element found:', !!monthlyChartElement);
            if (monthlyChartElement) {
                if (monthlyChartInstance) {
                    monthlyChartInstance.destroy();
                }
                
                const months = data.months || [];
                const pubData = Object.values(data.monthlyCounts?.Publication || {});
                const citData = Object.values(data.monthlyCounts?.Citation || {});
                const typeFilter = data.type;
                
                console.log('Chart data processed:', {
                    months: months,
                    pubData: pubData,
                    citData: citData,
                    typeFilter: typeFilter
                });
                
                // Calculate total values to determine which dataset has higher counts
                const pubTotal = pubData.reduce((sum, val) => sum + val, 0);
                const citTotal = citData.reduce((sum, val) => sum + val, 0);
                
                console.log('Totals:', { pubTotal, citTotal });
                
                let datasets = [];
                if (!typeFilter || typeFilter === 'Publication' || typeFilter === 'Publications') {
                    datasets.push({
                        label: 'Publications',
                        data: pubData,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        order: pubTotal > citTotal ? 1 : 0,
                    });
                }
                if (!typeFilter || typeFilter === 'Citation' || typeFilter === 'Citations') {
                    datasets.push({
                        label: 'Citations',
                        data: citData,
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        order: citTotal > pubTotal ? 1 : 0,
                    });
                }
                
                console.log('Datasets created:', datasets);
                
                monthlyChartInstance = new Chart(monthlyChartElement.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1000,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: { display: true },
                            tooltip: { 
                                enabled: true,
                                callbacks: {
                                    title: function(context) {
                                        const label = context[0].label;
                                        const dataIndex = context[0].dataIndex;
                                        
                                        // For date ranges (1-5, 6-10, etc.), show the actual dates
                                        if (label && label.includes('-') && label.length <= 5 && data.dateDetails && data.dateDetails[label]) {
                                            const dates = data.dateDetails[label];
                                            if (dates && dates.length > 0) {
                                                const dateStrings = dates.map(item => {
                                                    const date = new Date(item.date);
                                                    return date.toLocaleDateString('en-US', { 
                                                        weekday: 'short',
                                                        month: 'short',
                                                        day: 'numeric'
                                                    });
                                                });
                                                return dateStrings.join(', ');
                                            }
                                        }
                                        
                                        // For week periods, convert YYYY-MM-DD to day name
                                        if (label && label.includes('-') && label.split('-').length === 3 && data.period && (data.period === 'week' || data.period === 'This Week')) {
                                            const date = new Date(label);
                                            return date.toLocaleDateString('en-US', { 
                                                weekday: 'long',
                                                month: 'short',
                                                day: 'numeric'
                                            });
                                        }
                                        
                                        // For quarter/year periods, convert YYYY-MM to month name
                                        if (label && label.includes('-') && label.split('-').length === 2 && (data.period === 'quarter' || data.period === 'This Quarter' || data.period === 'year' || data.period === 'This Year' || data.period === 'all' || data.period === 'All Time' || !data.period || data.period === 'null')) {
                                            const date = new Date(label + '-01');
                                            return date.toLocaleDateString('en-US', { 
                                                month: 'long',
                                                year: 'numeric'
                                            });
                                        }
                                        
                                        // For other periods, show the original label
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                ticks: { precision: 0 },
                                animation: {
                                    duration: 1000
                                }
                            },
                            x: { 
                                grid: { display: false },
                                animation: {
                                    duration: 1000
                                }
                            }
                        }
                    }
                });
            }
            
            // Update Status Donut Chart with smooth transition
            const statusChartElement = document.getElementById('statusChart');
            console.log('Status chart element found:', !!statusChartElement);
            if (statusChartElement) {
                if (statusChartInstance) {
                    statusChartInstance.destroy();
                }
                
                const statusCounts = [data.statusCounts.pending, data.statusCounts.endorsed, data.statusCounts.rejected];
                statusChartInstance = new Chart(statusChartElement.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending', 'Endorsed', 'Rejected'],
                        datasets: [{
                            data: statusCounts,
                            backgroundColor: [
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(34, 197, 94, 0.8)',
                                'rgba(239, 68, 68, 0.8)'
                            ],
                            borderColor: [
                                'rgba(245, 158, 11, 1)',
                                'rgba(34, 197, 94, 1)',
                                'rgba(239, 68, 68, 1)'
                            ],
                            borderWidth: 2,
                            hoverBackgroundColor: [
                                'rgba(245, 158, 11, 1)',
                                'rgba(34, 197, 94, 1)',
                                'rgba(239, 68, 68, 1)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1000,
                            animateRotate: true,
                            animateScale: true
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: true }
                        }
                    }
                });
            }
            
            // Update status legend with smooth transitions
            updateStatusLegend(data.statusCounts);
            
            // Hide loading state after a short delay to ensure smooth transition
            setTimeout(() => {
                hideChartsLoading();
            }, 300);
        }

        function getCurrentFilters() {
            const url = new URL(window.location.href);
            const filters = {
                type: url.searchParams.get('type'),
                period: url.searchParams.get('period'),
                status: url.searchParams.get('status'),
                search: url.searchParams.get('search')
            };
            
            // Remove null/undefined values to prevent sending 'null' strings
            Object.keys(filters).forEach(key => {
                if (filters[key] === null || filters[key] === undefined || filters[key] === 'null') {
                    delete filters[key];
                }
            });
            
            return filters;
        }

        function testCharts() {
            console.log('Testing charts manually...');
            fetchAndUpdateCharts(getCurrentFilters());
        }

                function fetchAndUpdateCharts(filters) {
            console.log('fetchAndUpdateCharts called with filters:', filters);
            
            // Check if we're on the admin dashboard page
            const monthlyChartElement = document.querySelector('#monthlyChart');
            const statusChartElement = document.querySelector('#statusChart');
            
            console.log('Chart elements found:', {
                monthlyChart: !!monthlyChartElement,
                statusChart: !!statusChartElement
            });
            
            if (!monthlyChartElement) {
                console.log('Chart elements not found, skipping chart fetch');
                return;
            }
            
            console.log('Fetching charts with filters:', filters);
            
            // Show loading state
            showChartsLoading();
            
            const params = new URLSearchParams(filters).toString();
            console.log('Fetching from URL:', `/admin/dashboard/data?${params}`);
            
            fetch(`/admin/dashboard/data?${params}`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    console.log('Raw chart data received:', data);
                    
                    if (!data.months || !data.monthlyCounts || !data.statusCounts) {
                        console.error('Chart data missing keys:', data);
                        hideChartsLoading();
                        return;
                    }
                    
                    console.log('Chart data validation passed, updating charts...');
                    console.log('Total records in filtered data:', data.totalRecords);
                    updateChartsWithData({
                        months: data.months,
                        monthlyCounts: data.monthlyCounts,
                        statusCounts: data.statusCounts,
                        type: data.type || filters.type,
                        period: data.period || filters.period,
                        dateDetails: data.dateDetails || {}
                    });
                })
                .catch(err => {
                    console.error('Error fetching chart data:', err);
                    hideChartsLoading();
                    // Don't show error to user, just log it
                });
}

// Initialize real-time updates when page loads
document.addEventListener('DOMContentLoaded', function() {
            // Set initial chart title based on current URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const period = urlParams.get('period');
            if (period) {
                updateChartTitle(period);
            }
            
            // Wait a bit to ensure the page is fully loaded and authenticated
            setTimeout(() => {
                try {
    // Temporarily disable SSE to prevent database errors
    // initializeRealTimeUpdates();
    
    // Add data attributes to period stat cards for easy updating
    document.querySelectorAll('[href*="period="]').forEach(link => {
        const period = link.href.match(/period=([^&]+)/)?.[1];
        if (period) {
            const statElement = link.querySelector('.text-lg');
            if (statElement) {
                statElement.setAttribute('data-period', period);
            }
        }
    });
    
    // Add class to activity log container
                    const activityLogContainer = document.querySelector('.bg-white.border.border-gray-200.rounded-lg.shadow.mb-4 ul');
    if (activityLogContainer) {
        activityLogContainer.classList.add('activity-log-list');
    }
                    
    // Initial fetch for charts on first non-Turbo load
    if (!window.Turbo) {
        fetchAndUpdateCharts(getCurrentFilters());
    } else {
        // If Turbo is enabled, hide loading state after a delay if no turbo:load event fires
        setTimeout(() => {
            hideChartsLoading();
        }, 2000);
    }
                } catch (error) {
                    console.error('Error initializing dashboard:', error);
                }
            }, 500); // Wait 500ms to ensure everything is loaded
});

// Clean up connection when page unloads
window.addEventListener('beforeunload', function() {
    if (eventSource) {
        eventSource.close();
    }
});

        // Turbo lifecycle integration for charts and SSE
        document.addEventListener('turbo:load', () => {
            console.log('Turbo load event fired - fetching charts...');
            
            // Set initial chart title based on current URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const period = urlParams.get('period');
            if (period) {
                updateChartTitle(period);
            }
            
            // Temporarily disable SSE to prevent database errors
            // initializeRealTimeUpdates();
            // Defer to allow layout to settle
            setTimeout(() => fetchAndUpdateCharts(getCurrentFilters()), 0);
            
            // Fix scrollbar layout shift
            fixScrollbarLayout();
        });

        // Add immediate chart update when filters change (for better responsiveness)
        let chartUpdateTimeout;
        document.addEventListener('click', (e) => {
            // Check if clicking on filter links
            if (e.target.matches('a[href*="dashboard"]') || e.target.closest('a[href*="dashboard"]')) {
                const link = e.target.matches('a[href*="dashboard"]') ? e.target : e.target.closest('a[href*="dashboard"]');
                const url = new URL(link.href);
                
                // Clear any pending chart update
                if (chartUpdateTimeout) {
                    clearTimeout(chartUpdateTimeout);
                }
                
                // Update charts immediately with new filters
                const newFilters = {
                    type: url.searchParams.get('type'),
                    period: url.searchParams.get('period'),
                    status: url.searchParams.get('status'),
                    search: url.searchParams.get('search')
                };
                
                // Remove null/undefined values to prevent sending 'null' strings
                Object.keys(newFilters).forEach(key => {
                    if (newFilters[key] === null || newFilters[key] === undefined || newFilters[key] === 'null') {
                        delete newFilters[key];
                    }
                });
                
                // Debounce chart update to prevent too many requests
                chartUpdateTimeout = setTimeout(() => {
                    fetchAndUpdateCharts(newFilters);
                }, 100);
            }
        });

        document.addEventListener('turbo:before-cache', () => {
            // Destroy charts before Turbo caches the page to avoid stale canvas state
            if (monthlyChartInstance) {
                monthlyChartInstance.destroy();
                monthlyChartInstance = null;
            }
            if (statusChartInstance) {
                statusChartInstance.destroy();
                statusChartInstance = null;
            }
            if (eventSource) {
                try { eventSource.close(); } catch(e) {}
                eventSource = null;
            }
        });
        
        // Function to fix scrollbar layout shift
        function fixScrollbarLayout() {
            const scrollArea = document.querySelector('.table-scroll-area');
            if (scrollArea) {
                // Force scrollbar to always be present
                scrollArea.style.overflowY = 'scroll';
                
                // Add padding to account for scrollbar width
                const scrollbarWidth = scrollArea.offsetWidth - scrollArea.clientWidth;
                if (scrollbarWidth > 0) {
                    scrollArea.style.paddingRight = scrollbarWidth + 'px';
                }
                
                // Ensure minimum height to always show scrollbar
                const content = scrollArea.querySelector('.table-content');
                if (content && content.scrollHeight <= scrollArea.clientHeight) {
                    content.style.minHeight = (scrollArea.clientHeight + 1) + 'px';
                }
            }
        }
        
        // Call on page load
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(fixScrollbarLayout, 100);
        });
        
        // Call when filters change
        document.addEventListener('click', (e) => {
            if (e.target.matches('a[href*="dashboard"]') || e.target.closest('a[href*="dashboard"]')) {
                setTimeout(fixScrollbarLayout, 100);
            }
        });
    </script>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-5xl max-h-[90vh] bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200 flex flex-col">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 bg-maroon-800 text-white flex-shrink-0">
                    <h2 class="text-lg font-bold">Request Review</h2>
                    <button onclick="closeReviewModal()" class="text-white/80 hover:text-white text-xl font-bold transition-colors">&times;</button>
                </div>
                
                <!-- Loading State -->
                <div id="modalLoading" class="p-8 text-center flex-shrink-0">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-maroon-700 mx-auto"></div>
                    <p class="mt-3 text-gray-600 font-medium">Loading request details...</p>
                </div>
                
                <!-- Content -->
                <div id="modalContent" class="hidden flex-1 overflow-y-auto">
                    <div class="p-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Left Column -->
                        <div class="space-y-3">
                            <!-- Request Info -->
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                                <h3 class="text-base font-semibold text-maroon-800 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Request Information
                                </h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600 font-medium">Request Code:</span>
                                        <div class="font-semibold text-gray-900 mt-1" id="modalRequestCode">-</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 font-medium">Type:</span>
                                        <div class="font-semibold text-gray-900 mt-1" id="modalType">-</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 font-medium">Status:</span>
                                        <div id="modalStatus" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-1">-</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 font-medium">Submitted:</span>
                                        <div class="font-semibold text-gray-900 mt-1" id="modalDate">-</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Applicant Info -->
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                                <h3 class="text-base font-semibold text-maroon-800 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Applicant Information
                                </h3>
                                <div class="space-y-3 text-sm">
                                    <div>
                                        <span class="text-gray-600 font-medium">Name:</span>
                                        <div class="font-semibold text-gray-900 mt-1" id="modalUserName">-</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 font-medium">Email:</span>
                                        <div class="font-semibold text-gray-900 mt-1" id="modalUserEmail">-</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Admin Comment -->
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                                <label class="block text-maroon-800 font-semibold mb-2 flex items-center gap-2" for="adminComment">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Admin Comment
                                </label>
                                <textarea id="adminComment" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all" rows="2" placeholder="Optional note to include with status update..."></textarea>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="space-y-3">
                            <!-- Files Section -->
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                                <h3 class="text-base font-semibold text-maroon-800 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Files
                                </h3>
                                <div id="modalFiles" class="space-y-2"></div>
                                <div class="mt-4">
                                    <button id="downloadZipBtn" class="inline-flex items-center gap-2 px-4 py-2 bg-maroon-700 text-white rounded-lg hover:bg-maroon-800 hover:shadow-md transition-all duration-300 text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Download ZIP
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Signatories -->
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                                <h3 class="text-base font-semibold text-maroon-800 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Signatories
                                </h3>
                                <div id="modalFormData" class="space-y-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Actions -->
                <div class="flex justify-between items-center p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
                    <div class="text-sm text-gray-600 font-medium">Review all information before making a decision.</div>
                    <div class="flex items-center gap-3">
                        <button onclick="closeReviewModal()" class="px-4 py-2 text-gray-700 hover:text-gray-900 font-medium transition-colors">Close</button>
                        <button id="rejectBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 hover:shadow-md transition-all duration-300 font-medium">Reject</button>
                        <button id="endorseBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 hover:shadow-md transition-all duration-300 font-medium">Endorse</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
        // Review modal functions
function openReviewModal(requestId) {
    if (!requestId) {
        console.error('No request ID provided');
        return;
    }
    window.__currentReviewRequestId = requestId;
    document.getElementById('reviewModal').classList.remove('hidden');
    document.getElementById('modalLoading').classList.remove('hidden');
    document.getElementById('modalContent').classList.add('hidden');
            
    fetch(`/admin/requests/${requestId}/data`)
        .then(response => {
            if (!response.ok) throw new Error('Failed to load request data');
            return response.json();
        })
        .then(data => {
            populateModal(data);
            const zipBtn = document.getElementById('downloadZipBtn');
            if (zipBtn) zipBtn.onclick = () => { window.location.href = `/admin/requests/${requestId}/download-zip`; };
            const endorseBtn = document.getElementById('endorseBtn');
            const rejectBtn = document.getElementById('rejectBtn');
            if (endorseBtn) endorseBtn.onclick = () => submitStatusUpdate(requestId, 'endorsed');
            if (rejectBtn) rejectBtn.onclick = () => submitStatusUpdate(requestId, 'rejected');
            updateActionButtonsState(data.status);
        })
        .catch(error => {
            console.error('Error loading request data:', error);
                    alert('Failed to load request data. Please try again.');
        })
        .finally(() => {
            document.getElementById('modalLoading').classList.add('hidden');
            document.getElementById('modalContent').classList.remove('hidden');
        });
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
    // Reset modal content
    document.getElementById('modalRequestCode').textContent = '-';
    document.getElementById('modalType').textContent = '-';
    document.getElementById('modalStatus').textContent = '-';
    document.getElementById('modalDate').textContent = '-';
    document.getElementById('modalUserName').textContent = '-';
    document.getElementById('modalUserEmail').textContent = '-';
    document.getElementById('modalFormData').innerHTML = '';
    document.getElementById('modalFiles').innerHTML = '';
}

function populateModal(data) {
    // Populate basic info
    document.getElementById('modalRequestCode').textContent = data.request_code || 'N/A';
    document.getElementById('modalType').textContent = data.type || 'N/A';
    document.getElementById('modalDate').textContent = formatDate(data.requested_at);
    document.getElementById('modalUserName').textContent = data.user?.name || 'N/A';
    document.getElementById('modalUserEmail').textContent = data.user?.email || 'N/A';
            
    // Status
    const statusElement = document.getElementById('modalStatus');
    statusElement.textContent = data.status || 'N/A';
            statusElement.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium';
    if (data.status === 'pending') {
                statusElement.classList.add('bg-yellow-100', 'text-yellow-800');
    } else if (data.status === 'endorsed') {
                statusElement.classList.add('bg-green-100', 'text-green-800');
    } else if (data.status === 'rejected') {
                statusElement.classList.add('bg-red-100', 'text-red-800');
    }
            
    // Signatories
    const formDataContainer = document.getElementById('modalFormData');
    formDataContainer.innerHTML = '';
    if (data.signatories && data.signatories.length > 0) {
        data.signatories.forEach(signatory => {
            const fieldDiv = document.createElement('div');
                    fieldDiv.className = 'text-sm';
                    const roleLabel = signatory.role ? `<div class="text-gray-600 text-xs">${signatory.role}</div>` : '';
                    fieldDiv.innerHTML = `${roleLabel}<div class="font-medium text-gray-900">${signatory.name}</div>`;
            formDataContainer.appendChild(fieldDiv);
        });
    } else {
                formDataContainer.innerHTML = '<div class="text-gray-500 text-sm">No signatories found</div>';
    }
            
    // Files
    const filesContainer = document.getElementById('modalFiles');
    filesContainer.innerHTML = '';
    if (data.files && data.files.length > 0) {
        data.files.forEach(file => {
            const fileDiv = document.createElement('div');
                    fileDiv.className = 'flex items-center justify-between bg-white rounded border border-gray-200 p-3';
            const type = file.type === 'pdf' ? 'pdf' : 'docx';
            const key = file.key;
            const serveUrl = `/admin/requests/${data.id}/serve?type=${type}&key=${encodeURIComponent(key)}`;
            fileDiv.innerHTML = `
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-900">${file.name}</span>
                            <span class="text-xs text-gray-500">(${file.size})</span>
                </div>
                        <div class="flex gap-2">
                            <a href="${serveUrl}" target="_blank" class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors">View</a>
                            <a href="${serveUrl}" download class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors">Download</a>
                </div>
            `;
            filesContainer.appendChild(fileDiv);
        });
    } else {
                filesContainer.innerHTML = '<div class="text-gray-500 text-sm">No files uploaded for this request</div>';
    }
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function submitStatusUpdate(requestId, newStatus) {
    const adminComment = document.getElementById('adminComment')?.value || '';
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
    fetch(`/admin/requests/${requestId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ status: newStatus, admin_comment: adminComment })
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to update status');
        return res.text();
    })
    .then(() => {
        closeReviewModal();
        window.location.reload();
    })
    .catch(err => {
        console.error('Status update error:', err);
        alert('Failed to update status. Please try again.');
    });
}

function updateActionButtonsState(status) {
    const endorseBtn = document.getElementById('endorseBtn');
    const rejectBtn = document.getElementById('rejectBtn');
            
    [endorseBtn, rejectBtn].forEach(btn => {
        if (!btn) return;
        btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
    });
            
    if (status === 'endorsed' && endorseBtn) {
        endorseBtn.disabled = true;
                endorseBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
    if (status === 'rejected' && rejectBtn) {
        rejectBtn.disabled = true;
                rejectBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
}
</script>
</x-app-layout>

