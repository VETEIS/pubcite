<x-app-layout>
    <!-- Removed extra custom header here -->
    <div class="flex min-h-[calc(100vh-4rem)]">
        <!-- Sidebar -->
        <aside class="w-20 bg-gradient-to-b from-maroon-900 to-maroon-700 text-white flex flex-col items-center py-8 shadow-lg">
            <nav class="flex flex-col gap-8 w-full items-center">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 group focus:outline-none">
                    <svg class="w-7 h-7 text-white group-hover:text-maroon-200 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" /></svg>
                    <span class="text-xs mt-1 group-hover:text-maroon-200">Dashboard</span>
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
            <div x-data="adminDashboard()" class="w-full h-full flex-1 rounded-2xl shadow-xl bg-yellow-50 p-8 flex flex-col justify-center items-stretch overflow-auto">
                <!-- Dashboard Header with Navigation Tabs -->
                <div class="relative flex items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                    <!-- Centered Navigation Tabs -->
                    <div class="absolute left-1/2 transform -translate-x-1/2 flex gap-2">
                        <button @click="filterRequests('Citation')" class="px-5 py-1.5 rounded font-semibold shadow border border-maroon-900 focus:outline-none transition-colors" :class="activeType === 'Citation' ? 'bg-maroon-900 text-white' : 'bg-maroon-100 text-maroon-900 hover:bg-maroon-200'">Citation</button>
                        <button @click="filterRequests('Publication')" class="px-5 py-1.5 rounded font-semibold shadow border border-maroon-900 focus:outline-none transition-colors" :class="activeType === 'Publication' ? 'bg-maroon-900 text-white' : 'bg-maroon-100 text-maroon-900 hover:bg-maroon-200'">Publication</button>
                        <button @click="clearTypeFilter()" class="px-5 py-1.5 rounded font-semibold shadow border border-maroon-900 focus:outline-none transition-colors" :class="!activeType ? 'bg-maroon-900 text-white' : 'bg-white text-maroon-900 hover:bg-gray-50'">View All</button>
                        </div>
                    <!-- Search Input Field -->
                    <div class="absolute right-0 flex items-center">
                        <div class="relative">
                            <input type="text" x-model="searchQuery" @keyup.enter="performSearch" placeholder="Search..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent bg-white shadow-sm">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <!-- Stat Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 h-full">
                    <!-- Left/Main Column: Stat Cards + Table -->
                    <div class="flex flex-col gap-3 h-full flex-1">
                        <!-- Stat Cards (Compact Row) -->
                        <div class="flex flex-row gap-3 mb-2">
                            <div class="flex-1 bg-maroon-900 text-white rounded-lg p-2 flex flex-col items-start shadow min-w-0">
                                <span class="text-lg font-bold mb-0.5" x-text="stats.publication?.week || 0"></span>
                                <span class="text-xs font-semibold mb-1">Weekly</span>
                            </div>
                            <div class="flex-1 bg-green-600 text-white rounded-lg p-2 flex flex-col items-start shadow min-w-0">
                                <span class="text-lg font-bold mb-0.5" x-text="stats.publication?.month || 0"></span>
                                <span class="text-xs font-semibold mb-1">Monthly</span>
                        </div>
                            <div class="flex-1 bg-yellow-400 text-maroon-900 rounded-lg p-2 flex flex-col items-start shadow min-w-0">
                                <span class="text-lg font-bold mb-0.5" x-text="stats.publication?.quarter || 0"></span>
                                <span class="text-xs font-semibold mb-1">Quarterly</span>
                            </div>
                        </div>
                        <!-- Tabs -->
                        <div class="flex gap-2 mb-2">
                            <button @click="filterByStatus('')" class="px-3 py-1 rounded-full font-semibold text-xs transition" :class="!activeStatus ? 'bg-maroon-900 text-white shadow' : 'bg-gray-100 text-maroon-900 hover:bg-maroon-100'">All</button>
                            <button @click="filterByStatus('endorsed')" class="px-3 py-1 rounded-full font-semibold text-xs transition" :class="activeStatus === 'endorsed' ? 'bg-maroon-900 text-white shadow' : 'bg-gray-100 text-maroon-900 hover:bg-maroon-100'">Endorsed</button>
                            <button @click="filterByStatus('pending')" class="px-3 py-1 rounded-full font-semibold text-xs transition" :class="activeStatus === 'pending' ? 'bg-yellow-400 text-maroon-900 shadow' : 'bg-gray-100 text-maroon-900 hover:bg-yellow-100'">Pending</button>
                            <button @click="filterByStatus('rejected')" class="px-3 py-1 rounded-full font-semibold text-xs transition" :class="activeStatus === 'rejected' ? 'bg-red-500 text-white shadow' : 'bg-gray-100 text-maroon-900 hover:bg-red-100'">Rejected</button>
                        </div>
                        <!-- Applications Table (Expanding) -->
                        <div class="bg-yellow-100 rounded-xl shadow p-2 overflow-auto flex-1">
                            <h2 class="text-sm font-semibold mb-2">List of Requests</h2>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-maroon-200 text-xs">
                                    <thead class="bg-yellow-200">
                                        <tr>
                                            <th class="px-2 py-1 text-left font-bold text-maroon-900 uppercase tracking-wider">Date</th>
                                            <th class="px-2 py-1 text-left font-bold text-maroon-900 uppercase tracking-wider">Request Code</th>
                                            <th class="px-2 py-1 text-left font-bold text-maroon-900 uppercase tracking-wider">Name</th>
                                            <th class="px-2 py-1 text-left font-bold text-maroon-900 uppercase tracking-wider">Status</th>
                                            <th class="px-2 py-1 text-center font-bold text-maroon-900 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                                    <tbody class="bg-yellow-50 divide-y divide-maroon-100">
                                        <template x-for="(request, idx) in requests" :key="(request && request.id) ? request.id : idx">
                                            <template x-if="request">
                                                <tr>
                                                    <td class="px-2 py-1 text-maroon-900" x-text="request.requested_at ? new Date(request.requested_at).toLocaleDateString() : ''"></td>
                                                    <td class="px-2 py-1 font-bold text-maroon-900" x-text="request.request_code"></td>
                                                    <td class="px-2 py-1 text-maroon-900" x-text="request.user ? request.user.name : 'N/A'"></td>
                                                    <td class="px-2 py-1 text-center">
                                                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                              :class="{
                                                                  'bg-green-100 text-green-800': request && request.status === 'endorsed',
                                                                  'bg-red-100 text-red-800': request && request.status === 'rejected',
                                                                  'bg-yellow-100 text-yellow-800': request && request.status === 'pending'
                                                              }"
                                                              x-text="request && request.status ? request.status.charAt(0).toUpperCase() + request.status.slice(1) : ''"></span>
                                        </td>
                                                    <td class="px-2 py-1 text-center">
                                                        <div class="flex flex-row gap-1 items-center justify-center">
                                                <!-- Review Button (Blue) -->
                                                            <button @click="openReviewModal(request)" type="button" class="p-1 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition-colors" title="Review">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <!-- Change Status Button (Yellow) -->
                                                            <button @click="openStatusModal({id: request && request.id, status: request && request.status})" type="button" class="p-1 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 transition-colors" title="Change Status">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                                                    </svg>
                                                </button>
                                                <!-- Delete Button (Red) -->
                                                            <form :action="request && request.id ? `/admin/requests/${request.id}` : '#'" method="POST" onsubmit="return confirm('Are you sure you want to delete this request?');" style="display:inline;">
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
                                            </template>
                                        </template>
                                        <template x-if="requests.length === 0">
                                            <tr><td colspan="5" class="p-4 text-center text-maroon-400">No applications found.</td></tr>
                                        </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
                    <!-- Right Column: Recent Applications & Chart -->
                    <div class="flex flex-col gap-3 h-full flex-1">
                        <!-- Recent Applications (Expanding) -->
                        <div class="bg-yellow-100 rounded-xl shadow p-2 overflow-auto flex-1">
                            <h2 class="text-sm font-semibold mb-2">Recent Requests</h2>
                            <ul class="space-y-1">
                                @foreach($recentApplications as $app)
                                    <li class="flex items-center gap-2 bg-yellow-50 rounded-lg p-1">
                                        <img src="{{ $app->user->profile_photo_url ?? '/images/spjrd.png' }}" alt="{{ $app->user->name ?? 'User' }}" class="w-7 h-7 rounded-full object-cover">
                                        <div class="flex-1">
                                            <div class="font-semibold text-maroon-900 text-xs">{{ $app->user->name ?? 'User' }}</div>
                                            <div class="text-xs text-maroon-700">{{ $app->user->designation ?? 'N/A' }}</div>
                                        </div>
                                        <div class="text-xs text-maroon-400">{{ $app->requested_at ? \Carbon\Carbon::parse($app->requested_at)->format('M d, Y') : '' }}</div>
                                    </li>
                                                @endforeach
                            </ul>
                                                </div>
                        <!-- Bar Chart Placeholder (Expanding) -->
                        <div class="bg-yellow-100 rounded-xl shadow p-2 flex flex-col items-center justify-center flex-1 overflow-auto">
                            <h2 class="text-sm font-semibold mb-2">Request Stats</h2>
                            <div class="w-full h-16 flex items-end gap-1">
                                <div class="flex-1 bg-maroon-400 rounded-t-lg" style="height: 60%;"></div>
                                <div class="flex-1 bg-maroon-500 rounded-t-lg" style="height: 80%;"></div>
                                <div class="flex-1 bg-maroon-300 rounded-t-lg" style="height: 40%;"></div>
                                <div class="flex-1 bg-maroon-600 rounded-t-lg" style="height: 90%;"></div>
                                <div class="flex-1 bg-maroon-200 rounded-t-lg" style="height: 50%;"></div>
                            </div>
                                    </div>
                                </div>
                            </div>

                <!-- Review Modal (Redesigned) -->
                <div x-show="reviewModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
                    <div class="bg-white rounded-xl shadow-xl p-8 w-[700px] h-[500px] flex flex-col relative overflow-hidden">
                        <button @click="closeReviewModal()" class="absolute top-3 right-3 text-gray-400 hover:text-red-600 text-2xl">&times;</button>
                        <h2 class="text-2xl font-bold mb-4 text-maroon-900">Request Review</h2>
                        <div class="flex flex-row gap-6 h-full min-h-0">
                            <!-- Left: Summary & Form Data -->
                            <div class="flex-1 flex flex-col gap-4 min-w-0 min-h-0">
                                <!-- Request Summary -->
                                <div class="bg-yellow-100 rounded-lg p-3 mb-2 flex-shrink-0">
                                    <div class="font-semibold text-maroon-900 text-lg mb-1 truncate" x-text="modalData.user?.name || 'Unknown User'"></div>
                                    <div class="text-xs text-gray-700 mb-1">Type: <span class="font-semibold" x-text="modalData.type"></span></div>
                                    <div class="text-xs text-gray-700 mb-1">Status: <span class="font-semibold capitalize" x-text="modalData.status"></span></div>
                                    <div class="text-xs text-gray-700 mb-1">Request Code: <span class="font-mono" x-text="modalData.request_code"></span></div>
                                    <div class="text-xs text-gray-700">Date: <span x-text="modalData.requested_at ? new Date(modalData.requested_at).toLocaleString() : ''"></span></div>
                                </div>
                                <!-- Form Data -->
                                <div class="bg-gray-50 rounded-lg p-3 flex-1 min-h-0 overflow-hidden flex flex-col justify-between">
                                    <h3 class="font-semibold text-maroon-800 mb-2 text-sm">Form Data</h3>
                                    <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-xs">
                                        <template x-if="modalData.form_data">
                                            <template x-for="(value, key) in modalData.form_data" :key="key">
                                                <template x-if="['name','academicrank','college','campus','papertitle','journaltitle'].includes(key)">
                                                    <template x-if="typeof value !== 'object' || value === null">
                                                        <div class="contents">
                                                            <dt class="font-semibold text-maroon-900 truncate" :title="key" x-text="{
                                                                name: 'Name',
                                                                academicrank: 'Academic Rank',
                                                                college: 'College',
                                                                campus: 'Campus',
                                                                papertitle: 'Paper Title',
                                                                journaltitle: 'Journal Title'
                                                            }[key] || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())"></dt>
                                                            <dd class="truncate" :title="value" x-text="value"></dd>
                                                        </div>
                                                    </template>
                                                    <template x-if="Array.isArray(value)">
                                                        <div class="contents">
                                                            <dt class="font-semibold text-maroon-900 truncate" :title="key" x-text="{
                                                                name: 'Name',
                                                                academicrank: 'Academic Rank',
                                                                college: 'College',
                                                                campus: 'Campus',
                                                                papertitle: 'Paper Title',
                                                                journaltitle: 'Journal Title'
                                                            }[key] || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())"></dt>
                                                            <dd class="truncate" :title="value.join(', ')" x-text="value.join(', ')"></dd>
                                                        </div>
                                                    </template>
                                                </template>
                                            </template>
                                        </template>
                                    </dl>
                                            </div>
                                        </div>
                            <!-- Right: Files -->
                            <div class="flex flex-col gap-4 w-72 min-w-0 min-h-0 h-full">
                                <!-- Uploaded PDFs -->
                                <div class="bg-yellow-50 rounded-lg p-3 flex-1 min-h-0 flex flex-col">
                                    <h3 class="font-semibold text-maroon-800 mb-2 text-sm">Uploaded PDFs</h3>
                                    <div class="flex flex-col gap-2 min-h-0 overflow-y-auto">
                                        <template x-if="modalData.files && modalData.files.pdfs">
                                            <template x-for="(pdf, idx) in modalData.files.pdfs" :key="(pdf.name || '') + (pdf.file_name || '') + idx">
                                                <div class="flex items-center gap-2 bg-white rounded shadow-sm px-2 py-1 min-w-0">
                                                    <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h9" /><rect width="16" height="20" x="4" y="2" rx="2" fill="#fff" stroke="#e53e3e" stroke-width="2"/><text x="8" y="16" font-size="8" fill="#e53e3e">PDF</text></svg>
                                                    <span class="font-semibold text-xs text-maroon-900 truncate max-w-[100px]" :title="pdf.file_name" x-text="pdf.file_name"></span>
                                                    <template x-if="!pdf.missing && pdf.path">
                                                        <a :href="pdf.path.startsWith('http') ? pdf.path : '/storage/' + pdf.path" target="_blank" class="ml-auto px-2 py-1 text-xs rounded bg-green-100 text-green-800 hover:bg-green-200 font-semibold">View</a>
                                                    </template>
                                                    <template x-if="pdf.missing || !pdf.path">
                                                        <span class="ml-auto px-2 py-1 text-xs rounded bg-red-100 text-red-800 font-semibold">Missing</span>
                                                    </template>
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                                <!-- Generated DOCXs -->
                                <div class="bg-yellow-50 rounded-lg p-3 flex-1 min-h-0 flex flex-col">
                                    <h3 class="font-semibold text-maroon-800 mb-2 text-sm">Generated DOCX Files</h3>
                                    <div class="flex flex-col gap-2 min-h-0 overflow-y-auto">
                                        <template x-if="modalData.files && modalData.files.docx">
                                            <template x-for="(docx, idx) in modalData.files.docx" :key="(docx.name || '') + (docx.file_name || '') + idx">
                                                <div class="flex items-center gap-2 bg-white rounded shadow-sm px-2 py-1 min-w-0">
                                                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="16" height="20" x="4" y="2" rx="2" fill="#fff" stroke="#3182ce" stroke-width="2"/><text x="8" y="16" font-size="8" fill="#3182ce">DOCX</text></svg>
                                                    <span class="font-semibold text-xs text-maroon-900 truncate max-w-[100px]" :title="docx.file_name" x-text="docx.file_name"></span>
                                                    <template x-if="!docx.missing && docx.path">
                                                        <a :href="`/admin/requests/${modalData.id}/download?type=docx&key=${docx.name.toLowerCase()}`" class="ml-auto px-2 py-1 text-xs rounded bg-maroon-900 text-white hover:bg-maroon-700 font-semibold">Download</a>
                                                    </template>
                                                    <template x-if="docx.missing || !docx.path">
                                                        <span class="ml-auto px-2 py-1 text-xs rounded bg-gray-200 text-maroon-900 font-semibold">Not available</span>
                                                    </template>
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Modal -->
                <div x-show="statusModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
                    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm relative">
                        <button @click="closeStatusModal()" class="absolute top-2 right-2 text-gray-400 hover:text-red-600 text-xl">&times;</button>
                        <h2 class="text-lg font-bold mb-4">Change Request Status</h2>
                        <form method="POST" :action="`/admin/requests/${modalData.id}/status`">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="w-full border rounded p-2 mb-4">
                                <option value="pending" :selected="modalData.status === 'pending'">Pending</option>
                                <option value="endorsed" :selected="modalData.status === 'endorsed'">Endorsed</option>
                                <option value="rejected" :selected="modalData.status === 'rejected'">Rejected</option>
                            </select>
                            <div class="flex gap-2 justify-end">
                                <button type="button" @click="closeStatusModal()" class="px-4 py-2 bg-gray-200 text-maroon-900 rounded hover:bg-gray-300">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-maroon-800 text-white rounded hover:bg-maroon-900">Update</button>
                                                        </div>
                        </form>
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
                
                async clearTypeFilter() {
                    this.activeType = '';
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
                
                openReviewModal(request) {
                    // Deep clone to avoid mutating original
                    this.modalData = JSON.parse(JSON.stringify(request));
                    // Parse form_data if it's a string
                    if (typeof this.modalData.form_data === 'string') {
                        try {
                            this.modalData.form_data = JSON.parse(this.modalData.form_data);
                        } catch (e) {
                            this.modalData.form_data = {};
                        }
                    }
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

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Chart configuration and initialization
    document.addEventListener('DOMContentLoaded', function() {
        // Check if canvas elements exist before trying to get context
        const typeChartElement = document.getElementById('typeChart');
        const statusChartElement = document.getElementById('statusChart');
        
        // Only initialize charts if the canvas elements exist
        if (typeChartElement) {
            const typeCtx = typeChartElement.getContext('2d');
            const typeChart = new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Publications', 'Citations'],
                    datasets: [{
                        data: [{{ $stats['publication']['month'] ?? 0 }}, {{ $stats['citation']['month'] ?? 0 }}],
                        backgroundColor: [
                            'rgba(139, 69, 19, 0.8)',
                            'rgba(128, 0, 32, 0.8)'
                        ],
                        borderColor: [
                            'rgba(139, 69, 19, 1)',
                            'rgba(128, 0, 32, 1)'
                        ],
                        borderWidth: 2,
                        hoverBackgroundColor: [
                            'rgba(139, 69, 19, 1)',
                            'rgba(128, 0, 32, 1)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    onClick: function(event, elements) {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const types = ['Publication', 'Citation'];
                            filterByType(types[index]);
                        }
                    }
                }
            });
        }

        if (statusChartElement) {
            const statusCtx = statusChartElement.getContext('2d');
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Endorsed', 'Rejected'],
                    datasets: [{
                        data: [{{ $filterCounts['pending'] ?? 0 }}, {{ $filterCounts['endorsed'] ?? 0 }}, {{ $filterCounts['rejected'] ?? 0 }}],
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
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    onClick: function(event, elements) {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const statuses = ['pending', 'endorsed', 'rejected'];
                            filterByStatus(statuses[index]);
                        }
                    }
                }
            });
        }

        // Filter functions
        window.filterByType = function(type) {
            const url = new URL(window.location);
            if (type) {
                url.searchParams.set('type', type);
            } else {
                url.searchParams.delete('type');
            }
            url.searchParams.delete('page'); // Reset pagination
            window.location.href = url.toString();
        };

        window.filterByStatus = function(status) {
            const url = new URL(window.location);
            if (status) {
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
            }
            url.searchParams.delete('page'); // Reset pagination
            window.location.href = url.toString();
        };

        window.filterByPeriod = function(period) {
            const url = new URL(window.location);
            if (period) {
                url.searchParams.set('period', period);
            } else {
                url.searchParams.delete('period');
            }
            url.searchParams.delete('page'); // Reset pagination
            window.location.href = url.toString();
        };
    });
</script>
</x-app-layout>

