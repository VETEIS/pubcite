<!-- User Navbar Component -->
<div class="flex items-center gap-4">
    <!-- Modern Compact Filters -->
    @if(isset($showFilters) && $showFilters)
        <div class="flex items-center gap-2">
            @php
                $currentStatus = request('status');
                $filteredRequests = $requests ?? collect();
                if ($currentStatus) {
                    $filteredRequests = $filteredRequests->where('status', $currentStatus);
                }
            @endphp
            
            <!-- Compact Status Filter -->
            <div class="relative" id="status-filter-container">
                <button id="status-filter-button" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 h-8 w-32 justify-between">
                    <svg class="w-3.5 h-3.5 text-maroon-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="min-w-[60px] max-w-[80px] truncate">{{ $currentStatus ? ucfirst($currentStatus) : 'All Status' }}</span>
                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="status-filter-dropdown" class="absolute top-full left-0 mt-1 bg-white text-md font-semibold border border-gray-200 rounded-lg shadow-lg hidden z-50 min-w-[120px]">
                    <a href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => null])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ !$currentStatus ? 'bg-maroon-50 text-maroon-700' : '' }}">All Status</a>
                    <a href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentStatus === 'pending' ? 'bg-maroon-50 text-maroon-700' : '' }}">Pending</a>
                    <a href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'endorsed'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentStatus === 'endorsed' ? 'bg-maroon-50 text-maroon-700' : '' }}">Endorsed</a>
                    <a href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'rejected'])) }}" class="block px-3 py-2 text-md text-gray-700 hover:bg-gray-50 {{ $currentStatus === 'rejected' ? 'bg-maroon-50 text-maroon-700' : '' }}">Rejected</a>
                </div>
            </div>
        </div>
    @endif


    <!-- Drafts Dropdown - Only show on request pages -->
    @if(request()->is('publications/request') || request()->is('citations/request'))
    <div class="relative" id="drafts-container">
        <button id="drafts-button" 
                class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-all duration-200 h-8 text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300">
            <svg class="w-3.5 h-3.5 flex-shrink-0 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Drafts</span>
            <span id="drafts-count" class="bg-maroon-100 text-maroon-800 text-xs px-1.5 py-0.5 rounded-full w-6 h-5 flex items-center justify-center">0</span>
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <!-- Drafts Dropdown Menu -->
        <div id="drafts-dropdown" class="absolute right-0 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 z-50 min-w-[280px] max-w-[400px] hidden">
            <div class="p-3">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-900">Saved Drafts</h3>
                    <div id="drafts-loading" class="w-4 h-4 border-2 border-maroon-600 border-t-transparent rounded-full animate-spin hidden"></div>
                </div>
                
                <div id="drafts-empty" class="text-sm text-gray-500 text-center py-4 hidden">
                    No saved drafts
                </div>
                
                <div id="drafts-list" class="space-y-2 max-h-64 overflow-y-auto hidden">
                    <!-- Drafts will be populated here -->
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- User Profile Section -->
    <div class="relative flex items-center">
        <!-- Subtle separator -->
        <div class="w-px h-6 bg-gray-200 mr-3"></div>
        <a href="{{ route('profile.show') }}" class="flex items-center gap-2 hover:bg-gray-100 rounded-xl p-2 transition-all duration-300">
            @if(Auth::user()->profile_photo_path)
                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-200">
            @else
                <div class="w-10 h-10 rounded-full bg-maroon-600 flex items-center justify-center text-white font-bold shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
            @endif
        </a>
    </div>
</div>

<script>
// Initialize status filter dropdown - works with both regular page loads and Turbo
function initializeStatusFilterDropdown() {
    const statusFilterButton = document.getElementById('status-filter-button');
    const statusFilterDropdown = document.getElementById('status-filter-dropdown');
    
    if (!statusFilterButton) return; // Exit if not on a page with filters
    
    // Skip if already initialized for this page
    if (statusFilterButton.dataset.initialized === 'true') {
        return;
    }
    
    statusFilterButton.dataset.initialized = 'true';
    let isOpen = false;
    
    // Toggle dropdown
    statusFilterButton.addEventListener('click', function(e) {
        e.stopPropagation();
        if (isOpen) {
            closeStatusFilterDropdown();
        } else {
            openStatusFilterDropdown();
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (isOpen && !statusFilterButton.contains(e.target) && !statusFilterDropdown.contains(e.target)) {
            closeStatusFilterDropdown();
        }
    });
    
    // Close dropdown on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) {
            closeStatusFilterDropdown();
        }
    });
    
    function openStatusFilterDropdown() {
        isOpen = true;
        statusFilterDropdown.classList.remove('hidden');
    }
    
    function closeStatusFilterDropdown() {
        isOpen = false;
        statusFilterDropdown.classList.add('hidden');
    }
}

// Initialize draft dropdown - works with both regular page loads and Turbo
function initializeDraftDropdown() {
    const draftsButton = document.getElementById('drafts-button');
    const draftsDropdown = document.getElementById('drafts-dropdown');
    const draftsList = document.getElementById('drafts-list');
    const draftsEmpty = document.getElementById('drafts-empty');
    const draftsLoading = document.getElementById('drafts-loading');
    const draftsCount = document.getElementById('drafts-count');
    
    if (!draftsButton) return; // Exit if not on a request page
    
    // Skip if already initialized for this page
    if (draftsButton.dataset.initialized === 'true') {
        return;
    }
    draftsButton.dataset.initialized = 'true';
    
    let drafts = [];
    let isOpen = false;
    
    // Load drafts on page initialization
    loadDrafts();
    
    // Toggle dropdown
    draftsButton.addEventListener('click', function(e) {
        e.stopPropagation();
        if (isOpen) {
            closeDropdown();
        } else {
            openDropdown();
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (isOpen && !draftsButton.contains(e.target) && !draftsDropdown.contains(e.target)) {
            closeDropdown();
        }
    });
    
    // Close dropdown on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) {
            closeDropdown();
        }
    });
    
    function openDropdown() {
        isOpen = true;
        draftsDropdown.classList.remove('hidden');
        // Only reload if drafts haven't been loaded yet
        if (drafts.length === 0) {
            loadDrafts();
        }
    }
    
    function closeDropdown() {
        isOpen = false;
        draftsDropdown.classList.add('hidden');
    }
    
    function loadDrafts() {
        draftsLoading.classList.remove('hidden');
        draftsEmpty.classList.add('hidden');
        draftsList.classList.add('hidden');
        
        fetch('/api/drafts')
            .then(response => {
                return response.json();
            })
            .then(data => {
                drafts = data.drafts || [];
                renderDrafts();
                draftsLoading.classList.add('hidden');
            })
            .catch(error => {
                drafts = [];
                renderDrafts();
                draftsLoading.classList.add('hidden');
            });
    }
    
    function renderDrafts() {
        // Always show the count badge and update the number
        draftsCount.textContent = drafts.length;
        
        if (drafts.length === 0) {
            draftsEmpty.classList.remove('hidden');
            draftsList.classList.add('hidden');
        } else {
            draftsEmpty.classList.add('hidden');
            draftsList.classList.remove('hidden');
            
            draftsList.innerHTML = drafts.map(draft => `
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-1 cursor-pointer" onclick="loadDraft(${draft.id})">
                        <div class="text-sm font-medium text-gray-900">${draft.type} - ${draft.request_code}</div>
                        <div class="text-xs text-gray-500">${new Date(draft.requested_at).toLocaleString()}</div>
                    </div>
                    <button onclick="deleteDraft(${draft.id})" class="ml-2 p-1 text-gray-400 hover:text-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `).join('');
        }
    }
    
    // Global functions for draft actions
    window.loadDraft = function(draftId) {
        const draft = drafts.find(d => d.id === draftId);
        if (!draft) return;
        
        closeDropdown();
        const route = draft.type === 'Publication' ? '/publications/request' : '/citations/request';
        sessionStorage.setItem('loadDraftId', draftId);
        window.location.href = route;
    };
    
    window.deleteDraft = function(draftId) {
        if (!confirm('Are you sure you want to delete this draft?')) return;
        
        fetch(`/drafts/${draftId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                drafts = drafts.filter(draft => draft.id !== draftId);
                renderDrafts();
                // Reload the page to reflect the updated state
                window.location.reload();
            } else {
                alert('Failed to delete draft: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Failed to delete draft. Please try again.');
        });
    };
}

// Initialize on both regular page loads and Turbo navigation
document.addEventListener('DOMContentLoaded', function() {
    initializeStatusFilterDropdown();
    initializeDraftDropdown();
});
document.addEventListener('turbo:load', function() {
    initializeStatusFilterDropdown();
    initializeDraftDropdown();
});
</script>


