<x-app-layout>
    <!-- Global Notifications -->
    <x-global-notifications />
    
    <div class="h-screen bg-gray-50 flex overflow-hidden" style="scrollbar-gutter: stable;">
        @include('components.user-sidebar')

        <!-- Main Content -->
        <div class="flex-1 h-screen overflow-y-auto force-scrollbar">
            <!-- Content Area -->
            <main class="max-w-7xl mx-auto px-4 pt-2 pb-4 h-full flex flex-col main-content">
                <!-- Dashboard Header with Modern Compact Filters -->
                <div class="relative flex items-center justify-between mb-4 flex-shrink-0">
                    <!-- Overview Header with Request Counter -->
                    <div class="flex items-center gap-3 text-md font-semibold text-gray-600 bg-gray-50 px-3 py-2.5 rounded-lg h-10">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20v-6m0 0l-3 3m3-3l3 3M5 8l7-3 7 3-7 3-7-3z"/>
                        </svg>
                        <span>Signature Requests</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                            <div class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1.5 animate-pulse"></div>
                            {{ count($requests) }}
                        </span>
                    </div>
                    
                    <!-- Enhanced Search and User Controls -->
                    @include('components.user-navbar', ['showFilters' => false, 'showRoleBadge' => true])
                </div>

                <!-- Signature Requests Table Container -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex-1 flex flex-col" style="height: calc(100vh - 8rem);">
                            
                            <!-- Table Header (Fixed) -->
                            <div class="bg-gray-50 border-b border-gray-200 flex-shrink-0">
                                <div class="overflow-x-auto">
                                    <table class="w-full table-fixed">
                                        <thead>
                                            <tr>
                                                <th class="w-40 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center justify-center gap-1">
                                                        ID
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="w-20 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center justify-center gap-1">
                                                        Type
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="w-40 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center gap-1">
                                                        College
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="w-20 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center justify-center gap-1">
                                                        Date
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="w-24 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center justify-center gap-1">
                                                        Request Status
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="w-24 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center justify-center gap-1">
                                                        Signature Status
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
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
                                @if(count($requests) > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full table-fixed divide-y divide-gray-200">
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($requests as $index => $request)
                                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                        <td class="w-40 px-6 py-4 whitespace-nowrap text-center">
                                                            <span class="text-sm font-medium text-gray-900">{{ $request['request_code'] }}</span>
                                                        </td>
                                                        <td class="w-20 px-6 py-4 whitespace-nowrap">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                {{ $request['type'] === 'Publication' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                                {{ $request['type'] }}
                                                            </span>
                                                        </td>
                                                        <td class="w-40 px-6 py-4 whitespace-nowrap text-sm text-gray-900 overflow-hidden">
                                                            <span class="block truncate max-w-full">{{ $request['college'] ?: '—' }}</span>
                                                        </td>
                                                        <td class="w-20 px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ \Carbon\Carbon::parse($request['requested_at'])->format('M d, Y') }}
                                                        </td>
                                                        <td class="w-24 px-6 py-4 whitespace-nowrap text-center">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                <div class="w-2 h-2 bg-yellow-400 rounded-full mr-2 animate-pulse"></div>
                                                                Pending
                                                            </span>
                                                        </td>
                                                        <td class="w-24 px-6 py-4 whitespace-nowrap text-center">
                                                            @if($request['signature_status'] === 'signed')
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                    <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                                                    Signed
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                    <div class="w-2 h-2 bg-gray-400 rounded-full mr-2"></div>
                                                                    Pending
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="w-24 px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                            @if($request['signature_status'] === 'signed')
                                                                <div class="flex flex-col gap-2">
                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                                                        Signed
                                                                    </span>
                                                                    @if($request['can_revert'])
                                                                        <button onclick="revertDocument({{ $request['id'] }})" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-200 text-xs font-medium">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                                                            </svg>
                                                                            Revert
                                                                        </button>
                                                                    @else
                                                                        <span class="text-xs text-gray-500 italic">Cannot revert after 24 hours</span>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <div class="flex flex-col items-center gap-2">
                                                                    <button onclick="openReviewModal({{ $request['id'] }})" class="inline-flex items-center justify-center gap-1 w-full px-4 py-2 rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 transition-all duration-200 text-xs font-medium">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                        </svg>
                                                                        Review
                                                                    </button>
                                                                    @if($signatoryType !== 'center_manager')
                                                                        <button onclick="openUploadModal({{ $request['id'] }})" class="inline-flex items-center justify-center gap-1 w-full px-4 py-2 rounded-full bg-maroon-100 text-maroon-700 hover:bg-maroon-200 transition-all duration-200 text-xs font-medium">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                                                            </svg>
                                                                            Upload Signed
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <!-- Empty State (Centered) -->
                                    <div class="h-full flex items-center justify-center">
                                        <div class="flex flex-col items-center justify-center gap-3 text-center">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20v-6m0 0l-3 3m3-3l3 3M4 6h16M4 10h16M4 14h16"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900">No pending requests</h4>
                                                <p class="text-gray-500">Signature requests will appear here when they need your attention.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Pagination Footer (Fixed) -->
                            <div class="bg-white px-6 py-3 border-t border-gray-200 flex-shrink-0">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-700">
                                        Showing <span class="font-medium">1</span> to <span class="font-medium">{{ count($requests) }}</span> of <span class="font-medium">{{ count($requests) }}</span> results
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
                            <button id="modalRedoBtn" onclick="openRedoConfirmationModal()" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-medium flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Redo
                            </button>
                            <button id="modalUploadBtn" class="px-4 py-2 bg-maroon-700 text-white rounded-lg hover:bg-maroon-800 transition-colors font-medium flex items-center gap-2 text-sm">
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

    <!-- Upload Signed Documents Modal -->
    <div id="uploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-maroon-100">
                    <svg class="h-6 w-6 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Upload Signed Documents</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Upload the signed documents. They will replace the original files with the same names.
                    </p>
                    
                    <!-- Request Details -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Request Details:</label>
                        <div id="uploadRequestDetailsDisplay" class="text-sm text-gray-600 space-y-1">
                            <!-- Will show request details -->
                        </div>
                    </div>
                    
                    <!-- File Upload Area -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Signed Documents:</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-maroon-400 transition-colors">
                            <input type="file" id="signedDocuments" name="signed_documents[]" multiple accept=".pdf,.docx" class="hidden">
                            <button type="button" onclick="document.getElementById('signedDocuments').click()" class="text-maroon-600 hover:text-maroon-700 font-medium">
                                Click to select files
                            </button>
                            <p class="text-xs text-gray-500 mt-1">PDF, DOCX files only (max 10MB each)</p>
                            <div id="selectedFiles" class="mt-2 text-sm text-gray-600"></div>
                        </div>
                    </div>
                </div>
                
                <div class="items-center px-4 py-3">
                    <button id="confirmUploadBtn" class="px-4 py-2 bg-maroon-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-maroon-600 focus:outline-none focus:ring-2 focus:ring-maroon-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Upload Signed Documents
                    </button>
                    <button onclick="closeUploadModal()" class="mt-2 px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay - Now handled by simple loading system -->
    
    
    <script>
        let currentReviewRequestId = null;
        let reviewModalKeydownHandlerBound = false;

        async function openReviewModal(requestId) {
            if (!requestId) {
                return;
            }

            currentReviewRequestId = requestId;

            const modal = document.getElementById('reviewModal');
            const loading = document.getElementById('modalLoading');
            const content = document.getElementById('modalContent');
            const footer = document.getElementById('modalFooter');

            if (!modal || !loading || !content || !footer) {
                return;
            }

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            loading.classList.remove('hidden');
            content.classList.add('hidden');
            
            // Initialize upload button state (hide until data loads)
            const uploadBtn = document.getElementById('modalUploadBtn');
            const fileInput = document.getElementById('modalSignedDocuments');
            const selectedFilesDiv = document.getElementById('modalSelectedFiles');
            if (uploadBtn) {
                uploadBtn.style.display = 'none';
            }
            if (fileInput) {
                fileInput.value = '';
            }
            if (selectedFilesDiv) {
                selectedFilesDiv.innerHTML = '';
            }

            if (!reviewModalKeydownHandlerBound) {
                document.addEventListener('keydown', handleReviewModalKeydown);
                reviewModalKeydownHandlerBound = true;
            }

            try {
                const response = await fetch(`/signing/request/${requestId}/data`, {
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error('Failed to load request data');
                }

                const data = await response.json();
                populateReviewModal(data, requestId);

                const zipBtn = document.getElementById('downloadZipBtn');
                if (zipBtn) {
                    const downloadUrl = data.download_zip_url || `/signing/download-files/${requestId}`;
                    if (downloadUrl) {
                        zipBtn.disabled = false;
                        zipBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        zipBtn.onclick = () => {
                            window.location.href = downloadUrl;
                        };
                    } else {
                        zipBtn.disabled = true;
                        zipBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        zipBtn.onclick = null;
                    }
                }
                
                // Set up upload functionality
                setupModalUpload(requestId, data);

            } catch (error) {
                console.error('Review modal error:', error);
                if (window.notificationManager) {
                    window.notificationManager.error('Failed to load request data. Please try again.');
                } else {
                    alert('Failed to load request data. Please try again.');
                }
            } finally {
                loading.classList.add('hidden');
                content.classList.remove('hidden');
                // Footer is always visible, no need to show it
            }
        }

        function closeReviewModal() {
            const modal = document.getElementById('reviewModal');
            if (!modal) {
                return;
            }

            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');

            const loading = document.getElementById('modalLoading');
            const content = document.getElementById('modalContent');

            if (loading) {
                loading.classList.remove('hidden');
            }
            if (content) {
                content.classList.add('hidden');
            }

            // Reset modal content
            document.getElementById('modalRequestCode').textContent = '-';
            document.getElementById('modalType').textContent = '-';
            document.getElementById('modalStatus').textContent = '-';
            document.getElementById('modalDate').textContent = '-';
            document.getElementById('modalUserName').textContent = '-';
            document.getElementById('modalUserEmail').textContent = '-';
            // Clear only dynamic signatories, preserve fixed directors
            const formDataContainer = document.getElementById('modalFormData');
            const dynamicRows = formDataContainer.querySelectorAll('tr:not(.fixed-director)');
            dynamicRows.forEach(row => row.remove());

            const filesContainer = document.getElementById('modalFiles');
            if (filesContainer) {
                filesContainer.innerHTML = '<div class="text-gray-500">No files uploaded for this request.</div>';
            }
            
            // Reset upload button state
            const fileInput = document.getElementById('modalSignedDocuments');
            const uploadBtn = document.getElementById('modalUploadBtn');
            const selectedFilesDiv = document.getElementById('modalSelectedFiles');
            const separator = document.getElementById('modalSelectedFilesSeparator');
            
            if (fileInput) {
                fileInput.value = '';
                fileInput.removeAttribute('data-request-id');
            }
            
            if (selectedFilesDiv) {
                selectedFilesDiv.innerHTML = '';
            }
            
            // Hide separator when modal is closed
            if (separator) {
                separator.classList.add('hidden');
            }
            
            // Reset upload button to initial state
            if (uploadBtn) {
                uploadBtn.style.display = 'none'; // Will be shown by setupModalUpload if needed
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Signed Documents
                `;
                uploadBtn.onclick = null;
            }

            currentReviewRequestId = null;

            if (reviewModalKeydownHandlerBound) {
                document.removeEventListener('keydown', handleReviewModalKeydown);
                reviewModalKeydownHandlerBound = false;
            }
        }

        function populateReviewModal(data, requestId) {
            // Populate basic info
            const requestCode = data.request_code || 'N/A';
            const type = data.type || 'N/A';
            const date = formatDate(data.requested_at);
            const userName = data.user?.name || 'N/A';
            const userEmail = data.user?.email || 'N/A';
            
            document.getElementById('modalRequestCode').textContent = requestCode;
            document.getElementById('modalRequestCode').title = requestCode;
            document.getElementById('modalType').textContent = type;
            document.getElementById('modalType').title = type;
            document.getElementById('modalDate').textContent = date;
            document.getElementById('modalDate').title = date;
            document.getElementById('modalUserName').textContent = userName;
            document.getElementById('modalUserName').title = userName;
            document.getElementById('modalUserEmail').textContent = userEmail;
            document.getElementById('modalUserEmail').title = userEmail;
            
            // Status
            const statusElement = document.getElementById('modalStatus');
            const status = data.status || 'N/A';
            statusElement.textContent = status;
            statusElement.title = status;
            statusElement.className = 'inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium truncate max-w-full';
            if (data.status === 'pending') {
                statusElement.classList.add('bg-yellow-100', 'text-yellow-800');
            } else if (data.status === 'endorsed') {
                statusElement.classList.add('bg-green-100', 'text-green-800');
            } else if (data.status === 'rejected') {
                statusElement.classList.add('bg-red-100', 'text-red-800');
            }
            
            // Signatories
            const formDataContainer = document.getElementById('modalFormData');
            
            // Clear only dynamic signatories (first rows), preserve fixed directors
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
                    const type = file.type;
                    const key = file.key;
                    const needsSigning = file.needs_signing === true;
                    
                    // Use secure download URL if available, otherwise fallback with correct pluralized type
                    const downloadUrl = file.download_url || null;
                    const hasUrl = !!downloadUrl;
                    
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
                    
                    // Highlight files that need signing with colored background
                    const fileDiv = document.createElement('div');
                    if (needsSigning) {
                        fileDiv.className = 'flex items-center justify-between bg-amber-50 rounded-lg border-2 border-amber-300 p-3';
                    } else {
                        fileDiv.className = 'flex items-center justify-between bg-white rounded-lg border border-gray-200 p-3';
                    }
                    
                    fileDiv.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                ${needsSigning ? `<svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Requires your signature">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>` : `<svg class="w-4 h-4 ${iconClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>`}
                                <span class="text-xs ${needsSigning ? 'text-amber-900 font-semibold' : 'text-gray-900'}">${file.name}</span>
                            </div>
                            <span class="text-xs text-gray-500">(${file.size})</span>
                        </div>
                        <div class="flex gap-2">
                            ${hasUrl ? `<a href="${downloadUrl}" target="_blank" class="px-4 py-1 bg-green-600 text-white text-xs rounded-full hover:bg-green-700 transition-colors">View</a>` 
                                     : `<button class="px-4 py-1 bg-green-600 text-white text-xs rounded-full opacity-50 cursor-not-allowed" title="Download link unavailable" disabled>View</button>`}
                        </div>
                    `;
                    filesContainer.appendChild(fileDiv);
                });
            } else {
                filesContainer.innerHTML = '<div class="text-gray-500 text-xs">No files uploaded for this request</div>';
            }
        }

        function setupModalUpload(requestId, data) {
            const uploadBtn = document.getElementById('modalUploadBtn');
            const redoBtn = document.getElementById('modalRedoBtn');
            const fileInput = document.getElementById('modalSignedDocuments');
            const selectedFilesDiv = document.getElementById('modalSelectedFiles');
            
            if (!fileInput) {
                return;
            }
            
            // Set request ID on file input (needed for Redo button)
            fileInput.setAttribute('data-request-id', requestId);
            
            // Check if center manager can upload (only if workflow is in their stage and they haven't signed)
            const centerManagerHasSigned = data.signatories && data.signatories.some(s => 
                s.status === 'completed' && s.role_key === 'center_manager');
            const isInCenterManagerStage = data.workflow_state === 'pending_research_manager';
            const canUpload = isInCenterManagerStage && !centerManagerHasSigned && data.status !== 'endorsed';
            
            // Setup upload button
            if (uploadBtn) {
                if (!canUpload) {
                    uploadBtn.style.display = 'none';
                    if (selectedFilesDiv) {
                        selectedFilesDiv.innerHTML = '';
                    }
                    // Clear file input
                    if (fileInput) {
                        fileInput.value = '';
                    }
                } else {
                    // Show upload button
                    uploadBtn.style.display = 'flex';
                    uploadBtn.disabled = false;
                    
                    // Reset button to initial state
                    uploadBtn.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Upload Signed Documents
                    `;
                    
                    // Set up button click to open file picker
                    uploadBtn.onclick = () => {
                        fileInput.click();
                    };
                    
                    // Handle file selection
                    fileInput.onchange = function() {
                        updateModalSelectedFiles(this, selectedFilesDiv, requestId);
                    };
                }
            }
            
            // Redo button is always visible - no visibility logic needed
        }

        function updateModalSelectedFiles(fileInput, selectedFilesDiv, requestId) {
            if (!fileInput || !selectedFilesDiv) return;
            
            const separator = document.getElementById('modalSelectedFilesSeparator');
            
            const uploadBtn = document.getElementById('modalUploadBtn');
            
            if (fileInput.files.length > 0) {
                let filesList = '<div class="flex items-center gap-2 text-gray-700"><svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="font-medium">Selected:</span> ';
                const fileNames = [];
                for (let i = 0; i < fileInput.files.length; i++) {
                    fileNames.push(fileInput.files[i].name);
                }
                filesList += '<span class="text-gray-600">' + fileNames.join(', ') + '</span></div>';
                selectedFilesDiv.innerHTML = filesList;
                
                // Show separator when files are selected
                if (separator) {
                    separator.classList.remove('hidden');
                }
                
                // Update button to show upload action
                if (uploadBtn) {
                    uploadBtn.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Upload Now
                    `;
                    uploadBtn.onclick = (e) => {
                        e.preventDefault();
                        handleModalUpload(requestId, fileInput);
                    };
                }
            } else {
                selectedFilesDiv.innerHTML = '';
                
                // Hide separator when no files are selected
                if (separator) {
                    separator.classList.add('hidden');
                }
                
                // Reset button to file picker
                if (uploadBtn) {
                    uploadBtn.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Upload Signed Documents
                    `;
                    uploadBtn.onclick = () => {
                        fileInput.click();
                    };
                }
            }
        }

        async function handleModalUpload(requestId, fileInput) {
            if (!requestId || !fileInput || !fileInput.files || fileInput.files.length === 0) {
                return;
            }

            // Show loading state
            const uploadBtn = document.getElementById('modalUploadBtn');
            if (uploadBtn) {
                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';
            }

            try {
                const formData = new FormData();
                formData.append('request_id', requestId);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Add all selected files
                for (let i = 0; i < fileInput.files.length; i++) {
                    formData.append('signed_documents[]', fileInput.files[i]);
                }

                const response = await fetch('/signing/upload-signed', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                // Handle rate limiting
                if (response.status === 429) {
                    const errorMsg = 'Too many attempts. Please wait a moment before trying again.';
                    if (window.notificationManager) {
                        window.notificationManager.error(errorMsg);
                    } else {
                        alert(errorMsg);
                    }
                    if (uploadBtn) {
                        uploadBtn.disabled = false;
                        uploadBtn.innerHTML = `
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Upload Signed Documents
                        `;
                        uploadBtn.onclick = () => {
                            fileInput.click();
                        };
                    }
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    if (window.notificationManager) {
                        window.notificationManager.success(data.message || 'Signed documents uploaded successfully');
                    } else {
                        alert('Success: ' + data.message);
                    }
                    // Close modal and refresh page
                    closeReviewModal();
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    // Show error message
                    const errorMsg = data.message || 'Failed to upload signed documents. Please try again.';
                    if (window.notificationManager) {
                        window.notificationManager.error(errorMsg);
                    } else {
                        alert('Error: ' + errorMsg);
                    }
                    
                    // Reset button state properly
                    if (uploadBtn) {
                        uploadBtn.disabled = false;
                        // Check if files are still selected
                        if (fileInput && fileInput.files && fileInput.files.length > 0) {
                            uploadBtn.innerHTML = `
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Upload Now
                            `;
                            uploadBtn.onclick = (e) => {
                                e.preventDefault();
                                handleModalUpload(requestId, fileInput);
                            };
                        } else {
                            uploadBtn.innerHTML = `
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Upload Signed Documents
                            `;
                            uploadBtn.onclick = () => {
                                fileInput.click();
                            };
                        }
                    }
                }
            } catch (error) {
                console.error('Upload error:', error);
                let errorMessage = 'Network error: Failed to upload signed documents. Please check your connection and try again.';
                
                // Check for rate limiting
                if (error.message && error.message.includes('429')) {
                    errorMessage = 'Too many attempts. Please wait a moment before trying again.';
                }
                
                if (window.notificationManager) {
                    window.notificationManager.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
                
                // Reset button state properly
                if (uploadBtn) {
                    uploadBtn.disabled = false;
                    // Check if files are still selected
                    if (fileInput && fileInput.files && fileInput.files.length > 0) {
                        uploadBtn.innerHTML = `
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Upload Now
                        `;
                        uploadBtn.onclick = (e) => {
                            e.preventDefault();
                            handleModalUpload(requestId, fileInput);
                        };
                    } else {
                        uploadBtn.innerHTML = `
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Upload Signed Documents
                        `;
                        uploadBtn.onclick = () => {
                            fileInput.click();
                        };
                    }
                }
            }
        }

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
                const response = await fetch(`/signing/request/${currentRedoRequestId}/redo`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
            }
        }

        // Handle ESC key to close Redo modal
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const redoModal = document.getElementById('redoConfirmationModal');
                if (redoModal && !redoModal.classList.contains('hidden')) {
                    closeRedoConfirmationModal();
                }
            }
        });

        function formatWorkflowState(state) {
            if (!state) {
                return '—';
            }

            const mapping = {
                pending_research_manager: 'Pending Research Center Manager',
                pending_faculty: 'Pending Faculty',
                pending_dean: 'Pending College Dean',
                pending_deputy_director: 'Pending Deputy Director',
                pending_director: 'Pending RDD Director',
                completed: 'Completed',
            };

            if (Object.prototype.hasOwnProperty.call(mapping, state)) {
                return mapping[state];
            }

            return state.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
        }

        function formatSignatoryStatus(status) {
            const variants = {
                completed: { label: 'Completed', classes: 'bg-green-100 text-green-700' },
                current: { label: 'Awaiting Your Action', classes: 'bg-amber-100 text-amber-700' },
                pending: { label: 'Awaiting Previous Step', classes: 'bg-blue-100 text-blue-700' },
                upcoming: { label: 'Upcoming', classes: 'bg-gray-100 text-gray-600' },
            };

            return variants[status] || { label: 'Pending', classes: 'bg-gray-100 text-gray-600' };
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

        function handleReviewModalKeydown(event) {
            if (event.key === 'Escape') {
                closeReviewModal();
            }
        }

        let currentRequestId = null;

        function downloadRequestFiles(requestId) {
            
            // Use direct GET request like admin does
            window.location.href = `/signing/download-files/${requestId}`;
        }

        function openUploadModal(requestId) {
            
            // Allow multiple signatures - removed restriction
            
            currentRequestId = requestId;
            
            // Update modal with request details
            updateUploadModalDisplay();
            document.getElementById('uploadModal').classList.remove('hidden');
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
            currentRequestId = null;
            // Clear file selection
            document.getElementById('signedDocuments').value = '';
            document.getElementById('selectedFiles').innerHTML = '';
        }




        function updateUploadModalDisplay() {
            // Update request details display
            const requestDisplay = document.getElementById('uploadRequestDetailsDisplay');
            const request = window.requestsData?.find(r => r.id == currentRequestId);
            if (request) {
                requestDisplay.innerHTML = `
                    <div><strong>Request ID:</strong> ${request.request_code}</div>
                    <div><strong>Type:</strong> ${request.type}</div>
                    <div><strong>Role:</strong> ${request.matched_role.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</div>
                    <div><strong>Date:</strong> ${new Date(request.requested_at).toLocaleDateString()}</div>
                `;
            }
        }

        function updateSelectedFiles() {
            const fileInput = document.getElementById('signedDocuments');
            const selectedFilesDiv = document.getElementById('selectedFiles');
            
            if (fileInput.files.length > 0) {
                let filesList = '<div class="text-left"><strong>Selected files:</strong><ul class="mt-1">';
                for (let i = 0; i < fileInput.files.length; i++) {
                    const file = fileInput.files[i];
                    filesList += `<li class="text-xs">• ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</li>`;
                }
                filesList += '</ul></div>';
                selectedFilesDiv.innerHTML = filesList;
            } else {
                selectedFilesDiv.innerHTML = '';
            }
        }

        async function confirmUpload() {
            if (!currentRequestId) {
                if (window.notificationManager) {
                    window.notificationManager.error('No request selected.');
                } else {
                    alert('No request selected.');
                }
                return;
            }

            // Allow multiple signatures - removed restriction

            const fileInput = document.getElementById('signedDocuments');
            if (!fileInput.files || fileInput.files.length === 0) {
                if (window.notificationManager) {
                    window.notificationManager.warning('Please select at least one file to upload.');
                } else {
                    alert('Please select at least one file to upload.');
                }
                return;
            }

            // Show loading state
            window.showLoading('Signing Document', 'Please wait while we apply your signature...');
            document.getElementById('uploadModal').classList.add('hidden');

            try {
                const formData = new FormData();
                formData.append('request_id', currentRequestId);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Add all selected files
                for (let i = 0; i < fileInput.files.length; i++) {
                    formData.append('signed_documents[]', fileInput.files[i]);
                }

                const response = await fetch('/signing/upload-signed', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                // Handle rate limiting
                if (response.status === 429) {
                    const errorMsg = 'Too many attempts. Please wait a moment before trying again.';
                    if (window.notificationManager) {
                        window.notificationManager.error(errorMsg);
                    } else {
                        alert(errorMsg);
                    }
                    window.hideLoading();
                    document.getElementById('uploadModal').classList.remove('hidden');
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    if (window.notificationManager) {
                        window.notificationManager.success(data.message || 'Signed documents uploaded successfully');
                    } else {
                        alert('Success: ' + data.message);
                    }
                    // Refresh the page to show updated status
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (window.notificationManager) {
                        window.notificationManager.error(data.message || 'Failed to upload signed documents. Please try again.');
                    } else {
                        alert('Error: ' + data.message);
                    }
                    // Hide loading state
                    window.hideLoading();
                    document.getElementById('uploadModal').classList.remove('hidden');
                }
            } catch (error) {
                if (window.notificationManager) {
                    window.notificationManager.error('Failed to upload signed documents. Please try again.');
                } else {
                    alert('Failed to upload signed documents. Please try again.');
                }
                // Hide loading state
                window.hideLoading();
                document.getElementById('uploadModal').classList.remove('hidden');
            }
        }

        async function revertDocument(requestId) {
            if (!confirm('Are you sure you want to revert this document? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch('/signing/revert-document', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        request_id: requestId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    if (window.notificationManager) {
                        window.notificationManager.success(data.message || 'Document reverted successfully');
                    } else {
                        alert('Success: ' + data.message);
                    }
                    // Refresh the page to show updated status
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (window.notificationManager) {
                        window.notificationManager.error(data.message || 'Failed to revert document. Please try again.');
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            } catch (error) {
                if (window.notificationManager) {
                    window.notificationManager.error('Failed to revert document. Please try again.');
                } else {
                    alert('Failed to revert document. Please try again.');
                }
            }
        }

        // Store requests data globally for modal display
        window.requestsData = @json($requests);
        
        // Initialize page functionality
        document.addEventListener('DOMContentLoaded', function() {
            
            // Set up file input change event for upload modal
            const signedDocumentsInput = document.getElementById('signedDocuments');
            if (signedDocumentsInput) {
                signedDocumentsInput.addEventListener('change', updateSelectedFiles);
            }
            
            // Bind confirm buttons
            const confirmUploadBtn = document.getElementById('confirmUploadBtn');
            if (confirmUploadBtn) {
                confirmUploadBtn.onclick = confirmUpload;
            }
        });
    </script>
    
    <style>
        /* Table scrollbar styling to match dashboard */
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
    </style>
</x-app-layout> 