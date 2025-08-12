@props(['name','type','placeholder' => 'Select name...'])
<div x-data="signatorySelect('{{ $type }}', '{{ $name }}')" class="relative">
    <input type="text" x-model="query" @focus="open = true; fetchOptions()" @blur="handleBlur" @input.debounce.200ms="fetchOptions()" :placeholder="placeholder"
           class="w-full text-sm text-gray-700 text-center border-none focus:outline-none focus:ring-0 bg-transparent" autocomplete="off">
    <input type="hidden" name="{{ $name }}" :value="selectedName">
    <div x-show="open" class="absolute z-30 mt-1 w-full max-h-40 overflow-auto bg-white border border-gray-200 rounded shadow" @mouseenter="hovering = true" @mouseleave="hovering = false">
        <template x-if="loading">
            <div class="p-2 text-xs text-gray-500">Loading...</div>
        </template>
        <template x-for="opt in options" :key="opt.id">
            <div @mousedown.prevent="choose(opt)" class="px-2 py-1 text-sm cursor-pointer hover:bg-gray-100">
                <span x-text="opt.name"></span>
                <span class="text-xs text-gray-400" x-text="' · ' + opt.email"></span>
            </div>
        </template>
        <div x-show="!loading && options.length===0" class="p-2 text-xs text-gray-500">No matches</div>
    </div>
</div>
<script>
function signatorySelect(type, nameField) {
    return {
        open: false,
        query: '',
        options: [],
        loading: false,
        selectedName: '',
        hovering: false,
        fetchOptions() {
            this.loading = true;
            const params = new URLSearchParams({ type, q: this.query });
            fetch(`/signatories?${params.toString()}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.ok ? r.json() : [])
                .then(data => { this.options = Array.isArray(data) ? data : []; })
                .catch(() => { this.options = []; })
                .finally(() => { this.loading = false; });
        },
        choose(opt) {
            this.selectedName = opt.name;
            this.query = opt.name;
            this.open = false;
        },
        handleBlur() {
            // Delay closing so mousedown on an option can run first
            setTimeout(() => {
                if (!this.hovering) this.open = false;
            }, 150);
        }
    }
}
</script> 