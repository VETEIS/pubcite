<!-- Modern Review & Submit Section for Publications -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="text-center py-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-green-800 mb-2">Review Your Submission</h2>
        <p class="text-sm text-green-600">Please review all information before submitting your publication request</p>
    </div>
    
    <!-- Review Summary -->
    <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Submission Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Applicant Information</p>
                        <p class="text-sm text-gray-600" id="review-name">-</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">College</p>
                        <p class="text-sm text-gray-600" id="review-college">-</p>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Request Type</p>
                        <p class="text-sm text-gray-600">Research Publication</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Submission Date</p>
                        <p class="text-sm text-gray-600">{{ date('F d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Uploaded Files Review -->
    <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Uploaded Documents</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Recommendation Letter Review -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">Recommendation Letter</h4>
                </div>
                <p class="text-xs text-gray-600 mb-2" id="review-recommendation-letter">No file uploaded</p>
                <button type="button" class="text-xs text-maroon-600 hover:text-maroon-800 font-medium" onclick="document.getElementById('recommendation-letter-review').click()">Change File</button>
                <input type="file" id="recommendation-letter-review" class="hidden" accept=".pdf" onchange="updateReviewFile('recommendation-letter', this)">
            </div>

            <!-- Published Article Review -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">Published Article</h4>
                </div>
                <p class="text-xs text-gray-600 mb-2" id="review-published-article">No file uploaded</p>
                <button type="button" class="text-xs text-maroon-600 hover:text-maroon-800 font-medium" onclick="document.getElementById('published-article-review').click()">Change File</button>
                <input type="file" id="published-article-review" class="hidden" accept=".pdf" onchange="updateReviewFile('published-article', this)">
            </div>

            <!-- Journal Cover Review -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">Journal Cover & TOC</h4>
                </div>
                <p class="text-xs text-gray-600 mb-2" id="review-journal-cover">No file uploaded</p>
                <button type="button" class="text-xs text-maroon-600 hover:text-maroon-800 font-medium" onclick="document.getElementById('journal-cover-review').click()">Change File</button>
                <input type="file" id="journal-cover-review" class="hidden" accept=".pdf" onchange="updateReviewFile('journal-cover', this)">
            </div>

            <!-- Terminal Report Review -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">Terminal Report</h4>
                </div>
                <p class="text-xs text-gray-600 mb-2" id="review-terminal-report">No file uploaded</p>
                <button type="button" class="text-xs text-maroon-600 hover:text-maroon-800 font-medium" onclick="document.getElementById('terminal-report-review').click()">Change File</button>
                <input type="file" id="terminal-report-review" class="hidden" accept=".pdf" onchange="updateReviewFile('terminal-report', this)">
            </div>
        </div>
    </div>
    
    <!-- Generated Documents Section -->
    <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Generated Documents</h3>
        <p class="text-sm text-gray-600 mb-4">Your documents will be automatically generated after submission. You can preview them here:</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Incentive Application Preview -->
            <div class="bg-gradient-to-r from-maroon-50 to-burgundy-50 rounded-lg p-4 border border-maroon-200 hover:shadow-md transition-all duration-200 cursor-pointer" 
                 onclick="generateDocx('incentive')">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-maroon-800">Incentive Application</h4>
                        <p class="text-xs text-maroon-600">Click to generate DOCX</p>
                    </div>
                </div>
            </div>

            <!-- Recommendation Letter Preview -->
            <div class="bg-gradient-to-r from-maroon-50 to-burgundy-50 rounded-lg p-4 border border-maroon-200 hover:shadow-md transition-all duration-200 cursor-pointer" 
                 onclick="generateDocx('recommendation')">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-maroon-800">Recommendation Letter</h4>
                        <p class="text-xs text-maroon-600">Click to generate DOCX</p>
                    </div>
                </div>
            </div>

            <!-- Terminal Report Preview -->
            <div class="bg-gradient-to-r from-maroon-50 to-burgundy-50 rounded-lg p-4 border border-maroon-200 hover:shadow-md transition-all duration-200 cursor-pointer" 
                 onclick="generateDocx('terminal')">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-maroon-800">Terminal Report</h4>
                        <p class="text-xs text-maroon-600">Click to generate DOCX</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Final Confirmation -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-green-800 mb-2">Ready to Submit</h3>
                <p class="text-sm text-green-700 mb-4">
                    Please review all information above. Once submitted, your publication request will be sent to the administrators for review.
                </p>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="confirm-submission" class="w-4 h-4 text-maroon-600 border-gray-300 rounded focus:ring-maroon-500">
                    <label for="confirm-submission" class="text-sm text-green-800">
                        I confirm that all information is accurate and complete
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateReviewFile(type, input) {
    const fileName = input.files.length > 0 ? input.files[0].name : 'No file uploaded';
    const displayName = fileName.length > 20 ? fileName.slice(0, 10) + '...' + fileName.slice(-7) : fileName;
    
    const reviewElementId = `review-${type}`;
    const element = document.getElementById(reviewElementId);
    if (element) {
        element.textContent = displayName;
        element.title = fileName;
    }
    
    // Update the original file input
    const originalFieldName = type.replace('-', '_');
    const originalInput = document.querySelector(`[name="${originalFieldName}"]`);
    if (originalInput && input.files.length > 0) {
        originalInput.files = input.files;
    }
}

function generateDocx(type) {
    const form = document.getElementById('publication-request-form');
    const formData = new FormData(form);
    formData.append('docx_type', type);

    // Show loading state - Now handled by LoadingManager
    if (window.loadingManager) {
        const operationId = `submit-review-${Date.now()}`;
        window.loadingManager.show(operationId, {
            title: 'Submitting Request',
            message: 'Please wait while we process your submission...',
            showOverlay: true,
            disableButtons: true
        });
    }

    fetch('{{ route("publications.generateDocx") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Network response was not ok');
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        const timestamp = new Date().toISOString().slice(0, 10);
        a.download = type === 'incentive' 
            ? `Publication_Incentive_Application_${timestamp}.docx` 
            : type === 'recommendation'
            ? `Publication_Recommendation_Letter_${timestamp}.docx`
            : `Publication_Terminal_Report_${timestamp}.docx`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        alert('Error generating document. Please try again.');
    })
    .finally(() => {
        // Hide loading state
        if (window.loadingManager) {
            window.loadingManager.hide(operationId);
        }
    });
}
</script>
