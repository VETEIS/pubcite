<div wire:key="researcher-manager-component"
    x-data="{}"
    @researcher-edit-opened.window="console.log('[DEBUG] Livewire event received: researcher-edit-opened', $event.detail); console.log('[DEBUG] Current showModal after event:', $wire.get('showModal'));"
    @show-notification.window="
        (() => {
            const detail = $event.detail || {};
            const type = detail.type;
            const message = detail.message;
            
            if (window.notificationManager && type && message) {
                window.notificationManager[type](message);
            } else {
                console.warn('[ResearcherManager] Notification not triggered:', { 
                    hasManager: !!window.notificationManager, 
                    type, 
                    message,
                    detail 
                });
            }
        })()
    ">
    <!-- USEP Researchers Management Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6 relative">
        <!-- Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-maroon-50 to-red-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">USEP Researchers</h3>
                        <p class="text-sm text-gray-600 mt-1">Manage researcher profiles displayed on the landing page</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button"
                            wire:click="save" 
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm shadow-sm transition-all duration-200 {{ $hasChanges ? 'bg-maroon-600 text-white hover:bg-maroon-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}"
                            {{ !$hasChanges ? 'disabled' : '' }}>
                        <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg wire:loading wire:target="save" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">Save Changes</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </div>
        </div>
        

        <!-- Content: Grid View with Fixed Height and Scroll -->
        <div class="p-6 overflow-y-auto relative" style="height: 400px;">
            @if(empty($researchers))
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No researchers</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by adding a new researcher profile.</p>
                </div>
            @else
                <div class="grid grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-3">
                    @foreach($researchers as $index => $researcher)
                        <div class="group relative bg-white rounded-xl border border-gray-200 hover:border-maroon-400 hover:shadow-lg shadow-sm transition-all duration-300 overflow-visible transform hover:-translate-y-1">
                            <!-- Number Badge -->
                            <div class="absolute top-2 left-2 w-6 h-6 bg-gradient-to-br from-maroon-600 to-red-600 text-white rounded-full flex items-center justify-center text-xs font-bold z-10 shadow-md ring-2 ring-white">
                                {{ $index + 1 }}
                            </div>
                            
                            <!-- Card Content -->
                            <button type="button" 
                                    wire:click="editResearcher({{ $index }})"
                                    wire:loading.attr="disabled"
                                    class="w-full text-left p-3 focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:ring-offset-2 rounded-xl">
                                <!-- Profile Picture -->
                                <div class="flex justify-center mb-2.5">
                                    @if(isset($researcher['photo']) && $researcher['photo'])
                                        <div class="w-14 h-14 rounded-full overflow-hidden ring-2 ring-gray-100 group-hover:ring-maroon-200 shadow-md transition-all duration-300">
                                            <img src="{{ $researcher['photo']->temporaryUrl() }}" 
                                                 alt="Profile" 
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        </div>
                                    @elseif(!empty($researcher['photo_path']))
                                        <div class="w-14 h-14 rounded-full overflow-hidden ring-2 ring-gray-100 group-hover:ring-maroon-200 shadow-md transition-all duration-300">
                                            <img src="{{ Storage::disk('public')->url($researcher['photo_path']) }}" 
                                                 alt="Profile" 
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        </div>
                                    @else
                                        <div class="w-14 h-14 bg-gradient-to-br from-maroon-500 to-red-600 rounded-full flex items-center justify-center shadow-md ring-2 ring-gray-100 group-hover:ring-maroon-200 transition-all duration-300">
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Name -->
                                <div class="text-center">
                                    <h4 class="text-xs font-semibold text-gray-900 group-hover:text-maroon-600 transition-colors line-clamp-2 leading-tight min-h-[2rem] flex items-center justify-center" 
                                        title="{{ (!empty($researcher['prefix']) ? $researcher['prefix'] . ' ' : '') . (!empty($researcher['name']) ? $researcher['name'] : 'New Researcher') }}">
                                        {{ !empty($researcher['name']) ? $this->formatNameAsInitials($researcher['name'], $researcher['prefix'] ?? '') : 'New Researcher' }}
                                    </h4>
                                </div>
                            </button>
                            
                            <!-- Delete Button -->
                            <button type="button"
                                    wire:click.stop="confirmResearcherDeletion({{ $index }})"
                                    wire:loading.attr="disabled"
                                    class="absolute top-2 right-2 p-1.5 text-red-600 bg-white hover:bg-red-50 rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-sm hover:shadow-md border border-red-100 hover:border-red-200 z-20 pointer-events-auto"
                                    title="Remove Researcher"
                                    style="pointer-events: auto;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        <!-- Floating Action Button -->
        <button type="button" 
                wire:click="addResearcher"
                class="absolute bottom-6 right-6 w-14 h-14 bg-maroon-600 text-white rounded-full hover:bg-maroon-700 shadow-lg hover:shadow-xl flex items-center justify-center z-10 transition-all duration-200" 
                title="Add Researcher">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
        </button>
    </div>

    <!-- Edit Modal - Always rendered, Alpine controls visibility -->
    <div x-data="{ 
            shouldShow: false,
            checkVisibility() {
                const newValue = $wire.showModal && $wire.editingIndex !== null;
                if (newValue !== this.shouldShow) {
                    this.shouldShow = newValue;
                    this.updateBodyOverflow();
                }
            },
            init() {
                // Check visibility periodically to catch Livewire updates
                setInterval(() => this.checkVisibility(), 50);
                
                // Also check on Livewire updates
                $wire.on('updated', () => {
                    this.checkVisibility();
                });
                
                // Initial check
                this.checkVisibility();
            },
            updateBodyOverflow() {
                if (this.shouldShow) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            },
            close() {
                $wire.set('showModal', false);
            }
         }" 
         x-show="shouldShow" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="close()">
        <!-- Backdrop -->
        <div x-show="shouldShow" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             @click="close()"></div>
        
        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="shouldShow"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:w-full sm:max-w-4xl"
                 @click.stop>
                <div class="bg-white">
                    @if($editingIndex !== null && isset($researchers[$editingIndex]))
                        @php($researcher = $researchers[$editingIndex])
                        <!-- Modal Header -->
                        <div class="px-6 py-5 border-b border-maroon-700 bg-maroon-600">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-white">
                                        {{ !empty($researcher['name']) ? 'Edit: ' . (!empty($researcher['prefix']) ? $researcher['prefix'] . ' ' : '') . $researcher['name'] : 'Add New Researcher' }}
                                    </h3>
                                    <p class="text-sm text-maroon-100 mt-1">Update researcher information and profile details</p>
                                </div>
                                <button type="button"
                                        @click="close()"
                                        class="text-white hover:text-maroon-100 hover:bg-maroon-700 rounded-lg p-2 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-maroon-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <div class="max-h-[calc(100vh-200px)] overflow-y-auto">
                            <div class="p-6 space-y-6">
                                <!-- Profile Picture Section - Compact -->
                                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-center gap-4">
                                        <!-- Profile Picture Preview -->
                                        <div class="relative group flex-shrink-0">
                                            <div class="relative w-24 h-24 rounded-xl overflow-hidden ring-2 ring-white shadow-md bg-white">
                                                @if(isset($researcher['photo']) && $researcher['photo'])
                                                    <img src="{{ $researcher['photo']->temporaryUrl() }}" 
                                                         alt="Profile preview" 
                                                         class="w-full h-full object-cover">
                                                @elseif(!empty($researcher['photo_path']))
                                                    <img src="{{ Storage::disk('public')->url($researcher['photo_path']) }}" 
                                                         alt="Profile preview" 
                                                         class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-maroon-500 to-red-600">
                                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                
                                                <!-- Hover Overlay -->
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 rounded-xl transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                    <label class="cursor-pointer p-2 bg-white rounded-full shadow-lg hover:bg-gray-50 transition-colors">
                                                        <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        <input type="file" 
                                                               wire:model="researchers.{{ $editingIndex }}.photo"
                                                               accept="image/*"
                                                               class="hidden">
                                                    </label>
                                                </div>
                                                
                                                <!-- Loading Indicator -->
                                                <div wire:loading wire:target="researchers.{{ $editingIndex }}.photo" 
                                                     class="absolute inset-0 bg-black bg-opacity-50 rounded-xl flex items-center justify-center">
                                                    <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Upload Section -->
                                        <div class="flex-1 min-w-0">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                                            @if(!empty($researcher['photo_path']))
                                                <input type="hidden" wire:model="researchers.{{ $editingIndex }}.photo_path">
                                            @endif
                                            <label class="flex items-center gap-3 w-full px-4 py-2.5 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-white hover:border-maroon-400 hover:bg-maroon-50 transition-all duration-200 group">
                                                <svg class="w-5 h-5 text-gray-400 group-hover:text-maroon-600 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm text-gray-600 group-hover:text-maroon-700 transition-colors">
                                                        <span class="font-semibold">Click to upload</span> or drag and drop
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-0.5">JPG, PNG up to 10MB • Auto-converted to WebP</p>
                                                </div>
                                                <input type="file" 
                                                       wire:model="researchers.{{ $editingIndex }}.photo"
                                                       accept="image/*"
                                                       class="hidden">
                                            </label>
                                            @error('researchers.' . $editingIndex . '.photo')
                                                <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Basic Information Card -->
                                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                                    <h4 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Basic Information
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div class="grid grid-cols-12 gap-4">
                                            <div class="col-span-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Prefix</label>
                                                <select wire:model.blur="researchers.{{ $editingIndex }}.prefix"
                                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all">
                                                    <option value="">None</option>
                                                    <option value="Mr.">Mr.</option>
                                                    <option value="Mrs.">Mrs.</option>
                                                    <option value="Ms.">Ms.</option>
                                                    <option value="Dr.">Dr.</option>
                                                    <option value="Prof.">Prof.</option>
                                                    <option value="Eng.">Eng.</option>
                                                    <option value="Atty.">Atty.</option>
                                                    <option value="Rev.">Rev.</option>
                                                </select>
                                            </div>
                                            <div class="col-span-9">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Full Name <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                    </div>
                                                    <input type="text" 
                                                           wire:model.blur="researchers.{{ $editingIndex }}.name"
                                                           placeholder="John Doe" 
                                                           class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Title/Position <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <input type="text" 
                                                       wire:model.blur="researchers.{{ $editingIndex }}.title"
                                                       placeholder="Professor, College of Engineering" 
                                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all">
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <input type="email" 
                                                       wire:model.blur="researchers.{{ $editingIndex }}.profile_link"
                                                       placeholder="researcher@example.com" 
                                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all">
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Badge</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                    </svg>
                                                </div>
                                                <select wire:model.blur="researchers.{{ $editingIndex }}.status_badge" 
                                                        class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%23374151%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e')] bg-[length:1.5em_1.5em] bg-[right_0.5rem_center] bg-no-repeat">
                                                    <option value="Active">Active</option>
                                                    <option value="Research">Research</option>
                                                    <option value="Innovation">Innovation</option>
                                                    <option value="Leadership">Leadership</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Biography Card -->
                                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                                    <h4 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Biography
                                    </h4>
                                    <textarea wire:model.blur="researchers.{{ $editingIndex }}.bio" 
                                              rows="5" 
                                              placeholder="Brief description of research focus, achievements, and expertise..." 
                                              class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-sm bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all resize-none"></textarea>
                                </div>

                                <!-- Research Details Card -->
                                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                                    <h4 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                        Research Details
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Research Areas <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                    </svg>
                                                </div>
                                                <input type="text" 
                                                       wire:model.blur="researchers.{{ $editingIndex }}.research_areas"
                                                       placeholder="AI, Machine Learning, Data Science" 
                                                       required
                                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all @error('researchers.' . $editingIndex . '.research_areas') border-red-300 focus:ring-red-500 @enderror">
                                            </div>
                                            @error('researchers.' . $editingIndex . '.research_areas')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Background Color</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                                    </svg>
                                                </div>
                                                <select wire:model.blur="researchers.{{ $editingIndex }}.background_color" 
                                                        class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%23374151%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e')] bg-[length:1.5em_1.5em] bg-[right_0.5rem_center] bg-no-repeat">
                                                    <option value="maroon">Maroon</option>
                                                    <option value="blue">Blue</option>
                                                    <option value="green">Green</option>
                                                    <option value="purple">Purple</option>
                                                    <option value="orange">Orange</option>
                                                    <option value="teal">Teal</option>
                                                    <option value="rose">Rose</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Research Profile Links Card -->
                                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                                    <h4 class="text-base font-semibold text-gray-900 mb-5 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                        Research Profile Links
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
                                                </svg>
                                                SCOPUS
                                            </label>
                                            <input type="url" 
                                                   wire:model.blur="researchers.{{ $editingIndex }}.scopus_link"
                                                   placeholder="https://www.scopus.com/authid/detail.uri?authorId=..." 
                                                   class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                                </svg>
                                                ORCID
                                            </label>
                                            <input type="url" 
                                                   wire:model.blur="researchers.{{ $editingIndex }}.orcid_link"
                                                   placeholder="https://orcid.org/0000-0000-0000-0000" 
                                                   class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"></path>
                                                </svg>
                                                Web of Science
                                            </label>
                                            <input type="url" 
                                                   wire:model.blur="researchers.{{ $editingIndex }}.wos_link"
                                                   placeholder="https://www.webofscience.com/wos/author/record/..." 
                                                   class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
                                                </svg>
                                                Google Scholar
                                            </label>
                                            <input type="url" 
                                                   wire:model.blur="researchers.{{ $editingIndex }}.google_scholar_link"
                                                   placeholder="https://scholar.google.com/citations?user=..." 
                                                   class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent transition-all">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                            <p class="text-xs text-gray-500">
                                <span class="text-red-500">*</span> Required fields
                            </p>
                            <div class="flex gap-3">
                                <button type="button"
                                        @click="close()"
                                        class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon-500 transition-colors">
                                    Cancel
                                </button>
                                <button type="button"
                                        wire:click="save"
                                        wire:loading.attr="disabled"
                                        wire:target="save"
                                        class="px-5 py-2.5 text-sm font-medium text-white bg-maroon-600 rounded-lg hover:bg-maroon-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon-500 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="save">Save Changes</span>
                                    <span wire:loading wire:target="save">Saving...</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Researcher Confirmation Modal - Using same approach as edit modal -->
    <div x-data="{ 
            shouldShow: false,
            checkVisibility() {
                const newValue = $wire.confirmingResearcherDeletion === true;
                if (newValue !== this.shouldShow) {
                    this.shouldShow = newValue;
                    this.updateBodyOverflow();
                }
            },
            init() {
                // Check visibility periodically to catch Livewire updates
                setInterval(() => this.checkVisibility(), 50);
                
                // Also check on Livewire updates
                $wire.on('updated', () => {
                    this.checkVisibility();
                });
                
                // Initial check
                this.checkVisibility();
            },
            updateBodyOverflow() {
                if (this.shouldShow) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            },
            close() {
                $wire.set('confirmingResearcherDeletion', false);
            }
         }" 
         x-show="shouldShow" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="close()">
        <!-- Backdrop -->
        <div x-show="shouldShow" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             @click="close()"></div>
        
        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="shouldShow"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:w-full sm:max-w-lg"
                 @click.stop>
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto shrink-0 flex items-center justify-center size-12 rounded-full bg-red-100 sm:mx-0 sm:size-10">
                            <svg class="size-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>

                        <div class="mt-3 text-center sm:mt-0 sm:ms-4 sm:text-start">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ __('Delete Researcher') }}
                            </h3>

                            <div class="mt-4 text-sm text-gray-600">
                                @if($researcherToDelete !== null && isset($researchers[$researcherToDelete]))
                                    {{ __('Are you sure you would like to delete') }} 
                                    <strong>{{ !empty($researchers[$researcherToDelete]['name']) ? $researchers[$researcherToDelete]['name'] : 'this researcher' }}</strong>?
                                    {{ __('This action cannot be undone.') }}
                                @else
                                    {{ __('Are you sure you would like to delete this researcher? This action cannot be undone.') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-end">
                    <x-secondary-button wire:click="$set('confirmingResearcherDeletion', false)" wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button class="ms-3" wire:click="removeResearcher" wire:loading.attr="disabled">
                        {{ __('Delete') }}
                    </x-danger-button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Ensure notification listener is set up on document level as fallback
        document.addEventListener('livewire:init', () => {
            window.addEventListener('show-notification', (event) => {
                const detail = event.detail || {};
                const type = detail.type;
                const message = detail.message;
                
                if (window.notificationManager && type && message) {
                    window.notificationManager[type](message);
                }
            });
        });
    </script>
</div>
