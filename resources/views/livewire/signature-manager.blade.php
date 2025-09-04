<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <!-- Upload Section -->
    <div class="mb-8">
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-maroon-400 transition-colors cursor-pointer" style="min-height: 200px;" onclick="handleUploadAreaClick()">
            <!-- Upload Area (shown by default) -->
            <div id="uploadArea">
                <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div>
                    <span class="text-base font-medium text-gray-900">Upload Signature</span>
                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG up to 5MB</p>
                </div>
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
                <!-- Hidden file input inside the form -->
                <input 
                    type="file" 
                    id="signature"
                    name="signature"
                    accept="image/png,image/jpg,image/jpeg"
                    class="sr-only"
                    required
                    onchange="handleFileSelect(this)"
                >
                
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
                    disabled
                    class="w-full bg-gray-400 text-white py-2 px-4 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
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
            <div class="space-y-3" data-signatures-list>
                @foreach($signatures as $signature)
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 cursor-pointer hover:bg-gray-100 transition-colors signature-item" 
                         data-signature-id="{{ $signature->id }}"
                         data-signature-label="{{ $signature->label ?: 'Unnamed Signature' }}"
                         data-signature-path="{{ $signature->path }}"
                         data-signature-created="{{ $signature->created_at->toISOString() }}">
                        <div class="flex items-start space-x-4">
                            <!-- Signature Image -->
                            <div class="flex-shrink-0">
                                <div class="w-20 h-20 bg-white rounded-lg border border-gray-200 flex items-center justify-center overflow-hidden">
                                    <img 
                                        src="{{ route('signatures.show', $signature->id) }}" 
                                        alt="Signature"
                                        class="w-full h-full object-contain"
                                        loading="lazy"
                                    >
                                </div>
                            </div>
                            
                            <!-- Signature Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-medium text-gray-900 truncate" data-signature-label="{{ $signature->label ?: 'Unnamed Signature' }}">
                                        {{ $signature->label ?: 'Unnamed Signature' }}
                                    </h4>
                                    <button 
                                        data-signature-id="{{ $signature->id }}"
                                        class="text-red-500 hover:text-red-700 p-1 rounded transition-colors flex-shrink-0 delete-signature-btn"
                                        title="Delete signature"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 text-xs text-gray-500">
                                    <div>
                                        <span class="font-medium text-gray-700">Uploaded:</span>
                                        <span>{{ $signature->created_at->format('M j, Y') }}</span>
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-300 flex-shrink-0 selection-indicator mt-1"></div>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Type:</span>
                                        <span>{{ strtoupper($signature->mime_type ?: 'Unknown') }}</span>
                                    </div>
                                </div>
                            </div>
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
// Global state management
window.signatureManager = {
    currentFile: null,
    isUploading: false,
    
    // Initialize the component
    init() {
        console.log('Signature manager initialized');
        this.bindEvents();
        this.updateButtonState();
    },
    
    // Bind all event listeners
    bindEvents() {
        // Ensure file input change event is properly bound
        const fileInput = document.getElementById('signature');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => this.handleFileSelect(e.target));
        }
        
        // Form submission
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }
    },
    
    // Handle file selection
    handleFileSelect(input) {
        console.log('File selected:', input.files[0]);
        
        const file = input.files[0];
        if (!file) {
            this.clearFile();
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];
        if (!allowedTypes.includes(file.type)) {
            this.showNotification('error', 'Please select a PNG, JPG, or JPEG file.');
            this.clearFile();
            return;
        }
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            this.showNotification('error', 'File size must be less than 5MB.');
            this.clearFile();
            return;
        }
        
        this.currentFile = file;
        this.showPreview(file);
        this.updateButtonState();
    },
    
    // Show file preview
    showPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            // Update preview content
            const previewImage = document.getElementById('previewImage');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            
            if (previewImage && fileName && fileSize) {
                previewImage.src = e.target.result;
                
                // Truncate filename if longer than 25 characters
                const displayName = file.name.length > 25 ? file.name.substring(0, 25) + '...' : file.name;
                fileName.textContent = displayName;
                fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
                
                // Hide upload area and show preview
                document.getElementById('uploadArea').classList.add('hidden');
                document.getElementById('filePreview').classList.remove('hidden');
            }
        };
        reader.readAsDataURL(file);
    },
    
    // Clear selected file
    clearFile() {
        this.currentFile = null;
        
        // Reset file input
        const fileInput = document.getElementById('signature');
        if (fileInput) {
            fileInput.value = '';
        }
        
        // Reset preview content
        const previewImage = document.getElementById('previewImage');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        
        if (previewImage) previewImage.src = '';
        if (fileName) fileName.textContent = '';
        if (fileSize) fileSize.textContent = '';
        
        // Hide preview and show upload area
        document.getElementById('filePreview').classList.add('hidden');
        document.getElementById('uploadArea').classList.remove('hidden');
        
        this.updateButtonState();
    },
    
    // Update upload button state
    updateButtonState() {
        const button = document.getElementById('uploadBtn');
        if (!button) return;
        
        if (this.currentFile && !this.isUploading) {
            // Enable button
            button.disabled = false;
            button.className = button.className.replace('bg-gray-400', 'bg-maroon-600').replace('focus:ring-gray-500', 'focus:ring-maroon-500');
            button.classList.add('hover:bg-maroon-700');
        } else {
            // Disable button
            button.disabled = true;
            button.className = button.className.replace('bg-maroon-600', 'bg-gray-400').replace('focus:ring-maroon-500', 'focus:ring-gray-500');
            button.classList.remove('hover:bg-maroon-700');
        }
    },
    
    // Handle form submission
    async handleFormSubmit(event) {
        event.preventDefault();
        
        if (!this.currentFile || this.isUploading) {
            return;
        }
        
        this.isUploading = true;
        this.updateButtonState();
        
        // Show loading state
        const uploadText = document.getElementById('uploadText');
        const uploadingText = document.getElementById('uploadingText');
        if (uploadText) uploadText.classList.add('hidden');
        if (uploadingText) uploadingText.classList.remove('hidden');
        
        try {
            const form = event.target;
            const formData = new FormData(form);
            
            console.log('Submitting form with file:', this.currentFile.name);
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                if (response.status === 422) {
                    const errorData = await response.json();
                    throw new Error(`Validation failed: ${JSON.stringify(errorData.errors || errorData)}`);
                }
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned non-JSON response. This usually means there was a server error.');
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('success', data.message);
                this.clearFile();
                form.reset();
                
                // Refresh Livewire component to show new signature without page reload
                console.log('Upload successful, refreshing Livewire component');
                this.refreshSignaturesList();
            } else {
                throw new Error(data.message || 'Upload failed');
            }
            
        } catch (error) {
            console.error('Upload error:', error);
            this.showNotification('error', 'Upload failed: ' + error.message);
        } finally {
            this.isUploading = false;
            this.updateButtonState();
            
            // Reset button text
            if (uploadText) uploadText.classList.remove('hidden');
            if (uploadingText) uploadingText.classList.add('hidden');
        }
    },
    
    // Show notification
    showNotification(type, message) {
        console.log(`Showing ${type} notification:`, message);
        
        if (window.notificationManager) {
            window.notificationManager[type](message);
        } else {
            // Fallback: alert
            alert(`${type.toUpperCase()}: ${message}`);
        }
    },
    
    // Refresh the signatures list by fetching updated data
    async refreshSignaturesList() {
        try {
            console.log('Refreshing signatures list...');
            
            // Fetch the latest signatures data from the server
            const response = await fetch(window.location.href, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                // Parse the response to extract signatures data
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Find the signatures list in the response
                const newSignaturesList = doc.querySelector('[data-signatures-list]');
                const currentSignaturesList = document.querySelector('[data-signatures-list]');
                
                if (newSignaturesList && currentSignaturesList) {
                    // Replace the current list with the new one
                    currentSignaturesList.innerHTML = newSignaturesList.innerHTML;
                    console.log('Signatures list updated successfully');
                    
                    // Add a temporary success indicator
                    this.addSuccessIndicator();
                } else {
                    console.log('Could not find signatures list, using fallback');
                    this.updateSignaturesList();
                }
            } else {
                console.log('Failed to fetch updated signatures, using fallback');
                this.updateSignaturesList();
            }
            
        } catch (error) {
            console.log('Error refreshing signatures list:', error);
            // Fallback: update the list manually
            this.updateSignaturesList();
        }
    },
    
    // Add success indicator to the list
    addSuccessIndicator() {
        const signaturesList = document.querySelector('[data-signatures-list]');
        if (signaturesList) {
            const tempItem = document.createElement('div');
            tempItem.className = 'bg-green-50 border border-green-200 rounded-lg p-4 mb-3 animate-pulse';
            tempItem.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-sm text-green-800 font-medium">New signature uploaded successfully!</span>
                </div>
            `;
            
            // Insert at the top
            signaturesList.insertBefore(tempItem, signaturesList.firstChild);
            
            // Remove after 4 seconds
            setTimeout(() => {
                if (tempItem.parentNode) {
                    tempItem.classList.remove('animate-pulse');
                    tempItem.classList.add('opacity-0', 'transform', 'scale-95');
                    setTimeout(() => {
                        if (tempItem.parentNode) {
                            tempItem.remove();
                        }
                    }, 300);
                }
            }, 4000);
        }
    },
    
    // Fallback method to update signatures list
    updateSignaturesList() {
        // Add a temporary "new signature" indicator
        const signaturesList = document.querySelector('[data-signatures-list]');
        if (signaturesList) {
            const tempItem = document.createElement('div');
            tempItem.className = 'bg-green-50 border border-green-200 rounded-lg p-4 mb-3';
            tempItem.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-sm text-green-800">New signature uploaded successfully!</span>
                </div>
            `;
            
            // Insert at the top
            signaturesList.insertBefore(tempItem, signaturesList.firstChild);
            
            // Remove after 3 seconds
            setTimeout(() => {
                if (tempItem.parentNode) {
                    tempItem.remove();
                }
            }, 3000);
        }
    },

    // Get all signatures for the signing modal
    getSignatures() {
        const signaturesList = document.querySelector('[data-signatures-list]');
        if (!signaturesList) return [];
        
        const signatureItems = signaturesList.querySelectorAll('[data-signature-id]');
        const signatures = [];
        
        signatureItems.forEach(item => {
            const id = item.getAttribute('data-signature-id');
            const label = item.getAttribute('data-signature-label') || 'Signature';
            const path = item.getAttribute('data-signature-path') || '';
            
            if (id && path) {
                signatures.push({
                    id: id,
                    label: label,
                    path: path
                });
            }
        });
        
        return signatures;
    }
};

// Handle upload area click
function handleUploadAreaClick() {
    const fileInput = document.getElementById('signature');
    if (fileInput) {
        fileInput.click();
    }
}

// Handle file selection (legacy function for backward compatibility)
function handleFileSelect(input) {
    window.signatureManager.handleFileSelect(input);
}

// Clear file (legacy function for backward compatibility)
function clearFile() {
    window.signatureManager.clearFile();
}

// Handle form submit (legacy function for backward compatibility)
function handleFormSubmit(event) {
    window.signatureManager.handleFormSubmit(event);
}



// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (window.signatureManager) {
        window.signatureManager.init();
    }
});

// Also initialize if DOM is already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        if (window.signatureManager) {
            window.signatureManager.init();
        }
    });
} else {
    if (window.signatureManager) {
        window.signatureManager.init();
    }
}
</script>


