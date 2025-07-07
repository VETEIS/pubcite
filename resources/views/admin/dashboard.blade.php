<x-app-layout>
    <div x-data="adminDashboard()" class="min-h-[calc(100vh-4rem)] flex items-center justify-center">
        <div class="w-full max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8 rounded-lg relative">
                <!-- Notification Area: absolutely positioned, overlay -->
                <div class="absolute top-0 right-0 z-20 mt-2 mr-4">
                    @if(session('success'))
                        <div class="text-green-700 bg-green-100 border border-green-200 rounded px-3 py-1 text-xs font-medium shadow">{{ session('success') }}</div>
                    @elseif(session('error'))
                        <div class="text-red-700 bg-red-100 border border-red-200 rounded px-3 py-1 text-xs font-medium shadow">{{ session('error') }}</div>
                    @endif
                </div>
                <div x-show="hasActiveFilters" x-cloak class="fixed md:absolute top-6 right-8 z-30">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-xs font-semibold shadow transition focus:outline-none" style="box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        Clear Filter
                    </a>
                </div>
                
                <!-- Full-width Graphical Counter Tracker -->
                <div class="w-full flex flex-col sm:flex-row gap-2 sm:gap-4 mb-6">
                    <!-- Publication Requests Card -->
                    <div class="flex-1 bg-white rounded-xl shadow border p-2 sm:p-4 flex items-center gap-2 sm:gap-4 min-w-0">
                        <div class="flex items-center gap-1 sm:gap-2 min-w-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-maroon-100 flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-maroon-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </div>
                            <a href="?type=Publication" class="font-semibold text-maroon-800 text-sm sm:text-base truncate hover:underline hover:text-maroon-600 transition text-left {{ (request('type') === 'Publication') ? 'underline text-maroon-700' : '' }}">
                                Publication Requests
                            </a>
                        </div>
                        <div class="flex flex-1 justify-end gap-2 sm:gap-6 min-w-0">
                            <div class="flex flex-row gap-2 w-full justify-end">
                                <a href="?type=Publication&period=week" class="flex flex-col items-center justify-center min-w-0 bg-maroon-50 border border-maroon-200 rounded-lg shadow-sm py-1.5 px-3 transition cursor-pointer hover:bg-maroon-100 focus:ring-2 focus:ring-maroon-300 {{ (request('type') === 'Publication' && request('period') === 'week') ? 'ring-2 ring-maroon-400' : '' }}">
                                    <span class="text-base font-bold text-maroon-700">{{ $stats['publication']['week'] }}</span>
                                    <span class="text-xs text-maroon-800 font-semibold tracking-wide {{ (request('type') === 'Publication' && request('period') === 'week') ? 'text-maroon-600 font-medium' : '' }}">This Week</span>
                                </a>
                                <a href="?type=Publication&period=month" class="flex flex-col items-center justify-center min-w-0 bg-maroon-50 border border-maroon-200 rounded-lg shadow-sm py-1.5 px-3 transition cursor-pointer hover:bg-maroon-100 focus:ring-2 focus:ring-maroon-300 {{ (request('type') === 'Publication' && request('period') === 'month') ? 'ring-2 ring-maroon-400' : '' }}">
                                    <span class="text-base font-bold text-maroon-700">{{ $stats['publication']['month'] }}</span>
                                    <span class="text-xs text-maroon-800 font-semibold tracking-wide {{ (request('type') === 'Publication' && request('period') === 'month') ? 'text-maroon-600 font-medium' : '' }}">This Month</span>
                                </a>
                                <a href="?type=Publication&period=quarter" class="flex flex-col items-center justify-center min-w-0 bg-maroon-50 border border-maroon-200 rounded-lg shadow-sm py-1.5 px-3 transition cursor-pointer hover:bg-maroon-100 focus:ring-2 focus:ring-maroon-300 {{ (request('type') === 'Publication' && request('period') === 'quarter') ? 'ring-2 ring-maroon-400' : '' }}">
                                    <span class="text-base font-bold text-maroon-700">{{ $stats['publication']['quarter'] }}</span>
                                    <span class="text-xs text-maroon-800 font-semibold tracking-wide {{ (request('type') === 'Publication' && request('period') === 'quarter') ? 'text-maroon-600 font-medium' : '' }}">This Quarter</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Citation Requests Card -->
                    <div class="flex-1 bg-white rounded-xl shadow border p-2 sm:p-4 flex items-center gap-2 sm:gap-4 min-w-0">
                        <div class="flex items-center gap-1 sm:gap-2 min-w-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-burgundy-100 flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-burgundy-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </div>
                            <a href="?type=Citation" class="font-semibold text-burgundy-800 text-sm sm:text-base truncate hover:underline hover:text-burgundy-600 transition text-left {{ (request('type') === 'Citation') ? 'underline text-burgundy-700' : '' }}">
                                Citation Requests
                            </a>
                        </div>
                        <div class="flex flex-1 justify-end gap-2 sm:gap-6 min-w-0">
                            <div class="flex flex-row gap-2 w-full justify-end">
                                <a href="?type=Citation&period=week" class="flex flex-col items-center justify-center min-w-0 bg-burgundy-50 border border-burgundy-200 rounded-lg shadow-sm py-1.5 px-3 transition cursor-pointer hover:bg-burgundy-100 focus:ring-2 focus:ring-burgundy-300 {{ (request('type') === 'Citation' && request('period') === 'week') ? 'ring-2 ring-burgundy-400' : '' }}">
                                    <span class="text-base font-bold text-burgundy-700">{{ $stats['citation']['week'] }}</span>
                                    <span class="text-xs text-burgundy-800 font-semibold tracking-wide {{ (request('type') === 'Citation' && request('period') === 'week') ? 'text-burgundy-600 font-medium' : '' }}">This Week</span>
                                </a>
                                <a href="?type=Citation&period=month" class="flex flex-col items-center justify-center min-w-0 bg-burgundy-50 border border-burgundy-200 rounded-lg shadow-sm py-1.5 px-3 transition cursor-pointer hover:bg-burgundy-100 focus:ring-2 focus:ring-burgundy-300 {{ (request('type') === 'Citation' && request('period') === 'month') ? 'ring-2 ring-burgundy-400' : '' }}">
                                    <span class="text-base font-bold text-burgundy-700">{{ $stats['citation']['month'] }}</span>
                                    <span class="text-xs text-burgundy-800 font-semibold tracking-wide {{ (request('type') === 'Citation' && request('period') === 'month') ? 'text-burgundy-600 font-medium' : '' }}">This Month</span>
                                </a>
                                <a href="?type=Citation&period=quarter" class="flex flex-col items-center justify-center min-w-0 bg-burgundy-50 border border-burgundy-200 rounded-lg shadow-sm py-1.5 px-3 transition cursor-pointer hover:bg-burgundy-100 focus:ring-2 focus:ring-burgundy-300 {{ (request('type') === 'Citation' && request('period') === 'quarter') ? 'ring-2 ring-burgundy-400' : '' }}">
                                    <span class="text-base font-bold text-burgundy-700">{{ $stats['citation']['quarter'] }}</span>
                                    <span class="text-xs text-burgundy-800 font-semibold tracking-wide {{ (request('type') === 'Citation' && request('period') === 'quarter') ? 'text-burgundy-600 font-medium' : '' }}">This Quarter</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8">
                    <div class="flex gap-2 relative w-full mb-4">
                        <div class="flex bg-gray-100 rounded-full p-1 gap-1">
                            <a href="?{{ http_build_query(array_merge(request()->except(['status']), ['status' => ''])) }}" class="px-4 py-1 rounded-full font-semibold text-xs transition relative flex items-center focus:outline-none {{ !request('status') ? 'bg-maroon-700 text-white shadow' : 'bg-white text-gray-700 hover:bg-maroon-600 hover:text-white border border-gray-200' }}">All</a>
                            <a href="?{{ http_build_query(array_merge(request()->except(['status']), ['status' => 'pending'])) }}" class="px-4 py-1 rounded-full font-semibold text-xs transition relative flex items-center focus:outline-none {{ request('status') === 'pending' ? 'bg-yellow-500 text-white shadow' : 'bg-white text-gray-700 hover:bg-yellow-600 hover:text-white border border-gray-200' }}">Pending</a>
                            <a href="?{{ http_build_query(array_merge(request()->except(['status']), ['status' => 'endorsed'])) }}" class="px-4 py-1 rounded-full font-semibold text-xs transition relative flex items-center focus:outline-none {{ request('status') === 'endorsed' ? 'bg-green-600 text-white shadow' : 'bg-white text-gray-700 hover:bg-green-700 hover:text-white border border-gray-200' }}">Endorsed</a>
                            <a href="?{{ http_build_query(array_merge(request()->except(['status']), ['status' => 'rejected'])) }}" class="px-4 py-1 rounded-full font-semibold text-xs transition relative flex items-center focus:outline-none {{ request('status') === 'rejected' ? 'bg-red-600 text-white shadow' : 'bg-white text-gray-700 hover:bg-red-700 hover:text-white border border-gray-200' }}">Rejected</a>
                            <form method="GET" action="" class="flex flex-row gap-2 items-center ml-2 w-auto" style="min-width:0;">
                                @foreach(request()->except(['search', 'page']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                                @endforeach
                                <input type="text" name="search" value="{{ request('search', '') }}" placeholder="Code or Name" class="border rounded-lg px-3 py-1 focus:border-maroon-500 focus:ring-maroon-500 text-sm h-8 w-32 md:w-40" />
                                <button type="submit" class="ml-1 p-2 bg-maroon-700 text-white rounded-lg hover:bg-maroon-800 transition-colors flex items-center justify-center h-8 w-8" title="Search">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                        <div class="flex-1 flex justify-end items-center min-w-0">
                            @if(!empty($rangeDescription))
                                <div class="text-xs text-gray-500 font-normal whitespace-nowrap">{{ $rangeDescription }}</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-0 max-h-[45vh] h-[45vh] overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Date</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[140px]">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($requests as $request)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 max-w-[160px] truncate" title="{{ $request->user ? $request->user->name : 'N/A' }}">{{ $request->user ? $request->user->name : 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell max-w-[180px] truncate" title="{{ $request->user ? $request->user->email : 'N/A' }}">{{ $request->user ? $request->user->email : 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">{{ $request->request_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($request->status === 'endorsed') bg-green-100 text-green-800
                                                @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                                @elseif($request->status === 'pending') bg-yellow-100 text-yellow-800
                                                @endif
                                            ">{{ $request->status }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ $request->requested_at ? \Carbon\Carbon::parse($request->requested_at)->format('M d, Y h:i A') : '' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <div class="flex flex-row gap-2 items-center justify-center">
                                                <!-- Review Button (Blue) -->
                                                <button @click="openReviewModal(@json($request))" type="button" class="p-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition-colors" title="Review">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <!-- Change Status Button (Yellow) -->
                                                <button @click="openStatusModal(@json($request))" type="button" class="p-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 transition-colors" title="Change Status">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                                                    </svg>
                                                </button>
                                                <!-- Delete Button (Red) -->
                                                <form action="{{ route('admin.requests.destroy', $request->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this request?');" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors" title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-8 text-center text-gray-500">No requests found for your current filter or search.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Modal -->
        <div x-show="statusModalOpen" x-cloak @click.away="closeStatusModal()" @keydown.escape.window="closeStatusModal()" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4" style="backdrop-filter: blur(2px);" @click="closeStatusModal()">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-xs" @click.stop>
                <h4 class="text-lg font-semibold mb-1 text-maroon-800">Update Status</h4>
                <p class="text-xs text-gray-600 mb-4">Update the status of this request below.</p>
                <form method="POST" :action="`/admin/requests/${modalData.id}`" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="flex flex-col gap-3">
                        <label class="flex items-center gap-3 cursor-pointer p-2 rounded hover:bg-gray-50 transition-colors">
                            <input type="radio" name="status" value="pending" :checked="modalData.status === 'pending'" class="form-radio text-yellow-600">
                            <span class="text-sm font-medium">Pending</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer p-2 rounded hover:bg-gray-50 transition-colors">
                            <input type="radio" name="status" value="endorsed" :checked="modalData.status === 'endorsed'" class="form-radio text-green-600">
                            <span class="text-sm font-medium">Endorsed</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer p-2 rounded hover:bg-gray-50 transition-colors">
                            <input type="radio" name="status" value="rejected" :checked="modalData.status === 'rejected'" class="form-radio text-red-600">
                            <span class="text-sm font-medium">Rejected</span>
                        </label>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="closeStatusModal()" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-maroon-700 text-white hover:bg-maroon-800 font-semibold transition-colors">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Review Modal -->
        <div x-show="reviewModalOpen" x-cloak @click.away="closeReviewModal()" @keydown.escape.window="closeReviewModal()" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-2" style="backdrop-filter: blur(2px);" @click="closeReviewModal()">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-6xl max-h-[92vh] flex flex-col overflow-hidden admin-review-modal-content" @click.stop>
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-3 border-b border-gray-200 flex-shrink-0 bg-gray-50 rounded-t-xl">
                    <div class="flex flex-col gap-0.5">
                    <h3 class="text-lg font-semibold text-maroon-800">Request Review</h3>
                        <p class="text-xs text-gray-600">Review submission details and files</p>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="p-6 flex-1 overflow-hidden admin-review-modal-scroll">
                    @php
                        $selectedRequest = $requests->firstWhere('id', (int) (isset($currentRequestId) ? $currentRequestId : null));
                        if (!$selectedRequest && isset($requests) && $requests->count() > 0) {
                            $selectedRequest = $requests->first();
                        }
                    @endphp
                    
                    <div class="flex gap-3 h-full flex-col sm:flex-row sm:gap-3 gap-2">
                        <!-- Left Column: Files -->
                        <div class="w-full sm:w-1/4 flex flex-col mb-2 sm:mb-0">
                            <!-- Files Section -->
                            <div class="bg-white border border-gray-200 rounded-lg p-2 flex flex-col h-full">
                                <div class="flex items-center justify-between mb-2 flex-shrink-0">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <h5 class="font-semibold text-gray-900 text-base">Files & Documents</h5>
                                    </div>
                                    <div class="flex gap-1">
                                        <!-- Debug button hidden for security -->
                                        <!-- <button @click="debugFiles()" class="inline-flex items-center px-1.5 py-0.5 bg-gray-600 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">
                                            <svg class="h-2 w-2 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            Debug
                                        </button> -->
                                        <button @click="downloadAllAsZip()" class="inline-flex items-center px-1.5 py-0.5 bg-maroon-700 text-white text-xs font-medium rounded hover:bg-maroon-800 transition-colors">
                                            <svg class="h-2 w-2 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            ZIP
                                        </button>
                                    </div>
                                </div>
                                
                                @php
                                    $fileData = $selectedRequest ? (is_array($selectedRequest->pdf_path) ? $selectedRequest->pdf_path : json_decode($selectedRequest->pdf_path, true)) : null;
                                    
                                    // Check file existence
                                    $fileStatus = [];
                                    if ($fileData) {
                                        if (isset($fileData['pdfs'])) {
                                            foreach ($fileData['pdfs'] as $key => $info) {
                                                $filePath = storage_path('app/public/' . $info['path']);
                                                $fileStatus['pdfs'][$key] = file_exists($filePath);
                                            }
                                        }
                                        if (isset($fileData['docxs'])) {
                                            foreach ($fileData['docxs'] as $key => $path) {
                                                $filePath = storage_path('app/' . $path);
                                                $fileStatus['docxs'][$key] = file_exists($filePath);
                                            }
                                        }
                                    }
                                @endphp
                                
                                @if($fileData)
                                    <div class="space-y-2 flex-1 overflow-y-auto min-h-0">
                                        <!-- PDF Files -->
                                        <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                                            <h6 class="font-medium text-gray-800 mb-2 text-base flex items-center gap-1">
                                                <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                </svg>
                                                PDFs
                                            </h6>
                                            <div class="space-y-1.5">
                                            @foreach($fileData['pdfs'] ?? [] as $key => $info)
                                                @php $exists = $fileStatus['pdfs'][$key] ?? false; @endphp
                                                    <div class="flex items-center justify-between p-1.5 {{ $exists ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }} rounded-lg text-sm">
                                                        <div class="flex items-center gap-1.5 min-w-0 flex-1">
                                                            <div class="w-5 h-5 {{ $exists ? 'bg-green-100' : 'bg-red-100' }} rounded flex items-center justify-center flex-shrink-0">
                                                                <svg class="w-3 h-3 {{ $exists ? 'text-green-600' : 'text-red-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                            </svg>
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <div class="font-medium {{ $exists ? 'text-gray-800' : 'text-red-700' }} truncate text-sm">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                                                @if(!$exists)
                                                                    <div class="text-red-600 text-sm">Missing</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @if($exists)
                                                            <a href="{{ route('admin.requests.serve', ['request' => $selectedRequest->id, 'type' => 'pdf', 'key' => $key]) }}" class="inline-flex items-center px-2 py-1 bg-maroon-700 text-white text-xs font-medium rounded hover:bg-maroon-800 transition-colors" target="_blank">
                                                                <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                            </a>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-600 text-xs font-medium rounded">
                                                                <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </span>
                                                        @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <!-- DOCX Files -->
                                        <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                                            <h6 class="font-medium text-gray-800 mb-2 text-base flex items-center gap-1">
                                                <svg class="w-3 h-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                </svg>
                                                DOCX
                                            </h6>
                                            <div class="space-y-1.5">
                                            @foreach($fileData['docxs'] ?? [] as $key => $path)
                                                @php $exists = $fileStatus['docxs'][$key] ?? false; @endphp
                                                    <div class="flex items-center justify-between p-1.5 {{ $exists ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }} rounded-lg text-sm">
                                                        <div class="flex items-center gap-1.5 min-w-0 flex-1">
                                                            <div class="w-5 h-5 {{ $exists ? 'bg-green-100' : 'bg-red-100' }} rounded flex items-center justify-center flex-shrink-0">
                                                                <svg class="w-3 h-3 {{ $exists ? 'text-green-600' : 'text-red-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                            </svg>
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <div class="font-medium {{ $exists ? 'text-gray-800' : 'text-red-700' }} truncate text-sm">{{ ucfirst($key) }}</div>
                                                                @if(!$exists)
                                                                    <div class="text-red-600 text-sm">Missing</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @if($exists)
                                                            <a href="{{ route('admin.requests.serve', ['request' => $selectedRequest->id, 'type' => 'docx', 'key' => $key]) }}" class="inline-flex items-center px-2 py-1 bg-maroon-700 text-white text-xs font-medium rounded hover:bg-maroon-800 transition-colors" target="_blank">
                                                                <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                            </a>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-600 text-xs font-medium rounded">
                                                                <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                </div>
+                                           <div class="mt-2 p-2 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 text-xs rounded">
+                                               <strong>Disclaimer:</strong> For best results, please use <span class="font-semibold">Microsoft Word</span> to review DOCX files. The template and file design are optimized for Word and may not display correctly in other editors or viewers.
+                                           </div>
                                        </div>
                                    </div>
                                    
                                    <!-- File Status Summary -->
                                    @php
                                        $totalFiles = (isset($fileData['pdfs']) ? count($fileData['pdfs']) : 0) + (isset($fileData['docxs']) ? count($fileData['docxs']) : 0);
                                        $existingFiles = 0;
                                        if (isset($fileStatus['pdfs'])) $existingFiles += count(array_filter($fileStatus['pdfs']));
                                        if (isset($fileStatus['docxs'])) $existingFiles += count(array_filter($fileStatus['docxs']));
                                        $missingFiles = $totalFiles - $existingFiles;
                                    @endphp
                                    
                                    @if($missingFiles > 0)
                                        <div class="mt-2 p-1.5 bg-red-50 border border-red-200 rounded-lg flex-shrink-0">
                                            <div class="flex items-center gap-1 text-red-700">
                                                <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                </svg>
                                                <span class="text-sm">{{ $missingFiles }} missing</span>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-2 text-center flex-1 flex items-center justify-center">
                                        <div>
                                            <svg class="w-6 h-6 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="text-gray-500 text-sm">No files found</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Right Column: Request Info & Form Data -->
                        <div class="w-full sm:w-3/4 flex flex-col h-full">
                            <!-- Request Information Card -->
                            <div class="bg-white border border-gray-200 rounded-lg p-3 mb-3 flex-shrink-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h5 class="font-semibold text-gray-900 text-base">Request Information</h5>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                                    <div class="flex flex-col">
                                        <span class="text-gray-500">Request Code:</span>
                                        <span class="font-mono font-medium text-gray-900" x-text="modalData.code"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-gray-500">User:</span>
                                        <span class="font-medium text-gray-900" x-text="modalData.user"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-gray-500">Email:</span>
                                        <span class="font-medium text-gray-900" x-text="modalData.email"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-gray-500">Type:</span>
                                        <span class="font-medium text-gray-900" x-text="modalData.type"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-gray-500">Status:</span>
                                        <span class="font-medium" :class="{
                                            'text-yellow-600': modalData.status === 'pending',
                                            'text-green-600': modalData.status === 'endorsed',
                                            'text-red-600': modalData.status === 'rejected'
                                        }" x-text="modalData.status"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-gray-500">Date:</span>
                                        <span class="font-medium text-gray-900" x-text="new Date(modalData.date).toLocaleDateString()"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Data Card -->
                            @php
                                $formData = $selectedRequest ? (is_array($selectedRequest->form_data) ? $selectedRequest->form_data : json_decode($selectedRequest->form_data, true)) : null;
                            @endphp
                            
                            @if($formData)
                                <div class="bg-white border border-gray-200 rounded-lg p-3 flex-1 flex flex-col">
                                    <div class="flex items-center gap-2 mb-3 flex-shrink-0">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h5 class="font-semibold text-gray-900 text-base">Form Data</h5>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-3 gap-2 lg:gap-4 flex-1">
                                        <!-- Incentive Application -->
                                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                            <h6 class="font-semibold text-gray-700 mb-2 text-base">Incentive Application</h6>
                                            <div class="space-y-2 text-sm">
                                        @foreach(['name', 'college', 'papertitle', 'journaltitle'] as $field)
                                            @if(isset($formData[$field]))
                                                        <div class="flex flex-col">
                                                            <span class="font-medium text-gray-600 text-sm">
                                                                @if($field === 'papertitle')
                                                                    Paper Title
                                                                @elseif($field === 'journaltitle')
                                                                    Journal Title
                                                                @else
                                                                    {{ ucfirst($field) }}
                                                                @endif
                                                            </span>
                                                            <span class="text-gray-800 truncate text-sm" title="{{ $formData[$field] }}">
                                                                @if($field === 'papertitle' || $field === 'title')
                                                                    {{ $formData[$field] }}
                                                                @else
                                                                    {{ $formData[$field] }}
                                                                @endif
                                                            </span>
                                                        </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                        <!-- Recommendation Letter -->
                                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                            <h6 class="font-semibold text-gray-700 mb-2 text-base">Recommendation Letter</h6>
                                            <div class="space-y-2 text-sm">
                                                @foreach(['rec_collegeheader', 'rec_faculty_name', 'rec_citing_details', 'rec_indexing_details', 'rec_dean_name', 'facultyname', 'dean', 'indexing'] as $field)
                                            @if(isset($formData[$field]))
                                                        <div class="flex flex-col">
                                                            <span class="font-medium text-gray-600 text-sm">
                                                                @if($field === 'rec_collegeheader')
                                                                    College
                                                                @elseif($field === 'rec_faculty_name')
                                                                    Faculty Name
                                                                @elseif($field === 'rec_citing_details')
                                                                    Citing Details
                                                                @elseif($field === 'rec_indexing_details')
                                                                    Indexing Details
                                                                @elseif($field === 'rec_dean_name')
                                                                    Dean Name
                                                                @elseif($field === 'facultyname')
                                                                    Faculty Name
                                                                @elseif($field === 'dean')
                                                                    Dean Name
                                                                @elseif($field === 'indexing')
                                                                    Indexing
                                                                @else
                                                                    {{ ucfirst($field) }}
                                                                @endif
                                                            </span>
                                                            <span class="text-gray-800 break-words text-sm" title="{{ $formData[$field] }}">{{ $formData[$field] }}</span>
                                                        </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                        <!-- Terminal Report (only for Publication) -->
                                        @if($selectedRequest && strtolower($selectedRequest->type) === 'publication')
                                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                            <h6 class="font-semibold text-gray-700 mb-2 text-base">Terminal Report</h6>
                                            <div class="space-y-2 text-sm">
                                        @foreach(['title', 'author', 'duration'] as $field)
                                            @if(isset($formData[$field]))
                                                        <div class="flex flex-col">
                                                            <span class="font-medium text-gray-600 text-sm">{{ ucfirst($field) }}</span>
                                                            <span class="text-gray-800 truncate text-sm" title="{{ $formData[$field] }}">
                                                                {{ $formData[$field] }}
                                                            </span>
                                                        </div>
                                            @endif
                                        @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="bg-white border border-gray-200 rounded-lg p-3 flex-1 flex items-center justify-center">
                                    <div class="text-center">
                                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div class="text-gray-500 text-sm">No form data available</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
        function adminDashboard() {
            return {
                // Modal states
                statusModalOpen: false,
                reviewModalOpen: false,
                modalData: {},
                
                // Filter states
                activeType: '{{ request('type', '') }}',
                activePeriod: '{{ request('period', '') }}',
                activeStatus: '{{ request('status', '') }}',
                searchQuery: '{{ request('search', '') }}',
                
                // Data states
                requests: @json($requests),
                stats: @json($stats),
                filterCounts: Object.assign({pending: 0, endorsed: 0, rejected: 0}, @json($filterCounts)),
                loading: false,
                
                // Computed properties
                get hasActiveFilters() {
                    return this.activeType || this.activePeriod || this.activeStatus || this.searchQuery;
                },
                
                // Initialize
                init() {
                    this.loadData();
                },
                
                // AJAX data loading
                async loadData() {
                    this.loading = true;
                    
                    try {
                        const params = new URLSearchParams();
                        if (this.activeType) params.set('type', this.activeType);
                        if (this.activePeriod) params.set('period', this.activePeriod);
                        if (this.activeStatus) params.set('status', this.activeStatus);
                        if (this.searchQuery) params.set('search', this.searchQuery);
                        
                        const response = await fetch(`/admin/dashboard/data?${params.toString()}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            this.requests = data.requests;
                            this.stats = data.stats;
                            this.filterCounts = data.filterCounts;
                            
                            // Update URL without page refresh
                            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                            window.history.pushState({}, '', newUrl);
                        } else {
                            console.error('Failed to load data');
                        }
                    } catch (error) {
                        console.error('Error loading data:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                
                // Filter methods
                async filterRequests(type, period = '') {
                    this.activeType = type;
                    this.activePeriod = period;
                    await this.loadData();
                },
                
                async filterByStatus(status) {
                    this.activeStatus = status;
                    await this.loadData();
                },
                
                async performSearch() {
                    await this.loadData();
                },
                
                async clearAllFilters() {
                    this.activeType = '';
                    this.activePeriod = '';
                    this.activeStatus = '';
                    this.searchQuery = '';
                    await this.loadData();
                },
                
                // Delete request
                async deleteRequest(requestId) {
                    if (!confirm('Are you sure you want to delete this request?')) {
                        return;
                    }
                    
                    try {
                        const response = await fetch(`/admin/requests/${requestId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });
                        
                        if (response.ok) {
                            // Remove the request from the list
                            this.requests = this.requests.filter(r => r.id !== requestId);
                            // Reload data to update counts
                            await this.loadData();
                        } else {
                            alert('Failed to delete request');
                        }
                    } catch (error) {
                        console.error('Error deleting request:', error);
                        alert('Error deleting request');
                    }
                },
                
                // Modal methods
                openStatusModal(data) {
                    console.log('Opening status modal with data:', data);
                    this.modalData = { 
                        id: data.id, 
                        status: data.status 
                    };
                    this.statusModalOpen = true;
                    this.reviewModalOpen = false;
                    document.body.classList.add('overflow-hidden');
                },
                
                closeStatusModal() {
                    console.log('Closing status modal');
                    this.statusModalOpen = false;
                    document.body.classList.remove('overflow-hidden');
                },
                
                openReviewModal(data) {
                    console.log('Opening review modal with data:', data);
                    this.modalData = { 
                        id: data.id, 
                        code: data.request_code, 
                        user: data.user ? data.user.name : 'N/A', 
                        email: data.user ? data.user.email : 'N/A', 
                        type: data.type, 
                        status: data.status, 
                        date: data.requested_at 
                    };
                    this.reviewModalOpen = true;
                    this.statusModalOpen = false;
                    document.body.classList.add('overflow-hidden');
                },
                
                closeReviewModal() {
                    console.log('Closing review modal');
                    this.reviewModalOpen = false;
                    document.body.classList.remove('overflow-hidden');
                },

                downloadAllAsZip() {
                    console.log('downloadAllAsZip called, modalData:', this.modalData);
                    if (!this.modalData.id) {
            alert('No request selected');
            return;
        }
        
                    console.log('Opening ZIP download for request ID:', this.modalData.id);
                    window.open(`/admin/requests/${this.modalData.id}/download-zip`, '_blank');
                },

                debugFiles() {
                    console.log('debugFiles called, modalData:', this.modalData);
                    if (!this.modalData.id) {
            alert('No request selected');
            return;
        }
        
                    console.log('Opening debug for request ID:', this.modalData.id);
                    window.open(`/admin/requests/${this.modalData.id}/debug`, '_blank');
                }
            }
    }
</script>
</x-app-layout>

