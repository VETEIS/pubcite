<!-- Modern Review & Submit Section -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="text-center py-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-green-800 mb-2">Review Your Submission</h2>
        <p class="text-sm text-green-600">Please review all information before submitting your citation request</p>
    </div>
    
    <!-- Uploaded Files Review -->
    <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Uploaded Documents</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

            <!-- Citing Article Review -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">Citing Article</h4>
                </div>
                <p class="text-xs text-gray-600 mb-2" id="review-citing-article">No file uploaded</p>
                <button type="button" class="text-xs text-maroon-600 hover:text-maroon-800 font-medium" onclick="document.getElementById('citing-article-review').click()">Change File</button>
                <input type="file" id="citing-article-review" class="hidden" accept=".pdf" onchange="updateReviewFile('citing_article', this)">
            </div>

            <!-- Cited Article Review -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">Cited Article</h4>
                </div>
                <p class="text-xs text-gray-600 mb-2" id="review-cited-article">No file uploaded</p>
                <button type="button" class="text-xs text-maroon-600 hover:text-maroon-800 font-medium" onclick="document.getElementById('cited-article-review').click()">Change File</button>
                <input type="file" id="cited-article-review" class="hidden" accept=".pdf" onchange="updateReviewFile('cited_article', this)">
            </div>
        </div>
    </div>
    
    <!-- Generated Documents Section -->
    <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Generated Documents</h3>
        <p class="text-sm text-gray-600 mb-4">Your documents will be automatically generated after submission. You can preview them here:</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    Please review all information above. Once submitted, your citation request will be sent to the administrators for review.
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
    
    // Map field names to element IDs
    const elementIdMap = {
        'recommendation_letter': 'review-recommendation-letter',
        'citing_article': 'review-citing-article',
        'cited_article': 'review-cited-article'
    };
    
    const reviewElementId = elementIdMap[type] || `review-${type}`;
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

// Display uploaded files when page loads
function displayUploadedFiles() {
    const fileFields = [
        { fieldName: 'recommendation_letter', elementId: 'review-recommendation-letter' },
        { fieldName: 'citing_article', elementId: 'review-citing-article' },
        { fieldName: 'cited_article', elementId: 'review-cited-article' }
    ];
    
    fileFields.forEach(({ fieldName, elementId }) => {
        // Look for the actual file input in the upload tab
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input && input.files && input.files.length > 0) {
            const fileName = input.files[0].name;
            const displayName = fileName.length > 20 ? fileName.slice(0, 10) + '...' + fileName.slice(-7) : fileName;
            
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = displayName;
                element.title = fileName;
                element.classList.remove('text-gray-600');
                element.classList.add('text-green-600', 'font-medium');
            }
        } else {
            // Reset display if no file
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
        'citing_article': 'review-citing-article',
        'cited_article': 'review-cited-article'
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
    const form = document.getElementById('citation-request-form');
    if (!form) {
        console.error('Form not found: citation-request-form');
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

    fetch('{{ route("citations.generate") }}', {
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
            ? `Citation_Incentive_Application_${timestamp}.docx` 
            : `Citation_Recommendation_Letter_${timestamp}.docx`;
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
