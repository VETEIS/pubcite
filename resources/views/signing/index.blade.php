<x-app-layout>
    <!-- Global Notifications -->
    <x-global-notifications />
    
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
            <main class="p-4 rounded-bl-lg pb-8">
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
                <div class="grid grid-cols-1 xl:grid-cols-6 gap-4 max-w-7xl mx-auto">
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
                    <div class="xl:col-span-4">
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
                                                <th class="w-20 pl-4 pr-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center gap-1">
                                                        ID
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="w-24 px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center gap-1">
                                                        Type
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="w-32 px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center gap-1">
                                                        Role
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="w-24 px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center gap-1">
                                                        Date
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="w-28 px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center gap-1">
                                                        Request Status
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="w-28 px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center gap-1">
                                                        Signature Status
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="w-24 px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @if(count($requests) > 0)
                                                @foreach($requests as $index => $request)
                                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                        <td class="pl-4 pr-3 py-4 whitespace-nowrap">
                                                            <span class="text-sm font-medium text-gray-900">{{ $request['request_code'] }}</span>
                                                        </td>
                                                        <td class="px-3 py-4 whitespace-nowrap">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                {{ $request['type'] === 'Publication' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                                {{ $request['type'] }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ ucfirst(str_replace('_', ' ', $request['matched_role'])) }}
                                                        </td>
                                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ \Carbon\Carbon::parse($request['requested_at'])->format('M d, Y') }}
                                                        </td>
                                                        <td class="px-3 py-4 whitespace-nowrap">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                <div class="w-2 h-2 bg-yellow-400 rounded-full mr-2 animate-pulse"></div>
                                                                Pending
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-4 whitespace-nowrap">
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
                                                        <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                            @if($request['signature_status'] === 'signed')
                                                                @if($request['can_revert'])
                                                                    <button onclick="revertDocument({{ $request['id'] }})" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-200 text-sm font-medium">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                                                        </svg>
                                                                        Revert
                                                                    </button>
                                                                @else
                                                                    <span class="text-xs text-gray-500">Can't revert</span>
                                                                @endif
                                                            @else
                                                                <button onclick="openSignModal({{ $request['id'] }})" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-maroon-100 text-maroon-700 hover:bg-maroon-200 transition-all duration-200 text-sm font-medium">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                                    </svg>
                                                                    Sign
                                                                </button>
                                                            @endif
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

    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Signing Modal -->
    <div id="signModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-maroon-100">
                    <svg class="h-6 w-6 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Sign Document</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Confirm that you want to sign this document with your selected signature.
                    </p>
                    
                    <!-- Selected Signature Display -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Selected Signature:</label>
                        <div id="selectedSignatureDisplay" class="flex items-center p-3 border rounded-lg bg-gray-50">
                            <!-- Will show selected signature -->
                        </div>
                    </div>
                    
                    <!-- Request Details -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Request Details:</label>
                        <div id="requestDetailsDisplay" class="text-sm text-gray-600 space-y-1">
                            <!-- Will show request details -->
                        </div>
                    </div>
                </div>
                
                <div class="items-center px-4 py-3">
                    <button id="confirmSignBtn" class="px-4 py-2 bg-maroon-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-maroon-600 focus:outline-none focus:ring-2 focus:ring-maroon-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Sign Document
                    </button>
                    <button onclick="closeSignModal()" class="mt-2 px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-maroon-600 mx-auto"></div>
            <p class="mt-4 text-gray-700">Signing document...</p>
            <p class="text-sm text-gray-500">Please wait while we apply your signature.</p>
        </div>
    </div>
    
    <style>
        .signature-item {
            transition: all 0.2s ease-in-out;
            border: 2px solid transparent;
        }
        
        .signature-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-color: #e5e7eb;
        }
        
        .signature-item.ring-2 {
            border-color: #8b5cf6;
            background-color: #f3f4f6;
        }
        
        .selection-indicator {
            transition: all 0.2s ease-in-out;
        }
    </style>
    
    <script>
        let currentRequestId = null;

        function openSignModal(requestId) {
            console.log('Opening sign modal for request:', requestId);
            currentRequestId = requestId;
            
            // Check if a signature is selected
            if (!window.selectedSignatureData) {
                if (window.notificationManager) {
                    window.notificationManager.error('Please select a signature first by clicking on one from your list above.');
                } else {
                    alert('Please select a signature first by clicking on one from your list above.');
                }
                return;
            }
            
            // Update modal with selected signature and request details
            updateModalDisplay();
            document.getElementById('signModal').classList.remove('hidden');
        }

        function closeSignModal() {
            document.getElementById('signModal').classList.add('hidden');
            currentRequestId = null;
            // Don't clear selectedSignatureData - user keeps their selection
        }

        async function loadSignatures() {
            console.log('Loading signatures...');
            try {
                // Use the existing signature manager to get signatures
                if (window.signatureManager && typeof window.signatureManager.getSignatures === 'function') {
                    console.log('Signature manager found, getting signatures...');
                    const signatures = window.signatureManager.getSignatures();
                    console.log('Signatures loaded from manager:', signatures);
                    displaySignatures(signatures);
                } else {
                    console.log('Signature manager not available, fetching from server...');
                    const response = await fetch('/signatures', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
                    
                    if (response.ok) {
                        const signatures = await response.json();
                        console.log('Signatures loaded from server:', signatures);
                        displaySignatures(signatures);
                    } else {
                        console.error('Failed to load signatures');
                        displaySignatures([]);
                    }
                }
            } catch (error) {
                console.error('Error loading signatures:', error);
                displaySignatures([]);
            }
        }

        function displaySignatures(signatures) {
            // This function is no longer needed since we're using the Livewire component
            // The signatures are already displayed in the component
            console.log('Signatures loaded:', signatures);
        }



        function updateModalDisplay() {
            // Update selected signature display
            const signatureDisplay = document.getElementById('selectedSignatureDisplay');
            if (window.selectedSignatureData) {
                signatureDisplay.innerHTML = `
                    <img src="${window.selectedSignatureData.display_path}" alt="Signature" class="w-16 h-8 object-contain border rounded mr-3">
                    <div>
                        <div class="text-sm font-medium text-gray-900">${window.selectedSignatureData.label || 'Signature'}</div>
                        <div class="text-xs text-gray-500">Uploaded ${new Date(window.selectedSignatureData.created_at).toLocaleDateString()}</div>
                    </div>
                `;
            }
            
            // Update request details display
            const requestDisplay = document.getElementById('requestDetailsDisplay');
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

        async function confirmSign() {
            if (!currentRequestId || !window.selectedSignatureData) {
                if (window.notificationManager) {
                    window.notificationManager.error('Please select a signature first.');
                } else {
                    alert('Please select a signature first.');
                }
                return;
            }

            // Show loading overlay
            document.getElementById('loadingOverlay').classList.remove('hidden');
            document.getElementById('signModal').classList.add('hidden');

            try {
                const response = await fetch('/signing/sign-document', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        request_id: currentRequestId,
                        signature_id: window.selectedSignatureData.id
                    })
                });

                const data = await response.json();

                if (data.success) {
                    window.notificationManager.success(data.message);
                    // Refresh the page to show updated status
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    window.notificationManager.error(data.message);
                    document.getElementById('loadingOverlay').classList.add('hidden');
                    document.getElementById('signModal').classList.remove('hidden');
                }
            } catch (error) {
                window.notificationManager.error('Failed to sign document. Please try again.');
                document.getElementById('loadingOverlay').classList.add('hidden');
                document.getElementById('signModal').classList.remove('hidden');
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
                    window.notificationManager.success(data.message);
                    // Refresh the page to show updated status
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    window.notificationManager.error(data.message);
                }
            } catch (error) {
                window.notificationManager.error('Failed to revert document. Please try again.');
            }
        }

        // Store requests data globally for modal display
        window.requestsData = @json($requests);
        
        // Signature selection functions
        window.selectSignatureFromList = function(id, label, path, createdAt) {
            console.log('selectSignatureFromList called with:', { id, label, path, createdAt });
            
            // Remove selection from all signature items
            document.querySelectorAll('.signature-item').forEach(item => {
                item.classList.remove('ring-2', 'ring-maroon-500', 'bg-maroon-50');
                const indicator = item.querySelector('.selection-indicator');
                if (indicator) {
                    indicator.className = 'w-4 h-4 rounded-full border-2 border-gray-300 flex-shrink-0 selection-indicator mt-1';
                }
            });
            
            // Highlight selected signature
            const selectedItem = document.querySelector(`[data-signature-id="${id}"]`);
            if (selectedItem) {
                selectedItem.classList.add('ring-2', 'ring-maroon-500', 'bg-maroon-50');
                const indicator = selectedItem.querySelector('.selection-indicator');
                if (indicator) {
                    indicator.className = 'w-4 h-4 rounded-full border-2 border-maroon-500 bg-maroon-500 flex-shrink-0 selection-indicator mt-1';
                }
            }
            
            // Store selected signature data globally for the signing modal
            window.selectedSignatureData = {
                id: id,
                label: label,
                path: path, // This is now the storage path like "signatures/10/image.png"
                display_path: `/signatures/${id}`, // Use the show route for display
                created_at: createdAt
            };
            
            console.log('Signature selected from list:', window.selectedSignatureData);
            
            // Show success notification
            if (window.notificationManager) {
                window.notificationManager.success(`Signature "${label}" selected. You can now sign documents.`);
            } else {
                // Fallback to alert if notification manager is not available
                alert(`Signature "${label}" selected. You can now sign documents.`);
            }
        }
        
        window.deleteSignatureFromList = function(id) {
            // Call the Livewire delete method
            if (window.Livewire) {
                const wireElement = document.querySelector('[wire\\:id]');
                if (wireElement) {
                    const wireId = wireElement.getAttribute('wire:id');
                    window.Livewire.find(wireId).call('deleteSignature', id);
                }
            }
        }
        
        // Bind confirm button and load signatures
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, binding confirm button and loading signatures...');
            console.log('Signature manager available:', !!window.signatureManager);
            
            // Set up event delegation for signature selection and deletion
            document.addEventListener('click', function(e) {
                // Handle signature selection
                if (e.target.closest('.signature-item')) {
                    const signatureItem = e.target.closest('.signature-item');
                    const id = signatureItem.getAttribute('data-signature-id');
                    const label = signatureItem.getAttribute('data-signature-label');
                    const path = signatureItem.getAttribute('data-signature-path');
                    const createdAt = signatureItem.getAttribute('data-signature-created');
                    
                    if (id && label && path && createdAt) {
                        selectSignatureFromList(id, label, path, createdAt);
                    }
                }
                
                // Handle signature deletion
                if (e.target.closest('.delete-signature-btn')) {
                    e.preventDefault();
                    e.stopPropagation();
                    const button = e.target.closest('.delete-signature-btn');
                    const id = button.getAttribute('data-signature-id');
                    if (id) {
                        deleteSignatureFromList(id);
                    }
                }
            });
            
            // Wait a bit for Livewire to initialize
                setTimeout(() => {
                console.log('Loading signatures after delay...');
                loadSignatures();
            }, 500);
            
            // Bind confirm button
            const confirmBtn = document.getElementById('confirmSignBtn');
            if (confirmBtn) {
                console.log('Confirm button found, binding click event');
                confirmBtn.onclick = confirmSign;
            } else {
                console.error('Confirm button not found!');
            }
        });
    </script>
</x-app-layout> 