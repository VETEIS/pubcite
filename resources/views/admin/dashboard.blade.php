<x-app-layout>
    <!-- Hidden notification divs for global notification system -->
    @if(session('success'))
        <div id="success-notification" class="hidden">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div id="error-notification" class="hidden">{{ session('error') }}</div>
    @endif

    <style>
        body { 
            overflow-x: hidden; 
            min-height: 100vh;
        }
        html { 
            overflow-x: hidden; 
            height: 100%;
            /* Hide browser scrollbar to prevent layout shifts */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }
        html::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
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
        
        /* Prevent FOUC - hide content until ready */
        .fouc-prevent {
            visibility: hidden;
        }
        
        .fouc-ready {
            visibility: visible;
        }
        
        /* Prevent table layout shifts */
        .requests-table tbody tr {
            min-height: 60px;
        }
        
        .requests-table td {
            vertical-align: middle;
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
        
        /* Force scrollbar to always be visible to prevent layout shifts */
        .force-scrollbar {
            scrollbar-gutter: stable;
            overflow-y: scroll !important;
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
    <div x-data="{ 
        // loading: false, // loading.js
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
    }" class="bg-gray-50 flex admin-dashboard-container" style="scrollbar-gutter: stable;">
        
        <!-- Hidden notification divs for global notification system -->

        <!-- Error message overlay -->
        <div x-show="errorMessage" x-transition class="fixed top-20 right-4 z-[60] bg-red-600 text-white px-4 py-2 rounded shadow" style="display:none;">
            <span x-text="errorMessage"></span>
        </div>
        <!-- Loading overlay - Now handled by simple loading system -->

        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 ml-60 overflow-y-auto">
            <!-- Content Area -->
            <main class="p-4 rounded-bl-lg flex flex-col main-content fouc-prevent min-h-full" id="mainContent">
                <!-- Dashboard Header with Modern Compact Filters -->
                <div class="relative flex items-center justify-between mb-4 flex-shrink-0">
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
                        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 h-8 cursor-default">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-xs font-medium text-gray-500">
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
                        
                        <!-- Logs Dropdown -->
                        <div class="relative" x-data="{ showLogs: false }">
                            <button @click="showLogs = !showLogs" class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors flex items-center justify-center group relative">
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-800 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                            
                            <!-- Logs Dropdown -->
                            <div x-show="showLogs" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 @click.away="showLogs = false"
                                 class="absolute right-0 top-12 w-[650px] bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-[600px] overflow-hidden flex flex-col">
                                
                                <!-- Header -->
                                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex-shrink-0">
                                    <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Activity Log
                                    </h3>
                                </div>
                                
                                <!-- Activity Logs List -->
                                <div class="overflow-y-auto flex-1 p-4">
                                    @if($activityLogs->isEmpty())
                                        <div class="text-center text-gray-500 py-8">
                                            <p class="text-sm">No activity logs yet</p>
                                        </div>
                                    @else
                                        <ul class="space-y-2">
                                            @foreach($activityLogs as $log)
                                                <li class="grid grid-cols-[auto_1fr_auto_16px_100px] items-center gap-3 bg-gray-50 hover:bg-white hover:shadow-md rounded-lg p-2 transition-all duration-300 cursor-pointer group relative">
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
                                    @endif
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
                                    <h3 class="text-sm font-semibold text-gray-900">Search Requests</h3>
                                     </div>
                                
                                <!-- Search Form -->
                                <div class="p-4">
                                    <form method="GET" action="{{ route('admin.dashboard') }}" class="space-y-4">
                                        <!-- Search Input -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Term</label>
                                            <input type="text" 
                                                   name="search" 
                                                   value="{{ request('search') }}"
                                                   placeholder="Search by request code, user name, or email..."
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500">
                                     </div>
                                        
                                        <!-- Filter Options -->
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500">
                                                    <option value="">All Types</option>
                                                    <option value="Publication" {{ request('type') == 'Publication' ? 'selected' : '' }}>Publication</option>
                                                    <option value="Citation" {{ request('type') == 'Citation' ? 'selected' : '' }}>Citation</option>
                                                </select>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500">
                                                    <option value="">All Status</option>
                                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="endorsed" {{ request('status') == 'endorsed' ? 'selected' : '' }}>Endorsed</option>
                                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
                                                <a href="{{ route('admin.dashboard') }}" 
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

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 flex-shrink-0">
                    <!-- Charts Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow overflow-hidden min-h-[240px] flex flex-col">
                        <!-- Card Header -->
                        <div class="px-4 py-2 border-b border-gray-200 bg-gray-50">
                            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span id="chartTitle">Request Stats (Last 12 Months)</span>
                            </h2>
                        </div>
                        
                        <!-- Card Content -->
                        <div class="flex flex-col md:flex-row items-stretch justify-center overflow-y-auto flex-1 gap-2">
                            <!-- Request Stats (Line Chart) -->
                            <div class="flex-[3_3_0%] flex flex-col items-center justify-center min-w-0 overflow-hidden relative pl-3 pr-0 py-1">
                            <div class="w-full h-40 flex items-center justify-center relative overflow-hidden">
                                <!-- Loading Overlay for Line Chart -->
                                <div id="lineChartLoading" class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center z-50 transition-opacity duration-300" style="opacity: 1;">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-maroon-600"></div>
                                        <p class="text-xs text-gray-600 font-medium">Loading...</p>
                                    </div>
                                </div>
                                <canvas id="monthlyChart" class="w-full h-40 max-h-[160px] transition-opacity duration-500 opacity-0" style="max-height:160px;"></canvas>
                            </div>
                        </div>
                                                 <!-- Status Breakdown (Donut Chart + Legend) -->
                         <div class="flex-[1_1_0%] flex flex-col items-center justify-center min-w-0 overflow-hidden border-t md:border-t-0 md:border-l border-gray-200 px-3 relative">
                             <div class="w-full max-w-xs mx-auto h-52 flex flex-col items-center justify-center relative overflow-hidden">
                                 <!-- Loading Overlay for Pie Chart -->
                                 <div id="pieChartLoading" class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center z-50 transition-opacity duration-300" style="opacity: 1;">
                                     <div class="flex flex-col items-center gap-2">
                                         <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-maroon-600"></div>
                                         <p class="text-xs text-gray-600 font-medium">Loading...</p>
                                     </div>
                                 </div>
                                 <canvas id="statusChart" class="w-24 h-24 max-w-[96px] max-h-[96px] transition-opacity duration-500 opacity-0" style="max-width:96px;max-height:96px;"></canvas>
                                 <div id="statusLegend" class="mt-1 w-full px-2 transition-all duration-500">
                                    <table class="w-full text-xs min-w-0">
                                        <thead>
                                            <tr class="border-b border-gray-200">
                                                <th class="text-left py-1 font-semibold text-gray-700">Status</th>
                                                <th class="text-center py-1 font-semibold text-gray-700">Count</th>
                                                <th class="text-right py-1 font-semibold text-gray-700">%</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $statusLabels = ['PEND', 'END'];
                                                $statusColors = ['bg-yellow-500', 'bg-green-500'];
                                                $statusKeys = ['pending', 'endorsed'];
                                                $total = ($statusCounts['pending'] ?? 0) + ($statusCounts['endorsed'] ?? 0);
                                            @endphp
                                            @foreach($statusLabels as $i => $label)
                                                @php
                                                    $count = $statusCounts[$statusKeys[$i]] ?? 0;
                                                    $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
                                                @endphp
                                                <tr class="hover:bg-gray-50 transition-all duration-300">
                                                    <td class="py-1 flex items-center truncate">
                                                        <span class="inline-block w-2 h-2 rounded-full {{ $statusColors[$i] }} mr-1 flex-shrink-0"></span>
                                                        <span class="font-semibold truncate">{{ $label }}</span>
                                                    </td>
                                                    <td class="py-1 text-center font-semibold">{{ $count }}</td>
                                                    <td class="py-1 text-right text-gray-500">
                                                        {{ $percentage }}%
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
                    
                    <!-- College Requests Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden min-h-[240px] flex flex-col">
                        <!-- Card Header -->
                        <div class="px-4 py-2 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>Requests by College</span>
                            </h2>
                            <div id="collegeTotalCount" class="text-xs font-semibold text-maroon-600 bg-maroon-100 px-2.5 py-0.5 rounded-full">
                                <span id="collegeTotalValue">-</span> total
                            </div>
                        </div>
                        
                        <!-- Card Content -->
                        <div class="flex-1 p-3 flex flex-col relative overflow-hidden">
                            <!-- Loading Overlay -->
                            <div id="collegeChartLoading" class="absolute inset-0 bg-white bg-opacity-95 flex items-center justify-center z-50 transition-opacity duration-300" style="opacity: 1;">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-maroon-600"></div>
                                    <p class="text-xs text-gray-600 font-medium">Loading...</p>
                                </div>
                            </div>
                            <div class="flex-1 flex items-center justify-center min-h-0">
                                <canvas id="collegeChart" class="w-full h-full max-h-[220px] transition-opacity duration-500 opacity-0"></canvas>
                            </div>
                            <!-- Empty State (shown when no data) -->
                            <div id="collegeChartEmpty" class="hidden flex-1 flex items-center justify-center">
                                <div class="text-center py-6">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-600">No college data available</p>
                                    <p class="text-xs text-gray-400 mt-1">Requests will appear here once submitted</p>
                                </div>
                            </div>
                            </div>
                        </div>
                                </div>

                <!-- Requests Table Container -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col min-h-full">
                    <!-- Table (Combined Header and Body) -->
                    <div class="flex-1">
                        @if($filteredRequests->isEmpty())
                            <!-- Empty State (Centered) -->
                            <div class="min-h-[400px] flex items-center justify-center">
                                <div class="flex flex-col items-center justify-center gap-3 text-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20v-6m0 0l-3 3m3-3l3 3M4 6h16M4 10h16M4 14h16"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">No requests yet</h4>
                                        <p class="text-gray-500">No requests have been submitted yet.</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full divide-y divide-gray-200">
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
                                                Signed
                                            </th>
                                            <th class="w-24 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($filteredRequests as $index => $request)
                                    <tr class="hover:bg-white hover:shadow-md transition-all duration-300 cursor-pointer group" data-request-id="{{ $request->id }}">
                                        <td class="w-32 px-6 py-3 text-sm text-gray-900 overflow-hidden text-left">
                                            <div class="truncate">{{ \Carbon\Carbon::parse($request->requested_at)->format('M d, Y H:i') }}</div>
                                        </td>
                                        <td class="w-32 px-6 py-3 overflow-hidden text-left">
                                            <div class="text-sm font-medium text-gray-900 truncate">{{ $request->request_code }}</div>
                                        </td>
                                        <td class="w-48 px-6 py-3 overflow-hidden text-left">
                                            <div class="min-w-0 flex-1 overflow-hidden">
                                                <div class="text-sm font-medium text-gray-900 truncate">{{ $request->user->name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500 truncate">{{ $request->user->email ?? 'No email' }}</div>
                                            </div>
                                        </td>
                                        <td class="w-24 px-6 py-3 overflow-hidden text-center" style="width: 96px; max-width: 96px; min-width: 96px;">
                                            <div class="w-full flex justify-center">
                                                <span class="inline-flex items-center px-1 py-0.5 rounded-full text-xs font-medium w-20 justify-center truncate overflow-hidden
                                                    {{ $request->type === 'Publication' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                    <span class="truncate">{{ $request->type }}</span>
                                                </span>
                                                </div>
                                        </td>
                                        <td class="w-28 px-6 py-3 overflow-hidden text-center">
                                            <div class="w-full flex justify-center">
                                                @php
                                                    $statusColor = match($request->status) {
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'endorsed' => 'bg-green-100 text-green-800',
                                                        'rejected' => 'bg-red-100 text-red-800',
                                                        default => 'bg-gray-100 text-gray-800'
                                                    };
                                                    $statusDot = match($request->status) {
                                                        'pending' => 'bg-yellow-400',
                                                        'endorsed' => 'bg-green-400',
                                                        'rejected' => 'bg-red-400',
                                                        default => 'bg-gray-400'
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }} w-20 justify-center">
                                                    <div class="w-2 h-2 {{ $statusDot }} rounded-full mr-2"></div>
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="w-24 px-6 py-3 text-center text-sm font-medium overflow-hidden">
                                            @php
                                                $request->loadCount('signatures');
                                                $progress = $request->getSignatureProgress();
                                                $progressParts = explode('/', $progress);
                                                $current = (int)$progressParts[0];
                                                $total = (int)$progressParts[1];
                                                $isComplete = $current === $total;
                                            @endphp
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="text-xs font-semibold {{ $isComplete ? 'text-green-600' : 'text-gray-600' }}">
                                                    {{ $progress }}
                                                </span>
                                                @if(!$isComplete)
                                                    <span class="text-xs text-gray-400">{{ $request->getWorkflowStageName() }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="w-24 px-6 py-3 text-center text-sm font-medium overflow-hidden">
                                            <div class="flex items-center justify-center gap-1 w-full">
                                                <!-- Review Button -->
                                                <button type="button" class="flex-1 inline-flex items-center justify-center gap-1 px-2 py-1 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 hover:shadow-md hover:scale-105 transition-all duration-300 text-xs font-medium group-hover:bg-blue-200" title="Review" onclick="openReviewModal({{ $request->id }})">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    Review
                                                </button>
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
                    <div class="bg-white px-4 py-2 border-t border-gray-200 flex items-center justify-between flex-shrink-0">
                        <div class="text-sm text-gray-700 min-w-0">
                            <span class="whitespace-nowrap">
                                Showing 
                                <span class="font-medium">{{ $requests->firstItem() ?? 0 }}</span> 
                                to 
                                <span class="font-medium">{{ $requests->lastItem() ?? 0 }}</span> 
                                of 
                                <span class="font-medium">{{ $requests->total() }}</span> 
                                results
                            </span>
                            </div>
                        <div class="flex items-center space-x-2 flex-shrink-0">
                            {{ $requests->links() }}
                            </div>
                        </div>
                    </div>


            </main>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

        // Chart instances are now global to prevent redeclaration errors
        // let monthlyChartInstance = null;
        // let statusChartInstance = null;
// Global variables - prevent redeclaration during Turbo navigation
if (typeof window.eventSource === 'undefined') {
    window.eventSource = null;
}

// Real-time SSE updates removed to align with finalized admin-only updates after workflow completion

// Removed complex updateDashboard function - now using simple reinitialization

// Removed complex updatePeriodStats function - now using simple reinitialization

// Removed complex updateActivityLogs function - now using simple reinitialization

// Removed createActivityLogItem function - now using simple reinitialization

// Removed getIconColor function - now using simple reinitialization

// Removed getIconSvg function - now using simple reinitialization

// Removed getLogDescription function - now using simple reinitialization

// Removed getShortName function - now using simple reinitialization

// Removed getTimeAgo function - now using simple reinitialization

// Simple reinitialization function for charts and table
function reinitializeChartsAndTable() {
    // Show loading state
    showChartsLoading();
    
    // Fetch fresh data and reinitialize everything
    fetch(window.location.href, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.months || !data.monthlyCounts || !data.statusCounts) {
            hideChartsLoading();
            return;
        }
        
        // Reinitialize charts with fresh data
        // Destroy existing charts
        if (window.monthlyChartInstance) {
            window.monthlyChartInstance.destroy();
            window.monthlyChartInstance = null;
        }
        if (window.statusChartInstance) {
            window.statusChartInstance.destroy();
            window.statusChartInstance = null;
        }
        if (window.collegeChartInstance) {
            window.collegeChartInstance.destroy();
            window.collegeChartInstance = null;
        }
        
        // Recreate charts with fresh data
        initializeCharts(data);
        
        // Reinitialize table with fresh data
        if (data.requests) {
            updateTableWithData(data.requests);
        }
        
        // Update stats if available
        if (data.stats) {
            updateStatsWithData(data.stats);
        }
        
        hideChartsLoading();
    })
    .catch(error => {
        hideChartsLoading();
    });
}

// Function to abbreviate college names
function abbreviateCollegeName(fullName) {
    const abbreviations = {
        'College of Information and Computing': 'CIC',
        'College of Engineering': 'COE',
        'College of Architecture': 'COA',
        'College of Arts and Letters': 'CAL',
        'College of Business Administration': 'CBA',
        'College of Education': 'COED',
        'College of Fine Arts': 'CFA',
        'College of Home Economics': 'CHE',
        'College of Human Kinetics': 'CHK',
        'College of Law': 'COL',
        'College of Mass Communication': 'CMC',
        'College of Music': 'COM',
        'College of Nursing': 'CON',
        'College of Public Administration': 'CPA',
        'College of Science': 'COS',
        'College of Social Work and Community Development': 'CSWCD',
        'College of Statistics': 'COSTAT',
        'School of Economics': 'SOE',
        'School of Labor and Industrial Relations': 'SOLAIR',
        'School of Library and Information Studies': 'SLIS',
        'UP College of Medicine': 'UPCM',
        'UP Open University': 'UPOU'
    };
    
    // Check for exact match first
    if (abbreviations[fullName]) {
        return abbreviations[fullName];
    }
    
    // Check for case-insensitive match
    const lowerFullName = fullName.toLowerCase();
    for (const [key, value] of Object.entries(abbreviations)) {
        if (key.toLowerCase() === lowerFullName) {
            return value;
        }
    }
    
    // If no match, generate abbreviation from first letters of significant words
    // Skip common words like "of", "and", "the"
    const skipWords = ['of', 'and', 'the', 'a', 'an', 'in', 'on', 'at', 'to', 'for'];
    const words = fullName.split(' ');
    const significantWords = words.filter(word => 
        word.length > 0 && !skipWords.includes(word.toLowerCase())
    );
    
    if (significantWords.length > 0) {
        // Take first letter of each significant word
        return significantWords.map(word => word.charAt(0).toUpperCase()).join('');
    }
    
    // Fallback: return first 3-4 letters if it's a short name
    return fullName.length <= 4 ? fullName : fullName.substring(0, 4).toUpperCase();
}

// Initialize charts with fresh data
function initializeCharts(data) {
    const months = data.months || [];
    const pubData = Object.values(data.monthlyCounts?.Publication || {});
    const citData = Object.values(data.monthlyCounts?.Citation || {});
    const typeFilter = data.type;
    
    // Initialize monthly chart
    const monthlyChartElement = document.getElementById('monthlyChart');
    if (monthlyChartElement) {
        // Create datasets based on available data and filter
        let datasets = [];
        
        // Show publications if no filter or filter matches
        if ((!typeFilter || typeFilter === 'Publication' || typeFilter === 'Publications') && pubData.length > 0) {
            datasets.push({
                label: 'Publications',
                data: pubData,
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            });
        }
        
        // Show citations if no filter or filter matches
        if ((!typeFilter || typeFilter === 'Citation' || typeFilter === 'Citations') && citData.length > 0) {
            datasets.push({
                label: 'Citations',
                data: citData,
                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            });
        }
        
        // Create the chart
        window.monthlyChartInstance = new Chart(monthlyChartElement.getContext('2d'), {
            type: 'line',
            data: {
                labels: months,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        animation: { duration: 1000 }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { display: false },
                        animation: { duration: 1000 }
                    }
                }
            }
        });
    }
    
    // Initialize status chart
    const statusChartElement = document.getElementById('statusChart');
    if (statusChartElement) {
        const pendingCount = data.statusCounts.pending || 0;
        const endorsedCount = data.statusCounts.endorsed || 0;
        const totalCount = pendingCount + endorsedCount;
        
        if (totalCount === 0) {
            // Show empty state
            window.statusChartInstance = new Chart(statusChartElement.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['No Data'],
                    datasets: [{
                        data: [1],
                        backgroundColor: ['rgba(156, 163, 175, 0.3)'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        } else {
            window.statusChartInstance = new Chart(statusChartElement.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Endorsed'],
                    datasets: [{
                        data: [pendingCount, endorsedCount],
                        backgroundColor: ['#eab308', '#10b981'], // Yellow for pending, green for endorsed
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    }
    
    // Update status legend
    updateStatusLegend(data.statusCounts);
    
    // Initialize college chart with academic rank breakdown
    const collegeChartElement = document.getElementById('collegeChart');
    const collegeChartEmpty = document.getElementById('collegeChartEmpty');
    const collegeTotalValue = document.getElementById('collegeTotalValue');
    
    if (collegeChartElement) {
        const collegeCounts = data.collegeCounts || {};
        const collegeRankBreakdown = data.collegeRankBreakdown || {};
        const colleges = Object.keys(collegeCounts);
        
        // Calculate total
        const total = Object.values(collegeCounts).reduce((sum, count) => sum + count, 0);
        if (collegeTotalValue) {
            collegeTotalValue.textContent = total;
        }
        
        // Limit to top 10 colleges for readability
        const topColleges = colleges.slice(0, 10);
        
        if (topColleges.length === 0) {
            // Show empty state
            if (collegeChartEmpty) {
                collegeChartEmpty.classList.remove('hidden');
            }
            collegeChartElement.style.opacity = '0';
            
            // Create a minimal chart to prevent errors
            window.collegeChartInstance = new Chart(collegeChartElement.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        } else {
            // Hide empty state
            if (collegeChartEmpty) {
                collegeChartEmpty.classList.add('hidden');
            }
            
            // Collect all unique academic ranks across all colleges
            const allRanks = new Set();
            topColleges.forEach(college => {
                const ranks = collegeRankBreakdown[college] || {};
                Object.keys(ranks).forEach(rank => allRanks.add(rank));
            });
            const uniqueRanks = Array.from(allRanks).sort();
            
            // Color palette for academic ranks (distinct colors)
            const rankColorPalette = [
                { bg: 'rgba(139, 21, 56, 0.9)', border: 'rgba(139, 21, 56, 1)' }, // Maroon
                { bg: 'rgba(220, 38, 38, 0.9)', border: 'rgba(220, 38, 38, 1)' }, // Red-600
                { bg: 'rgba(239, 68, 68, 0.9)', border: 'rgba(239, 68, 68, 1)' }, // Red-500
                { bg: 'rgba(99, 102, 241, 0.9)', border: 'rgba(99, 102, 241, 1)' }, // Indigo
                { bg: 'rgba(124, 58, 237, 0.9)', border: 'rgba(124, 58, 237, 1)' }, // Violet
                { bg: 'rgba(168, 85, 247, 0.9)', border: 'rgba(168, 85, 247, 1)' }, // Purple
                { bg: 'rgba(236, 72, 153, 0.9)', border: 'rgba(236, 72, 153, 1)' }, // Pink
                { bg: 'rgba(34, 197, 94, 0.9)', border: 'rgba(34, 197, 94, 1)' }, // Green
                { bg: 'rgba(59, 130, 246, 0.9)', border: 'rgba(59, 130, 246, 1)' }, // Blue
                { bg: 'rgba(245, 158, 11, 0.9)', border: 'rgba(245, 158, 11, 1)' }  // Amber
            ];
            
            // Create datasets for each academic rank
            const datasets = uniqueRanks.map((rank, rankIndex) => {
                const color = rankColorPalette[rankIndex % rankColorPalette.length];
                return {
                    label: rank,
                    data: topColleges.map(college => {
                        const ranks = collegeRankBreakdown[college] || {};
                        return ranks[rank] || 0;
                    }),
                    backgroundColor: color.bg,
                    borderColor: color.border,
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                };
            });
            
            const ctx = collegeChartElement.getContext('2d');
            
            window.collegeChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: topColleges.map(c => abbreviateCollegeName(c)),
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    layout: {
                        padding: {
                            left: 5,
                            right: 10,
                            top: 5,
                            bottom: 5
                        }
                    },
                    animation: {
                        duration: 1200,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: { 
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.85)',
                            padding: 12,
                            titleFont: {
                                size: 13,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            cornerRadius: 8,
                            displayColors: true,
                            callbacks: {
                                title: function(context) {
                                    // Show full college name in tooltip
                                    const index = context[0].dataIndex;
                                    return topColleges[index] || '';
                                },
                                label: function(context) {
                                    const rank = context.dataset.label;
                                    const count = context.parsed.x;
                                    const collegeIndex = context.dataIndex;
                                    const collegeTotal = collegeCounts[topColleges[collegeIndex]] || 0;
                                    const rankPercentage = collegeTotal > 0 ? ((count / collegeTotal) * 100).toFixed(1) : 0;
                                    return [
                                        `${rank}: ${count} request${count !== 1 ? 's' : ''}`,
                                        `${rankPercentage}% of ${abbreviateCollegeName(topColleges[collegeIndex])}`
                                    ];
                                },
                                footer: function(tooltipItems) {
                                    const collegeIndex = tooltipItems[0].dataIndex;
                                    const collegeTotal = collegeCounts[topColleges[collegeIndex]] || 0;
                                    return `Total: ${collegeTotal} request${collegeTotal !== 1 ? 's' : ''}`;
                                },
                                labelColor: function(context) {
                                    const rankIndex = uniqueRanks.indexOf(context.dataset.label);
                                    const color = rankColorPalette[rankIndex % rankColorPalette.length];
                                    return {
                                        borderColor: color.border,
                                        backgroundColor: color.bg
                                    };
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                font: {
                                    size: 11,
                                    weight: '500'
                                },
                                color: 'rgba(107, 114, 128, 0.8)',
                                padding: 8
                            }
                        },
                        y: {
                            stacked: true,
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    weight: '500'
                                },
                                color: 'rgba(107, 114, 128, 0.8)',
                                padding: 10
                            }
                        }
                    },
                    onHover: (event, activeElements) => {
                        collegeChartElement.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
                    }
                }
            });
        }
        
        // Hide loading overlay
        const collegeChartLoading = document.getElementById('collegeChartLoading');
        if (collegeChartLoading) {
            collegeChartLoading.style.opacity = '0';
            collegeChartLoading.style.pointerEvents = 'none';
        }
        
        // Show chart
        collegeChartElement.style.opacity = '1';
    }
}

// Helper function to update table with fresh data
function updateTableWithData(requests) {
    const tableBody = document.querySelector('tbody');
    if (!tableBody) return;
    
    // Clear existing table rows
    tableBody.innerHTML = '';
    
    // Add new rows (simplified - just reload the page content)
    // For a complete solution, we'd need to render the table rows here
    // But since we're doing simple reinitialization, we'll just reload
    window.location.reload();
}

// Helper function to update stats with fresh data
function updateStatsWithData(stats) {
    // Update period stats
    const type = '{{ request("type") }}';
    const periodStats = {
        'week': type === 'Citation' ? (stats.citation?.week || 0)
            : (type === 'Publication' ? (stats.publication?.week || 0)
            : ((stats.publication?.week || 0) + (stats.citation?.week || 0))),
        'month': type === 'Citation' ? (stats.citation?.month || 0)
            : (type === 'Publication' ? (stats.publication?.month || 0)
            : ((stats.publication?.month || 0) + (stats.citation?.month || 0))),
        'quarter': type === 'Citation' ? (stats.citation?.quarter || 0)
            : (type === 'Publication' ? (stats.publication?.quarter || 0)
            : ((stats.publication?.quarter || 0) + (stats.citation?.quarter || 0))),
    };
    
    // Update period stat cards
    Object.keys(periodStats).forEach(period => {
        const statElement = document.querySelector(`[data-period="${period}"]`);
        if (statElement) {
            statElement.textContent = periodStats[period];
        }
    });
}

        function showChartsLoading() {
            const lineChartLoading = document.getElementById('lineChartLoading');
            const pieChartLoading = document.getElementById('pieChartLoading');
            const collegeChartLoading = document.getElementById('collegeChartLoading');
            
            if (lineChartLoading) {
                lineChartLoading.style.opacity = '1';
                lineChartLoading.style.pointerEvents = 'auto';
            }
            if (pieChartLoading) {
                pieChartLoading.style.opacity = '1';
                pieChartLoading.style.pointerEvents = 'auto';
            }
            if (collegeChartLoading) {
                collegeChartLoading.style.opacity = '1';
                collegeChartLoading.style.pointerEvents = 'auto';
            }
            
            // Fade out charts
            const charts = document.querySelectorAll('#monthlyChart, #statusChart, #collegeChart');
            charts.forEach(chart => {
                chart.style.opacity = '0.3';
            });
        }

        function hideChartsLoading() {
            const lineChartLoading = document.getElementById('lineChartLoading');
            const pieChartLoading = document.getElementById('pieChartLoading');
            const collegeChartLoading = document.getElementById('collegeChartLoading');
            
            if (lineChartLoading) {
                lineChartLoading.style.opacity = '0';
                lineChartLoading.style.pointerEvents = 'none';
            }
            if (pieChartLoading) {
                pieChartLoading.style.opacity = '0';
                pieChartLoading.style.pointerEvents = 'none';
            }
            if (collegeChartLoading) {
                collegeChartLoading.style.opacity = '0';
                collegeChartLoading.style.pointerEvents = 'none';
            }
            
            // Fade in charts smoothly
            const charts = document.querySelectorAll('#monthlyChart, #statusChart, #collegeChart');
            charts.forEach(chart => {
                chart.style.opacity = '1';
            });
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
            
            const total = (statusCounts.pending || 0) + (statusCounts.endorsed || 0);
            
            // If no data, show empty state
            if (total === 0) {
                legendElement.innerHTML = `
                    <div class="text-center py-4 text-gray-500">
                        <div class="flex items-center justify-center mb-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-gray-400 mr-2"></span>
                            <span class="text-sm font-medium">No Data Available</span>
                        </div>
                        <p class="text-xs text-gray-400">No requests found for the selected period</p>
                    </div>
                `;
                return;
            }
            
            const statusLabels = ['PEND', 'END'];
            const statusColors = ['bg-yellow-500', 'bg-green-500'];
            const statusKeys = ['pending', 'endorsed'];
            
            legendElement.innerHTML = `
                <table class="w-full text-xs min-w-0">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-1 font-semibold text-gray-700 w-20">Status</th>
                            <th class="text-center py-1 font-semibold text-gray-700 w-12">Count</th>
                            <th class="text-right py-1 font-semibold text-gray-700 w-12">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${statusLabels.map((label, i) => {
                            const count = statusCounts[statusKeys[i]] || 0;
                            const rawPercentage = total > 0 ? (count / total) * 100 : 0;
                            const percentage = rawPercentage === 100 ? '100' : rawPercentage.toFixed(1);
                            
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
            // Show loading state
            showChartsLoading();
            
            // Update chart title based on period
            updateChartTitle(data.period);
            
            // Update Monthly Chart with smooth transition
            const monthlyChartElement = document.getElementById('monthlyChart');
            if (monthlyChartElement) {
                if (window.monthlyChartInstance) {
                    window.monthlyChartInstance.destroy();
                }
                
                const months = data.months || [];
                const pubData = Object.values(data.monthlyCounts?.Publication || {});
                const citData = Object.values(data.monthlyCounts?.Citation || {});
                const typeFilter = data.type;
                
                // Calculate total values to determine which dataset has higher counts
                const pubTotal = pubData.reduce((sum, val) => sum + val, 0);
                const citTotal = citData.reduce((sum, val) => sum + val, 0);
                const totalData = pubTotal + citTotal;
                
                // If no data at all, show grayed-out chart
                if (totalData === 0) {
                    window.monthlyChartInstance = new Chart(monthlyChartElement.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: months.length > 0 ? months : ['No Data'],
                            datasets: [{
                                label: 'No Data Available',
                                data: months.length > 0 ? new Array(months.length).fill(0) : [0],
                                backgroundColor: 'rgba(156, 163, 175, 0.3)',
                                borderColor: 'rgba(156, 163, 175, 0.6)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3,
                                pointRadius: 0,
                                pointHoverRadius: 0,
                                borderDash: [5, 5]
                            }]
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
                                        label: function(context) {
                                            return 'No data available';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: { 
                                    beginAtZero: true, 
                                    ticks: { precision: 0 },
                                    animation: { duration: 1000 }
                                },
                                x: { 
                                    grid: { display: false },
                                    animation: { duration: 1000 }
                                }
                            }
                        }
                    });
                } else {
                    // Create datasets based on available data and filter
                let datasets = [];
                    
                    // Show publications if no filter or filter matches
                    if ((!typeFilter || typeFilter === 'Publication' || typeFilter === 'Publications') && pubTotal > 0) {
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
                    
                    // Show citations if no filter or filter matches
                    if ((!typeFilter || typeFilter === 'Citation' || typeFilter === 'Citations') && citTotal > 0) {
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
                    
                    // If filtered data has no results, show grayed-out chart for that type
                    if (datasets.length === 0) {
                        const filteredType = typeFilter === 'Publication' || typeFilter === 'Publications' ? 'Publications' : 'Citations';
                        datasets.push({
                            label: `No ${filteredType} Data`,
                            data: months.length > 0 ? new Array(months.length).fill(0) : [0],
                            backgroundColor: 'rgba(156, 163, 175, 0.3)',
                            borderColor: 'rgba(156, 163, 175, 0.6)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 0,
                            borderDash: [5, 5]
                    });
                }
                
                window.monthlyChartInstance = new Chart(monthlyChartElement.getContext('2d'), {
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
            }
            
            // Update Status Donut Chart with smooth transition
            const statusChartElement = document.getElementById('statusChart');
            if (statusChartElement) {
                if (window.statusChartInstance) {
                    window.statusChartInstance.destroy();
                }
                
                const pendingCount = data.statusCounts.pending || 0;
                const endorsedCount = data.statusCounts.endorsed || 0;
                const totalCount = pendingCount + endorsedCount;
                
                // If no data, show grayed-out pie chart
                if (totalCount === 0) {
                    window.statusChartInstance = new Chart(statusChartElement.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: ['No Data'],
                            datasets: [{
                                data: [1], // Single segment to show the circle
                                backgroundColor: ['rgba(156, 163, 175, 0.3)'], // Gray with low opacity
                                borderColor: ['rgba(156, 163, 175, 0.6)'], // Gray border
                                borderWidth: 2,
                                hoverBackgroundColor: ['rgba(156, 163, 175, 0.4)']
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
                                tooltip: { 
                                    enabled: true,
                                    callbacks: {
                                        label: function(context) {
                                            return 'No data available';
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                window.statusChartInstance = new Chart(statusChartElement.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending', 'Endorsed'],
                        datasets: [{
                            data: [pendingCount, endorsedCount],
                            backgroundColor: [
                                'rgba(234, 179, 8, 0.8)', // Yellow for pending
                                'rgba(16, 185, 129, 0.8)' // Green for endorsed
                            ],
                            borderColor: [
                                'rgba(234, 179, 8, 1)',
                                'rgba(16, 185, 129, 1)'
                            ],
                            borderWidth: 2,
                            hoverBackgroundColor: [
                                'rgba(234, 179, 8, 1)',
                                'rgba(16, 185, 129, 1)'
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
            }
            
            // Update status legend with smooth transitions
            updateStatusLegend(data.statusCounts);
            
            // Update College Chart with smooth transition (stacked by academic rank)
            const collegeChartElement = document.getElementById('collegeChart');
            const collegeChartEmpty = document.getElementById('collegeChartEmpty');
            const collegeTotalValue = document.getElementById('collegeTotalValue');
            
            if (collegeChartElement) {
                if (window.collegeChartInstance) {
                    window.collegeChartInstance.destroy();
                }
                
                const collegeCounts = data.collegeCounts || {};
                const collegeRankBreakdown = data.collegeRankBreakdown || {};
                const colleges = Object.keys(collegeCounts);
                
                // Calculate total
                const total = Object.values(collegeCounts).reduce((sum, count) => sum + count, 0);
                if (collegeTotalValue) {
                    collegeTotalValue.textContent = total;
                }
                
                // Limit to top 10 colleges for readability
                const topColleges = colleges.slice(0, 10);
                
                if (topColleges.length === 0) {
                    // Show empty state
                    if (collegeChartEmpty) {
                        collegeChartEmpty.classList.remove('hidden');
                    }
                    collegeChartElement.style.opacity = '0';
                    
                    // Create a minimal chart to prevent errors
                    window.collegeChartInstance = new Chart(collegeChartElement.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: [],
                            datasets: []
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            }
                        }
                    });
                } else {
                    // Hide empty state
                    if (collegeChartEmpty) {
                        collegeChartEmpty.classList.add('hidden');
                    }
                    
                    // Collect all unique academic ranks across all colleges
                    const allRanks = new Set();
                    topColleges.forEach(college => {
                        const ranks = collegeRankBreakdown[college] || {};
                        Object.keys(ranks).forEach(rank => allRanks.add(rank));
                    });
                    const uniqueRanks = Array.from(allRanks).sort();
                    
                    // Color palette for academic ranks (distinct colors)
                    const rankColorPalette = [
                        { bg: 'rgba(139, 21, 56, 0.9)', border: 'rgba(139, 21, 56, 1)' }, // Maroon
                        { bg: 'rgba(220, 38, 38, 0.9)', border: 'rgba(220, 38, 38, 1)' }, // Red-600
                        { bg: 'rgba(239, 68, 68, 0.9)', border: 'rgba(239, 68, 68, 1)' }, // Red-500
                        { bg: 'rgba(99, 102, 241, 0.9)', border: 'rgba(99, 102, 241, 1)' }, // Indigo
                        { bg: 'rgba(124, 58, 237, 0.9)', border: 'rgba(124, 58, 237, 1)' }, // Violet
                        { bg: 'rgba(168, 85, 247, 0.9)', border: 'rgba(168, 85, 247, 1)' }, // Purple
                        { bg: 'rgba(236, 72, 153, 0.9)', border: 'rgba(236, 72, 153, 1)' }, // Pink
                        { bg: 'rgba(34, 197, 94, 0.9)', border: 'rgba(34, 197, 94, 1)' }, // Green
                        { bg: 'rgba(59, 130, 246, 0.9)', border: 'rgba(59, 130, 246, 1)' }, // Blue
                        { bg: 'rgba(245, 158, 11, 0.9)', border: 'rgba(245, 158, 11, 1)' }  // Amber
                    ];
                    
                    // Create datasets for each academic rank
                    const datasets = uniqueRanks.map((rank, rankIndex) => {
                        const color = rankColorPalette[rankIndex % rankColorPalette.length];
                        return {
                            label: rank,
                            data: topColleges.map(college => {
                                const ranks = collegeRankBreakdown[college] || {};
                                return ranks[rank] || 0;
                            }),
                            backgroundColor: color.bg,
                            borderColor: color.border,
                            borderWidth: 1,
                            borderRadius: 4,
                            borderSkipped: false,
                        };
                    });
                    
                    const ctx = collegeChartElement.getContext('2d');
                    
                    window.collegeChartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: topColleges.map(c => abbreviateCollegeName(c)),
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y',
                            layout: {
                                padding: {
                                    left: 5,
                                    right: 10,
                                    top: 5,
                                    bottom: 5
                                }
                            },
                            animation: {
                                duration: 1200,
                                easing: 'easeOutQuart'
                            },
                            plugins: {
                                legend: { 
                                    display: false
                                },
                                tooltip: {
                                    enabled: true,
                                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                    padding: 12,
                                    titleFont: {
                                        size: 13,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 12
                                    },
                                    cornerRadius: 8,
                                    displayColors: true,
                                    callbacks: {
                                        title: function(context) {
                                            // Show full college name in tooltip
                                            const index = context[0].dataIndex;
                                            return topColleges[index] || '';
                                        },
                                        label: function(context) {
                                            const rank = context.dataset.label;
                                            const count = context.parsed.x;
                                            const collegeIndex = context.dataIndex;
                                            const collegeTotal = collegeCounts[topColleges[collegeIndex]] || 0;
                                            const rankPercentage = collegeTotal > 0 ? ((count / collegeTotal) * 100).toFixed(1) : 0;
                                            return [
                                                `${rank}: ${count} request${count !== 1 ? 's' : ''}`,
                                                `${rankPercentage}% of ${abbreviateCollegeName(topColleges[collegeIndex])}`
                                            ];
                                        },
                                        footer: function(tooltipItems) {
                                            const collegeIndex = tooltipItems[0].dataIndex;
                                            const collegeTotal = collegeCounts[topColleges[collegeIndex]] || 0;
                                            return `Total: ${collegeTotal} request${collegeTotal !== 1 ? 's' : ''}`;
                                        },
                                        labelColor: function(context) {
                                            const rankIndex = uniqueRanks.indexOf(context.dataset.label);
                                            const color = rankColorPalette[rankIndex % rankColorPalette.length];
                                            return {
                                                borderColor: color.border,
                                                backgroundColor: color.bg
                                            };
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    stacked: true,
                                    beginAtZero: true,
                                    grid: {
                                        display: true,
                                        color: 'rgba(0, 0, 0, 0.05)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        stepSize: 1,
                                        precision: 0,
                                        font: {
                                            size: 11,
                                            weight: '500'
                                        },
                                        color: 'rgba(107, 114, 128, 0.8)',
                                        padding: 8
                                    }
                                },
                                y: {
                                    stacked: true,
                                    grid: {
                                        display: false,
                                        drawBorder: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 11,
                                            weight: '500'
                                        },
                                        color: 'rgba(107, 114, 128, 0.8)',
                                        padding: 10
                                    }
                                }
                            },
                            onHover: (event, activeElements) => {
                                collegeChartElement.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
                            }
                        }
                    });
                }
            }
            
            // Hide loading state after a short delay to ensure smooth transition
            setTimeout(() => {
                hideChartsLoading();
            }, 300);
        }

        function getGlobalFilters() {
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


                function fetchAndUpdateCharts(filters) {
            // Check if we're on the admin dashboard page
            const monthlyChartElement = document.querySelector('#monthlyChart');
            const statusChartElement = document.querySelector('#statusChart');
            
            if (!monthlyChartElement) {
                return;
            }
            
            // Show loading state
            showChartsLoading();
            
            const params = new URLSearchParams(filters).toString();
            
            fetch(`/admin/dashboard/data?${params}`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    if (!data.months || !data.monthlyCounts || !data.statusCounts) {
                        hideChartsLoading();
                        return;
                    }
                    updateChartsWithData({
                        months: data.months,
                        monthlyCounts: data.monthlyCounts,
                        statusCounts: data.statusCounts,
                        collegeCounts: data.collegeCounts || {},
                        collegeRankBreakdown: data.collegeRankBreakdown || {},
                        type: data.type || filters.type,
                        period: data.period || filters.period,
                        dateDetails: data.dateDetails || {}
                    });
                })
                .catch(err => {
                    hideChartsLoading();
                    // Silent fail for chart data fetch
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
            // SSE disabled for stability
    
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
        // Initialize charts with server-side data first
        @php
            $initialChartData = [
                'months' => $months ?? [],
                'monthlyCounts' => $monthlyCounts ?? [],
                'statusCounts' => $statusCounts ?? [],
                'collegeCounts' => $collegeCounts ?? [],
                'collegeRankBreakdown' => $collegeRankBreakdown ?? [],
                'type' => request('type'),
                'period' => request('period'),
                'dateDetails' => []
            ];
        @endphp
        const initialData = @json($initialChartData);
        if (initialData.months && initialData.monthlyCounts && initialData.statusCounts) {
            initializeCharts(initialData);
        }
        // Then fetch updated data
        fetchAndUpdateCharts(getGlobalFilters());
    } else {
        // If Turbo is enabled, hide loading state after a delay if no turbo:load event fires
        setTimeout(() => {
            hideChartsLoading();
        }, 2000);
    }
                } catch (error) {
                    // Silent fail for dashboard initialization
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
            
            // Show content when ready to prevent FOUC
            const mainContent = document.getElementById('mainContent');
            if (mainContent) {
                mainContent.classList.remove('fouc-prevent');
                mainContent.classList.add('fouc-ready');
            }
            
            // Set initial chart title based on current URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const period = urlParams.get('period');
            if (period) {
                updateChartTitle(period);
            }
            
            // SSE disabled for stability
            // Defer to allow layout to settle
            setTimeout(() => fetchAndUpdateCharts(getGlobalFilters()), 0);
            
            // Fix scrollbar layout shift
            fixScrollbarLayout();
        });

        // Add immediate chart update when filters change (for better responsiveness)
        if (typeof window.chartUpdateTimeout === 'undefined') {
            window.chartUpdateTimeout = null;
        }
        document.addEventListener('click', (e) => {
            // Check if clicking on filter links
            if (e.target.matches('a[href*="dashboard"]') || e.target.closest('a[href*="dashboard"]')) {
                const link = e.target.matches('a[href*="dashboard"]') ? e.target : e.target.closest('a[href*="dashboard"]');
                const url = new URL(link.href);
                
                // Clear any pending chart update
                if (window.chartUpdateTimeout) {
                    clearTimeout(window.chartUpdateTimeout);
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
                window.chartUpdateTimeout = setTimeout(() => {
                    fetchAndUpdateCharts(newFilters);
                }, 100);
            }
        });

        document.addEventListener('turbo:before-cache', () => {
            // Destroy charts before Turbo caches the page to avoid stale canvas state
            if (window.monthlyChartInstance) {
                window.monthlyChartInstance.destroy();
                window.monthlyChartInstance = null;
            }
            if (window.statusChartInstance) {
                window.statusChartInstance.destroy();
                window.statusChartInstance = null;
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
            // Show content when ready to prevent FOUC
            const mainContent = document.getElementById('mainContent');
            if (mainContent) {
                mainContent.classList.remove('fouc-prevent');
                mainContent.classList.add('fouc-ready');
            }
            
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
            <div class="w-[95vw] h-[90vh] max-w-6xl bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200 flex flex-col">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 bg-maroon-800 text-white flex-shrink-0">
                    <h2 class="text-lg font-bold">Request Review</h2>
                    <button onclick="closeReviewModal()" class="px-4 py-2 text-white/80 hover:text-white font-medium transition-colors rounded-lg hover:bg-white/10 text-sm">
                        Cancel
                    </button>
                </div>
                
                <!-- Loading State -->
                <div id="modalLoading" class="flex-1 flex items-center justify-center p-8">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-maroon-700 mx-auto"></div>
                        <p class="mt-3 text-gray-600 font-medium">Loading request details...</p>
                    </div>
                </div>
                
                <!-- Content -->
                <div id="modalContent" class="hidden flex-1 overflow-hidden">
                    <div class="p-4 h-full flex flex-col space-y-4">
                        <!-- Main Content Grid -->
                        <div class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-4 min-h-0">
                            <!-- Left Column - Combined Request/Applicant/Signatories Card -->
                            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm flex flex-col h-full">
                                <div class="flex items-center gap-2">
                                    
                                    </div>
                                
                                <!-- Summary Section -->
                                <div class="mb-3 flex-1 flex flex-col min-h-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-6 h-6 bg-maroon-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-3 h-3 text-maroon-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-xs font-semibold text-gray-800 border-gray-200">Summary</h4>
                                    </div>
                                    <div class="flex-1 overflow-hidden rounded-lg border border-gray-300">
                                        <style>
                                            .summary-table td {
                                                white-space: nowrap;
                                                overflow: hidden;
                                                text-overflow: ellipsis;
                                            }
                                        </style>
                                        <table class="w-full h-full text-xs summary-table" style="table-layout: fixed;">
                                            <tbody class="divide-y divide-gray-300">
                                                <tr class="bg-gray-50">
                                                    <td class="px-2 py-0.5 font-bold text-gray-700 border-r border-gray-300 w-1/2 truncate">Request Code</td>
                                                    <td class="px-2 py-0.5 font-bold text-gray-900 w-1/2 truncate" id="modalRequestCode" title="">-</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-2 py-0.5 font-bold text-gray-700 border-r border-gray-300 w-1/2 truncate">Academic Rank</td>
                                                    <td class="px-2 py-0.5 font-bold text-gray-900 w-1/2 truncate" id="modalAcademicRank" title="">-</td>
                                                </tr>
                                                <tr class="bg-gray-50">
                                                    <td class="px-2 py-0.5 font-bold text-gray-700 border-r border-gray-300 w-1/2 truncate">College</td>
                                                    <td class="px-2 py-0.5 font-bold text-gray-900 w-1/2 truncate" id="modalCollege" title="">-</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-2 py-0.5 font-bold text-gray-700 border-r border-gray-300 w-1/2 truncate">Submitted</td>
                                                    <td class="px-2 py-0.5 font-bold text-gray-900 w-1/2 truncate" id="modalDate" title="">-</td>
                                                </tr>
                                                <tr class="bg-gray-50">
                                                    <td class="px-2 py-0.5 font-bold text-gray-700 border-r border-gray-300 w-1/2 truncate">Full Name</td>
                                                    <td class="px-2 py-0.5 font-bold text-gray-900 w-1/2 truncate" id="modalUserName" title="">-</td>
                                                </tr>
                                                <tr>
                                                    <td class="px-2 py-0.5 font-bold text-gray-700 border-r border-gray-300 w-1/2 truncate">Email Address</td>
                                                    <td class="px-2 py-0.5 font-bold text-gray-900 w-1/2 truncate" id="modalUserEmail" title="">-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Signatories Section -->
                                <div class="flex-1 flex flex-col min-h-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-6 h-6 bg-maroon-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-3 h-3 text-maroon-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-xs font-semibold text-gray-800 border-gray-200">Signatories</h4>
                                    </div>
                                    <div class="flex-1 overflow-hidden rounded-lg border border-gray-300">
                                        <style>
                                            .signatories-table td {
                                                white-space: nowrap;
                                                overflow: hidden;
                                                text-overflow: ellipsis;
                                            }
                                        </style>
                                        <table class="w-full h-full text-xs signatories-table" style="table-layout: fixed;">
                                                <tbody class="divide-y divide-gray-300" id="modalFormData">
                                                    <!-- Dynamic Signatories will be populated here -->
                                                    <!-- Fixed Directors -->
                                                    <tr class="bg-gray-50 fixed-director">
                                                        <td class="px-2 py-0.5 font-bold text-gray-700 w-1/2 truncate border-r border-gray-300" title="{{ \App\Models\Setting::get('official_deputy_director_title', 'Deputy Director, Publication Unit') }}">{{ \App\Models\Setting::get('official_deputy_director_title', 'Deputy Director, Publication Unit') }}</td>
                                                        <td class="px-2 py-0.5 font-bold text-gray-900 w-1/2 truncate" title="{{ \App\Models\Setting::get('official_deputy_director_name', 'RANDY A. TUDY, PhD') }}">{{ \App\Models\Setting::get('official_deputy_director_name', 'RANDY A. TUDY, PhD') }}</td>
                                                    </tr>
                                                    <tr class="fixed-director">
                                                        <td class="px-2 py-0.5 font-bold text-gray-700 w-1/2 truncate border-r border-gray-300" title="{{ \App\Models\Setting::get('official_rdd_director_title', 'Director, Research and Development Division') }}">{{ \App\Models\Setting::get('official_rdd_director_title', 'Director, Research and Development Division') }}</td>
                                                        <td class="px-2 py-0.5 font-bold text-gray-900 w-1/2 truncate" title="{{ \App\Models\Setting::get('official_rdd_director_name', 'MERLINA H. JURUENA, PhD') }}">{{ \App\Models\Setting::get('official_rdd_director_name', 'MERLINA H. JURUENA, PhD') }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column - Files & Admin Comment Cards -->
                            <div class="flex flex-col space-y-3 min-h-0">
                                <!-- Files Section Card -->
                                <div class="bg-white rounded-xl p-3 border border-gray-200 shadow-sm flex-1 min-h-0 flex flex-col">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                            </div>
                                            <h3 class="text-xs font-semibold text-gray-900">Submitted Files</h3>
                        </div>
                                        <button id="downloadZipBtn" class="inline-flex items-center gap-1 px-2 py-1 bg-maroon-700 text-white rounded-lg hover:bg-maroon-800 hover:shadow-md transition-all duration-300 text-xs font-medium">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                            Download Zip
                                    </button>
                                </div>
                                    <div id="modalFiles" class="flex-1 overflow-y-auto space-y-1 text-xs">
                                        <!-- Files will be populated here -->
                                </div>
                            </div>
                            
                                <!-- Admin Comment Card -->
                                <div class="bg-white rounded-xl p-3 border border-gray-200 shadow-sm flex-1 min-h-0 flex flex-col">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-6 h-6 bg-amber-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-xs font-semibold text-gray-900">Admin Comment (Optional)</h3>
                                    </div>
                                    <div class="flex-1 flex flex-col">
                                        <textarea id="adminComment" 
                                            class="flex-1 w-full border border-gray-300 rounded-lg px-2 py-1 text-xs focus:border-maroon-500 focus:ring-2 focus:ring-maroon-500/20 transition-all resize-none" 
                                            placeholder="Add your review notes or comments here..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Actions -->
                <div id="modalFooter" class="flex justify-between items-center p-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 flex-shrink-0">
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span class="font-medium">Review all information before making a decision.</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg font-medium flex items-center gap-1 text-sm">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Status: Automated Workflow
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300">
                <!-- Modal Header -->
                <div class="flex items-center gap-3 p-6 border-b border-gray-200">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Delete Request</h3>
                    </div>
                </div>
                
                <!-- Modal Content -->
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-gray-700 mb-2">Are you sure you want to delete this request?</p>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Request Code:</span> 
                                <span id="deleteRequestCode" class="font-mono text-gray-900">-</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                    <button onclick="closeDeleteModal()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        Cancel
                    </button>
                    <button id="confirmDeleteBtn" onclick="confirmDelete()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
        // Review modal functions
function openReviewModal(requestId) {
    if (!requestId) {
        return;
    }
    window.__currentReviewRequestId = requestId;
    document.getElementById('reviewModal').classList.remove('hidden');
    document.getElementById('modalLoading').classList.remove('hidden');
    document.getElementById('modalContent').classList.add('hidden');
    document.getElementById('modalFooter').classList.add('hidden');
            
    fetch(`/admin/requests/${requestId}/data`)
        .then(response => {
            if (!response.ok) throw new Error('Failed to load request data');
            return response.json();
        })
        .then(data => {
            populateModal(data);
            const zipBtn = document.getElementById('downloadZipBtn');
            if (zipBtn) {
                zipBtn.onclick = () => { 
                    window.location.href = `/admin/requests/${requestId}/download-zip`; 
                };
            }
            // REMOVED: Status update button handlers - Status changes are now automated through the 5-stage signature workflow
        })
        .catch(error => {
                    alert('Failed to load request data. Please try again.');
        })
        .finally(() => {
            document.getElementById('modalLoading').classList.add('hidden');
            document.getElementById('modalContent').classList.remove('hidden');
            document.getElementById('modalFooter').classList.remove('hidden');
        });
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
    // Reset modal content
    document.getElementById('modalRequestCode').textContent = '-';
    document.getElementById('modalAcademicRank').textContent = '-';
    document.getElementById('modalCollege').textContent = '-';
    document.getElementById('modalDate').textContent = '-';
    document.getElementById('modalUserName').textContent = '-';
    document.getElementById('modalUserEmail').textContent = '-';
    // Clear only dynamic signatories, preserve fixed directors
    const formDataContainer = document.getElementById('modalFormData');
    const dynamicRows = formDataContainer.querySelectorAll('tr:not(.fixed-director)');
    dynamicRows.forEach(row => row.remove());
    document.getElementById('modalFiles').innerHTML = '';
}

function populateModal(data) {
    // Populate basic info
    const requestCode = data.request_code || 'N/A';
    const academicRank = data.academic_rank || 'N/A';
    const college = data.college || 'N/A';
    const date = formatDate(data.requested_at);
    const userName = data.user?.name || 'N/A';
    const userEmail = data.user?.email || 'N/A';
    
    document.getElementById('modalRequestCode').textContent = requestCode;
    document.getElementById('modalRequestCode').title = requestCode;
    document.getElementById('modalAcademicRank').textContent = academicRank;
    document.getElementById('modalAcademicRank').title = academicRank;
    document.getElementById('modalCollege').textContent = college;
    document.getElementById('modalCollege').title = college;
    document.getElementById('modalDate').textContent = date;
    document.getElementById('modalDate').title = date;
    document.getElementById('modalUserName').textContent = userName;
    document.getElementById('modalUserName').title = userName;
    document.getElementById('modalUserEmail').textContent = userEmail;
    document.getElementById('modalUserEmail').title = userEmail;
            
    // Signatories
    const formDataContainer = document.getElementById('modalFormData');
    
    // Clear only dynamic signatories (first 3 rows), preserve fixed directors
    const dynamicRows = formDataContainer.querySelectorAll('tr:not(.fixed-director)');
    dynamicRows.forEach(row => row.remove());
    
    if (data.signatories && data.signatories.length > 0) {
        data.signatories.forEach((signatory, index) => {
            const row = document.createElement('tr');
            row.className = index % 2 === 0 ? 'bg-gray-50' : '';
            
            // Position in column 1
            const positionCell = document.createElement('td');
            positionCell.className = 'px-2 py-0.5 font-bold text-gray-700 w-1/2 truncate border-r border-gray-300';
            positionCell.textContent = signatory.role || 'N/A';
            positionCell.title = signatory.role || 'N/A';
            
            // Name in column 2
            const nameCell = document.createElement('td');
            nameCell.className = 'px-2 py-0.5 font-bold text-gray-900 w-1/2 truncate';
            nameCell.textContent = signatory.name || 'N/A';
            nameCell.title = signatory.name || 'N/A';
            
            row.appendChild(positionCell);
            row.appendChild(nameCell);
            formDataContainer.insertBefore(row, formDataContainer.querySelector('.fixed-director'));
        });
    } else {
        const noDataRow = document.createElement('tr');
        noDataRow.className = 'bg-gray-50';
        
        const noDataCell1 = document.createElement('td');
        noDataCell1.className = 'px-2 py-0.5 font-bold text-gray-700 w-1/2 truncate border-r border-gray-300';
        noDataCell1.textContent = 'No signatories found';
        
        const noDataCell2 = document.createElement('td');
        noDataCell2.className = 'px-2 py-0.5 font-bold text-gray-900 w-1/2 truncate';
        noDataCell2.textContent = '';
        
        noDataRow.appendChild(noDataCell1);
        noDataRow.appendChild(noDataCell2);
        formDataContainer.insertBefore(noDataRow, formDataContainer.querySelector('.fixed-director'));
    }
            
    // Files
    const filesContainer = document.getElementById('modalFiles');
    filesContainer.innerHTML = '';
    if (data.files && data.files.length > 0) {
        data.files.forEach(file => {
            const fileDiv = document.createElement('div');
                    fileDiv.className = 'flex items-center justify-between bg-white rounded-lg border border-gray-200 p-3';
            const type = file.type;
            const key = file.key;
            
            // Use secure download URL if available, otherwise fallback to old method
            const downloadUrl = file.download_url || `/admin/requests/${data.id}/serve?type=${type}&key=${encodeURIComponent(key)}`;
            
            // Get appropriate icon and color based on file type
            let iconClass = 'text-gray-500';
            let bgColor = 'bg-gray-600';
            
            if (type === 'pdf') {
                iconClass = 'text-red-500';
                bgColor = 'bg-red-600';
            } else if (type === 'docx') {
                iconClass = 'text-blue-500';
                bgColor = 'bg-blue-600';
            } else if (type === 'signed') {
                iconClass = 'text-green-500';
                bgColor = 'bg-green-600';
            } else if (type === 'backup') {
                iconClass = 'text-orange-500';
                bgColor = 'bg-orange-600';
            }
            
            fileDiv.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 ${iconClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs text-gray-900">${file.name}</span>
                            </div>
                            <span class="text-xs text-gray-500">(${file.size})</span>
                </div>
                        <div class="flex gap-2">
                            <a href="${downloadUrl}" target="_blank" class="px-2 py-1 ${bgColor} text-white text-xs rounded hover:opacity-80 transition-colors">View</a>
                            <a href="${downloadUrl}" download class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors">Download</a>
                </div>
            `;
            filesContainer.appendChild(fileDiv);
        });
    } else {
                filesContainer.innerHTML = '<div class="text-gray-500 text-xs">No files uploaded for this request</div>';
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

// REMOVED: submitStatusUpdate function - Status changes are now automated through the 5-stage signature workflow

// REMOVED: updateActionButtonsState function - Status buttons are no longer available

function updateRequestStatusInTable(requestId, newStatus) {
    // Find the table row for this request
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const reviewBtn = row.querySelector('button[onclick*="openReviewModal"]');
        if (reviewBtn && reviewBtn.onclick.toString().includes(requestId)) {
            // Update status badge
            const statusCell = row.querySelector('td:nth-child(3)'); // Assuming status is 3rd column
            if (statusCell) {
                const statusBadge = statusCell.querySelector('.status-badge');
                if (statusBadge) {
                    // Remove existing status classes
                    statusBadge.classList.remove('bg-yellow-100', 'text-yellow-800', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
                    
                    // Add new status classes
                    if (newStatus === 'endorsed') {
                        statusBadge.classList.add('bg-green-100', 'text-green-800');
                        statusBadge.textContent = 'Endorsed';
                    } else if (newStatus === 'rejected') {
                        statusBadge.classList.add('bg-red-100', 'text-red-800');
                        statusBadge.textContent = 'Rejected';
                    }
                }
            }
            
            // Update action buttons in the row
            const actionButtons = row.querySelectorAll('button');
            actionButtons.forEach(btn => {
                if (btn.textContent.includes('Review')) {
                    // Disable review button for completed requests
                    if (newStatus === 'endorsed' || newStatus === 'rejected') {
                        btn.disabled = true;
                        btn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                }
            });
        }
    });
}

// Delete modal functions - prevent redeclaration during Turbo navigation
if (typeof window.currentDeleteRequestId === 'undefined') {
    window.currentDeleteRequestId = null;
}

function openDeleteModal(requestId, requestCode) {
    window.currentDeleteRequestId = requestId;
    document.getElementById('deleteRequestCode').textContent = requestCode;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    
    // Add keyboard event listener for ESC key
    document.addEventListener('keydown', handleDeleteModalKeydown);
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    window.currentDeleteRequestId = null;
    
    // Remove keyboard event listener
    document.removeEventListener('keydown', handleDeleteModalKeydown);
}

function handleDeleteModalKeydown(event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
    }
}

function confirmDelete() {
    if (!window.currentDeleteRequestId) {
        return;
    }
    
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const originalText = confirmBtn.innerHTML;
    
    // Show loading state
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = `
        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        Deleting...
    `;
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/requests/${window.currentDeleteRequestId}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    
    // Submit form
    form.submit();
}
</script>

<!-- Include loading and notification systems -->
<script src="{{ asset('js/loading.js') }}"></script>
<x-global-notifications />
</x-app-layout>

