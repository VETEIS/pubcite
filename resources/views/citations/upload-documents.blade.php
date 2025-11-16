<!-- Simple Upload Documents Section for Citations -->
<div class="space-y-6 h-full flex flex-col min-h-[calc(100vh)]">
    <!-- Header Section -->
    <div class="text-center py-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
        <h2 class="text-2xl font-bold text-blue-800 mb-2">Upload Required Documents</h2>
        <p class="text-sm text-blue-600">Please upload all required PDF documents for your citation request</p>
    </div>
    
    
    <!-- Upload Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 flex-1 items-start">
        <!-- Citing Article Card -->
        <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 group cursor-pointer"
             x-data="{ fileName: '', displayName: '', isDragOver: false }"
             @click="$refs.citingArticle.click()"
             @dragover.prevent="isDragOver = true"
             @dragleave.prevent="isDragOver = false"
             @drop.prevent="isDragOver = false; handleFileDrop($event, 'citing_article')"
             :class="isDragOver ? 'border-blue-400 bg-blue-50' : 'hover:border-maroon-300'">
            <div class="p-4 text-center">
                <div class="w-10 h-10 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-3">Citing Article</h3>
                <div class="text-xs text-maroon-600 font-medium mb-1 truncate" x-text="displayName || 'Click to upload'"></div>
                <p class="text-xs text-gray-400">Max 20MB</p>
            </div>
            <input type="file" name="citing_article" accept=".pdf" class="hidden" x-ref="citingArticle" required
                   @change="handleFileSelection($event, $data)">
        </div>

        <!-- Cited Article Card -->
        <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 group cursor-pointer"
             x-data="{ fileName: '', displayName: '', isDragOver: false }"
             @click="$refs.citedArticle.click()"
             @dragover.prevent="isDragOver = true"
             @dragleave.prevent="isDragOver = false"
             @drop.prevent="isDragOver = false; handleFileDrop($event, 'cited_article')"
             :class="isDragOver ? 'border-blue-400 bg-blue-50' : 'hover:border-maroon-300'">
            <div class="p-4 text-center">
                <div class="w-10 h-10 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-3">Cited Article</h3>
                <div class="text-xs text-maroon-600 font-medium mb-1 truncate" x-text="displayName || 'Click to upload'"></div>
                <p class="text-xs text-gray-400">Max 20MB</p>
            </div>
            <input type="file" name="cited_article" accept=".pdf" class="hidden" x-ref="citedArticle" required
                   @change="handleFileSelection($event, $data)">
        </div>

        <!-- WOS / Scopus Evidence Card -->
        <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 group cursor-pointer"
             x-data="{ fileName: '', displayName: '', isDragOver: false }"
             @click="$refs.indexingEvidence.click()"
             @dragover.prevent="isDragOver = true"
             @dragleave.prevent="isDragOver = false"
             @drop.prevent="isDragOver = false; handleFileDrop($event, 'indexing_evidence')"
             :class="isDragOver ? 'border-blue-400 bg-blue-50' : 'hover:border-maroon-300'">
            <div class="p-4 text-center">
                <div class="w-10 h-10 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-3">Indexing Evidence</h3>
                <div class="text-xs text-maroon-600 font-medium mb-1 truncate" x-text="displayName || 'Click to upload'"></div>
                <p class="text-xs text-gray-400">Upload screenshot evidence (Max 10MB)</p>
            </div>
            <input type="file" name="indexing_evidence" accept=".pdf,.png,.jpg,.jpeg" class="hidden" x-ref="indexingEvidence" required
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
        const allowedTypesByField = {
            'citing_article': ['application/pdf'],
            'cited_article': ['application/pdf'],
            'indexing_evidence': ['application/pdf', 'image/png', 'image/jpeg']
        };
        const allowedTypes = allowedTypesByField[fieldName] || ['application/pdf'];
        const errorMessages = {
            'citing_article': 'Please upload PDF files only.',
            'cited_article': 'Please upload PDF files only.',
            'indexing_evidence': 'Please upload PDF, PNG, or JPG files only.'
        };

        if (allowedTypes.includes(file.type)) {
            const input = document.querySelector(`[name="${fieldName}"]`);
            if (input) {
                input.files = files;
                input.dispatchEvent(new Event('change'));
            }
        } else {
            alert(errorMessages[fieldName] || 'File type not supported.');
        }
    }
}

function handleFileSelection(event, componentData) {
    const file = event.target.files[0];
    if (!file) return;
    
    const inputName = event.target.name;
    const allowedTypesByField = {
        'citing_article': ['application/pdf'],
        'cited_article': ['application/pdf'],
        'indexing_evidence': ['application/pdf', 'image/png', 'image/jpeg']
    };
    const errorMessages = {
        'citing_article': 'Please upload PDF files only.',
        'cited_article': 'Please upload PDF files only.',
        'indexing_evidence': 'Please upload PDF, PNG, or JPG files only.'
    };
    const allowedTypes = allowedTypesByField[inputName] || ['application/pdf'];

    // Validate file type
    if (!allowedTypes.includes(file.type)) {
        alert(errorMessages[inputName] || 'File type not supported.');
        event.target.value = '';
        return;
    }
    
    // Validate file size based on document type
    const maxSizes = {
        'citing_article': 20,        // 20MB
        'cited_article': 20,         // 20MB
        'indexing_evidence': 10      // 10MB
    };
    
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
