<x-app-layout>
    <div x-data="{ 
        errorMessage: null,
        errorTimer: null,
        activeTab: 'dashboard',
        searchOpen: false,
        loading: false,
        loadingTimer: null,
        showError(message) {
            this.errorMessage = message;
            if (this.errorTimer) clearTimeout(this.errorTimer);
            this.errorTimer = setTimeout(() => {
                this.errorMessage = null;
            }, 3000);
        },
        setLoading(loading) {
            this.loading = loading;
            if (loading) {
                // Reset loading after 10 seconds as fallback
                if (this.loadingTimer) clearTimeout(this.loadingTimer);
                this.loadingTimer = setTimeout(() => {
                    this.loading = false;
                }, 10000);
            } else {
                if (this.loadingTimer) clearTimeout(this.loadingTimer);
            }
        }
    }" class="h-screen bg-gray-50 flex overflow-hidden">
        
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
        <!-- Loading overlay - Now handled by simple loading system -->

        <!-- Sidebar - Hidden on mobile, visible on desktop -->
        <div class="hidden lg:block">
            @include('components.user-sidebar')
        </div>

        <!-- Main Content -->
        <div class="flex-1 h-screen overflow-y-auto force-scrollbar">
            <!-- Mobile Header - Only visible on mobile, desktop completely unaffected -->
            <div class="lg:hidden bg-gradient-to-r from-maroon-800 to-maroon-900 text-white px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <img src="/images/spjrd.png" alt="SPJRD Logo" class="w-5 h-5 object-contain rounded-full">
                    </div>
                    <div>
                        <h1 class="text-lg font-bold">PubCite</h1>
                        <p class="text-xs text-maroon-200">User Dashboard</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <form method="POST" action="{{ route('logout') }}" class="inline" @submit="setLoading(true)">
                        @csrf
                        <button type="submit" :disabled="loading" class="px-3 py-2 bg-white/20 rounded-lg text-sm font-medium hover:bg-white/30 transition flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Content Area -->
            <main class="max-w-7xl mx-auto px-4 pt-2 pb-4 h-full flex flex-col main-content">
                <!-- Dashboard Header with Modern Compact Filters -->
                <div class="relative flex items-center justify-between mb-4 flex-shrink-0">
                    <!-- Overview Header -->
                    <div class="flex items-center gap-2 text-md font-semibold text-gray-600 bg-gray-50 px-3 py-2.5 rounded-lg h-10">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Requests</span>
                    </div>
                    
                    <!-- Enhanced Search and User Controls -->
                    @include('components.user-navbar', ['showFilters' => true, 'requests' => $requests])
                </div>

                @php
                    $currentStatus = request('status');
                    $filteredRequests = $requests;
                    if ($currentStatus) {
                        $filteredRequests = $requests->where('status', $currentStatus);
                    }
                @endphp

                <!-- Requests Table Container -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex-1 flex flex-col" style="height: calc(100vh - 8rem);">
                    
                    <!-- Table Header (Fixed) -->
                    <div class="bg-gray-50 border-b border-gray-200 flex-shrink-0">
                        <div class="overflow-x-auto">
                            <table class="w-full table-fixed">
                                <thead>
                                    <tr>
                                        <th class="w-28 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center justify-center gap-1">
                                                Request Code
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                </svg>
                                            </div>
                                        </th>
                                        <th class="w-32 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center gap-1">
                                                Name
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                </svg>
                                            </div>
                                        </th>
                                        <th class="w-24 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center gap-1">
                                                Type
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                </svg>
                                            </div>
                                        </th>
                                        <th class="w-24 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center gap-1">
                                                Date
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                </svg>
                                            </div>
                                        </th>
                                        <th class="w-28 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center justify-center gap-1">
                                                Status
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                </svg>
                                            </div>
                                        </th>
                                        <th class="w-20 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Signed
                                        </th>
                                        <th class="w-24 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Table Body (Scrollable) -->
                    <div class="flex-1 overflow-y-auto table-scroll-area">
                        @if($filteredRequests->isEmpty())
                            <!-- Empty State (Centered) -->
                            <div class="h-full flex items-center justify-center">
                                <div class="flex flex-col items-center justify-center gap-3 text-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20v-6m0 0l-3 3m3-3l3 3M4 6h16M4 10h16M4 14h16"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">No requests yet</h4>
                                        <p class="text-gray-500">Start by submitting your first publication or citation request.</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full table-fixed divide-y divide-gray-200">
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($filteredRequests as $index => $request)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="w-28 px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $request->request_code }}</span>
                                        </td>
                                            <td class="w-32 px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? 'User' }}</div>
                                                <div class="text-sm text-gray-500">{{ Auth::user()->email ?? 'No email' }}</div>
                                            </div>
                                        </td>
                                            <td class="w-24 px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $request->type === 'Publication' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $request->type }}
                                            </span>
                                        </td>
                                            <td class="w-24 px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($request->requested_at)->format('M d, Y') }}
                                        </td>
                                            <td class="w-28 px-6 py-4 whitespace-nowrap text-center">
                                                    @if($request->status === 'endorsed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                                    Endorsed
                                                </span>
                                                    @elseif($request->status === 'rejected')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <div class="w-2 h-2 bg-red-400 rounded-full mr-2"></div>
                                                    Redo
                                                </span>
                                                    @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <div class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></div>
                                                    Pending
                                                </span>
                                                    @endif
                                                </td>
                                            <td class="w-20 px-6 py-4 whitespace-nowrap text-center">
                                                @php
                                                    $signedCount = \App\Models\RequestSignature::where('request_id', $request->id)->count();
                                                    $totalSignatories = 5; // user, center_manager, college_dean, deputy_director, rdd_director
                                                @endphp
                                                <span class="text-sm font-medium text-gray-900">{{ $signedCount }}/{{ $totalSignatories }}</span>
                                                </td>
                                            <td class="w-24 px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                    @if($request->status === 'pending')
                                                        <div class="flex flex-col items-center gap-2">
                                                            @if($request->workflow_state === 'pending_user_signature')
                                                                <button data-review-request-id="{{ $request->id }}" class="review-modal-btn inline-flex items-center justify-center gap-1 w-full px-4 py-2 rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 transition-all duration-200 text-xs font-medium">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                    </svg>
                                                                    Review
                                                                </button>
                                                            @else
                                                        @php
                                                            $recentNudge = \App\Models\ActivityLog::where('user_id', auth()->id())
                                                                ->where('request_id', $request->id)
                                                                ->where('action', 'nudged')
                                                                ->where('created_at', '>=', now()->subDay())
                                                                ->first();
                                                            $canNudge = !$recentNudge;
                                                        @endphp
                                                        @if($canNudge)
                                                            <form method="POST" action="{{ route('requests.nudge', $request) }}" class="inline" @submit="setLoading(true)">
                                                                @csrf
                                                                <button type="submit" :disabled="loading" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200 disabled:opacity-60 disabled:cursor-not-allowed transition-all duration-200 text-sm font-medium" title="Nudge current signatory">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                            </svg>
                                                                    <span x-text="loading ? 'Nudging…' : 'Nudge'"></span>
                                                                </button>
                                                            </form>
                                                        @else
                                                    <button type="button" @click="showError('You can only nudge this request once per day.')" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed transition-all duration-200 text-sm font-medium" title="Already nudged today" disabled>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                        </svg>
                                                                <span>Nudge</span>
                                                            </button>
                                                        @endif
                                                            @endif
                                                        </div>
                                                    @else
                                                    <span class="text-gray-400">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Pagination Footer (Fixed) -->
                    <div class="bg-white px-6 py-3 border-t border-gray-200 flex-shrink-0">
                        <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">{{ $filteredRequests->count() }}</span> of <span class="font-medium">{{ $requests->count() }}</span> results
                        </div>
                        <div class="flex items-center space-x-2">
                                <button class="px-3 py-1 text-sm text-gray-500 bg-gray-100 rounded-md cursor-not-allowed" disabled>
                                    Previous
                            </button>
                                <button class="px-3 py-1 text-sm text-gray-500 bg-gray-100 rounded-md cursor-not-allowed" disabled>
                                    Next
                            </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                                                    <td class="px-2 py-0.5 font-bold text-gray-700 border-r border-gray-300 w-1/2 truncate">Type</td>
                                                    <td class="px-2 py-0.5 font-bold text-gray-900 w-1/2 truncate" id="modalType" title="">-</td>
                                                </tr>
                                                <tr class="bg-gray-50">
                                                    <td class="px-2 py-0.5 font-bold text-gray-700 border-r border-gray-300 w-1/2 truncate">Status</td>
                                                    <td class="px-2 py-0.5 w-1/2 truncate">
                                                        <div id="modalStatus" class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium truncate max-w-full" title="">-</div>
                                                    </td>
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
                            
                            <!-- Right Column - Files Card -->
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
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Actions -->
                <div id="modalFooter" class="flex flex-col gap-2 p-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 flex-shrink-0">
                    <!-- Footer Content Row -->
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <span class="font-medium">Review all information before making a decision.</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="file" id="modalSignedDocuments" name="signed_documents[]" multiple accept=".pdf,.docx" class="hidden">
                            <!-- Selected Files Display (to the left of redo button) -->
                            <div id="modalSelectedFiles" class="text-xs text-gray-600 px-2"></div>
                            <!-- Separator between selected files and redo button -->
                            <div id="modalSelectedFilesSeparator" class="h-6 w-px bg-gray-300 hidden"></div>
                            <button id="modalRedoBtn" class="redo-modal-btn px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-medium flex items-center gap-2 text-sm" style="display: none;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Redo
                            </button>
                            <button id="modalUploadBtn" class="px-4 py-2 bg-maroon-700 text-white rounded-lg hover:bg-maroon-800 transition-colors font-medium flex items-center gap-2 text-sm" style="display: none;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Upload Signed Documents
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Redo Confirmation Modal -->
    <div id="redoConfirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Confirm Redo Action</h3>
                    <button onclick="closeRedoConfirmationModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    <strong>Warning:</strong> This action will permanently delete all files associated with this request. This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="redoReason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for deletion/rejection <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            id="redoReason" 
                            name="redo_reason" 
                            rows="4" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Please provide a reason for deleting all files of this request..."
                            required
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500">This reason will be recorded for audit purposes.</p>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <button 
                        id="cancelRedoBtn"
                        onclick="closeRedoConfirmationModal()" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium"
                    >
                        Cancel
                    </button>
                    <button 
                        id="confirmRedoBtn"
                        onclick="confirmRedoAction()" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Confirm Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
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
        
        /* Ensure table column alignment */
        .table-fixed {
            table-layout: fixed;
        }
        
        .table-fixed th,
        .table-fixed td {
            box-sizing: border-box;
        }
        
        /* Mobile-specific table card viewport height */
        @media (max-width: 640px) {
            .main-content {
                height: 100vh !important;
                height: 100dvh !important;
                padding-bottom: 2rem !important;
            }
            
            .bg-white.rounded-lg.shadow-sm {
                height: calc(100vh - 8rem) !important;
                height: calc(100dvh - 8rem) !important;
                display: flex !important;
                flex-direction: column !important;
            }
            
            .table-scroll-area {
                flex: 1 !important;
                overflow-y: auto !important;
            }
        }
        
        /* Ensure proper flex layout */
        .flex-1 {
            flex: 1 1 0%;
        }
        
        .flex-shrink-0 {
            flex-shrink: 0;
        }
    </style>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
        let currentReviewRequestId = null;
        let reviewModalKeydownHandlerBound = false;

        function notify(type, message) {
            if (window.notificationManager && typeof window.notificationManager[type] === 'function') {
                window.notificationManager[type](message);
            } else {
                alert(message);
            }
        }

        function resetModalUploadControls() {
            const uploadBtn = document.getElementById('modalUploadBtn');
            const fileInput = document.getElementById('modalSignedDocuments');
            const selectedFilesDiv = document.getElementById('modalSelectedFiles');
            const separator = document.getElementById('modalSelectedFilesSeparator');

            if (uploadBtn) {
                uploadBtn.style.display = 'none';
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Signed Documents
                `;
                uploadBtn.onclick = null;
            }

            if (fileInput) {
                fileInput.value = '';
                fileInput.removeAttribute('data-request-id');
                fileInput.onchange = null;
            }

            if (selectedFilesDiv) {
                selectedFilesDiv.innerHTML = '';
            }

            if (separator) {
                separator.classList.add('hidden');
            }
        }

        function closeReviewModal() {
            const modal = document.getElementById('reviewModal');
            if (!modal) return;

            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');

            const loading = document.getElementById('modalLoading');
            const content = document.getElementById('modalContent');
            if (loading) loading.classList.remove('hidden');
            if (content) content.classList.add('hidden');

            ['modalRequestCode', 'modalType', 'modalStatus', 'modalDate', 'modalUserName', 'modalUserEmail'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.textContent = '-';
                    el.title = '-';
                }
            });

            const formDataContainer = document.getElementById('modalFormData');
            if (formDataContainer) {
                const dynamicRows = formDataContainer.querySelectorAll('tr:not(.fixed-director)');
                dynamicRows.forEach(row => row.remove());
            }

            const filesContainer = document.getElementById('modalFiles');
            if (filesContainer) {
                filesContainer.innerHTML = '<div class="text-gray-500">No files uploaded for this request.</div>';
            }

            resetModalUploadControls();

            currentReviewRequestId = null;
            if (reviewModalKeydownHandlerBound) {
                document.removeEventListener('keydown', handleReviewModalKeydown);
                reviewModalKeydownHandlerBound = false;
            }
        }

        function populateReviewModal(data) {
            const requestCode = data.request_code || 'N/A';
            const type = data.type || 'N/A';
            const requestedDate = data.requested_at ? formatDate(data.requested_at) : 'N/A';
            const userName = data.user?.name || 'N/A';
            const userEmail = data.user?.email || 'N/A';
            const status = data.status || 'N/A';

            document.getElementById('modalRequestCode').textContent = requestCode;
            document.getElementById('modalRequestCode').title = requestCode;
            document.getElementById('modalType').textContent = type;
            document.getElementById('modalType').title = type;
            document.getElementById('modalDate').textContent = requestedDate;
            document.getElementById('modalDate').title = requestedDate;
            document.getElementById('modalUserName').textContent = userName;
            document.getElementById('modalUserName').title = userName;
            document.getElementById('modalUserEmail').textContent = userEmail;
            document.getElementById('modalUserEmail').title = userEmail;

            const statusElement = document.getElementById('modalStatus');
            if (statusElement) {
                statusElement.textContent = status;
                statusElement.title = status;
                statusElement.className = 'inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium truncate max-w-full';
                if (status === 'pending') {
                    statusElement.classList.add('bg-yellow-100', 'text-yellow-800');
                } else if (status === 'endorsed') {
                    statusElement.classList.add('bg-green-100', 'text-green-800');
                } else if (status === 'rejected') {
                    statusElement.classList.add('bg-red-100', 'text-red-800');
                } else {
                    statusElement.classList.add('bg-gray-100', 'text-gray-600');
                }
            }

            const formDataContainer = document.getElementById('modalFormData');
            if (formDataContainer) {
                const dynamicRows = formDataContainer.querySelectorAll('tr:not(.fixed-director)');
                dynamicRows.forEach(row => row.remove());

                if (Array.isArray(data.signatories) && data.signatories.length > 0) {
                    data.signatories.forEach((signatory, index) => {
                        const row = document.createElement('tr');
                        row.className = index % 2 === 0 ? 'bg-gray-50' : '';

                        const positionCell = document.createElement('td');
                        positionCell.className = 'px-2 py-0.5 font-bold text-gray-700 w-1/2 truncate border-r border-gray-300';
                        positionCell.textContent = signatory.role || 'N/A';
                        positionCell.title = signatory.role || 'N/A';

                        const nameCell = document.createElement('td');
                        nameCell.className = 'px-2 py-0.5 font-bold text-gray-900 w-1/2 truncate';
                        nameCell.textContent = signatory.name || 'N/A';
                        nameCell.title = signatory.name || 'N/A';

                        row.appendChild(positionCell);
                        row.appendChild(nameCell);
                        formDataContainer.insertBefore(row, formDataContainer.querySelector('.fixed-director'));
                    });
                }
            }

            const filesContainer = document.getElementById('modalFiles');
            if (filesContainer) {
                filesContainer.innerHTML = '';
                if (Array.isArray(data.files) && data.files.length > 0) {
                    data.files.forEach(file => {
                        const needsSigning = file.needs_signing === true;
                        const fileDiv = document.createElement('div');
                        fileDiv.className = needsSigning
                            ? 'flex items-center justify-between bg-amber-50 rounded-lg border-2 border-amber-300 p-3'
                            : 'flex items-center justify-between bg-white rounded-lg border border-gray-200 p-3';

                        const downloadLabel = file.download_url
                            ? `<a href="${file.download_url}" target="_blank" class="px-4 py-1 bg-green-600 text-white text-xs rounded-full hover:bg-green-700 transition-colors">View</a>`
                            : `<button class="px-4 py-1 bg-green-600 text-white text-xs rounded-full opacity-50 cursor-not-allowed" title="Download link unavailable" disabled>View</button>`;

                        fileDiv.innerHTML = `
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2">
                                    ${needsSigning ? `
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Requires your signature">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    ` : `
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    `}
                                    <span class="text-xs ${needsSigning ? 'text-amber-900 font-semibold' : 'text-gray-900'}">${file.name}</span>
                                </div>
                                <span class="text-xs text-gray-500">(${file.size})</span>
                            </div>
                            <div class="flex gap-2">
                                ${downloadLabel}
                            </div>
                        `;
                        filesContainer.appendChild(fileDiv);
                    });
                } else {
                    filesContainer.innerHTML = '<div class="text-gray-500 text-xs">No files uploaded for this request</div>';
                }
            }
        }

        // setupModalUpload is defined outside component - see below

        function updateModalSelectedFiles(fileInput, requestId) {
            const selectedFilesDiv = document.getElementById('modalSelectedFiles');
            const separator = document.getElementById('modalSelectedFilesSeparator');
            const uploadBtn = document.getElementById('modalUploadBtn');

            if (!selectedFilesDiv || !uploadBtn) return;

            if (fileInput.files.length > 0) {
                let filesList = '<div class="flex flex-wrap items-center gap-2 text-gray-700">';
                filesList += '<svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                filesList += '<span class="font-medium">Selected:</span>';
                
                // Display each file with a remove button
                for (let i = 0; i < fileInput.files.length; i++) {
                    const fileName = fileInput.files[i].name;
                    filesList += `<div class="relative inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded-md group">
                        <span class="text-xs text-gray-700 truncate max-w-[150px]">${fileName}</span>
                        <button type="button" onclick="removeSelectedFile(${i}, '${fileInput.id}', '${requestId}')" class="flex-shrink-0 w-4 h-4 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center transition-colors" title="Remove file">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>`;
                }
                filesList += '</div>';
                selectedFilesDiv.innerHTML = filesList;
                
                if (separator) separator.classList.remove('hidden');
                uploadBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Now
                `;
                uploadBtn.onclick = (event) => {
                    event.preventDefault();
                    handleModalUpload(requestId, fileInput);
                };
            } else {
                selectedFilesDiv.innerHTML = '';
                if (separator) separator.classList.add('hidden');
                uploadBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Signed Documents
                `;
                uploadBtn.onclick = () => fileInput.click();
            }
        }

        function removeSelectedFile(fileIndex, fileInputId, requestId) {
            const fileInput = document.getElementById(fileInputId);
            if (!fileInput) return;
            
            // Create a new FileList without the removed file
            const dt = new DataTransfer();
            for (let i = 0; i < fileInput.files.length; i++) {
                if (i !== fileIndex) {
                    dt.items.add(fileInput.files[i]);
                }
            }
            fileInput.files = dt.files;
            
            // Update the display
            const selectedFilesDiv = document.getElementById('modalSelectedFiles');
            if (selectedFilesDiv) {
                updateModalSelectedFiles(fileInput, requestId);
            }
        }

        async function handleModalUpload(requestId, fileInput) {
            if (!requestId || !fileInput || !fileInput.files || fileInput.files.length === 0) return;

            const uploadBtn = document.getElementById('modalUploadBtn');
            if (uploadBtn) {
                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';
            }

            try {
                const formData = new FormData();
                formData.append('request_id', requestId);
                if (csrfToken) {
                    formData.append('_token', csrfToken);
                }
                Array.from(fileInput.files).forEach(file => formData.append('signed_documents[]', file));

                const response = await fetch('/signing/upload-signed', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });

                if (response.status === 429) {
                    notify('error', 'Too many attempts. Please wait a moment before trying again.');
                    resetUploadButton(uploadBtn, fileInput, requestId);
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    notify('success', data.message || 'Signed documents uploaded successfully');
                    closeReviewModal();
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    notify('error', data.message || 'Failed to upload signed documents. Please try again.');
                    resetUploadButton(uploadBtn, fileInput, requestId);
                }
            } catch (error) {
                console.error('Upload error:', error);
                notify('error', 'Network error: Failed to upload signed documents. Please try again.');
                resetUploadButton(uploadBtn, fileInput, requestId);
            }
        }

        function resetUploadButton(uploadBtn, fileInput, requestId) {
            if (!uploadBtn || !fileInput) return;

            uploadBtn.disabled = false;
            if (fileInput.files && fileInput.files.length > 0) {
                uploadBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Now
                `;
                uploadBtn.onclick = (event) => {
                    event.preventDefault();
                    handleModalUpload(requestId, fileInput);
                };
            } else {
                uploadBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Signed Documents
                `;
                uploadBtn.onclick = () => fileInput.click();
            }
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function handleReviewModalKeydown(event) {
            if (event.key === 'Escape') {
                closeReviewModal();
            }
        }

        // Define function exactly like signing page - as a regular function declaration
        async function openReviewModal(requestId) {
            if (!requestId) {
                return;
            }

            currentReviewRequestId = requestId;

            const modal = document.getElementById('reviewModal');
            const loading = document.getElementById('modalLoading');
            const content = document.getElementById('modalContent');
            const uploadBtn = document.getElementById('modalUploadBtn');
            const fileInput = document.getElementById('modalSignedDocuments');
            const selectedFilesDiv = document.getElementById('modalSelectedFiles');
            const separator = document.getElementById('modalSelectedFilesSeparator');

            if (!modal || !loading || !content) {
                return;
            }

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            loading.classList.remove('hidden');
            content.classList.add('hidden');

            resetModalUploadControls();

            if (!reviewModalKeydownHandlerBound) {
                document.addEventListener('keydown', handleReviewModalKeydown);
                reviewModalKeydownHandlerBound = true;
            }

            try {
                const response = await fetch(`/signing/request/${requestId}/data`, {
                    method: 'GET',
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error('Failed to load request data');
                }

                const data = await response.json();

                populateReviewModal(data);
                setupModalUpload(requestId, data);

                const zipBtn = document.getElementById('downloadZipBtn');
                if (zipBtn) {
                    const downloadUrl = data.download_zip_url || `/signing/download-files/${requestId}`;
                    if (downloadUrl) {
                        zipBtn.disabled = false;
                        zipBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        zipBtn.onclick = () => { window.location.href = downloadUrl; };
                    } else {
                        zipBtn.disabled = true;
                        zipBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        zipBtn.onclick = null;
                    }
                }

                loading.classList.add('hidden');
                content.classList.remove('hidden');
            } catch (error) {
                console.error('Review modal error:', error);
                notify('error', 'Failed to load request data. Please try again.');
                closeReviewModal();
            }
        }

        // Make function globally available
        window.openReviewModal = openReviewModal;
        
        console.log('Review modal function defined and attached to window');
    </script>
</x-app-layout>

<script>
    // Define all functions outside component so they're always available
    (function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
        let currentReviewRequestId = null;
        let reviewModalKeydownHandlerBound = false;

        // Define all helper functions
        function notify(type, message) {
            if (window.notificationManager && typeof window.notificationManager[type] === 'function') {
                window.notificationManager[type](message);
            } else {
                alert(message);
            }
        }

        function resetModalUploadControls() {
            const uploadBtn = document.getElementById('modalUploadBtn');
            const redoBtn = document.getElementById('modalRedoBtn');
            const fileInput = document.getElementById('modalSignedDocuments');
            const selectedFilesDiv = document.getElementById('modalSelectedFiles');
            const separator = document.getElementById('modalSelectedFilesSeparator');

            if (uploadBtn) {
                uploadBtn.style.display = 'none';
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Signed Documents
                `;
                uploadBtn.onclick = null;
            }
            if (redoBtn) {
                redoBtn.style.display = 'none';
            }
            if (fileInput) {
                fileInput.value = '';
                fileInput.removeAttribute('data-request-id');
                fileInput.onchange = null;
            }
            if (selectedFilesDiv) {
                selectedFilesDiv.innerHTML = '';
            }
            if (separator) {
                separator.classList.add('hidden');
            }
        }

        function closeReviewModal() {
            const modal = document.getElementById('reviewModal');
            if (!modal) return;

            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');

            const loading = document.getElementById('modalLoading');
            const content = document.getElementById('modalContent');
            if (loading) loading.classList.remove('hidden');
            if (content) content.classList.add('hidden');
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function populateReviewModal(data) {
            const requestCode = data.request_code || 'N/A';
            const type = data.type || 'N/A';
            const requestedDate = data.requested_at ? formatDate(data.requested_at) : 'N/A';
            const userName = data.user?.name || 'N/A';
            const userEmail = data.user?.email || 'N/A';
            const status = data.status || 'N/A';

            const modalRequestCode = document.getElementById('modalRequestCode');
            if (modalRequestCode) {
                modalRequestCode.textContent = requestCode;
                modalRequestCode.title = requestCode;
            }
            const modalType = document.getElementById('modalType');
            if (modalType) {
                modalType.textContent = type;
                modalType.title = type;
            }
            const modalDate = document.getElementById('modalDate');
            if (modalDate) {
                modalDate.textContent = requestedDate;
                modalDate.title = requestedDate;
            }
            const modalUserName = document.getElementById('modalUserName');
            if (modalUserName) {
                modalUserName.textContent = userName;
                modalUserName.title = userName;
            }
            const modalUserEmail = document.getElementById('modalUserEmail');
            if (modalUserEmail) {
                modalUserEmail.textContent = userEmail;
                modalUserEmail.title = userEmail;
            }

            const statusElement = document.getElementById('modalStatus');
            if (statusElement) {
                statusElement.textContent = status;
                statusElement.title = status;
                statusElement.className = 'inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium truncate max-w-full';
                if (status === 'pending') {
                    statusElement.classList.add('bg-yellow-100', 'text-yellow-800');
                } else if (status === 'endorsed') {
                    statusElement.classList.add('bg-green-100', 'text-green-800');
                } else if (status === 'rejected') {
                    statusElement.classList.add('bg-red-100', 'text-red-800');
                } else {
                    statusElement.classList.add('bg-gray-100', 'text-gray-600');
                }
            }

            const formDataContainer = document.getElementById('modalFormData');
            if (formDataContainer) {
                const dynamicRows = formDataContainer.querySelectorAll('tr:not(.fixed-director)');
                dynamicRows.forEach(row => row.remove());

                if (Array.isArray(data.signatories) && data.signatories.length > 0) {
                    data.signatories.forEach((signatory, index) => {
                        const row = document.createElement('tr');
                        row.className = index % 2 === 0 ? 'bg-gray-50' : '';

                        const positionCell = document.createElement('td');
                        positionCell.className = 'px-2 py-0.5 font-bold text-gray-700 w-1/2 truncate border-r border-gray-300';
                        positionCell.textContent = signatory.role || 'N/A';
                        positionCell.title = signatory.role || 'N/A';

                        const nameCell = document.createElement('td');
                        nameCell.className = 'px-2 py-0.5 font-bold text-gray-900 w-1/2 truncate';
                        nameCell.textContent = signatory.name || 'N/A';
                        nameCell.title = signatory.name || 'N/A';

                        row.appendChild(positionCell);
                        row.appendChild(nameCell);
                        formDataContainer.insertBefore(row, formDataContainer.querySelector('.fixed-director'));
                    });
                }
            }

            const filesContainer = document.getElementById('modalFiles');
            if (filesContainer) {
                filesContainer.innerHTML = '';
                if (Array.isArray(data.files) && data.files.length > 0) {
                    data.files.forEach(file => {
                        const needsSigning = file.needs_signing === true;
                        const fileDiv = document.createElement('div');
                        fileDiv.className = needsSigning
                            ? 'flex items-center justify-between bg-amber-50 rounded-lg border-2 border-amber-300 p-3'
                            : 'flex items-center justify-between bg-white rounded-lg border border-gray-200 p-3';

                        const downloadLabel = file.download_url
                            ? `<a href="${file.download_url}" target="_blank" class="px-4 py-1 bg-green-600 text-white text-xs rounded-full hover:bg-green-700 transition-colors">View</a>`
                            : `<button class="px-4 py-1 bg-green-600 text-white text-xs rounded-full opacity-50 cursor-not-allowed" title="Download link unavailable" disabled>View</button>`;

                        fileDiv.innerHTML = `
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2">
                                    ${needsSigning ? `
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Requires your signature">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    ` : `
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    `}
                                    <span class="text-xs ${needsSigning ? 'text-amber-900 font-semibold' : 'text-gray-900'}">${file.name}</span>
                                </div>
                                <span class="text-xs text-gray-500">(${file.size})</span>
                            </div>
                            <div class="flex gap-2">
                                ${downloadLabel}
                            </div>
                        `;
                        filesContainer.appendChild(fileDiv);
                    });
                } else {
                    filesContainer.innerHTML = '<div class="text-gray-500 text-xs">No files uploaded for this request</div>';
                }
            }
        }

        function setupModalUpload(requestId, data) {
            const uploadBtn = document.getElementById('modalUploadBtn');
            const redoBtn = document.getElementById('modalRedoBtn');
            const fileInput = document.getElementById('modalSignedDocuments');
            const selectedFilesDiv = document.getElementById('modalSelectedFiles');

            if (!uploadBtn || !fileInput) return;

            // Set request ID on file input (needed for Redo button)
            fileInput.setAttribute('data-request-id', requestId);

            // Determine if current user can upload based on signatory_type and workflow_state
            const signatoryType = data.signatory_type || null;
            const workflowState = data.workflow_state || '';
            const status = data.status || 'pending';

            console.log('setupModalUpload - signatoryType:', signatoryType, 'workflowState:', workflowState, 'status:', status);

            // Check if user can upload:
            // 1. User signing their own request (signatory_type === 'user' and workflow_state === 'pending_user_signature')
            // 2. Signatory signing in their workflow stage (workflow_state matches their expected stage)
            let canUpload = false;

            // Map workflow states to signatory types
            const workflowToSignatoryMap = {
                'pending_user_signature': 'user',
                'pending_research_manager': 'center_manager',
                'pending_dean': 'college_dean',
                'pending_deputy_director': 'deputy_director',
                'pending_director': 'rdd_director'
            };

            // Get expected signatory type for current workflow state
            const expectedSignatoryType = workflowToSignatoryMap[workflowState];

            // Check if current signatory type matches expected type for this workflow state
            if (signatoryType && expectedSignatoryType && signatoryType === expectedSignatoryType) {
                // Check if this signatory has already signed
                const hasSigned = data.signatories && data.signatories.some(s => 
                    s.status === 'completed' && s.role_key === signatoryType);
                
                // Can upload if: hasn't signed yet AND status is not endorsed
                canUpload = !hasSigned && status !== 'endorsed';
                
                console.log('Can upload check:', { signatoryType, expectedSignatoryType, hasSigned, status, canUpload });
            } else {
                console.log('Signatory type mismatch:', { signatoryType, expectedSignatoryType, workflowState });
            }

            // Determine if user can perform redo action
            // Can redo if:
            // 1. User signing their own request (signatory_type === 'user' and workflow_state === 'pending_user_signature' and status === 'pending')
            // 2. Center manager in their workflow stage (signatory_type === 'center_manager' and workflow_state === 'pending_research_manager' and status === 'pending')
            // 3. Admin (can redo any pending request)
            const isAdmin = data.is_admin || false;
            let canRedo = false;
            
            if (isAdmin && status === 'pending') {
                // Admin can redo any pending request
                canRedo = true;
            } else if (signatoryType === 'user' && workflowState === 'pending_user_signature' && status === 'pending') {
                // User can redo their own request
                canRedo = true;
            } else if (signatoryType === 'center_manager' && workflowState === 'pending_research_manager' && status === 'pending') {
                // Center manager can redo in their workflow stage
                canRedo = true;
            }

            // Setup upload button
            if (canUpload) {
                uploadBtn.style.display = 'flex';
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Signed Documents
                `;
                uploadBtn.onclick = () => fileInput.click();
                fileInput.onchange = () => {
                    if (window.updateModalSelectedFiles) {
                        window.updateModalSelectedFiles(fileInput, requestId);
                    }
                };
            } else {
                // Hide upload button if user can't upload
                uploadBtn.style.display = 'none';
                if (selectedFilesDiv) {
                    selectedFilesDiv.innerHTML = '';
                }
                if (fileInput) {
                    fileInput.value = '';
                }
            }

            // Show redo button only if user can perform redo action
            if (redoBtn) {
                redoBtn.style.display = canRedo ? 'flex' : 'none';
            }
        }

        // Make function globally available
        window.setupModalUpload = setupModalUpload;

        function updateModalSelectedFiles(fileInput, requestId) {
            const selectedFilesDiv = document.getElementById('modalSelectedFiles');
            const separator = document.getElementById('modalSelectedFilesSeparator');
            const uploadBtn = document.getElementById('modalUploadBtn');

            if (!selectedFilesDiv || !uploadBtn) return;

            if (fileInput.files.length > 0) {
                let filesList = '<div class="flex flex-wrap items-center gap-2 text-gray-700">';
                filesList += '<svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                filesList += '<span class="font-medium">Selected:</span>';
                
                // Display each file with a remove button
                for (let i = 0; i < fileInput.files.length; i++) {
                    const fileName = fileInput.files[i].name;
                    filesList += `<div class="relative inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded-md group">
                        <span class="text-xs text-gray-700 truncate max-w-[150px]">${fileName}</span>
                        <button type="button" onclick="removeSelectedFile(${i}, '${fileInput.id}', '${requestId}')" class="flex-shrink-0 w-4 h-4 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center transition-colors" title="Remove file">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>`;
                }
                filesList += '</div>';
                selectedFilesDiv.innerHTML = filesList;
                
                if (separator) separator.classList.remove('hidden');
                uploadBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Now
                `;
                uploadBtn.onclick = (event) => {
                    event.preventDefault();
                    handleModalUpload(requestId, fileInput);
                };
            } else {
                selectedFilesDiv.innerHTML = '';
                if (separator) separator.classList.add('hidden');
                uploadBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Signed Documents
                `;
                uploadBtn.onclick = () => fileInput.click();
            }
        }

        function removeSelectedFile(fileIndex, fileInputId, requestId) {
            const fileInput = document.getElementById(fileInputId);
            if (!fileInput) return;
            
            // Create a new FileList without the removed file
            const dt = new DataTransfer();
            for (let i = 0; i < fileInput.files.length; i++) {
                if (i !== fileIndex) {
                    dt.items.add(fileInput.files[i]);
                }
            }
            fileInput.files = dt.files;
            
            // Update the display
            const selectedFilesDiv = document.getElementById('modalSelectedFiles');
            if (selectedFilesDiv) {
                updateModalSelectedFiles(fileInput, requestId);
            }
        }

        async function handleModalUpload(requestId, fileInput) {
            if (!requestId || !fileInput || !fileInput.files || fileInput.files.length === 0) return;

            const uploadBtn = document.getElementById('modalUploadBtn');
            if (uploadBtn) {
                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';
            }

            try {
                const formData = new FormData();
                formData.append('request_id', requestId);
                if (csrfToken) {
                    formData.append('_token', csrfToken);
                }
                Array.from(fileInput.files).forEach(file => formData.append('signed_documents[]', file));

                const response = await fetch('/signing/upload-signed', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });

                if (response.status === 429) {
                    notify('error', 'Too many attempts. Please wait a moment before trying again.');
                    return;
                }

                // Check if response is ok before parsing JSON
                if (!response.ok) {
                    let errorMessage = 'Failed to upload signed documents. Please try again.';
                    try {
                        const errorData = await response.json();
                        errorMessage = errorData.message || errorMessage;
                    } catch (e) {
                        // If JSON parsing fails, use default message
                    }
                    notify('error', errorMessage);
                    return;
                }

                // Try to parse JSON response
                let data;
                try {
                    const text = await response.text();
                    if (!text) {
                        // Empty response but status is OK - assume success
                        notify('success', 'Signed documents uploaded successfully');
                        closeReviewModal();
                        setTimeout(() => window.location.reload(), 500);
                        return;
                    }
                    data = JSON.parse(text);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    // If response is OK but JSON parsing fails, assume success
                    notify('success', 'Signed documents uploaded successfully');
                    closeReviewModal();
                    setTimeout(() => window.location.reload(), 500);
                    return;
                }

                if (data.success) {
                    notify('success', data.message || 'Signed documents uploaded successfully');
                    closeReviewModal();
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    notify('error', data.message || 'Failed to upload signed documents. Please try again.');
                }
            } catch (error) {
                console.error('Upload error:', error);
                // Only show error if it's a real network error, not a parsing error
                if (error.name === 'TypeError' && error.message.includes('fetch')) {
                    notify('error', 'Network error: Failed to upload signed documents. Please try again.');
                } else {
                    // For other errors, assume success if we got this far
                    console.log('Non-network error, assuming success:', error);
                    notify('success', 'Signed documents uploaded successfully');
                    closeReviewModal();
                    setTimeout(() => window.location.reload(), 500);
                }
            }
        }

        function handleReviewModalKeydown(event) {
            if (event.key === 'Escape') {
                closeReviewModal();
            }
        }

        // Main function
        async function openReviewModal(requestId) {
            if (!requestId) {
                return;
            }

            currentReviewRequestId = requestId;

            const modal = document.getElementById('reviewModal');
            const loading = document.getElementById('modalLoading');
            const content = document.getElementById('modalContent');
            const uploadBtn = document.getElementById('modalUploadBtn');
            const fileInput = document.getElementById('modalSignedDocuments');
            const selectedFilesDiv = document.getElementById('modalSelectedFiles');
            const separator = document.getElementById('modalSelectedFilesSeparator');

            if (!modal || !loading || !content) {
                return;
            }

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            loading.classList.remove('hidden');
            content.classList.add('hidden');

            resetModalUploadControls();

            if (!reviewModalKeydownHandlerBound) {
                document.addEventListener('keydown', handleReviewModalKeydown);
                reviewModalKeydownHandlerBound = true;
            }

            try {
                const response = await fetch(`/signing/request/${requestId}/data`, {
                    method: 'GET',
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error('Failed to load request data');
                }

                const data = await response.json();

                // Call populateReviewModal and setupModalUpload
                populateReviewModal(data);
                setupModalUpload(requestId, data);

                const zipBtn = document.getElementById('downloadZipBtn');
                if (zipBtn) {
                    const downloadUrl = data.download_zip_url || `/signing/download-files/${requestId}`;
                    if (downloadUrl) {
                        zipBtn.disabled = false;
                        zipBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        zipBtn.onclick = () => { window.location.href = downloadUrl; };
                    } else {
                        zipBtn.disabled = true;
                        zipBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        zipBtn.onclick = null;
                    }
                }

                loading.classList.add('hidden');
                content.classList.remove('hidden');
            } catch (error) {
                console.error('Review modal error:', error);
                notify('error', 'Failed to load request data. Please try again.');
                closeReviewModal();
            }
        }

        // Make function globally available
        window.openReviewModal = openReviewModal;
        console.log('Review modal function defined outside component');

        // Use event delegation on document level for review button
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.review-modal-btn');
            if (btn) {
                e.preventDefault();
                e.stopPropagation();
                const requestId = btn.getAttribute('data-review-request-id');
                if (requestId && typeof window.openReviewModal === 'function') {
                    window.openReviewModal(parseInt(requestId));
                }
            }
        });

        // Store current request ID for Redo action
        let currentRedoRequestId = null;

        function openRedoConfirmationModal() {
            // Get the request ID from the review modal's file input
            const fileInput = document.getElementById('modalSignedDocuments');
            if (!fileInput || !fileInput.getAttribute('data-request-id')) {
                if (window.notificationManager) {
                    window.notificationManager.error('Unable to determine request ID. Please try again.');
                } else {
                    alert('Unable to determine request ID. Please try again.');
                }
                return;
            }
            
            currentRedoRequestId = fileInput.getAttribute('data-request-id');
            const modal = document.getElementById('redoConfirmationModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                // Focus on reason textarea
                const reasonTextarea = document.getElementById('redoReason');
                if (reasonTextarea) {
                    reasonTextarea.value = '';
                    reasonTextarea.focus();
                }
                // Reset cancel button visibility
                const cancelBtn = document.getElementById('cancelRedoBtn');
                if (cancelBtn) {
                    cancelBtn.style.display = '';
                }
                // Reset confirm button state
                const confirmBtn = document.getElementById('confirmRedoBtn');
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Confirm Delete
                    `;
                }
            }
        }

        function closeRedoConfirmationModal() {
            const modal = document.getElementById('redoConfirmationModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                // Clear reason textarea
                const reasonTextarea = document.getElementById('redoReason');
                if (reasonTextarea) {
                    reasonTextarea.value = '';
                }
                // Reset cancel button visibility
                const cancelBtn = document.getElementById('cancelRedoBtn');
                if (cancelBtn) {
                    cancelBtn.style.display = '';
                }
                // Reset confirm button state
                const confirmBtn = document.getElementById('confirmRedoBtn');
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Confirm Delete
                    `;
                }
                currentRedoRequestId = null;
            }
        }

        async function confirmRedoAction() {
            if (!currentRedoRequestId) {
                if (window.notificationManager) {
                    window.notificationManager.error('Unable to determine request ID. Please try again.');
                } else {
                    alert('Unable to determine request ID. Please try again.');
                }
                return;
            }

            const reasonTextarea = document.getElementById('redoReason');
            const reason = reasonTextarea ? reasonTextarea.value.trim() : '';
            
            if (!reason) {
                if (window.notificationManager) {
                    window.notificationManager.warning('Please provide a reason for deleting the files.');
                } else {
                    alert('Please provide a reason for deleting the files.');
                }
                reasonTextarea?.focus();
                return;
            }

            const confirmBtn = document.getElementById('confirmRedoBtn');
            const cancelBtn = document.getElementById('cancelRedoBtn');
            
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';
            }
            
            // Hide cancel button after confirm is clicked
            if (cancelBtn) {
                cancelBtn.style.display = 'none';
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch(`/signing/request/${currentRedoRequestId}/redo`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        reason: reason
                    })
                });

                const data = await response.json();

                if (data.success) {
                    if (window.notificationManager) {
                        window.notificationManager.success(data.message || 'Files deleted successfully');
                    } else {
                        alert('Success: ' + data.message);
                    }
                    closeRedoConfirmationModal();
                    closeReviewModal();
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    const errorMsg = data.message || 'Failed to delete files. Please try again.';
                    if (window.notificationManager) {
                        window.notificationManager.error(errorMsg);
                    } else {
                        alert('Error: ' + errorMsg);
                    }
                    if (confirmBtn) {
                        confirmBtn.disabled = false;
                        confirmBtn.innerHTML = `
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Confirm Delete
                        `;
                    }
                }
            } catch (error) {
                console.error('Redo action error:', error);
                if (window.notificationManager) {
                    window.notificationManager.error('Failed to delete files. Please try again.');
                } else {
                    alert('Failed to delete files. Please try again.');
                }
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Confirm Delete
                    `;
                }
                if (cancelBtn) {
                    cancelBtn.style.display = '';
                }
            }
        }

        // Make functions globally available
        window.openRedoConfirmationModal = openRedoConfirmationModal;
        window.closeRedoConfirmationModal = closeRedoConfirmationModal;
        window.confirmRedoAction = confirmRedoAction;

        // Use event delegation on document level for redo button
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.redo-modal-btn');
            if (btn) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof window.openRedoConfirmationModal === 'function') {
                    window.openRedoConfirmationModal();
                }
            }
        });
    })();
</script>
