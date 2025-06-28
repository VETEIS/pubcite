<x-app-layout>
    <div x-data="{ 
        statusModalOpen: false, 
        reviewModalOpen: false,
        currentRequestId: null, 
        currentStatus: null,
        code: '',
        user: '',
        email: '',
        type: '',
        status: '',
        date: '',
        openStatusModal(id, status) {
            this.currentRequestId = id;
            this.currentStatus = status;
            this.statusModalOpen = true;
        },
        openReviewModal(id, code, user, email, type, status, date) {
            this.currentRequestId = id;
            this.reviewModalOpen = true;
            this.code = code;
            this.user = user;
            this.email = email;
            this.type = type;
            this.status = status;
            this.date = date;
        }
    }" class="min-h-[calc(100vh-4rem)] flex items-center justify-center">
        <div class="w-full max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8 rounded-lg relative">
                @if(request('type') || request('period'))
                    <a href="?" class="fixed md:absolute top-6 right-8 z-30 inline-flex items-center gap-1 px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-xs font-semibold shadow transition focus:outline-none" style="box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        Clear Filter
                    </a>
                @endif
                
                <!-- Full-width Graphical Counter Tracker -->
                <div class="w-full grid grid-cols-2 gap-4 mb-6 items-center">
                    <div class="flex flex-col bg-maroon-50 border border-maroon-200 rounded-lg px-4 py-2 shadow-sm min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="h-5 w-5 text-maroon-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <a href="?type=Publication" class="font-semibold text-maroon-800 text-sm truncate hover:underline hover:text-maroon-600 transition {{ (request('type') == 'Publication' && !request('period')) ? 'underline' : '' }}">Publications Requests</a>
                        </div>
                        <div class="grid grid-cols-3 gap-2 w-full">
                            <a href="?type=Publication&period=today" class="bg-white rounded-lg px-2 py-1 text-center flex flex-col items-center transition hover:bg-maroon-100 hover:text-maroon-800 cursor-pointer {{ (request('type') == 'Publication' && request('period') == 'today') ? 'ring-2 ring-maroon-400' : '' }}">
                                <span class="text-[11px] text-gray-500">Today</span>
                                <span class="text-lg font-bold text-maroon-700">{{ $stats['publication']['today'] }}</span>
                            </a>
                            <a href="?type=Publication&period=week" class="bg-white rounded-lg px-2 py-1 text-center flex flex-col items-center transition hover:bg-maroon-100 hover:text-maroon-800 cursor-pointer {{ (request('type') == 'Publication' && request('period') == 'week') ? 'ring-2 ring-maroon-400' : '' }}">
                                <span class="text-[11px] text-gray-500">Week</span>
                                <span class="text-lg font-bold text-maroon-700">{{ $stats['publication']['week'] }}</span>
                            </a>
                            <a href="?type=Publication&period=month" class="bg-white rounded-lg px-2 py-1 text-center flex flex-col items-center transition hover:bg-maroon-100 hover:text-maroon-800 cursor-pointer {{ (request('type') == 'Publication' && request('period') == 'month') ? 'ring-2 ring-maroon-400' : '' }}">
                                <span class="text-[11px] text-gray-500">Month</span>
                                <span class="text-lg font-bold text-maroon-700">{{ $stats['publication']['month'] }}</span>
                            </a>
                        </div>
                    </div>
                    <div class="flex flex-col bg-burgundy-50 border border-burgundy-200 rounded-lg px-4 py-2 shadow-sm min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="h-5 w-5 text-burgundy-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <a href="?type=Citation" class="font-semibold text-burgundy-800 text-sm truncate hover:underline hover:text-burgundy-600 transition {{ (request('type') == 'Citation' && !request('period')) ? 'underline' : '' }}">Citations Requests</a>
                        </div>
                        <div class="grid grid-cols-3 gap-2 w-full">
                            <a href="?type=Citation&period=today" class="bg-white rounded-lg px-2 py-1 text-center flex flex-col items-center transition hover:bg-burgundy-100 hover:text-burgundy-800 cursor-pointer {{ (request('type') == 'Citation' && request('period') == 'today') ? 'ring-2 ring-burgundy-400' : '' }}">
                                <span class="text-[11px] text-gray-500">Today</span>
                                <span class="text-lg font-bold text-burgundy-700">{{ $stats['citation']['today'] }}</span>
                            </a>
                            <a href="?type=Citation&period=week" class="bg-white rounded-lg px-2 py-1 text-center flex flex-col items-center transition hover:bg-burgundy-100 hover:text-burgundy-800 cursor-pointer {{ (request('type') == 'Citation' && request('period') == 'week') ? 'ring-2 ring-burgundy-400' : '' }}">
                                <span class="text-[11px] text-gray-500">Week</span>
                                <span class="text-lg font-bold text-burgundy-700">{{ $stats['citation']['week'] }}</span>
                            </a>
                            <a href="?type=Citation&period=month" class="bg-white rounded-lg px-2 py-1 text-center flex flex-col items-center transition hover:bg-burgundy-100 hover:text-burgundy-800 cursor-pointer {{ (request('type') == 'Citation' && request('period') == 'month') ? 'ring-2 ring-burgundy-400' : '' }}">
                                <span class="text-[11px] text-gray-500">Month</span>
                                <span class="text-lg font-bold text-burgundy-700">{{ $stats['citation']['month'] }}</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8">
                    <h3 class="text-2xl font-semibold text-maroon-800 mb-4">All User Requests</h3>
                    
                    <!-- Filters and Search -->
                    <form method="GET" action="" class="flex flex-col md:flex-row md:items-center gap-4 mb-4">
                        <div class="flex gap-2 relative">
                            <a href="?" class="px-3 py-1 rounded {{ !request('status') ? 'bg-maroon-700 text-white' : 'bg-gray-100 text-gray-700' }} font-semibold text-xs transition hover:bg-maroon-600 hover:text-white">All</a>
                            <div class="relative">
                                <a href="?status=pending{{ request('search') ? '&search='.request('search') : '' }}" class="px-3 py-1 rounded {{ request('status')=='pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700' }} font-semibold text-xs transition hover:bg-yellow-600 hover:text-white">Pending</a>
                                @if(isset($filterCounts['pending']) && $filterCounts['pending'] > 0)
                                    <span class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5 min-w-[20px] text-center select-none">
                                        {{ $filterCounts['pending'] > 99 ? '99+' : $filterCounts['pending'] }}
                                    </span>
                                @endif
                            </div>
                            <div class="relative">
                                <a href="?status=endorsed{{ request('search') ? '&search='.request('search') : '' }}" class="px-3 py-1 rounded {{ request('status')=='endorsed' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700' }} font-semibold text-xs transition hover:bg-green-700 hover:text-white">Endorsed</a>
                                @if(isset($filterCounts['endorsed']) && $filterCounts['endorsed'] > 0)
                                    <span class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5 min-w-[20px] text-center select-none">
                                        {{ $filterCounts['endorsed'] > 99 ? '99+' : $filterCounts['endorsed'] }}
                                    </span>
                                @endif
                            </div>
                            <div class="relative">
                                <a href="?status=rejected{{ request('search') ? '&search='.request('search') : '' }}" class="px-3 py-1 rounded {{ request('status')=='rejected' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700' }} font-semibold text-xs transition hover:bg-red-700 hover:text-white">Rejected</a>
                                @if(isset($filterCounts['rejected']) && $filterCounts['rejected'] > 0)
                                    <span class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5 min-w-[20px] text-center select-none">
                                        {{ $filterCounts['rejected'] > 99 ? '99+' : $filterCounts['rejected'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 flex justify-end">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Code or Name" class="border rounded-lg px-2 py-1 text-sm w-full md:w-48 focus:border-maroon-500 focus:ring-maroon-500" />
                            <button type="submit" class="ml-2 px-3 py-1 bg-maroon-700 text-white rounded-lg font-semibold text-sm">Search</button>
                        </div>
                    </form>
                    
                    <div class="bg-white rounded-lg shadow p-0 max-h-[45vh] h-[45vh] overflow-y-auto">
                        @if($allRequests->isEmpty())
                            <div class="p-8 text-center text-gray-500">No requests found for your current filter or search.</div>
                        @else
                            <div class="overflow-x-auto">
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
                                @foreach($allRequests as $request)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $request->user->name ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">{{ $request->user->email ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">{{ $request->request_code }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->type }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($request->status === 'endorsed')
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Endorsed</span>
                                        @elseif($request->status === 'rejected')
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                        @else
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                        @endif
                                    </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ \Carbon\Carbon::parse($request->requested_at)->format('M d, Y H:i') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                    <div class="flex flex-row gap-1 items-center justify-center">
                                                        <!-- Change Status Button -->
                                                        <button @click="openStatusModal('{{ $request->id }}', '{{ $request->status }}')" type="button" class="px-2 py-1 rounded bg-yellow-500 text-white font-semibold text-xs hover:bg-yellow-600 transition whitespace-nowrap">Status</button>
                                                        <!-- Delete Button -->
                                                        <form action="{{ route('admin.requests.destroy', $request->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this request?');" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="px-2 py-1 rounded bg-red-600 text-white font-semibold text-xs hover:bg-red-700 transition whitespace-nowrap">Delete</button>
                                                        </form>
                                                        <!-- Review Button -->
                                                        <button @click="openReviewModal('{{ $request->id }}', '{{ $request->request_code }}', '{{ $request->user->name ?? 'N/A' }}', '{{ $request->user->email ?? 'N/A' }}', '{{ $request->type }}', '{{ $request->status }}', '{{ $request->requested_at }}')" type="button" class="px-2 py-1 rounded bg-maroon-700 text-white font-semibold text-xs hover:bg-maroon-800 transition whitespace-nowrap">Review</button>
                                                    </div>
                                                </td>
                                            </tr>
                                @endforeach
                            </tbody>
                        </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Modal -->
        <div x-show="statusModalOpen" @click.away="statusModalOpen = false" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-40 p-4" style="backdrop-filter: blur(2px);">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-xs z-50">
                <h4 class="text-lg font-semibold mb-4 text-maroon-800">Update Status</h4>
                <form method="POST" :action="`/admin/requests/${currentRequestId}`" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="flex flex-col gap-3">
                        <label class="flex items-center gap-3 cursor-pointer p-2 rounded hover:bg-gray-50 transition-colors">
                            <input type="radio" name="status" value="pending" :checked="currentStatus === 'pending'" class="form-radio text-yellow-600">
                            <span class="text-sm font-medium">Pending</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer p-2 rounded hover:bg-gray-50 transition-colors">
                            <input type="radio" name="status" value="endorsed" :checked="currentStatus === 'endorsed'" class="form-radio text-green-600">
                            <span class="text-sm font-medium">Endorsed</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer p-2 rounded hover:bg-gray-50 transition-colors">
                            <input type="radio" name="status" value="rejected" :checked="currentStatus === 'rejected'" class="form-radio text-red-600">
                            <span class="text-sm font-medium">Rejected</span>
                        </label>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="statusModalOpen = false" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-maroon-700 text-white hover:bg-maroon-800 font-semibold transition-colors">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Review Modal -->
        <div x-show="reviewModalOpen" @click.away="reviewModalOpen = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4" style="backdrop-filter: blur(2px);">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-3xl z-50 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-maroon-800">Review Submission</h4>
                    <button @click="reviewModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <!-- Review content -->
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-semibold text-maroon-800 mb-3">Request Information</h5>
                            <div class="space-y-2 text-sm">
                                <div><span class="font-medium">Request Code:</span> <span x-text="code" class="font-mono"></span></div>
                                <div><span class="font-medium">Type:</span> <span x-text="type"></span></div>
                                <div><span class="font-medium">Status:</span> 
                                    <span x-text="status" :class="{
                                        'px-2 py-1 text-xs font-semibold rounded-full': true,
                                        'bg-yellow-100 text-yellow-800': status === 'pending',
                                        'bg-green-100 text-green-800': status === 'endorsed',
                                        'bg-red-100 text-red-800': status === 'rejected'
                                    }"></span>
                                </div>
                                <div><span class="font-medium">Date:</span> <span x-text="new Date(date).toLocaleDateString()"></span></div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-semibold text-maroon-800 mb-3">User Information</h5>
                            <div class="space-y-2 text-sm">
                                <div><span class="font-medium">Name:</span> <span x-text="user"></span></div>
                                <div><span class="font-medium">Email:</span> <span x-text="email"></span></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h5 class="font-semibold text-blue-800 mb-2">Note</h5>
                        <p class="text-sm text-blue-700">
                            To view the complete submission details including uploaded documents and generated files, 
                            please use the user's dashboard or contact the user directly.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>