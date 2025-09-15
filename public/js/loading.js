/**
 * Simple Loading System - Direct DOM manipulation
 * No complex classes, just simple functions that work
 */

// Prevent redeclaration errors when script is loaded multiple times
if (typeof window.loadingSystemInitialized === 'undefined') {
    window.loadingSystemInitialized = true;
    
    // Simple loading overlay
    let loadingOverlay = null;

    function createLoadingOverlay(showFirstGenNotice = false) {
        // Remove existing overlay if it exists and notice parameter is different
        if (loadingOverlay) {
            const existingNotice = loadingOverlay.querySelector('.bg-maroon-50');
            const hasNotice = existingNotice !== null;
            if (hasNotice !== showFirstGenNotice) {
                loadingOverlay.remove();
                loadingOverlay = null;
            } else {
                return; // Same configuration, reuse existing
            }
        }
        
        loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'loading-overlay';
        loadingOverlay.className = 'fixed inset-0 flex items-center justify-center z-50 hidden backdrop-blur-md';
        
        const firstGenNotice = showFirstGenNotice ? `
            <div class="bg-maroon-50 border border-maroon-200 rounded-lg p-3 mb-4">
                <p class="text-xs text-maroon-800 font-medium">
                    💡 <strong>Notice:</strong> First generation may be slower as templates are being optimized.
                    Subsequent generations will be instant!
                </p>
            </div>
        ` : '';
        
        loadingOverlay.innerHTML = `
            <div class="bg-white rounded-xl shadow-2xl px-8 py-8 flex flex-col items-center max-w-md mx-4 transform transition-all duration-300 scale-95 opacity-0" id="loading-card">
                <div class="relative mb-6">
                    <div class="w-16 h-16 border-4 border-gray-200 border-t-maroon-600 rounded-full animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-8 h-8 bg-maroon-600 rounded-full animate-pulse"></div>
                    </div>
                </div>
                <div class="text-center w-full">
                    <h3 class="text-xl font-bold text-gray-900 mb-2" id="loading-title">Processing...</h3>
                    <p class="text-sm text-gray-600 mb-2" id="loading-message">Please wait while we process your request</p>
                    ${firstGenNotice}
                    <div class="space-y-2" id="progress-steps">
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span id="current-step">Initializing...</span>
                            <span id="step-counter">1/5</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-maroon-600 h-2 rounded-full transition-all duration-500 ease-out" id="progress-bar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Ensure document.body exists before appending
        if (document.body) {
            document.body.appendChild(loadingOverlay);
        } else {
            // If document.body doesn't exist yet, wait for DOM to be ready
            document.addEventListener('DOMContentLoaded', () => {
                if (document.body && !document.getElementById('loading-overlay')) {
                    document.body.appendChild(loadingOverlay);
                }
            });
        }
    }

    function showLoading(title, message, progressSteps = [], showFirstGenNotice = false) {
        createLoadingOverlay(showFirstGenNotice);
        
        // Wait for elements to be available with retry mechanism
        const updateContent = () => {
            const titleEl = document.getElementById('loading-title');
            const messageEl = document.getElementById('loading-message');
            
            if (titleEl) {
                titleEl.textContent = title;
            } else {
                console.warn('Loading title element not found');
            }
            if (messageEl) {
                messageEl.textContent = message;
            } else {
                console.warn('Loading message element not found');
            }
        };
        
        // Try to update immediately
        updateContent();
        
        // If elements still don't exist, retry with increasing delays
        let retryCount = 0;
        const maxRetries = 10;
        const retryInterval = setInterval(() => {
            const titleEl = document.getElementById('loading-title');
            const messageEl = document.getElementById('loading-message');
            
            if (titleEl && messageEl) {
                clearInterval(retryInterval);
                updateContent();
            } else if (retryCount >= maxRetries) {
                clearInterval(retryInterval);
                console.error('Failed to find loading elements after', maxRetries, 'retries');
            } else {
                retryCount++;
            }
        }, 50);
        
        // Show overlay
        if (loadingOverlay) {
            loadingOverlay.classList.remove('hidden');
        }
        
        // Animate in
        const card = document.getElementById('loading-card');
        if (card) {
            setTimeout(() => {
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        
        // Initialize progress
        if (progressSteps.length > 0) {
            updateProgress(0, progressSteps);
        }
    }

    function updateProgress(currentStep, steps) {
        const progressBar = document.getElementById('progress-bar');
        const currentStepEl = document.getElementById('current-step');
        const stepCounter = document.getElementById('step-counter');
        
        if (progressBar && currentStepEl && stepCounter) {
            const progress = ((currentStep + 1) / steps.length) * 100;
            progressBar.style.width = `${progress}%`;
            currentStepEl.textContent = steps[currentStep] || 'Processing...';
            stepCounter.textContent = `${currentStep + 1}/${steps.length}`;
        } else {
            // If elements don't exist yet, try again after a short delay
            setTimeout(() => updateProgress(currentStep, steps), 50);
        }
    }

    function hideLoading() {
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
        }
    }

    // Make functions globally available
    window.showLoading = showLoading;
    window.updateProgress = updateProgress;
    window.hideLoading = hideLoading;

    console.log('Loading system initialized');
}
