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
<input type="text" x-model="query" @input.debounce.300ms="fetchOptions()" class="w-full px-2 py-1 text-sm border rounded" :placeholder="placeholderText" />
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
    <input type="text" x-model="query" @focus="open = true; fetchOptions()" @blur="handleBlur" @input.debounce.300ms="fetchOptions()" :placeholder="placeholderText"
           class="{{ $inputClass }}" autocomplete="off">
    <input type="hidden" name="{{ $name }}" :value="selectedName" required>
    <div x-show="open" class="absolute left-0 z-30 mt-1 w-max min-w-48 max-w-80 max-h-40 overflow-auto bg-white border border-gray-200 rounded shadow" @mouseenter="hovering = true" @mouseleave="hovering = false">
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
        loading: false,
        selectedName: '',
        hovering: false,
        placeholderText: phText,
        popX: 0, popY: 0, popW: 200,
        cache: new Map(), // Client-side cache
        lastFetchTime: 0,
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
            setTimeout(() => { if (!this.hovering) this.open = false; }, 150);
        }
    }
}
</script> 