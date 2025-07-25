<x-app-layout>
    <!-- Removed extra custom header here -->
    <div class="flex h-[calc(100vh-4rem)] p-4 gap-x-6">
        <!-- Sidebar -->
        <aside class="w-20 h-full flex-shrink-0 bg-white/30 backdrop-blur border border-white/40 text-maroon-900 flex flex-col items-center shadow-lg rounded-full">
            <nav class="flex flex-col gap-8 w-full items-center flex-1 justify-center">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 group focus:outline-none {{ request()->routeIs('dashboard') && !request()->is('admin/users*') ? '' : 'hover:scale-110 hover:-translate-y-1 transition-all duration-200' }}">
                    <svg class="w-7 h-7 {{ request()->routeIs('dashboard') && !request()->is('admin/users*') ? 'text-maroon-800 fill-maroon-800' : 'text-maroon-800' }} group-hover:text-maroon-800 transition" fill="{{ request()->routeIs('dashboard') && !request()->is('admin/users*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6" /></svg>
                    <span class="text-xs mt-1 pl-2 {{ request()->routeIs('dashboard') && !request()->is('admin/users*') ? 'font-extrabold text-maroon-800' : 'font-bold' }} group-hover:text-maroon-800">Overview</span>
                    @if(request()->routeIs('dashboard') && !request()->is('admin/users*'))
                        <div class="h-0.5 bg-maroon-800 rounded-full w-8 mx-auto mt-1"></div>
                    @endif
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center gap-1 group focus:outline-none {{ request()->is('admin/users*') ? '' : 'hover:scale-110 hover:-translate-y-1 transition-all duration-200' }}">
                    <svg class="w-7 h-7 {{ request()->is('admin/users*') ? 'text-maroon-800 fill-maroon-800' : 'text-maroon-800' }} group-hover:text-maroon-800 transition" fill="{{ request()->is('admin/users*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <span class="text-xs mt-1 {{ request()->is('admin/users*') ? 'font-extrabold text-maroon-800' : 'font-bold' }} group-hover:text-maroon-800">Users</span>
                    @if(request()->is('admin/users*'))
                        <div class="h-0.5 bg-maroon-800 rounded-full w-8 mx-auto mt-1"></div>
                    @endif
                </a>
                <button class="flex flex-col items-center gap-1 group focus:outline-none hover:scale-110 hover:-translate-y-1 transition-all duration-200">
                    <svg class="w-7 h-7 text-maroon-800 group-hover:text-maroon-800 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2M9 17a4 4 0 01-8 0v-2a4 4 0 018 0v2zm0 0v-2a4 4 0 018 0v2zm0 0v-2a4 4 0 018 0v2z" /></svg>
                    <span class="text-xs mt-1 font-bold group-hover:text-maroon-800">Archives</span>
                </button>
                <button class="flex flex-col items-center gap-1 group focus:outline-none hover:scale-110 hover:-translate-y-1 transition-all duration-200">
                    <svg class="w-7 h-7 text-maroon-800 group-hover:text-maroon-800 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span class="text-xs mt-1 font-bold group-hover:text-maroon-800">Settings</span>
                </button>
            </nav>
        </aside>
        <!-- Main Content -->
        <div class="flex-1 flex items-center justify-center h-full m-0">
            <div class="w-full h-full flex-1 rounded-2xl shadow-xl bg-white/30 backdrop-blur border border-white/40 p-4 flex flex-col justify-center items-stretch">
                <!-- Dashboard Header with Navigation Tabs -->
                <div class="relative flex items-center mb-6">
                    <h1 class="text-2xl font-bold text-maroon-900 flex items-center gap-2">
                        @if(request('type') === 'Citation')
                            Citation
                        @elseif(request('type') === 'Publication')
                            Publication
                        @else
                            <svg class="w-7 h-7 text-maroon-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" /></svg>
                            Overview
                        @endif
                    </h1>
                    <!-- Centered Navigation Tabs -->
                    <div class="absolute left-1/2 transform -translate-x-1/2 flex gap-2 dashboard-chart-filter">
                        <a href="{{ route('dashboard', array_merge(request()->except('type', 'page', 'period', 'status', 'search'), ['type' => 'Citation'])) }}"
                           class="px-5 py-1.5 rounded-full font-semibold shadow border border-maroon-900 focus:outline-none transition-colors group {{ request('type') === 'Citation' ? 'bg-maroon-900 text-white' : 'bg-gray-100 text-maroon-900 hover:bg-maroon-900 hover:text-white' }}">
                            <svg class="w-5 h-5 inline-block mr-1 align-middle {{ request('type') === 'Citation' ? 'text-white' : 'text-maroon-800 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2z" /><path stroke-linecap="round" stroke-linejoin="round" d="M17 3v4M7 3v4" /></svg>
                            Citation
                        </a>
                        <a href="{{ route('dashboard', array_merge(request()->except('type', 'page', 'period', 'status', 'search'), ['type' => 'Publication'])) }}"
                           class="px-5 py-1.5 rounded-full font-semibold shadow border border-maroon-900 focus:outline-none transition-colors group {{ request('type') === 'Publication' ? 'bg-maroon-900 text-white' : 'bg-gray-100 text-maroon-900 hover:bg-maroon-900 hover:text-white' }}">
                            <svg class="w-5 h-5 inline-block mr-1 align-middle {{ request('type') === 'Publication' ? 'text-white' : 'text-maroon-800 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m0 0H3m9 0h9" /></svg>
                            Publication
                        </a>
                        <a href="{{ route('dashboard') }}"
                           class="px-5 py-1.5 rounded-full font-semibold shadow border border-maroon-900 focus:outline-none transition-colors group {{ !request('type') && !request('period') && !request('status') && !request('search') ? 'bg-maroon-900 text-white' : 'bg-gray-100 text-maroon-900 hover:bg-maroon-900 hover:text-white' }}">
                            <svg class="w-5 h-5 inline-block mr-1 align-middle {{ !request('type') && !request('period') && !request('status') && !request('search') ? 'text-white' : 'text-maroon-800 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            View All
                        </a>
                    </div>
                    <!-- Search Input Field -->
                    <div class="absolute right-0 flex items-center">
                        <form method="GET" action="{{ route('dashboard') }}" class="relative dashboard-chart-search">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="pl-12 pr-4 py-2 border-2 border-maroon-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-maroon-900 shadow-sm w-56 bg-white text-maroon-900 placeholder-maroon-900" style="transition:background 0.2s;" autocomplete="on">
                            <button type="submit" class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full flex items-center justify-center bg-maroon-900 text-white shadow focus:outline-none" tabindex="-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                </div>
                <!-- Stat Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 h-full">
                    <!-- Left/Main Column: Stat Cards + Table -->
                    <div class="flex flex-col gap-3 h-full flex-1 min-h-0">
                        <!-- Stat Cards (Compact Row) -->
                        @php
                            $type = request('type');
                            $periodStats = [
                                'week' => $type === 'Citation' ? ($stats['citation']['week'] ?? 0)
                                    : ($type === 'Publication' ? ($stats['publication']['week'] ?? 0)
                                    : (($stats['publication']['week'] ?? 0) + ($stats['citation']['week'] ?? 0))),
                                'month' => $type === 'Citation' ? ($stats['citation']['month'] ?? 0)
                                    : ($type === 'Publication' ? ($stats['publication']['month'] ?? 0)
                                    : (($stats['publication']['month'] ?? 0) + ($stats['citation']['month'] ?? 0))),
                                'quarter' => $type === 'Citation' ? ($stats['citation']['quarter'] ?? 0)
                                    : ($type === 'Publication' ? ($stats['publication']['quarter'] ?? 0)
                                    : (($stats['publication']['quarter'] ?? 0) + ($stats['citation']['quarter'] ?? 0))),
                            ];
                        @endphp
                        {{-- Period Filter Cards --}}
                        @php
                            $periods = ['week' => 'Weekly', 'month' => 'Monthly', 'quarter' => 'Quarterly'];
                            $currentPeriod = request('period');
                        @endphp
                        <div class="flex flex-row gap-3 mb-2 dashboard-chart-filter">
                            @foreach($periods as $key => $label)
                                <a href="{{ route('dashboard', array_merge(request()->except('page', 'period'), ['period' => $key])) }}"
                                   class="flex-1 rounded-lg p-2 flex flex-row items-start shadow min-w-0 bg-white/30 backdrop-blur border border-white/40 transition cursor-pointer {{ $currentPeriod === $key ? 'ring-2 ring-maroon-700' : '' }} hover:scale-105 hover:-translate-y-2 hover:shadow-2xl hover:shadow-gray-900/40 transition-all duration-200">
                                    <div class="w-1.5 rounded-l-lg h-full bg-maroon-700"></div>
                                    <div class="pl-2 flex flex-col text-maroon-900">
                                        <span class="text-lg font-bold mb-0.5">{{ $periodStats[$key] ?? 0 }}</span>
                                        <span class="text-xs font-semibold mb-1">{{ $label }}</span>
                                    </div>
                                </a>
                            @endforeach
                            <!-- Yearly Card -->
                            <a href="{{ route('dashboard', array_merge(request()->except('page', 'period'), ['period' => 'year'])) }}"
                               class="flex-1 rounded-lg p-2 flex flex-row items-start shadow min-w-0 bg-white/30 backdrop-blur border border-white/40 transition cursor-pointer {{ $currentPeriod === 'year' ? 'ring-2 ring-maroon-700' : '' }} hover:scale-105 hover:-translate-y-2 hover:shadow-2xl hover:shadow-gray-900/40 transition-all duration-200">
                                <div class="w-1.5 rounded-l-lg h-full bg-maroon-700"></div>
                                <div class="pl-2 flex flex-col text-maroon-900">
                                    <span class="text-lg font-bold mb-0.5">{{ $type === 'Citation' ? ($stats['citation']['year'] ?? 0)
                                        : ($type === 'Publication' ? ($stats['publication']['year'] ?? 0)
                                        : (($stats['publication']['year'] ?? 0) + ($stats['citation']['year'] ?? 0))) }}</span>
                                    <span class="text-xs font-semibold mb-1">Yearly</span>
                                </div>
                            </a>
                        </div>

                        {{-- Status Filter Pills --}}
                        <!-- Applications Table -->
                        <div class="bg-white/30 backdrop-blur border border-white/40 rounded-xl shadow p-2 overflow-y-auto flex-1 min-h-0">
                            <div class="flex items-center justify-between mb-2">
                                <h2 class="text-sm font-semibold pl-2 mb-0">List of Requests</h2>
                                <div class="flex gap-2 flex-wrap mb-2 dashboard-chart-filter">
                                    @php
                                        $currentStatus = request('status');
                                        $filters = [
                                            ['label' => 'All', 'value' => null, 'icon' => 'list'],
                                            ['label' => 'Pending', 'value' => 'pending', 'icon' => 'clock'],
                                            ['label' => 'Endorsed', 'value' => 'endorsed', 'icon' => 'check'],
                                            ['label' => 'Rejected', 'value' => 'rejected', 'icon' => 'x'],
                                        ];
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
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-maroon-200 text-xs border border-white/40 rounded-t-xl overflow-hidden">
                                    @if($requests->count())
                                    <thead class="bg-white/30 backdrop-blur border-b border-white/40 rounded-t-xl">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-bold text-maroon-900 uppercase tracking-wider">Date</th>
                                            <th class="px-2 py-2 text-left font-bold text-maroon-900 uppercase tracking-wider">Request Code</th>
                                            <th class="px-2 py-2 text-left font-bold text-maroon-900 uppercase tracking-wider">Name</th>
                                            <th class="px-2 py-2 pl-9 text-left font-bold text-maroon-900 uppercase tracking-wider">Status</th>
                                            <th class="px-2 py-2 text-center font-bold text-maroon-900 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                                    <tbody class="divide-y divide-maroon-100">
                                        @foreach($requests as $request)
                                        <tr class="{{ $loop->even ? 'bg-white/30 backdrop-blur' : '' }} hover:bg-white/40 transition">
                                            <td class="px-4 py-1 text-maroon-900 font-semibold">{{ $request->requested_at ? \Carbon\Carbon::parse($request->requested_at)->setTimezone('Asia/Manila')->format('M d, Y H:i') : '' }}</td>
                                            <td class="px-2 py-1 font-bold text-maroon-900">{{ $request->request_code }}</td>
                                            <td class="px-2 py-1 text-maroon-900 font-semibold">{{ $request->user ? $request->user->name : 'N/A' }}</td>
                                            <td class="px-2 py-1 text-center align-middle">
                                                <form action="{{ route('admin.requests.status', $request->id) }}" method="POST" class="inline-block w-full">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="mx-auto block px-2 py-0.5 pr-4 min-w-[90px] rounded-full font-semibold text-xs leading-5 border border-white/40 appearance-none transition
    {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800 focus:ring-0' : ($request->status === 'endorsed' ? 'bg-green-100 text-green-800 focus:ring-0' : ($request->status === 'rejected' ? 'bg-red-100 text-red-800 focus:ring-2 focus:ring-red-500' : 'bg-white/30 backdrop-blur text-maroon-900 focus:ring-0')) }}"
                                                        style="background-position:right 0.5rem center; background-repeat:no-repeat; background-size:1.25em 1.25em;"
                                                        onchange="if(confirm('Change status?')) this.form.submit()"
                                                        >
                                                        <option value="pending" {{ $request->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="endorsed" {{ $request->status === 'endorsed' ? 'selected' : '' }}>Endorsed</option>
                                                        <option value="rejected" {{ $request->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                    </select>
                                                </form>
                                        </td>
                                            <td class="px-2 py-1 text-center">
                                                <div class="flex flex-row gap-1 items-center justify-center">
                                                <!-- Review Button (Blue) -->
                                                    <button type="button" class="p-1 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition-colors" title="Review" onclick="openReviewModal({{ $request->id }})">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <!-- Delete Button (Red) -->
                                                <form action="{{ route('admin.requests.destroy', $request->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this request?');" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                        <button type="submit" class="p-1 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors" title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                        @endforeach
                            </tbody>
                                                        @else
                                    <tbody>
                                        <tr><td colspan="5" class="p-4 text-center text-maroon-400">No requests found.</td></tr>
                                    </tbody>
                                    @endif
                        </table>
                                <div class="mt-2">{{ $requests->links() }}</div>
                    </div>
                            {{-- Floating Active Filters Bar (Inside Table Card) --}}
                            @php
                                $activeFilters = [];
                                if (request('type')) $activeFilters['type'] = ucfirst(request('type'));
                                if (request('period')) $activeFilters['period'] = ucfirst(request('period'));
                                if (request('status')) $activeFilters['status'] = ucfirst(request('status'));
                                if (request('search')) $activeFilters['search'] = 'Search: "' . request('search') . '"';
                            @endphp
                            @if(count($activeFilters))
                                <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 z-10 bg-white/30 backdrop-blur rounded-lg shadow-md border border-white/40 px-3 py-1">
                                    <div class="flex items-center justify-center gap-2 whitespace-nowrap">
                                        <span class="text-xs text-maroon-900 font-medium mr-1">Filters:</span>
                                        @foreach($activeFilters as $key => $label)
                                            <form method="GET" action="{{ route('dashboard') }}" class="inline">
                                                @foreach(request()->except($key, 'page') as $k => $v)
                                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                                @endforeach
                                                <button type="submit" class="inline-flex items-center px-2 py-0.5 rounded-full bg-white text-maroon-900 text-xs font-semibold border border-yellow-400 hover:bg-yellow-50 transition">
                                                    {{ $label }}
                                                    <span class="ml-1 text-maroon-700 font-bold">&times;</span>
                                                </button>
                                            </form>
                                        @endforeach
                                        <form method="GET" action="{{ route('dashboard') }}" class="inline">
                                            <button type="submit" class="ml-1 px-2 py-0.5 rounded-full bg-maroon-900 text-white text-xs font-semibold border border-maroon-700 hover:bg-maroon-800 transition">Clear All</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                                    </div>
                                </div>
                    <!-- Right Column: Recent Applications & Chart -->
                    <div class="flex flex-col gap-3 h-full flex-1">
                        <!-- Activity Log Card -->
                        <div class="bg-white/30 backdrop-blur border border-white/40 rounded-xl shadow pb-2 px-2 overflow-visible flex-1 max-h-[45vh] h-[45vh] relative">
                            <div class="bg-white sticky top-0 left-0 right-0 z-10 -mx-2 w-[calc(100%+1rem)] pt-4 pb-2 border-b border-white/40 rounded-t-xl">
                                <h2 class="text-sm font-semibold pl-4">Activity Log</h2>
                            </div>
                            <div class="overflow-y-auto max-h-[calc(45vh-3rem)] activity-log-scroll">
                            <ul class="space-y-1">
                                @foreach($activityLogs as $log)
                                        <li class="grid grid-cols-[auto_1fr_auto_16px_80px] items-center gap-2 bg-white/30 backdrop-blur rounded-lg p-1">
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
                                                $desc = 'Request <b>' . e($log->details['request_code'] ?? '') . '</b> submitted (' . e($log->details['type'] ?? '') . ')';
                                            } elseif ($log->action === 'status_changed') {
                                                $desc = 'Status changed <b>' . e($log->details['old_status'] ?? '') . '</b> → <b>' . e($log->details['new_status'] ?? '') . '</b> for <b>' . e($log->details['request_code'] ?? '') . '</b>';
                                            } elseif ($log->action === 'deleted') {
                                                $desc = 'Request <b>' . e($log->details['request_code'] ?? '') . '</b> deleted';
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
                                        <span class="min-w-0 text-xs text-maroon-900 font-bold truncate">
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
                        <!-- Combined Chart Card: Request Stats + Status Breakdown -->
                        <div class="bg-white/30 backdrop-blur border border-white/40 rounded-xl shadow p-2 flex flex-col md:flex-row items-stretch justify-center flex-1 overflow-y-auto min-h-[220px] max-h-[45vh] h-[45vh] gap-2">
                            <!-- Request Stats (Line Chart) -->
                            <div class="flex-[2_2_0%] flex flex-col items-center justify-center min-w-0 overflow-hidden px-3">
                                <h2 class="text-sm font-semibold mb-2 text-left w-full">Request Stats (Last 12 Months)</h2>
                                <div class="w-full flex-1 flex items-center justify-center">
                                    <canvas id="monthlyChart" class="w-full h-40 max-h-[160px]" style="max-height:160px;"></canvas>
                                </div>
                            </div>
                            <!-- Status Breakdown (Donut Chart + Legend) -->
                            <div class="flex-[1_1_0%] flex flex-col items-center justify-center min-w-0 overflow-hidden border-t md:border-t-0 md:border-l border-yellow-200 pl-0 md:pl-4 pt-2 md:pt-0 px-3">
                                <div class="w-full flex flex-col items-center justify-center">
                                    <canvas id="statusChart" class="w-28 h-28 max-w-[112px] max-h-[112px]" style="max-width:112px;max-height:112px;"></canvas>
                                    <div class="mt-2 w-full flex flex-col gap-1 text-xs">
                                        @php
                                            $statusLabels = ['Pending', 'Endorsed', 'Rejected'];
                                            $statusColors = ['bg-yellow-400', 'bg-green-500', 'bg-red-500'];
                                            $total = array_sum(array_values($statusCounts));
                            @endphp
                                        @foreach($statusLabels as $i => $label)
                                            <div class="flex w-full items-center">
                                                <span class="inline-block w-2 h-2 rounded-full {{ $statusColors[$i] }} mr-2"></span>
                                                <span class="font-semibold">{{ $label }}</span>
                                                <span class="flex-1"></span>
                                                <span class="text-right w-8">{{ $statusCounts[strtolower($label)] ?? 0 }}</span>
                                                <span class="ml-2 text-gray-500 text-right w-12">@if($total > 0){{ number_format(100 * ($statusCounts[strtolower($label)] ?? 0) / $total, 1) }}%@else 0%@endif</span>
                                                        </div>
                                        @endforeach
                                    </div>
                                    </div>
                                </div>
                                                        </div>
                                            </div>
                                        </div>
                <!-- Review Modal (Compact, No-Scroll, Two-Column) -->
                <div id="reviewModal" class="fixed inset-0 z-50 hidden">
                    <div class="fixed inset-0 bg-black bg-opacity-50"></div>
                    <div class="fixed inset-0 flex items-center justify-center p-2">
                        <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl min-w-[700px] min-h-[400px]" style="max-width:900px;">
                            <!-- Header -->
                            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                                <h2 class="text-lg font-bold text-maroon-900">Request Review</h2>
                                <button onclick="closeReviewModal()" class="text-gray-400 hover:text-maroon-900 text-2xl font-bold">&times;</button>
                            </div>
                            <!-- Loading State -->
                            <div id="modalLoading" class="p-8 text-center">
                                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-maroon-900 mx-auto"></div>
                                <p class="mt-2 text-gray-600 text-sm">Loading request details...</p>
                            </div>
                            <!-- Content -->
                            <div id="modalContent" class="hidden">
                                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- Left Column -->
                                    <div class="flex flex-col gap-2">
                                        <!-- Combined Request Summary & Applicant Card -->
                                        <div class="bg-yellow-50 rounded-lg p-2 border border-yellow-200 text-xs">
                                            <div class="font-semibold text-maroon-900 mb-1">Request & Applicant</div>
                                            <div class="grid grid-cols-2 gap-x-2 gap-y-1">
                                                <div><span class="text-gray-600">Code:</span> <span id="modalRequestCode" class="font-bold text-maroon-900">-</span></div>
                                                <div><span class="text-gray-600">Type:</span> <span id="modalType" class="font-bold text-maroon-900">-</span></div>
                                                <div><span class="text-gray-600">Status:</span> <span id="modalStatus" class="px-2 py-0.5 rounded-full text-xs font-semibold">-</span></div>
                                                <div><span class="text-gray-600">Submitted:</span> <span id="modalDate" class="text-maroon-900">-</span></div>
                                                <div><span class="text-gray-600">Applicant:</span> <span id="modalUserName" class="text-maroon-900">-</span></div>
                                                <div><span class="text-gray-600">Email:</span> <span id="modalUserEmail" class="text-maroon-900">-</span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Right Column -->
                                    <div class="flex flex-col gap-2">
                                        <!-- Files Section -->
                                        <div class="bg-white rounded-lg p-2 border border-gray-200 text-xs">
                                            <div class="font-semibold text-maroon-900 mb-1">Files</div>
                                            <div id="modalFiles" class="space-y-1"></div>
                                    </div>
                                </div>
                                    <!-- Full Width: Signatories -->
                                    <div class="md:col-span-2 bg-white rounded-lg p-2 border border-gray-200 text-xs">
                                        <div class="font-semibold text-maroon-900 mb-1">Signatories Required</div>
                                        <div id="modalFormData" class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-1"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Footer -->
                            <div class="flex justify-end gap-2 p-4 border-t border-gray-200 bg-gray-50">
                                <button onclick="closeReviewModal()" class="px-3 py-1 text-gray-600 hover:text-gray-800 font-medium text-xs">Close</button>
                                <button onclick="closeReviewModal()" class="px-3 py-1 bg-maroon-900 text-white rounded-lg hover:bg-maroon-800 font-medium text-xs">Review Complete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
// Real-time dashboard updates using Server-Sent Events
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
    li.className = 'grid grid-cols-[auto_1fr_auto_16px_80px] items-center gap-2 bg-white/30 backdrop-blur rounded-lg p-1';
    
    // Create icon
    const icon = document.createElement('span');
    icon.className = `flex items-center justify-center w-7 h-7 rounded-full bg-white border ${getIconColor(log.action)}`;
    icon.innerHTML = getIconSvg(log.action);
    
    // Create description
    const desc = document.createElement('span');
    desc.className = 'min-w-0 text-xs text-maroon-900 font-medium truncate';
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
    // Find the specific bell icon in the navbar using its ID
    const bellButton = document.getElementById('notification-bell');
    if (!bellButton) return;
    
    const bellRect = bellButton.getBoundingClientRect();
    
    // Create notification that animates from the bell
    const notification = document.createElement('div');
    notification.className = 'fixed z-50 bg-green-500 text-white px-3 py-1.5 rounded-lg shadow-lg text-sm font-medium transform transition-all duration-500';
    notification.textContent = 'Dashboard updated';
    
    // Start position: inside the bell (left side)
    notification.style.left = (bellRect.left + bellRect.width/2) + 'px';
    notification.style.top = (bellRect.top + bellRect.height/2) + 'px';
    notification.style.transform = 'scale(0) translateX(0)';
    notification.style.transformOrigin = 'center';
    
    document.body.appendChild(notification);
    
    // Animate out to the left (coming out of the bell)
    setTimeout(() => {
        notification.style.transform = 'scale(1) translateX(-60px)';
        notification.style.left = (bellRect.left - 60) + 'px';
        notification.style.top = (bellRect.top + bellRect.height/2 - 10) + 'px';
    }, 100);
    
    // After 3 seconds, animate back to the right (going back into the bell)
    setTimeout(() => {
        notification.style.transform = 'scale(0) translateX(60px)';
        notification.style.left = (bellRect.left + bellRect.width/2) + 'px';
        notification.style.opacity = '0';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Initialize real-time updates when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeRealTimeUpdates();
    
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
    const activityLogContainer = document.querySelector('.bg-yellow-100.rounded-xl.shadow.p-2.overflow-auto.flex-1 ul');
    if (activityLogContainer) {
        activityLogContainer.classList.add('activity-log-list');
    }
});

// Clean up connection when page unloads
window.addEventListener('beforeunload', function() {
    if (eventSource) {
        eventSource.close();
    }
});

// Simple and robust review modal functions
function openReviewModal(requestId) {
    if (!requestId) {
        console.error('No request ID provided');
        return;
    }
    
    console.log('Opening modal for request ID:', requestId);
    
    // Show modal and loading state
    document.getElementById('reviewModal').classList.remove('hidden');
    document.getElementById('modalLoading').classList.remove('hidden');
    document.getElementById('modalContent').classList.add('hidden');
    
    // Fetch request data
    fetch(`/admin/requests/${requestId}/data`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load request data');
            }
            return response.json();
        })
        .then(data => {
            console.log('Request data loaded:', data);
            populateModal(data);
        })
        .catch(error => {
            console.error('Error loading request data:', error);
            showError('Failed to load request data. Please try again.');
        })
        .finally(() => {
            // Hide loading, show content
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
    statusElement.className = 'px-2 py-0.5 rounded-full text-xs font-semibold';
    if (data.status === 'pending') {
        statusElement.classList.add('bg-yellow-400', 'text-maroon-900');
    } else if (data.status === 'endorsed') {
        statusElement.classList.add('bg-green-500', 'text-white');
    } else if (data.status === 'rejected') {
        statusElement.classList.add('bg-red-500', 'text-white');
    }
    // Form Data (compact grid) - Use server-side extracted signatories
    const formDataContainer = document.getElementById('modalFormData');
    formDataContainer.innerHTML = '';
    
    if (data.signatories && data.signatories.length > 0) {
        data.signatories.forEach(signatory => {
            const fieldDiv = document.createElement('div');
            fieldDiv.className = 'flex flex-col';
            fieldDiv.innerHTML = `<span class="font-medium text-gray-600 text-xs">${signatory.role}: ${signatory.field}</span><span class="text-maroon-900 text-xs font-semibold">${signatory.name}</span>`;
            formDataContainer.appendChild(fieldDiv);
        });
    } else {
        formDataContainer.innerHTML = '<div class="text-gray-500 text-xs">No signatories found in form data</div>';
    }
    // Files (compact)
    const filesContainer = document.getElementById('modalFiles');
    filesContainer.innerHTML = '';
    if (data.files && data.files.length > 0) {
        data.files.forEach(file => {
            const fileDiv = document.createElement('div');
            fileDiv.className = 'flex items-center justify-between bg-gray-50 rounded p-1 mb-1';
            fileDiv.innerHTML = `
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-700">${file.name}</span>
                    <span class="text-xs text-gray-400">(${file.size})</span>
                </div>
                <div class="flex gap-1">
                    <a href="/storage/${file.path}" target="_blank" class="px-2 py-0.5 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors">View</a>
                    <a href="/storage/${file.path}" download="${file.name}" class="px-2 py-0.5 bg-green-500 text-white text-xs rounded hover:bg-green-600 transition-colors">Download</a>
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

function formatFieldName(key) {
    if (typeof key !== 'string') return 'Unknown Field';
    return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}

function showError(message) {
    const contentDiv = document.getElementById('modalContent');
    contentDiv.innerHTML = `
        <div class="p-8 text-center">
            <div class="text-red-500 text-lg font-semibold mb-2">Error</div>
            <p class="text-gray-600">${message}</p>
        </div>
    `;
    contentDiv.classList.remove('hidden');
}
</script>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let monthlyChartInstance = null;
let statusChartInstance = null;

function updateChartsWithData(data) {
    // Update Monthly Chart
    const monthlyChartElement = document.getElementById('monthlyChart');
    if (monthlyChartElement) {
        if (monthlyChartInstance) monthlyChartInstance.destroy();
        const months = data.months;
        const pubData = Object.values(data.monthlyCounts.Publication);
        const citData = Object.values(data.monthlyCounts.Citation);
        const typeFilter = data.type;
        let datasets = [];
        if (!typeFilter || typeFilter === 'Publication') {
            datasets.push({
                label: 'Publications',
                data: pubData,
                backgroundColor: 'rgba(139, 69, 19, 0.7)',
                borderColor: 'rgba(139, 69, 19, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
            });
        }
        if (!typeFilter || typeFilter === 'Citation') {
            datasets.push({
                label: 'Citations',
                data: citData,
                backgroundColor: 'rgba(128, 0, 32, 0.7)',
                borderColor: 'rgba(128, 0, 32, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
            });
        }
        monthlyChartInstance = new Chart(monthlyChartElement.getContext('2d'), {
            type: 'line',
            data: {
                labels: months.map(m => m.replace(/\d{4}-/, '')),
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true },
                    tooltip: { enabled: true }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision:0 } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
    // Update Status Donut Chart
    const statusChartElement = document.getElementById('statusChart');
    if (statusChartElement) {
        if (statusChartInstance) statusChartInstance.destroy();
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
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                }
            }
        });
    }
}

function getCurrentFilters() {
    const url = new URL(window.location.href);
    return {
        type: url.searchParams.get('type'),
        period: url.searchParams.get('period'),
        status: url.searchParams.get('status'),
        search: url.searchParams.get('search')
    };
}

function fetchAndUpdateCharts(filters) {
    const params = new URLSearchParams(filters).toString();
    fetch(`/admin/dashboard/data?${params}`)
        .then(res => res.json())
        .then(data => {
            if (!data.months || !data.monthlyCounts || !data.statusCounts) {
                console.error('Chart data missing keys:', data);
                alert('Chart data could not be loaded. Please try again or contact support.');
                return;
            }
            updateChartsWithData({
                months: data.months,
                monthlyCounts: data.monthlyCounts,
                statusCounts: data.statusCounts,
                type: filters.type
            });
        })
        .catch(err => {
            console.error('Error fetching chart data:', err);
            alert('Failed to load chart data. Please check your connection or contact support.');
        });
}

// Attach event listeners after DOM is ready
// document.addEventListener('DOMContentLoaded', () => {
//     fetchAndUpdateCharts(getCurrentFilters());
// });
document.addEventListener('turbo:render', () => {
    fetchAndUpdateCharts(getCurrentFilters());
});
document.removeEventListener && document.removeEventListener('turbo:load', fetchAndUpdateCharts);
</script>
</x-app-layout>

<style>
/* Transparent scrollbar track, visible thumb for activity log */
.activity-log-scroll::-webkit-scrollbar {
    width: 8px;
    background: transparent;
}
.activity-log-scroll::-webkit-scrollbar-thumb {
    background: #b91c1c; /* Maroon-700 */
    border-radius: 4px;
}
.activity-log-scroll {
    scrollbar-color: #b91c1c transparent;
}
</style>

