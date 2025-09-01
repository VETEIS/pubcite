<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <!-- Upload Section -->
    <div class="mb-8">
        <!-- Fixed height container to prevent layout shifts -->
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-maroon-400 transition-colors cursor-pointer" style="min-height: 200px;" onclick="document.getElementById('signature').click()">
            <!-- Upload Area (shown by default) -->
            <div id="uploadArea">
                <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div>
                    <span class="text-base font-medium text-gray-900">Upload Signature</span>
                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG up to 5MB</p>
                </div>
                <input 
                    type="file" 
                    id="signature"
                    name="signature"
                    accept="image/png,image/jpg,image/jpeg"
                    class="sr-only"
                    required
                    onchange="previewFile(this)"
                >
            </div>
            
            <!-- File Preview (hidden by default, replaces upload area) -->
            <div id="filePreview" class="hidden">
                <div class="flex flex-col items-center justify-center h-full relative">
                    <!-- Preview Image Container - Fixed size to match upload area height -->
                    <div class="mb-4 w-32 h-32 bg-white rounded-lg border-2 border-gray-200 flex items-center justify-center overflow-hidden">
                        <img id="previewImage" src="" alt="Preview" class="w-full h-full object-contain">
                    </div>
                    
                    <!-- File Info -->
                    <div class="text-center mb-4">
                        <p id="fileName" class="text-sm font-medium text-gray-900 mb-1"></p>
                        <p id="fileSize" class="text-xs text-gray-500"></p>
                    </div>
                    
                    <!-- Clear Button - Overlay positioned absolutely -->
                    <button type="button" onclick="clearFile()" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 p-1.5 rounded-full bg-white/80 hover:bg-white shadow-sm border border-gray-200 transition-all duration-200 hover:scale-110">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Form Elements (always visible) -->
            <form action="{{ route('signatures.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 mt-6" onclick="event.stopPropagation()" onsubmit="handleFormSubmit(event)">
                @csrf
                <div>
                    <label for="label" class="block text-sm font-medium text-gray-700 mb-2">Label (optional)</label>
                    <input 
                        type="text" 
                        id="label"
                        name="label"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:border-transparent text-sm"
                        placeholder="e.g., Personal, Business, etc."
                    >
                </div>
                
                <button 
                    type="submit"
                    id="uploadBtn"
                    class="w-full bg-maroon-600 text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-maroon-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon-500 transition-colors"
                >
                    <span id="uploadText">Upload Signature</span>
                    <span id="uploadingText" class="hidden">Uploading...</span>
                </button>
            </form>
        </div>
    </div>
    
    <!-- Signatures List -->
    <div>
        <h3 class="text-sm font-medium text-gray-900 mb-4">Your Signatures</h3>
        
        @if($signatures->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($signatures as $signature)
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                {{ $signature->label ?: 'Unnamed Signature' }}
                            </h4>
                            <button 
                                wire:click="deleteSignature({{ $signature->id }})"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="text-red-500 hover:text-red-700 p-1 rounded transition-colors"
                                title="Delete signature"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="bg-white rounded-lg border border-gray-200 p-3 mb-3">
                            <img 
                                src="{{ route('signatures.show', $signature->id) }}" 
                                alt="Signature"
                                class="w-full h-24 object-contain rounded"
                                loading="lazy"
                            >
                        </div>
                        
                        <div class="text-xs text-gray-500">
                            <p>Uploaded: {{ $signature->created_at->format('M j, Y') }}</p>
                            <p>Type: {{ strtoupper($signature->mime_type ?: 'Unknown') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="text-gray-500">No signatures uploaded yet.</p>
                <p class="text-sm text-gray-400 mt-1">Upload your first signature to get started.</p>
            </div>
        @endif
    </div>
</div>

<script>
function previewFile(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Update preview content
            document.getElementById('previewImage').src = e.target.result;
            
            // Truncate filename if longer than 15 characters
            const fileName = file.name.length > 25 ? file.name.substring(0, 25) + '...' : file.name;
            document.getElementById('fileName').textContent = fileName;
            document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
            
            // Hide upload area and show preview
            document.getElementById('uploadArea').classList.add('hidden');
            document.getElementById('filePreview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function clearFile() {
    // Reset file input
    document.getElementById('signature').value = '';
    
    // Reset preview content
    document.getElementById('previewImage').src = '';
    document.getElementById('fileName').textContent = '';
    document.getElementById('fileSize').textContent = '';
    
    // Hide preview and show upload area
    document.getElementById('filePreview').classList.add('hidden');
    document.getElementById('uploadArea').classList.remove('hidden');
}

function handleFormSubmit(event) {
    event.preventDefault();
    
    // Show loading state
    document.getElementById('uploadText').classList.add('hidden');
    document.getElementById('uploadingText').classList.remove('hidden');
    document.getElementById('uploadBtn').disabled = true;
    
    // Submit form via AJAX
    const form = event.target;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response. This usually means there was a server error.');
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            if (window.Livewire) {
                window.Livewire.dispatch('notify', {
                    type: 'success',
                    message: data.message
                });
            }
            
            // Reset form and preview
            clearFile();
            form.reset();
            
            // Refresh Livewire component to show new signature
            if (window.Livewire) {
                window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).call('$refresh');
            }
        } else {
            throw new Error(data.message || 'Upload failed');
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        // Show error message
        if (window.Livewire) {
            window.Livewire.dispatch('notify', {
                type: 'error',
                message: 'Upload failed: ' + error.message
            });
        }
    })
    .finally(() => {
        // Reset button state
        document.getElementById('uploadText').classList.remove('hidden');
        document.getElementById('uploadingText').classList.add('hidden');
        document.getElementById('uploadBtn').disabled = false;
    });
}
</script>

