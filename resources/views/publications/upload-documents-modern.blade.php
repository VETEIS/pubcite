<!-- Modern Upload Documents Section for Publications -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="text-center py-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-blue-800 mb-2">Upload Required Documents</h2>
        <p class="text-sm text-blue-600">Please upload all required PDF documents for your publication request</p>
    </div>
    
    <!-- Upload Instructions -->
    <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl border border-amber-200 p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-amber-800 mb-2">Upload Guidelines</h3>
                <ul class="text-sm text-amber-700 space-y-1">
                    <li>• All files must be in PDF format</li>
                    <li>• Maximum file size: 20MB per document</li>
                    <li>• Ensure documents are clear and readable</li>
                    <li>• Files will be automatically organized by type</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Upload Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Recommendation Letter Card -->
        <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-all duration-200 group cursor-pointer"
             x-data="{ fileName: '', displayName: '', isDragOver: false }"
             @click="$refs.recommendationLetter.click()"
             @dragover.prevent="isDragOver = true"
             @dragleave.prevent="isDragOver = false"
             @drop.prevent="isDragOver = false; handleFileDrop($event, 'recommendation_letter')"
             :class="isDragOver ? 'border-blue-400 bg-blue-50' : 'hover:border-maroon-300'">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-8 h-8 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Recommendation Letter</h3>
                <p class="text-sm text-gray-600 mb-4">Approved by the College Dean</p>
                <div class="text-sm text-maroon-600 font-medium mb-2 truncate" x-text="displayName || 'Click to upload'"></div>
                <p class="text-xs text-gray-500">Max 20MB • PDF only</p>
                
                <!-- Upload Progress -->
                <div x-show="isUploading" class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-maroon-600 h-2 rounded-full transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1" x-text="`Uploading... ${uploadProgress}%`"></p>
                </div>
            </div>
            <input type="file" name="recommendation_letter" accept=".pdf" class="hidden" x-ref="recommendationLetter" required
                   @change="handleFileUpload($event, 'recommendation_letter', $data)">
        </div>

        <!-- Published Article Card -->
        <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-all duration-200 group cursor-pointer"
             x-data="{ fileName: '', displayName: '', isDragOver: false, isUploading: false, uploadProgress: 0 }"
             @click="$refs.publishedArticle.click()"
             @dragover.prevent="isDragOver = true"
             @dragleave.prevent="isDragOver = false"
             @drop.prevent="isDragOver = false; handleFileDrop($event, 'published_article')"
             :class="isDragOver ? 'border-blue-400 bg-blue-50' : 'hover:border-maroon-300'">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-8 h-8 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Published Article</h3>
                <p class="text-sm text-gray-600 mb-4">Copy of the published article (PDF)</p>
                <div class="text-sm text-maroon-600 font-medium mb-2 truncate" x-text="displayName || 'Click to upload'"></div>
                <p class="text-xs text-gray-500">Max 20MB • PDF only</p>
                
                <!-- Upload Progress -->
                <div x-show="isUploading" class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-maroon-600 h-2 rounded-full transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1" x-text="`Uploading... ${uploadProgress}%`"></p>
                </div>
            </div>
            <input type="file" name="published_article" accept=".pdf" class="hidden" x-ref="publishedArticle" required
                   @change="handleFileUpload($event, 'published_article', $data)">
        </div>

        <!-- Journal Cover & TOC Card -->
        <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-all duration-200 group cursor-pointer"
             x-data="{ fileName: '', displayName: '', isDragOver: false, isUploading: false, uploadProgress: 0 }"
             @click="$refs.journalCover.click()"
             @dragover.prevent="isDragOver = true"
             @dragleave.prevent="isDragOver = false"
             @drop.prevent="isDragOver = false; handleFileDrop($event, 'journal_cover')"
             :class="isDragOver ? 'border-blue-400 bg-blue-50' : 'hover:border-maroon-300'">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-8 h-8 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Journal Cover & TOC</h3>
                <p class="text-sm text-gray-600 mb-4">Journal cover and table of contents (PDF)</p>
                <div class="text-sm text-maroon-600 font-medium mb-2 truncate" x-text="displayName || 'Click to upload'"></div>
                <p class="text-xs text-gray-500">Max 20MB • PDF only</p>
                
                <!-- Upload Progress -->
                <div x-show="isUploading" class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-maroon-600 h-2 rounded-full transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1" x-text="`Uploading... ${uploadProgress}%`"></p>
                </div>
            </div>
            <input type="file" name="journal_cover" accept=".pdf" class="hidden" x-ref="journalCover" required
                   @change="handleFileUpload($event, 'journal_cover', $data)">
        </div>

        <!-- Terminal Report Card -->
        <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-all duration-200 group cursor-pointer"
             x-data="{ fileName: '', displayName: '', isDragOver: false, isUploading: false, uploadProgress: 0 }"
             @click="$refs.terminalReport.click()"
             @dragover.prevent="isDragOver = true"
             @dragleave.prevent="isDragOver = false"
             @drop.prevent="isDragOver = false; handleFileDrop($event, 'terminal_report')"
             :class="isDragOver ? 'border-blue-400 bg-blue-50' : 'hover:border-maroon-300'">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-8 h-8 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Terminal Report</h3>
                <p class="text-sm text-gray-600 mb-4">Terminal report document (PDF)</p>
                <div class="text-sm text-maroon-600 font-medium mb-2 truncate" x-text="displayName || 'Click to upload'"></div>
                <p class="text-xs text-gray-500">Max 20MB • PDF only</p>
                
                <!-- Upload Progress -->
                <div x-show="isUploading" class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-maroon-600 h-2 rounded-full transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1" x-text="`Uploading... ${uploadProgress}%`"></p>
                </div>
            </div>
            <input type="file" name="terminal_report" accept=".pdf" class="hidden" x-ref="terminalReport" required
                   @change="handleFileUpload($event, 'terminal_report', $data)">
        </div>
    </div>
    
    <!-- Upload Progress Section -->
    <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm" x-data="uploadProgress()">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload Progress</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Recommendation Letter</span>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full" :class="uploadStatus.recommendation_letter ? 'bg-green-500' : 'bg-gray-300'"></div>
                    <span class="text-xs" :class="uploadStatus.recommendation_letter ? 'text-green-600' : 'text-gray-500'" 
                          x-text="uploadStatus.recommendation_letter ? 'Uploaded' : 'Not uploaded'"></span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Published Article</span>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full" :class="uploadStatus.published_article ? 'bg-green-500' : 'bg-gray-300'"></div>
                    <span class="text-xs" :class="uploadStatus.published_article ? 'text-green-600' : 'text-gray-500'" 
                          x-text="uploadStatus.published_article ? 'Uploaded' : 'Not uploaded'"></span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Journal Cover & TOC</span>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full" :class="uploadStatus.journal_cover ? 'bg-green-500' : 'bg-gray-300'"></div>
                    <span class="text-xs" :class="uploadStatus.journal_cover ? 'text-green-600' : 'text-gray-500'" 
                          x-text="uploadStatus.journal_cover ? 'Uploaded' : 'Not uploaded'"></span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Terminal Report</span>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full" :class="uploadStatus.terminal_report ? 'bg-green-500' : 'bg-gray-300'"></div>
                    <span class="text-xs" :class="uploadStatus.terminal_report ? 'text-green-600' : 'text-gray-500'" 
                          x-text="uploadStatus.terminal_report ? 'Uploaded' : 'Not uploaded'"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global upload status tracking
window.uploadStatus = {
    recommendation_letter: false,
    published_article: false,
    journal_cover: false,
    terminal_report: false
};

// Alpine.js data for upload progress
function uploadProgress() {
    return {
        uploadStatus: window.uploadStatus
    };
}

// Handle file drop
function handleFileDrop(event, fieldName) {
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];
        if (file.type === 'application/pdf') {
            const input = document.querySelector(`[name="${fieldName}"]`);
            if (input) {
                input.files = files;
                input.dispatchEvent(new Event('change'));
            }
        } else {
            alert('Please upload only PDF files.');
        }
    }
}

// Handle file upload with progress
function handleFileUpload(event, fieldName, componentData) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validate file type
    if (file.type !== 'application/pdf') {
        alert('Please upload only PDF files.');
        return;
    }
    
    // Validate file size (20MB)
    if (file.size > 20 * 1024 * 1024) {
        alert('File size must be less than 20MB.');
        return;
    }
    
    // Update display name
    componentData.fileName = file.name;
    componentData.displayName = file.name.length > 20 ? 
        file.name.slice(0, 10) + '...' + file.name.slice(-7) : 
        file.name;
    
    // Start upload
    uploadFile(file, fieldName, componentData);
}

// Upload file with progress tracking
function uploadFile(file, fieldName, componentData) {
    const formData = new FormData();
    formData.append(fieldName, file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Set uploading state
    componentData.isUploading = true;
    componentData.uploadProgress = 0;
    
    // Create XMLHttpRequest for progress tracking
    const xhr = new XMLHttpRequest();
    
    // Track upload progress
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            componentData.uploadProgress = Math.round(percentComplete);
        }
    });
    
    // Handle upload completion
    xhr.addEventListener('load', function() {
        componentData.isUploading = false;
        
        if (xhr.status === 200) {
            // Upload successful
            componentData.uploadProgress = 100;
            window.uploadStatus[fieldName] = true;
            
            // Update progress indicators
            updateProgressIndicators();
            
            // Show success message
            showNotification('File uploaded successfully!', 'success');
        } else {
            // Upload failed
            componentData.uploadProgress = 0;
            showNotification('Upload failed. Please try again.', 'error');
        }
    });
    
    // Handle upload error
    xhr.addEventListener('error', function() {
        componentData.isUploading = false;
        componentData.uploadProgress = 0;
        showNotification('Upload failed. Please check your connection.', 'error');
    });
    
    // Start upload
    xhr.open('POST', '/publications/upload-temp', true);
    xhr.send(formData);
}

// Update progress indicators
function updateProgressIndicators() {
    // Trigger Alpine.js reactivity
    const progressComponent = document.querySelector('[x-data="uploadProgress()"]');
    if (progressComponent && progressComponent._x_dataStack) {
        progressComponent._x_dataStack[0].uploadStatus = { ...window.uploadStatus };
    }
}

// Show notification
function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
