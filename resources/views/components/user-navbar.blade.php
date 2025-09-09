<!-- User Navbar Component -->
<div x-data="{ userMenuOpen: false, draftsOpen: false, drafts: [], loadingDrafts: false }" class="flex items-center gap-4">
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
            <div class="relative group">
                <button class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 h-8 w-32 justify-between">
                    <svg class="w-3.5 h-3.5 text-maroon-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="min-w-[60px] max-w-[80px] truncate">{{ $currentStatus ? ucfirst($currentStatus) : 'All Status' }}</span>
                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="absolute top-full left-0 mt-1 bg-white text-md font-semibold border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 min-w-[120px]">
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
    <div class="relative">
        <button @click="if (drafts.length > 0) { loadDrafts(); draftsOpen = !draftsOpen; }" 
                :class="drafts.length === 0 ? 'text-gray-400 bg-gray-100 border-gray-200 cursor-not-allowed' : 'text-gray-700 bg-white border-gray-200 hover:bg-gray-50 hover:border-gray-300'"
                :disabled="drafts.length === 0"
                class="flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-all duration-200 h-8">
            <svg :class="drafts.length === 0 ? 'text-gray-400' : 'text-maroon-600'" class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Drafts</span>
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <!-- Drafts Dropdown Menu -->
        <div x-cloak x-show="draftsOpen" @click.away="draftsOpen = false" @keydown.escape.window="draftsOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 z-50 min-w-[280px] max-w-[400px]" style="display: none;">
            <div class="p-3">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-900">Saved Drafts</h3>
                    <div x-show="loadingDrafts" class="w-4 h-4 border-2 border-maroon-600 border-t-transparent rounded-full animate-spin"></div>
                </div>
                
                <div x-show="!loadingDrafts && drafts.length === 0" class="text-sm text-gray-500 text-center py-4">
                    No saved drafts
                </div>
                
                <div x-show="!loadingDrafts && drafts.length > 0" class="space-y-2 max-h-64 overflow-y-auto">
                    <template x-for="draft in drafts" :key="draft.id">
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div @click="loadDraft(draft)" class="flex-1 cursor-pointer">
                                <div class="text-sm font-medium text-gray-900" x-text="draft.type + ' - ' + draft.request_code"></div>
                                <div class="text-xs text-gray-500" x-text="new Date(draft.created_at).toLocaleString()"></div>
                            </div>
                            <button @click="deleteDraft(draft.id)" class="ml-2 p-1 text-gray-400 hover:text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- User Profile Section -->
    <div class="relative">
        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 p-2 rounded-lg bg-gradient-to-r from-maroon-600 to-maroon-700 hover:from-maroon-700 hover:to-maroon-800 transition-all duration-300 group shadow-lg">
            @if(Auth::user()->profile_photo_path)
                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-white/20">
            @else
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-white/20 to-white/10 flex items-center justify-center text-white font-bold shadow-lg">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'User' }}</div>
                <div class="text-xs text-maroon-100 truncate">{{ Auth::user()->email ?? 'No email' }}</div>
            </div>
            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
            <svg class="w-4 h-4 text-maroon-100 group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>

<script>
// Draft management functions
function loadDrafts() {
    if (this.drafts.length > 0) return; // Already loaded
    
    this.loadingDrafts = true;
    
    fetch('/api/drafts')
        .then(response => response.json())
        .then(data => {
            this.drafts = data.drafts || [];
            this.loadingDrafts = false;
        })
        .catch(error => {
            console.error('Error loading drafts:', error);
            this.loadingDrafts = false;
        });
}

function loadDraft(draft) {
    // Close the dropdown
    this.draftsOpen = false;
    
    // Determine which page to redirect to based on draft type
    const route = draft.type === 'Publication' ? '/publications/request' : '/citations/request';
    
    // Store the draft ID in sessionStorage for the request page to pick up
    sessionStorage.setItem('loadDraftId', draft.id);
    
    // Redirect to the appropriate request page
    window.location.href = route;
}

function deleteDraft(draftId) {
    if (!confirm('Are you sure you want to delete this draft?')) {
        return;
    }
    
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
            // Remove the draft from the local array
            this.drafts = this.drafts.filter(draft => draft.id !== draftId);
        } else {
            alert('Failed to delete draft: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error deleting draft:', error);
        alert('Failed to delete draft. Please try again.');
    });
}
</script>
