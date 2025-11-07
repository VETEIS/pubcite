<!-- Simple Upload Documents Section for Publications -->
<div class="space-y-6 h-full flex flex-col min-h-[calc(100vh)]">
    <!-- Header Section -->
    <div class="text-center py-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
        <h2 class="text-2xl font-bold text-blue-800 mb-2">Upload Required Documents</h2>
        <p class="text-sm text-blue-600">Please upload all required PDF documents for your publication request</p>
    </div>
    
    
    <!-- Upload Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 flex-1 items-start">
        <!-- Recommendation Letter Card -->
        <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 group cursor-pointer"
             x-data="{ fileName: '', displayName: '', isDragOver: false }"
             @click="$refs.recommendationLetter.click()"
             @dragover.prevent="isDragOver = true"
             @dragleave.prevent="isDragOver = false"
             @drop.prevent="isDragOver = false; handleFileDrop($event, 'recommendation_letter')"
             :class="isDragOver ? 'border-blue-400 bg-blue-50' : 'hover:border-maroon-300'">
            <div class="p-4 text-center">
                <div class="w-10 h-10 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-3">Recommendation Letter</h3>
                <div class="text-xs text-maroon-600 font-medium mb-1 truncate" x-text="displayName || 'Click to upload'"></div>
                <p class="text-xs text-gray-400">Max 5MB</p>
            </div>
            <input type="file" name="recommendation_letter" accept=".pdf" class="hidden" x-ref="recommendationLetter" required
                   @change="handleFileSelection($event, $data)">
        </div>

        <!-- Published Article Card -->
        <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 group cursor-pointer"
             x-data="{ fileName: '', displayName: '', isDragOver: false }"
             @click="$refs.publishedArticle.click()"
             @dragover.prevent="isDragOver = true"
             @dragleave.prevent="isDragOver = false"
             @drop.prevent="isDragOver = false; handleFileDrop($event, 'published_article')"
             :class="isDragOver ? 'border-blue-400 bg-blue-50' : 'hover:border-maroon-300'">
            <div class="p-4 text-center">
                <div class="w-10 h-10 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-3">Published Article</h3>
                <div class="text-xs text-maroon-600 font-medium mb-1 truncate" x-text="displayName || 'Click to upload'"></div>
                <p class="text-xs text-gray-400">Max 20MB</p>
            </div>
            <input type="file" name="published_article" accept=".pdf" class="hidden" x-ref="publishedArticle" required
                   @change="handleFileSelection($event, $data)">
        </div>

        <!-- Peer Review Card -->
        <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 group cursor-pointer"
             x-data="{ fileName: '', displayName: '', isDragOver: false }"
             @click="$refs.peerReview.click()"
             @dragover.prevent="isDragOver = true"
             @dragleave.prevent="isDragOver = false"
             @drop.prevent="isDragOver = false; handleFileDrop($event, 'peer_review')"
             :class="isDragOver ? 'border-blue-400 bg-blue-50' : 'hover:border-maroon-300'">
            <div class="p-4 text-center">
                <div class="w-10 h-10 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-3">Peer Reviewed</h3>
                <div class="text-xs text-maroon-600 font-medium mb-1 truncate" x-text="displayName || 'Click to upload'"></div>
                <p class="text-xs text-gray-400">Max 10MB</p>
            </div>
            <input type="file" name="peer_review" accept=".pdf" class="hidden" x-ref="peerReview" required
                   @change="handleFileSelection($event, $data)">
        </div>

        <!-- Terminal Report Card -->
        <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 group cursor-pointer"
             x-data="{ fileName: '', displayName: '', isDragOver: false }"
             @click="$refs.terminalReport.click()"
             @dragover.prevent="isDragOver = true"
             @dragleave.prevent="isDragOver = false"
             @drop.prevent="isDragOver = false; handleFileDrop($event, 'terminal_report')"
             :class="isDragOver ? 'border-blue-400 bg-blue-50' : 'hover:border-maroon-300'">
            <div class="p-4 text-center">
                <div class="w-10 h-10 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-3">Terminal Report</h3>
                <div class="text-xs text-maroon-600 font-medium mb-1 truncate" x-text="displayName || 'Click to upload'"></div>
                <p class="text-xs text-gray-400">Max 15MB</p>
            </div>
            <input type="file" name="terminal_report" accept=".pdf" class="hidden" x-ref="terminalReport" required
                   @change="handleFileSelection($event, $data)">
        </div>
    </div>
</div>

<script>
// Simple file handling functions
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

function handleFileSelection(event, componentData) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validate file type
    if (file.type !== 'application/pdf') {
        alert('Please upload only PDF files.');
        event.target.value = '';
        return;
    }
    
    // Validate file size based on document type
    const maxSizes = {
        'recommendation_letter': 5,  // 5MB
        'published_article': 20,     // 20MB
        'peer_review': 10,           // 10MB
        'terminal_report': 15        // 15MB
    };
    
    const inputName = event.target.name;
    const maxSizeMB = maxSizes[inputName] || 20;
    
    if (file.size > maxSizeMB * 1024 * 1024) {
        alert(`File size must be less than ${maxSizeMB}MB.`);
        event.target.value = '';
        return;
    }
    
    // Update display name
    componentData.fileName = file.name;
    componentData.displayName = file.name.length > 20 ? 
        file.name.slice(0, 10) + '...' + file.name.slice(-7) : 
        file.name;
    
    // Update Alpine.js component data
    const alpineComponent = Alpine.$data(document.querySelector('[x-data]'));
    
    if (alpineComponent && typeof alpineComponent.updateUploadedFile === 'function') {
        alpineComponent.updateUploadedFile(inputName, file.name);
    }
}
</script>
