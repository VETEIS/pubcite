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
                <input type="file" id="recommendation-letter-review" class="hidden" accept=".pdf" onchange="updateReviewFile('recommendation_letter', this)">
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
                <input type="file" id="published-article-review" class="hidden" accept=".pdf" onchange="updateReviewFile('published_article', this)">
            </div>

            <!-- Peer Review Review -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">Peer Review</h4>
                </div>
                <p class="text-xs text-gray-600 mb-2" id="review-peer-review">No file uploaded</p>
                <button type="button" class="text-xs text-maroon-600 hover:text-maroon-800 font-medium" onclick="document.getElementById('peer-review-review').click()">Change File</button>
                <input type="file" id="peer-review-review" class="hidden" accept=".pdf" onchange="updateReviewFile('peer_review', this)">
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
                <input type="file" id="terminal-report-review" class="hidden" accept=".pdf" onchange="updateReviewFile('terminal_report', this)">
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
                    <input type="checkbox" id="confirm-submission" x-model="confirmChecked" class="w-4 h-4 text-maroon-600 border-gray-300 rounded focus:ring-maroon-500">
                    <label for="confirm-submission" class="text-sm text-green-800">
                        I confirm that all information is accurate and complete
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Display uploaded files when page loads
function displayUploadedFiles() {
    console.log('displayUploadedFiles called');
    const fileFields = [
        { fieldName: 'recommendation_letter', elementId: 'review-recommendation-letter' },
        { fieldName: 'published_article', elementId: 'review-published-article' },
        { fieldName: 'peer_review', elementId: 'review-peer-review' },
        { fieldName: 'terminal_report', elementId: 'review-terminal-report' }
    ];
    
    fileFields.forEach(({ fieldName, elementId }) => {
        // Look for the actual file input in the upload tab
        const input = document.querySelector(`[name="${fieldName}"]`);
        console.log(`Checking field ${fieldName}:`, input, input ? input.files : 'no input');
        
        if (input && input.files && input.files.length > 0) {
            const fileName = input.files[0].name;
            const displayName = fileName.length > 20 ? fileName.slice(0, 10) + '...' + fileName.slice(-7) : fileName;
            
            console.log(`Found file for ${fieldName}:`, fileName);
            
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = displayName;
                element.title = fileName;
                element.classList.remove('text-gray-600');
                element.classList.add('text-green-600', 'font-medium');
            }
        } else {
            // Reset display if no file
            console.log(`No file found for ${fieldName}`);
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = 'No file uploaded';
                element.title = '';
                element.classList.remove('text-green-600', 'font-medium');
                element.classList.add('text-gray-600');
            }
        }
    });
}

// Sync file changes between upload tab and review tab
function syncFileDisplay(fieldName) {
    const input = document.querySelector(`[name="${fieldName}"]`);
    const reviewElementId = `review-${fieldName}`;
    const element = document.getElementById(reviewElementId);
    
    if (input && input.files && input.files.length > 0) {
        const fileName = input.files[0].name;
        const displayName = fileName.length > 20 ? fileName.slice(0, 10) + '...' + fileName.slice(-7) : fileName;
        
        if (element) {
            element.textContent = displayName;
            element.title = fileName;
            element.classList.remove('text-gray-600');
            element.classList.add('text-green-600', 'font-medium');
        }
    } else {
        if (element) {
            element.textContent = 'No file uploaded';
            element.title = '';
            element.classList.remove('text-green-600', 'font-medium');
            element.classList.add('text-gray-600');
        }
    }
}

function updateReviewFile(type, input) {
    const fileName = input.files.length > 0 ? input.files[0].name : 'No file uploaded';
    const displayName = fileName.length > 20 ? fileName.slice(0, 10) + '...' + fileName.slice(-7) : fileName;
    
    // Map field names to element IDs
    const elementIdMap = {
        'recommendation_letter': 'review-recommendation-letter',
        'published_article': 'review-published-article',
        'peer_review': 'review-peer-review',
        'terminal_report': 'review-terminal-report'
    };
    
    const reviewElementId = elementIdMap[type] || `review-${type}`;
    const element = document.getElementById(reviewElementId);
    if (element) {
        element.textContent = displayName;
        element.title = fileName;
        if (input.files.length > 0) {
            element.classList.remove('text-gray-600');
            element.classList.add('text-green-600', 'font-medium');
        } else {
            element.classList.remove('text-green-600', 'font-medium');
            element.classList.add('text-gray-600');
        }
    }
    
    // Update the original file input
    const originalInput = document.querySelector(`[name="${type}"]`);
    if (originalInput && input.files.length > 0) {
        // Create a new FileList-like object
        const dt = new DataTransfer();
        dt.items.add(input.files[0]);
        originalInput.files = dt.files;
        
        // Trigger change event to update form state
        originalInput.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

// Display uploaded files when page loads
document.addEventListener('DOMContentLoaded', function() {
    displayUploadedFiles();
});

function generateDocx(type) {
    const form = document.getElementById('publication-request-form');
    if (!form) {
        console.error('Form not found: publication-request-form');
        alert('Error: Form not found. Please refresh the page and try again.');
        return;
    }
    
    const formData = new FormData(form);
    formData.append('docx_type', type);

    // Debug: Log form data
    console.log('Form data for', type, ':');
    for (let [key, value] of formData.entries()) {
        console.log(key, ':', value);
    }

    // Show loading state with simple UI feedback
    const button = event.target.closest('.cursor-pointer');
    if (button) {
        button.style.opacity = '0.6';
        button.style.pointerEvents = 'none';
        const originalText = button.querySelector('p').textContent;
        button.querySelector('p').textContent = 'Generating...';
    }

    fetch('{{ route("publications.generateDocx") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Check content type
        const contentType = response.headers.get('content-type');
        console.log('Response content type:', contentType);
        
        return response.blob();
    })
    .then(blob => {
        // Check if blob is valid
        if (!blob || blob.size === 0) {
            throw new Error('Generated file is empty or corrupted');
        }
        
        console.log('Generated blob size:', blob.size, 'bytes');
        console.log('Generated blob type:', blob.type);
        
        // Ensure proper MIME type for DOCX
        const docxBlob = new Blob([blob], { 
            type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' 
        });
        
        const url = window.URL.createObjectURL(docxBlob);
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
        
        // Clean up
        setTimeout(() => {
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }, 100);
    })
    .catch(error => {
        console.error('Error generating document:', error);
        alert(`Error generating document: ${error.message}. Please check your form data and try again.`);
    })
    .finally(() => {
        // Restore button state
        if (button) {
            button.style.opacity = '1';
            button.style.pointerEvents = 'auto';
            button.querySelector('p').textContent = 'Click to generate DOCX';
        }
    });
}
</script>
