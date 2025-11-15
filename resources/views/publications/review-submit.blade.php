<!-- Modern Review & Submit Section for Publications -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="text-center py-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200">
        <h2 class="text-2xl font-bold text-green-800 mb-2">Review Your Submission</h2>
        <p class="text-sm text-green-600">Please review all information before submitting your publication request</p>
    </div>
    
    <!-- Uploaded Files Review -->
    <div class="bg-white/50 backdrop-blur-sm rounded-xl border border-gray-200 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Uploaded Documents</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

            <!-- Indexing Evidence Review -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">WOS / Scopus Evidence</h4>
                </div>
                <p class="text-xs text-gray-600 mb-2" id="review-indexing-evidence">No file uploaded</p>
                <button type="button" class="text-xs text-maroon-600 hover:text-maroon-800 font-medium" onclick="document.getElementById('indexing-evidence-review').click()">Change File</button>
                <input type="file" id="indexing-evidence-review" class="hidden" accept=".pdf" onchange="updateReviewFile('indexing_evidence', this)">
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
                 id="incentive-doc-button"
                 onclick="generateDocx('incentive')">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-maroon-800">Incentive Application</h4>
                        <p class="text-xs text-maroon-600" id="incentive-button-text">View PDF</p>
                    </div>
                </div>
            </div>

            <!-- Recommendation Letter Preview -->
            <div class="bg-gradient-to-r from-maroon-50 to-burgundy-50 rounded-lg p-4 border border-maroon-200 hover:shadow-md transition-all duration-200 cursor-pointer" 
                 id="recommendation-doc-button"
                 onclick="generateDocx('recommendation')">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-maroon-800">Recommendation Letter</h4>
                        <p class="text-xs text-maroon-600" id="recommendation-button-text">View PDF</p>
                    </div>
                </div>
            </div>

            <!-- Terminal Report Preview -->
            <div class="bg-gradient-to-r from-maroon-50 to-burgundy-50 rounded-lg p-4 border border-maroon-200 hover:shadow-md transition-all duration-200 cursor-pointer" 
                 id="terminal-doc-button"
                 onclick="generateDocx('terminal')">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-maroon-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-maroon-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-maroon-800">Terminal Report</h4>
                        <p class="text-xs text-maroon-600" id="terminal-button-text">View PDF</p>
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
// Function moved to main Alpine.js component as a method
// This ensures it's always accessible when tab switching occurs

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
        'published_article': 'review-published-article',
        'indexing_evidence': 'review-indexing-evidence'
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
    updateDocumentButtonsForTurbo();
});

// Update document buttons for Turbo compatibility
function updateDocumentButtonsForTurbo() {
    const form = document.getElementById('publication-request-form');
    if (!form) return;
    
    const alpineComponent = Alpine.$data(form.closest('[x-data]'));
    if (alpineComponent && alpineComponent.updateDocumentButtonStates) {
        setTimeout(() => {
            alpineComponent.updateDocumentButtonStates();
        }, 300);
    }
}

// Re-initialize on Turbo navigation
document.addEventListener('turbo:load', function() {
    updateDocumentButtonsForTurbo();
});

document.addEventListener('turbo:render', function() {
    updateDocumentButtonsForTurbo();
});

// Function to generate and download DOCX or serve PDF if available
function generateDocx(type) {
    const form = document.getElementById('publication-request-form');
    if (!form) {
        alert('Error: Form not found. Please refresh the page and try again.');
        return;
    }
    
    // Get Alpine.js component data
    const alpineComponent = Alpine.$data(form.closest('[x-data]'));
    
    // Check if file is already generated and form data hasn't changed
    // If PDF exists (DOCX was converted), the server will serve PDF instead
    if (alpineComponent && alpineComponent.generatedDocxPaths && alpineComponent.generatedDocxPaths[type]) {
        const currentHash = alpineComponent.calculateFormDataHash(type);
        if (alpineComponent.formDataHashes[type] === currentHash) {
            // Try to fetch - server will serve PDF if it exists, DOCX otherwise
            fetchPreGeneratedDocx(type, alpineComponent.generatedDocxPaths[type]);
            return;
        }
    }
    
    // Create FormData but exclude file inputs to avoid 413 errors
    const formData = new FormData();
    const formElements = form.querySelectorAll('input, textarea, select');
    
    formElements.forEach(element => {
        // Skip file inputs - we don't need them for DOCX generation
        if (element.type === 'file') {
            return;
        }
        
        // Handle checkboxes and radios
        if (element.type === 'checkbox' || element.type === 'radio') {
            if (element.checked) {
                formData.append(element.name, element.value || '1');
            }
        } else if (element.name && element.value !== null) {
            // Handle text inputs, textareas, selects, and hidden inputs
            formData.append(element.name, element.value);
        }
    });
    
    formData.append('docx_type', type);
    formData.append('store_for_submit', '1'); // Store for submission use

    // Show loading modal for document generation
    const docTypeNames = {
        'incentive': 'Incentive Application',
        'recommendation': 'Recommendation Letter',
        'terminal': 'Terminal Report'
    };
    
    const progressSteps = [
        'Preparing document template...',
        'Processing form data...',
        'Generating document...',
        'Finalizing...'
    ];
    
    window.showLoading('Generating Document', `Creating ${docTypeNames[type] || type} document, please wait...`, progressSteps, false);

    fetch('{{ route("publications.generate") }}', {
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
        
        // Check content type to determine if it's PDF or DOCX
        const contentType = response.headers.get('content-type') || '';
        const isPdf = contentType.includes('application/pdf');
        
        return response.blob().then(blob => ({ blob, isPdf }));
    })
    .then(({ blob, isPdf }) => {
        if (!blob || blob.size === 0) {
            throw new Error('Generated file is empty or corrupted');
        }
        
        const fileBlob = new Blob([blob], { 
            type: isPdf ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        });
        const url = window.URL.createObjectURL(fileBlob);
        const a = document.createElement('a');
        a.href = url;
        const timestamp = new Date().toISOString().slice(0, 10);
        
        if (isPdf) {
            a.download = type === 'incentive' 
                ? `Publication_Incentive_Application_${timestamp}.pdf` 
                : type === 'recommendation'
                ? `Publication_Recommendation_Letter_${timestamp}.pdf`
                : `Publication_Terminal_Report_${timestamp}.pdf`;
        } else {
            a.download = type === 'incentive' 
                ? `Publication_Incentive_Application_${timestamp}.docx` 
                : type === 'recommendation'
                ? `Publication_Recommendation_Letter_${timestamp}.docx`
                : `Publication_Terminal_Report_${timestamp}.docx`;
        }
        
        document.body.appendChild(a);
        a.click();
        
        // Clean up
        setTimeout(() => {
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }, 100);
        
        // Update button states after generation
        if (alpineComponent && alpineComponent.updateDocumentButtonStates) {
            setTimeout(() => {
                alpineComponent.updateDocumentButtonStates();
            }, 500);
        }
    })
    .catch(error => {
        alert(`Error generating document: ${error.message}. Please check your form data and try again.`);
    })
    .finally(() => {
        // Hide loading state
        window.hideLoading();
    });
}

// Fetch pre-generated DOCX file or PDF if available
async function fetchPreGeneratedDocx(type, filePath) {
    // Show loading modal for file download
    const docTypeNames = {
        'incentive': 'Incentive Application',
        'recommendation': 'Recommendation Letter',
        'terminal': 'Terminal Report'
    };
    
    window.showLoading('Preparing Download', `Preparing ${docTypeNames[type] || type} file for download...`, ['Loading file...'], false);
    
    try {
        // Request the file from the server - it will serve PDF if available, DOCX otherwise
        const response = await fetch(`{{ route("publications.generate") }}?file_path=${encodeURIComponent(filePath)}&docx_type=${type}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const blob = await response.blob();
        if (!blob || blob.size === 0) {
            throw new Error('Generated file is empty or corrupted');
        }
        
        // Check content type to determine if it's PDF or DOCX
        const contentType = response.headers.get('content-type') || '';
        const isPdf = contentType.includes('application/pdf') || blob.type.includes('pdf');
        
        const fileBlob = new Blob([blob], { 
            type: isPdf ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        });
        
        const url = window.URL.createObjectURL(fileBlob);
        const a = document.createElement('a');
        a.href = url;
        const timestamp = new Date().toISOString().slice(0, 10);
        
        if (isPdf) {
            a.download = type === 'incentive' 
                ? `Publication_Incentive_Application_${timestamp}.pdf` 
                : type === 'recommendation'
                ? `Publication_Recommendation_Letter_${timestamp}.pdf`
                : `Publication_Terminal_Report_${timestamp}.pdf`;
        } else {
            a.download = type === 'incentive' 
                ? `Publication_Incentive_Application_${timestamp}.docx` 
                : type === 'recommendation'
                ? `Publication_Recommendation_Letter_${timestamp}.docx`
                : `Publication_Terminal_Report_${timestamp}.docx`;
        }
        
        document.body.appendChild(a);
        a.click();
        
        setTimeout(() => {
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }, 100);
    } catch (error) {
        // Fallback to normal generation if pre-generated file fetch fails
        console.warn('Failed to fetch pre-generated file, generating new one:', error);
        
        // Clear the invalid path to prevent retry loops
        const form = document.getElementById('publication-request-form');
        if (form) {
            const alpineComponent = Alpine.$data(form.closest('[x-data]'));
            if (alpineComponent && alpineComponent.generatedDocxPaths) {
                alpineComponent.generatedDocxPaths[type] = null;
                alpineComponent.formDataHashes[type] = null;
            }
        }
        
        // Generate fresh DOCX
        generateDocx(type);
    } finally {
        // Hide loading state
        window.hideLoading();
    }
}
</script>
