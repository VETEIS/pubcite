<x-app-layout>
    <div x-data="{ 
        loading: false,
        errorMessage: null,
        errorTimer: null,
        showError(message) {
            this.errorMessage = message;
            if (this.errorTimer) clearTimeout(this.errorTimer);
            this.errorTimer = setTimeout(() => {
                this.errorMessage = null;
            }, 3000);
        }
    }" class="h-[calc(100vh-4rem)] flex items-center justify-center p-4 sm:p-6 relative">
        @if(session('success'))
            <div class="fixed top-20 right-4 z-[60] bg-green-600 text-white px-4 py-2 rounded shadow">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="fixed top-20 right-4 z-[60] bg-red-600 text-white px-4 py-2 rounded shadow">{{ session('error') }}</div>
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
        <div class="w-full max-w-6xl h-[calc(90vh-4rem)] flex items-center justify-center">
            <div class="bg-white/30 backdrop-blur-md border border-white/40 overflow-hidden shadow-xl sm:rounded-lg p-0 relative h-full flex flex-col">
                <div class="h-full flex flex-col md:flex-row">
                    <!-- Left Column -->
                    <div class="flex-1 flex flex-col p-6 border-b md:border-b-0 md:border-r border-gray-100 min-w-[260px] max-w-md">
                        <!-- Header -->
                        <div class="bg-white/40 backdrop-blur border border-white/40 rounded-lg p-3 flex flex-col justify-center mb-6 relative" style="min-height: 120px;">
                            <div class="flex items-center w-full">
                                <!-- Avatar -->
                                <div class="w-12 h-12 rounded-full bg-maroon-700 flex items-center justify-center text-white text-xl font-bold shadow">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <!-- Name -->
                                <div class="ml-3 flex-1 min-w-0">
                                    <div class="text-lg font-bold text-maroon-900 truncate">
                                        {{ Auth::user()->name ?? 'User' }}
                                    </div>
                                </div>
                                <!-- Role Badge -->
                                <div class="absolute right-3 top-3">
                                    @php
                                        $role = Auth::user()->role ?? 'Member';
                                        $isAdmin = strtolower($role) === 'admin';
                                    @endphp
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                                        {{ $isAdmin ? 'bg-maroon-700 text-white' : 'bg-gray-200 text-maroon-800' }}">
                                        {{ $isAdmin ? 'Admin' : 'User' }}
                                    </span>
                                </div>
                            </div>
                            <!-- Email -->
                            <div class="text-xs text-gray-700 mt-2 truncate w-full">
                                {{ Auth::user()->email ?? 'No email available' }}
                            </div>
                            <!-- Welcome -->
                            <div class="text-xs text-gray-500 mt-1 w-full">Welcome to your dashboard!</div>
                        </div>
                        <!-- Actions -->
                        <div class="bg-white/40 backdrop-blur border border-white/40 rounded-lg p-4 flex flex-col flex-1 justify-center">
                            <div class="flex flex-col gap-4 w-full">
                                <a href="{{ route('publications.request') }}" data-turbo="false" class="bg-maroon-700 hover:bg-maroon-800 text-white font-semibold py-4 px-4 rounded-lg shadow transition flex flex-row items-center gap-2 justify-between group text-base">
                                    <div class="flex flex-row items-center gap-2">
                                        <svg class="h-6 w-6 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="text-base">Application for Publications</span>
                                    </div>
                                    <svg class="h-5 w-5 text-white flex-shrink-0 ml-2 arrow-animate-x transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                </a>
                                <a href="{{ route('citations.request') }}" data-turbo="false" class="bg-burgundy-700 hover:bg-burgundy-800 text-white font-semibold py-4 px-4 rounded-lg shadow transition flex flex-row items-center gap-2 justify-between group text-base">
                                    <div class="flex flex-row items-center gap-2">
                                        <svg class="h-6 w-6 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="text-base">Application for Citations</span>
                                    </div>
                                    <svg class="h-5 w-5 text-white flex-shrink-0 ml-2 arrow-animate-x transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                </a>
                                <div class="flex flex-col gap-4 mt-2">
                                    <div class="flex items-start gap-2">
                                        <svg class="h-6 w-6 text-maroon-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 pt-1">Incentive Application Form</p>
                                            <p class="text-xs text-gray-500">Apply for publication or citation incentives</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <svg class="h-6 w-6 text-maroon-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 pt-1">Recommendation Letter from the Dean</p>
                                            <p class="text-xs text-gray-500">Required for all applications</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <svg class="h-6 w-6 text-maroon-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 pt-1">Terminal Report Template</p>
                                            <p class="text-xs text-gray-500">Template for terminal report submission</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Right Column: Recent Submissions Table -->
                    <div class="flex-[2.5] flex flex-col min-w-0 h-full p-6">
                        <div class="bg-white/40 backdrop-blur border border-white/40 rounded-lg shadow-xl flex flex-col h-full">
                            <!-- Card Header -->
                            <div class="flex items-center gap-3 px-6 pt-6 pb-3 border-b border-white/30">
                                <svg class="h-7 w-7 text-maroon-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                <h3 class="text-xl font-bold text-maroon-800">Previously Submitted Requests</h3>
                            </div>
                            <!-- Search and Filter -->
                            <div class="flex flex-col md:flex-row md:items-center gap-3 px-6 pt-4 pb-2">
                                <div class="flex gap-2 flex-wrap">
                                    @php
                                        $currentStatus = request('status');
                                        $filters = [
                                            ['label' => 'All', 'value' => null, 'icon' => 'list'],
                                            ['label' => 'Pending', 'value' => 'pending', 'icon' => 'clock'],
                                            ['label' => 'Endorsed', 'value' => 'endorsed', 'icon' => 'check'],
                                            ['label' => 'Redo', 'value' => 'rejected', 'icon' => 'x'],
                                        ];
                                        $filteredRequests = $requests;
                                        if ($currentStatus) {
                                            $filteredRequests = $requests->where('status', $currentStatus);
                                        }
                                    @endphp
                                    @foreach($filters as $filter)
                                        <a href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => $filter['value']])) }}"
                                           class="px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1 transition
                                           {{ ($currentStatus === $filter['value'] || ($filter['value'] === null && !$currentStatus))
                                                ? 'bg-maroon-800 text-white shadow'
                                                : ($filter['value'] === 'pending' ? 'bg-yellow-100 text-maroon-900 hover:bg-yellow-200'
                                                : ($filter['value'] === 'endorsed' ? 'bg-green-100 text-green-800 hover:bg-green-200'
                                                : ($filter['value'] === 'rejected' ? 'bg-red-100 text-red-800 hover:bg-red-200'
                                                : 'bg-gray-100 text-maroon-900 hover:bg-gray-200'))) }}">
                                            @if($filter['icon'] === 'list')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                            @elseif($filter['icon'] === 'clock')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                                            @elseif($filter['icon'] === 'check')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @elseif($filter['icon'] === 'x')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            @endif
                                            {{ $filter['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <!-- Table -->
                            <div class="overflow-x-auto overflow-y-auto flex-1 max-h-full rounded-t-lg rounded-b-lg px-3 md:px-6 relative overflow-hidden">
                                <table class="min-w-full w-full table-fixed divide-y divide-gray-200 rounded-t-lg overflow-hidden">
                                    <thead class="sticky top-0 bg-white/60 backdrop-blur z-10 rounded-t-lg">
                                        <tr>
                                            <th class="px-4 py-2 w-40 text-left text-sm font-semibold text-maroon-800 uppercase tracking-wider">Date</th>
                                            <th class="px-4 py-2 w-44 text-left text-sm font-semibold text-maroon-800 uppercase tracking-wider">Code</th>
                                            <th class="px-4 py-2 w-32 text-left text-sm font-semibold text-maroon-800 uppercase tracking-wider">Type</th>
                                            <th class="px-4 py-2 w-28 text-center text-sm font-semibold text-maroon-800 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-2 w-20 text-center text-sm font-semibold text-maroon-800 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @if($filteredRequests->isEmpty())
                                            <tr>
                                                <td colspan="5" class="py-16 text-center w-full">
                                                    <div class="flex flex-col items-center justify-center gap-2">
                                                        <svg class="w-7 h-7 mb-1 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20v-6m0 0l-3 3m3-3l3 3M4 6h16M4 10h16M4 14h16" />
                                                        </svg>
                                                        <span class="text-gray-500 text-md">No entries</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @else
                                            @foreach($filteredRequests as $request)
                                            <tr class="hover:bg-white/30 transition">
                                                <td class="px-4 py-2 truncate font-semibold text-gray-900 w-40 text-xs">{{ \Carbon\Carbon::parse($request->requested_at)->format('M d, Y • H:i') }}</td>
                                                <td class="px-4 py-2 truncate font-semibold text-maroon-900 w-44 text-xs">{{ $request->request_code }}</td>
                                                <td class="px-4 py-2 truncate font-semibold text-gray-900 w-32 text-xs">{{ $request->type }}</td>
                                                <td class="px-4 py-2 truncate text-center font-semibold w-28 text-xs">
                                                    @if($request->status === 'endorsed')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Endorsed</span>
                                                    @elseif($request->status === 'rejected')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Redo</span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-center text-xs">
                                                    @if($request->status === 'pending')
                                                        @php
                                                            $recentNudge = \App\Models\ActivityLog::where('user_id', auth()->id())
                                                                ->where('request_id', $request->id)
                                                                ->where('action', 'nudged')
                                                                ->where('created_at', '>=', now()->subDay())
                                                                ->first();
                                                            $canNudge = !$recentNudge;
                                                        @endphp
                                                        @if($canNudge)
                                                            <form method="POST" action="{{ route('requests.nudge', $request) }}" class="inline" @submit="loading = true">
                                                                @csrf
                                                                <button type="submit" :disabled="loading" class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 disabled:opacity-60 disabled:cursor-not-allowed transition text-xs whitespace-nowrap" title="Nudge admin">
                                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                                                    <span x-text="loading ? 'Nudging…' : 'Nudge'"></span>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <button type="button" @click="showError('You can only nudge this request once per day.')" class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gray-100 text-gray-500 cursor-not-allowed transition text-xs whitespace-nowrap" title="Already nudged today" disabled>
                                                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                                                <span>Nudge</span>
                                                            </button>
                                                        @endif
                                                    @else
                                                    <span class="text-gray-400">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="5" class="py-6 text-center w-full">
                                                    <div class="flex flex-col items-center justify-center gap-1">
                                                        <svg class="w-7 h-7 mb-1 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20v-6m0 0l-3 3m3-3l3 3M4 6h16M4 10h16M4 14h16" />
                                                        </svg>
                                                        <span class="text-gray-500 text-md">No more entries</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
