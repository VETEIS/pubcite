<x-app-layout>
    <!-- Removed extra custom header here -->
    <div class="flex min-h-[calc(100vh-4rem)]">
        <!-- Sidebar -->
        <aside class="w-20 bg-gradient-to-b from-maroon-900 to-maroon-700 text-white flex flex-col items-center py-8 shadow-lg">
            <nav class="flex flex-col gap-8 w-full items-center">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 group focus:outline-none">
                    <svg class="w-7 h-7 text-white group-hover:text-maroon-200 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" /></svg>
                    <span class="text-xs mt-1 group-hover:text-maroon-200">Overview</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center gap-1 group focus:outline-none">
                    <svg class="w-7 h-7 text-white group-hover:text-maroon-200 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <span class="text-xs mt-1 group-hover:text-maroon-200">Users</span>
                </a>
                <button class="flex flex-col items-center gap-1 group focus:outline-none">
                    <svg class="w-7 h-7 text-white group-hover:text-maroon-200 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2M9 17a4 4 0 01-8 0v-2a4 4 0 018 0v2zm0 0v-2a4 4 0 018 0v2zm0 0v-2a4 4 0 018 0v2z" /></svg>
                    <span class="text-xs mt-1 group-hover:text-maroon-200">Reports</span>
                </button>
                <button class="flex flex-col items-center gap-1 group focus:outline-none">
                    <svg class="w-7 h-7 text-white group-hover:text-maroon-200 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span class="text-xs mt-1 group-hover:text-maroon-200">Settings</span>
                </button>
            </nav>
        </aside>
        <!-- Main Content -->
        <div class="flex-1 flex items-center justify-center min-h-[calc(100vh-4rem)] p-4 m-0">
            <div class="w-full h-full flex-1 rounded-2xl shadow-xl bg-gray-100 p-8 flex flex-col justify-center items-stretch overflow-auto">
                <!-- Dashboard Header with Navigation Tabs -->
                <div class="relative flex items-center mb-6">
                    <h1 class="text-2xl font-bold text-maroon-900">
                        @if(request('type') === 'Citation')
                            Citation
                        @elseif(request('type') === 'Publication')
                            Publication
                        @else
                            Overview
                        @endif
                    </h1>
                    <!-- Centered Navigation Tabs -->
                    <div class="absolute left-1/2 transform -translate-x-1/2 flex gap-2">
                        <a href="{{ route('dashboard', array_merge(request()->except('type', 'page'), ['type' => 'Citation'])) }}" class="px-5 py-1.5 rounded-full font-semibold shadow border border-maroon-900 focus:outline-none transition-colors {{ request('type') === 'Citation' ? 'bg-maroon-900 text-white' : 'bg-gray-100 text-maroon-900 hover:bg-gray-300' }}">Citation</a>
                        <a href="{{ route('dashboard', array_merge(request()->except('type', 'page'), ['type' => 'Publication'])) }}" class="px-5 py-1.5 rounded-full font-semibold shadow border border-maroon-900 focus:outline-none transition-colors {{ request('type') === 'Publication' ? 'bg-maroon-900 text-white' : 'bg-gray-100 text-maroon-900 hover:bg-gray-300' }}">Publication</a>
                        <a href="{{ route('dashboard', array_merge(request()->except('type', 'page'))) }}" class="px-5 py-1.5 rounded-full font-semibold shadow border border-maroon-900 focus:outline-none transition-colors {{ !request('type') ? 'bg-maroon-900 text-white' : 'bg-gray-100 text-maroon-900 hover:bg-gray-300' }}">View All</a>
                    </div>
                    <!-- Search Input Field -->
                    <div class="absolute right-0 flex items-center">
                        <form method="GET" action="{{ route('dashboard') }}" class="relative">
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
                    <div class="flex flex-col gap-3 h-full flex-1">
                        <!-- Stat Cards (Compact Row) -->
                        @php
                            $type = request('type');
                            $weekStat = $type === 'Citation' ? ($stats['citation']['week'] ?? 0)
                                : ($type === 'Publication' ? ($stats['publication']['week'] ?? 0)
                                : (($stats['publication']['week'] ?? 0) + ($stats['citation']['week'] ?? 0)));
                            $monthStat = $type === 'Citation' ? ($stats['citation']['month'] ?? 0)
                                : ($type === 'Publication' ? ($stats['publication']['month'] ?? 0)
                                : (($stats['publication']['month'] ?? 0) + ($stats['citation']['month'] ?? 0)));
                            $quarterStat = $type === 'Citation' ? ($stats['citation']['quarter'] ?? 0)
                                : ($type === 'Publication' ? ($stats['publication']['quarter'] ?? 0)
                                : (($stats['publication']['quarter'] ?? 0) + ($stats['citation']['quarter'] ?? 0)));
                        @endphp
                        <div class="flex flex-row gap-3 mb-2">
                            <a href="{{ route('dashboard', array_merge(request()->except('period', 'page'), ['period' => 'week'])) }}" class="flex-1 rounded-lg p-2 flex flex-row items-start shadow min-w-0 bg-yellow-100 transition cursor-pointer {{ request('period', 'week') === 'week' ? 'ring-2 ring-maroon-700' : 'hover:bg-yellow-200' }}">
                                <div class="w-1.5 rounded-l-lg h-full bg-maroon-700"></div>
                                <div class="pl-2 flex flex-col text-maroon-900">
                                    <span class="text-lg font-bold mb-0.5">{{ $weekStat }}</span>
                                    <span class="text-xs font-semibold mb-1">Weekly</span>
                                </div>
                            </a>
                            <a href="{{ route('dashboard', array_merge(request()->except('period', 'page'), ['period' => 'month'])) }}" class="flex-1 rounded-lg p-2 flex flex-row items-start shadow min-w-0 bg-yellow-100 transition cursor-pointer {{ request('period') === 'month' ? 'ring-2 ring-green-700' : 'hover:bg-yellow-200' }}">
                                <div class="w-1.5 rounded-l-lg h-full bg-green-700"></div>
                                <div class="pl-2 flex flex-col text-maroon-900">
                                    <span class="text-lg font-bold mb-0.5">{{ $monthStat }}</span>
                                    <span class="text-xs font-semibold mb-1">Monthly</span>
                                </div>
                            </a>
                            <a href="{{ route('dashboard', array_merge(request()->except('period', 'page'), ['period' => 'quarter'])) }}" class="flex-1 rounded-lg p-2 flex flex-row items-start shadow min-w-0 bg-yellow-100 transition cursor-pointer {{ request('period') === 'quarter' ? 'ring-2 ring-yellow-600' : 'hover:bg-yellow-200' }}">
                                <div class="w-1.5 rounded-l-lg h-full bg-yellow-500"></div>
                                <div class="pl-2 flex flex-col text-maroon-900">
                                    <span class="text-lg font-bold mb-0.5">{{ $quarterStat }}</span>
                                    <span class="text-xs font-semibold mb-1">Quarterly</span>
                                </div>
                            </a>
                        </div>
                        <!-- Applications Table (Expanding) -->
                        <div class="bg-yellow-100 rounded-xl shadow p-2 overflow-auto flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <h2 class="text-sm font-semibold pl-2 mb-0">List of Requests</h2>
                                <div class="flex gap-2">
                                    <a href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => null])) }}" class="px-3 py-1 rounded-full font-semibold text-xs transition {{ !request('status') ? 'bg-maroon-900 text-white shadow' : 'bg-gray-100 text-maroon-900 hover:bg-gray-300' }}" id="status-all-link">All</a>
                                    <a href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'endorsed'])) }}" class="px-3 py-1 rounded-full font-semibold text-xs transition {{ request('status') === 'endorsed' ? 'bg-green-600 text-white shadow' : 'bg-gray-100 text-maroon-900 hover:bg-gray-300' }}" id="status-endorsed-link">Endorsed</a>
                                    <a href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}" class="px-3 py-1 rounded-full font-semibold text-xs transition {{ request('status') === 'pending' ? 'bg-yellow-400 text-maroon-900 shadow' : 'bg-gray-100 text-maroon-900 hover:bg-gray-300' }}" id="status-pending-link">Pending</a>
                                    <a href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'rejected'])) }}" class="px-3 py-1 rounded-full font-semibold text-xs transition {{ request('status') === 'rejected' ? 'bg-red-500 text-white shadow' : 'bg-gray-100 text-maroon-900 hover:bg-gray-300' }}" id="status-rejected-link">Rejected</a>
                        </div>
                    </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-maroon-200 text-xs">
                                    @if($requests->count())
                                    <thead class="bg-yellow-200">
                                        <tr>
                                            <th class="px-2 py-1 text-left font-bold text-maroon-900 uppercase tracking-wider">Date</th>
                                            <th class="px-2 py-1 text-left font-bold text-maroon-900 uppercase tracking-wider">Request Code</th>
                                            <th class="px-2 py-1 text-left font-bold text-maroon-900 uppercase tracking-wider">Name</th>
                                            <th class="px-2 py-1 pl-9 text-left font-bold text-maroon-900 uppercase tracking-wider">Status</th>
                                            <th class="px-2 py-1 text-center font-bold text-maroon-900 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                                    <tbody class="bg-yellow-50 divide-y divide-maroon-100">
                                        @foreach($requests as $request)
                                        <tr>
                                            <td class="px-2 py-1 text-maroon-900">{{ $request->requested_at ? \Carbon\Carbon::parse($request->requested_at)->format('M d, Y') : '' }}</td>
                                            <td class="px-2 py-1 font-bold text-maroon-900">{{ $request->request_code }}</td>
                                            <td class="px-2 py-1 text-maroon-900">{{ $request->user ? $request->user->name : 'N/A' }}</td>
                                            <td class="px-2 py-1 text-center align-middle">
                                                <form action="{{ route('admin.requests.status', $request->id) }}" method="POST" class="inline-block w-full">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="mx-auto block px-2 py-0.5 pr-4 min-w-[90px] rounded-full font-semibold text-xs leading-5 focus:outline-none focus:ring-2 focus:ring-maroon-400 transition bg-yellow-100 border border-yellow-300 text-maroon-900 appearance-none"
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
                                    </div>
                                </div>
                    <!-- Right Column: Recent Applications & Chart -->
                    <div class="flex flex-col gap-3 h-full flex-1">
                        <!-- Activity Log Card -->
                        <div class="bg-yellow-100 rounded-xl shadow p-2 overflow-auto flex-1">
                            <h2 class="text-sm font-semibold mb-2 pl-2">Activity Log</h2>
                            <ul class="space-y-1">
                                @foreach($activityLogs as $log)
                                    <li class="flex items-center gap-2 bg-yellow-50 rounded-lg p-1">
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
                                        <span class="flex items-center justify-center w-7 h-7 rounded-full bg-white border {{ $iconColor }}">
                                            @if($icon === 'plus-circle')
                                                <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                            @elseif($icon === 'refresh-cw')
                                                <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.13-3.36L23 10M1 14l5.37 5.36A9 9 0 0020.49 15"/></svg>
                                            @elseif($icon === 'trash-2')
                                                <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m5 0V4a2 2 0 012-2h0a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                            @else
                                                <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                                                @endif
                                                            </span>
                                        <div class="flex-1 min-w-0">
                                            <span class="text-xs text-maroon-900" style="font-weight:500;" >{!! $desc !!}</span>
                                            <div class="text-xs text-gray-500">
                                                @if($log->user)
                                                    {{ $log->user->name }}
                                                        @else
                                                    System
                                                        @endif
                                                &middot;
                                                <span title="{{ $log->created_at->toDayDateTimeString() }}">{{ $log->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- Combined Chart Card: Request Stats + Status Breakdown -->
                        <div class="bg-yellow-100 rounded-xl shadow p-2 flex flex-col md:flex-row items-stretch justify-center flex-1 overflow-hidden min-h-[220px] max-h-[260px] gap-2">
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
                <!-- Review Modal (Alpine.js for UI only, not data loading) -->
                {{-- Keep Alpine.js for review modal interactivity if needed --}}
            </div>
        </div>
    </div>

<script>
        // Remove Alpine.js data state and AJAX/data-loading logic for dashboard content
        // Only keep Alpine.js for review modal interactivity if needed
        // (No adminDashboard() Alpine.js component for data)
</script>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Bar/Line Chart
        const monthlyChartElement = document.getElementById('monthlyChart');
        if (monthlyChartElement) {
            const months = @json($months);
            const pubData = @json(array_values($monthlyCounts['Publication']));
            const citData = @json(array_values($monthlyCounts['Citation']));
            const typeFilter = @json($type);
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
            new Chart(monthlyChartElement.getContext('2d'), {
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
        // Status Donut Chart
        const statusChartElement = document.getElementById('statusChart');
        if (statusChartElement) {
            const statusCounts = @json(array_values($statusCounts));
            new Chart(statusChartElement.getContext('2d'), {
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
                        legend: { display: false }, // Hide Chart.js legend
                        tooltip: { enabled: true }
                    }
                }
            });
        }
    });
</script>
</x-app-layout>

