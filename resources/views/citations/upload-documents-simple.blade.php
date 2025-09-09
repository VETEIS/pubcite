<!-- Simple Upload Documents Section for Citations -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="text-center py-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-blue-800 mb-2">Upload Required Documents</h2>
        <p class="text-sm text-blue-600">Please upload all required PDF documents for your citation request</p>
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
                    <li>• Files will be uploaded when you submit the form</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Upload Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                <p class="text-sm text-gray-600 mb-4">From faculty member (PDF)</p>
                <div class="text-sm text-maroon-600 font-medium mb-2 truncate" x-text="displayName || 'Click to upload'"></div>
                <p class="text-xs text-gray-500">Max 20MB • PDF only</p>
            </div>
            <input type="file" name="recommendation_letter" accept=".pdf" class="hidden" x-ref="recommendationLetter" required
                   @change="handleFileSelection($event, $data)">
        </div>

        <!-- Citing Article Card -->
        <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-all duration-200 group cursor-pointer"
             x-data="{ fileName: '', displayName: '', isDragOver: false }"
             @click="$refs.citingArticle.click()"
             @dragover.prevent="isDragOver = true"
             @dragleave.prevent="isDragOver = false"
             @drop.prevent="isDragOver = false; handleFileDrop($event, 'citing_article')"
             :class="isDragOver ? 'border-blue-400 bg-blue-50' : 'hover:border-maroon-300'">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-8 h-8 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Citing Article</h3>
                <p class="text-sm text-gray-600 mb-4">Your article that cites the work (PDF)</p>
                <div class="text-sm text-maroon-600 font-medium mb-2 truncate" x-text="displayName || 'Click to upload'"></div>
                <p class="text-xs text-gray-500">Max 20MB • PDF only</p>
            </div>
            <input type="file" name="citing_article" accept=".pdf" class="hidden" x-ref="citingArticle" required
                   @change="handleFileSelection($event, $data)">
        </div>

        <!-- Cited Article Card -->
        <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-all duration-200 group cursor-pointer"
             x-data="{ fileName: '', displayName: '', isDragOver: false }"
             @click="$refs.citedArticle.click()"
             @dragover.prevent="isDragOver = true"
             @dragleave.prevent="isDragOver = false"
             @drop.prevent="isDragOver = false; handleFileDrop($event, 'cited_article')"
             :class="isDragOver ? 'border-blue-400 bg-blue-50' : 'hover:border-maroon-300'">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-maroon-100 to-maroon-200 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-8 h-8 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Cited Article</h3>
                <p class="text-sm text-gray-600 mb-4">The article you cited (PDF)</p>
                <div class="text-sm text-maroon-600 font-medium mb-2 truncate" x-text="displayName || 'Click to upload'"></div>
                <p class="text-xs text-gray-500">Max 20MB • PDF only</p>
            </div>
            <input type="file" name="cited_article" accept=".pdf" class="hidden" x-ref="citedArticle" required
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
    
    // Validate file size (20MB)
    if (file.size > 20 * 1024 * 1024) {
        alert('File size must be less than 20MB.');
        event.target.value = '';
        return;
    }
    
    // Update display name
    componentData.fileName = file.name;
    componentData.displayName = file.name.length > 20 ? 
        file.name.slice(0, 10) + '...' + file.name.slice(-7) : 
        file.name;
}
</script>
