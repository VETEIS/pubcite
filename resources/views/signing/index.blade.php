<x-app-layout>
    <div class="h-screen bg-gray-50 flex overflow-hidden" style="scrollbar-gutter: stable;">
        @include('components.user-sidebar')

        <!-- Main Content -->
        <div class="flex-1 ml-4 h-screen overflow-y-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
            <style>
                .flex-1::-webkit-scrollbar {
                    display: none;
                }
            </style>
            <!-- Content Area -->
            <main class="p-4 rounded-bl-lg h-full">
                <!-- Dashboard Header with Modern Compact Filters -->
                <div class="relative flex items-center justify-between mb-4">
                    <!-- Overview Header -->
                    <div class="flex items-center gap-2 text-md font-semibold text-gray-600 bg-gray-50 px-3 py-2.5 rounded-lg h-10">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20v-6m0 0l-3 3m3-3l3 3M5 8l7-3 7 3-7 3-7-3z"/>
                        </svg>
                        <span>Signature Requests</span>
                    </div>
                    
                    <!-- Enhanced Search and User Controls -->
                    @include('components.user-navbar', ['showFilters' => false])
                </div>

                <!-- Optimized Content Grid -->
                <div class="grid grid-cols-1 xl:grid-cols-5 gap-4 max-w-7xl mx-auto h-[calc(100vh-140px)]">
                    <!-- Left: Signature Management Card -->
                    <div class="xl:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 h-full flex flex-col">
                            <!-- Compact Card Header -->
                            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-maroon-50 to-maroon-100/30 flex-shrink-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </div>
                                    <h2 class="text-base font-semibold text-maroon-900">Digital Signatures</h2>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-maroon-100 text-maroon-800 border border-maroon-200">
                                    {{ Auth::user()->signatures()->count() }} signatures
                                </span>
                            </div>
                        </div>
                            
                            <!-- Card Content -->
                            <div class="p-4 flex-1 overflow-hidden">
                                @livewire('signature-manager')
                            </div>
                        </div>
                    </div>

                    <!-- Right: Pending Requests Card -->
                    <div class="xl:col-span-3">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 h-full flex flex-col">
                            <!-- Compact Card Header -->
                            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100/30 flex-shrink-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <h2 class="text-base font-semibold text-gray-900">Pending Signature Requests</h2>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <div class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1.5 animate-pulse"></div>
                                        {{ count($requests) }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Card Content - Table -->
                            <div class="flex-1 overflow-hidden">
                                <div class="h-full overflow-y-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center gap-1">
                                                        ID
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center gap-1">
                                                        Type
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center gap-1">
                                                        Role
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center gap-1">
                                                        Date
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center gap-1">
                                                        Status
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @if(count($requests) > 0)
                                                @foreach($requests as $index => $request)
                                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span class="text-sm font-medium text-gray-900">{{ $request['request_code'] }}</span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                {{ $request['type'] === 'Publication' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                                {{ $request['type'] }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ ucfirst(str_replace('_', ' ', $request['matched_role'])) }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ \Carbon\Carbon::parse($request['requested_at'])->format('M d, Y') }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                <div class="w-2 h-2 bg-yellow-400 rounded-full mr-2 animate-pulse"></div>
                                                                Pending
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                            <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-maroon-100 text-maroon-700 hover:bg-maroon-200 transition-all duration-200 text-sm font-medium">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                                </svg>
                                                                Sign
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="px-6 py-12 text-center">
                                                        <div class="flex flex-col items-center justify-center gap-3">
                                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <h4 class="text-lg font-semibold text-gray-900">No pending requests</h4>
                                                                <p class="text-gray-500">Signature requests will appear here when they need your attention.</p>
                                                            </div>
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
            </main>
        </div>
    </div>
</x-app-layout> 