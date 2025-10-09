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
                    @include('components.user-navbar', ['showFilters' => false])
                </div>

                <!-- Signature Requests Table Container -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex-1 flex flex-col" style="height: calc(100vh - 8rem);">
                            
                            <!-- Table Header (Fixed) -->
                            <div class="bg-gray-50 border-b border-gray-200 flex-shrink-0">
                                <div class="overflow-x-auto">
                                    <table class="w-full table-fixed">
                                        <thead>
                                            <tr>
                                                <th class="w-20 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center justify-center gap-1">
                                                        ID
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
                                                <th class="w-32 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center gap-1">
                                                        Role
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
                                                        Request Status
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                        </svg>
                                                    </div>
                                                </th>
                                                <th class="w-28 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
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
                                                        <td class="w-20 px-6 py-4 whitespace-nowrap text-center">
                                                            <span class="text-sm font-medium text-gray-900">{{ $request['request_code'] }}</span>
                                                        </td>
                                                        <td class="w-24 px-6 py-4 whitespace-nowrap">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                {{ $request['type'] === 'Publication' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                                {{ $request['type'] }}
                                                            </span>
                                                        </td>
                                                        <td class="w-32 px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ ucfirst(str_replace('_', ' ', $request['matched_role'])) }}
                                                        </td>
                                                        <td class="w-24 px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ \Carbon\Carbon::parse($request['requested_at'])->format('M d, Y') }}
                                                        </td>
                                                        <td class="w-28 px-6 py-4 whitespace-nowrap text-center">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                <div class="w-2 h-2 bg-yellow-400 rounded-full mr-2 animate-pulse"></div>
                                                                Pending
                                                            </span>
                                                        </td>
                                                        <td class="w-28 px-6 py-4 whitespace-nowrap text-center">
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
                                                                <div class="flex flex-col gap-2">
                                                                    <button onclick="downloadRequestFiles({{ $request['id'] }})" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition-all duration-200 text-sm font-medium">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                        </svg>
                                                                        Download
                                                                    </button>
                                                                    <button onclick="openUploadModal({{ $request['id'] }})" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-maroon-100 text-maroon-700 hover:bg-maroon-200 transition-all duration-200 text-sm font-medium">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                                                        </svg>
                                                                        Upload Signed
                                                                    </button>
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
                alert('No request selected.');
                return;
            }

            // Allow multiple signatures - removed restriction

            const fileInput = document.getElementById('signedDocuments');
            if (!fileInput.files || fileInput.files.length === 0) {
                alert('Please select at least one file to upload.');
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

                const data = await response.json();

                if (data.success) {
                    alert('Success: ' + data.message);
                    // Refresh the page to show updated status
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert('Error: ' + data.message);
                    // Hide loading state
                    window.hideLoading();
                    document.getElementById('uploadModal').classList.remove('hidden');
                }
            } catch (error) {
                alert('Failed to upload signed documents. Please try again.');
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
                    alert('Success: ' + data.message);
                    // Refresh the page to show updated status
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Failed to revert document. Please try again.');
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