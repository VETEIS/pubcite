@props(['name','type','placeholder' => 'Select name...', 'inline' => false, 'width' => 'w-full'])
@php
    $wrapperClass = $inline ? 'relative inline-block align-baseline' : 'relative';
    $inputClass = $inline
        ? "$width text-sm text-gray-700 text-center border-none focus:outline-none focus:ring-0 bg-transparent"
        : 'w-full text-sm text-gray-700 text-center border-none focus:outline-none focus:ring-0 bg-transparent';
    $typeJson = json_encode($type);
    $nameJson = json_encode($name);
    $phJson = json_encode($placeholder);
@endphp
@if($inline)
<span x-data='signatorySelect({!! $typeJson !!}, {!! $nameJson !!}, true, {!! $phJson !!})' data-field="{{ $name }}" class="{{ $wrapperClass }}">
    <button type="button" x-ref="trigger" @click="open = true; updatePosition(); fetchOptions()" @keydown.escape.window="open=false"
            class="align-baseline underline decoration-dotted text-maroon-900 font-semibold px-1 rounded focus:outline-none focus:ring-2 focus:ring-maroon-300">
        <span x-text="selectedName || placeholderText"></span>
    </button>
    <input type="hidden" name="{{ $name }}" :value="selectedName" required>
    <div x-show="open" class="fixed z-50 max-h-48 overflow-auto bg-white border border-gray-200 rounded shadow"
         :style="'top:' + popY + 'px; left:' + popX + 'px; min-width:' + popW + 'px'"
         @mouseenter="hovering = true" @mouseleave="hovering = false">
        <div class="p-1 border-b">
        <input type="text" x-model="query" @input="fetchOptions()" class="w-full px-2 py-1 text-sm border rounded" :placeholder="placeholderText" />
        </div>
        <template x-if="loading"><div class="p-2 text-xs text-gray-500">Loading...</div></template>
        <template x-if="!loading">
            <template x-for="opt in options" :key="opt.id">
                <div @mousedown.prevent="choose(opt)" class="px-3 py-1.5 text-sm cursor-pointer hover:bg-gray-100 whitespace-nowrap">
                    <span x-text="opt.name"></span>
                    <span class="text-xs text-gray-400" x-text="' · ' + opt.email"></span>
                </div>
            </template>
        </template>
        <div x-show="!loading && options.length===0" class="p-2 text-xs text-gray-500">No matches</div>
    </div>
</span>
@else
<div x-data='signatorySelect({!! $typeJson !!}, {!! $nameJson !!}, false, {!! $phJson !!})' data-field="{{ $name }}" class="{{ $wrapperClass }}">
    <input type="text" x-model="query" @focus="open = true; fetchOptions()" @blur="handleBlur" @input="fetchOptions()" :placeholder="placeholderText"
           :class="isValidSelection ? '{{ $inputClass }}' : '{{ $inputClass }} border-red-500 bg-red-50'"
           autocomplete="off">
    <input type="hidden" name="{{ $name }}" :value="selectedName" required>
    <div x-show="open" class="absolute left-1/2 transform -translate-x-1/2 z-30 mt-1 w-max min-w-48 max-w-80 max-h-40 overflow-auto bg-white border border-gray-200 rounded shadow" @mouseenter="hovering = true" @mouseleave="hovering = false">
        <template x-if="loading"><div class="p-2 text-xs text-gray-500">Loading...</div></template>
        <template x-if="!loading">
            <template x-for="opt in options" :key="opt.id">
                <div @mousedown.prevent="choose(opt)" class="px-2 py-1 text-sm cursor-pointer hover:bg-gray-100 whitespace-nowrap">
                    <span x-text="opt.name"></span>
                    <span class="text-xs text-gray-400" x-text="' · ' + opt.email"></span>
                </div>
            </template>
        </template>
        <div x-show="!loading && options.length===0" class="p-2 text-xs text-gray-500">No matches</div>
    </div>
</div>
@endif
<script>
function signatorySelect(type, nameField, inlineMode = false, phText = 'Select name...') {
    return {
        open: false,
        query: '',
        options: [],
        allSignatories: [], // Preloaded signatories
        loading: false,
        selectedName: '',
        hovering: false,
        placeholderText: phText,
        popX: 0, popY: 0, popW: 200,
        cache: new Map(), // Client-side cache
        lastFetchTime: 0,
        isValidSelection: true,
        preloaded: false,
        init() {
            // Preload signatories on component initialization
            this.preloadSignatories();
            
            // Validate pre-populated name if it exists
            if (this.query && this.query.trim() !== '') {
                this.validatePrePopulatedName();
            }
        },
        preloadSignatories() {
            // Check if already preloaded globally
            if (window.signatoryCache && window.signatoryCache[type]) {
                this.allSignatories = window.signatoryCache[type];
                this.preloaded = true;
                return;
            }
            
            // Preload all signatories for this type
            this.fetchSignatoriesFromServer();
        },
        fetchSignatoriesFromServer() {
            const params = new URLSearchParams({ type, q: '' });
            fetch(`/signatories?${params.toString()}`, { 
                headers: { 'X-Requested-With': 'XMLHttpRequest' } 
            })
            .then(r => r.ok ? r.json() : [])
            .then(data => { 
                this.allSignatories = Array.isArray(data) ? data : [];
                this.preloaded = true;
                
                // Store in global cache for other components
                if (!window.signatoryCache) window.signatoryCache = {};
                window.signatoryCache[type] = this.allSignatories;
            })
            .catch(() => { 
                this.allSignatories = [];
                this.preloaded = true;
            });
        },
        refreshCache() {
            // Clear global cache and reload
            if (window.signatoryCache) {
                delete window.signatoryCache[type];
            }
            this.preloaded = false;
            this.fetchSignatoriesFromServer();
        },
        updatePosition() {
            if (!inlineMode) return;
            this.$nextTick(() => {
                const r = this.$refs.trigger.getBoundingClientRect();
                this.popX = r.left + window.scrollX;
                this.popY = r.bottom + window.scrollY + 4;
                this.popW = Math.max(r.width, 200);
            });
        },
        fetchOptions() {
            // If preloaded, use client-side filtering for instant results
            if (this.preloaded && this.allSignatories.length > 0) {
                this.filterSignatories();
                return;
            }
            
            // Fallback to server request if not preloaded yet
            this.fetchFromServer();
        },
        filterSignatories() {
            if (!this.query || this.query.trim() === '') {
                this.options = this.allSignatories.slice(0, 20); // Show first 20
                return;
            }
            
            const query = this.query.toLowerCase().trim();
            this.options = this.allSignatories.filter(signatory => 
                signatory.name.toLowerCase().includes(query) || 
                signatory.email.toLowerCase().includes(query)
            ).slice(0, 20); // Limit to 20 results
        },
        fetchFromServer() {
            // Check client-side cache first
            const cacheKey = `${type}_${this.query}`;
            const now = Date.now();
            
            // Use cached result if available and not older than 30 seconds
            if (this.cache.has(cacheKey) && (now - this.cache.get(cacheKey).timestamp) < 30000) {
                this.options = this.cache.get(cacheKey).data;
                return;
            }
            
            // Prevent duplicate requests
            if (this.loading) return;
            
            this.loading = true;
            const params = new URLSearchParams({ type, q: this.query });
            
            fetch(`/signatories?${params.toString()}`, { 
                headers: { 'X-Requested-With': 'XMLHttpRequest' } 
            })
                .then(r => r.ok ? r.json() : [])
                .then(data => { 
                    const result = Array.isArray(data) ? data : [];
                    this.options = result;
                    
                    // Cache the result
                    this.cache.set(cacheKey, {
                        data: result,
                        timestamp: now
                    });
                    
                    // Clean old cache entries (keep only last 10)
                    if (this.cache.size > 10) {
                        const oldestKey = this.cache.keys().next().value;
                        this.cache.delete(oldestKey);
                    }
                })
                .catch(() => { this.options = []; })
                .finally(() => { this.loading = false; });
        },
        choose(opt) {
            this.selectedName = opt.name;
            this.query = opt.name;
            this.isValidSelection = true;
            this.open = false;
            // Trigger validation after selection
            setTimeout(() => {
                // Trigger custom event for validation
                document.dispatchEvent(new CustomEvent('signatory-selected', {
                    detail: { fieldName: this.nameField, selectedName: opt.name }
                }));
                
                // Also trigger Alpine.js validation if available
                if (window.Alpine && window.Alpine.store('tabNav')) {
                    window.Alpine.store('tabNav').checkTabs();
                }
            }, 100);
        },
        handleBlur() {
            setTimeout(() => { 
                if (!this.hovering) {
                    this.open = false;
                    // Validate that the typed text matches a valid option
                    this.validateSelection();
                }
            }, 150);
        },
        validatePrePopulatedName() {
            // Validate a pre-populated name against the database
            if (!this.query || this.query.trim() === '') return;
            
            const params = new URLSearchParams({ 
                name: this.query.trim(), 
                type: type 
            });
            
            fetch(`/signatories/validate?${params.toString()}`, { 
                headers: { 'X-Requested-With': 'XMLHttpRequest' } 
            })
            .then(r => r.ok ? r.json() : { valid: false })
            .then(data => {
                if (!data.valid) {
                    // Store the invalid name for logging before clearing
                    const invalidName = this.query;
                    
                    // Signatory no longer exists, clear the field
                    this.query = '';
                    this.selectedName = '';
                    this.isValidSelection = false;
                    
                    // Show a subtle notification
                    console.warn(`Signatory "${invalidName}" no longer exists and has been cleared.`);
                } else {
                    // Signatory is valid
                    this.selectedName = this.query;
                    this.isValidSelection = true;
                }
            })
            .catch(() => {
                // On error, assume invalid to be safe
                this.query = '';
                this.selectedName = '';
                this.isValidSelection = false;
            });
        },
        validateSelection() {
            // If there's a query but no valid selection, validate it
            if (this.query && this.query !== this.selectedName) {
                // Check if the query matches any of the available options
                const isValidOption = this.options.some(opt => opt.name === this.query);
                if (!isValidOption) {
                    // Mark as invalid
                    this.isValidSelection = false;
                    // Clear invalid selection
                    this.query = this.selectedName || '';
                    this.selectedName = '';
                    // Show error message or visual feedback
                    this.showValidationError();
                } else {
                    this.isValidSelection = true;
                }
            } else if (this.query === this.selectedName) {
                this.isValidSelection = true;
            }
        },
        showValidationError() {
            // Create a temporary error message
            const errorMsg = document.createElement('div');
            errorMsg.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50';
            errorMsg.textContent = 'Please select a valid signatory from the dropdown';
            document.body.appendChild(errorMsg);
            
            // Remove error message after 3 seconds
            setTimeout(() => {
                if (errorMsg.parentNode) {
                    errorMsg.parentNode.removeChild(errorMsg);
                }
            }, 3000);
        }
    }
}
</script> 