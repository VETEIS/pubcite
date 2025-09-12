/**
 * Simple Loading System - Direct DOM manipulation
 * No complex classes, just simple functions that work
 */

// Simple loading overlay
let loadingOverlay = null;

function createLoadingOverlay() {
    if (loadingOverlay) return;
    
    loadingOverlay = document.createElement('div');
    loadingOverlay.id = 'loading-overlay';
    loadingOverlay.className = 'fixed inset-0 flex items-center justify-center z-50 hidden backdrop-blur-md';
    loadingOverlay.innerHTML = `
        <div class="bg-white rounded-xl shadow-2xl px-8 py-8 flex flex-col items-center max-w-md mx-4 transform transition-all duration-300 scale-95 opacity-0" id="loading-card">
            <div class="relative mb-6">
                <div class="w-16 h-16 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-8 h-8 bg-blue-600 rounded-full animate-pulse"></div>
                </div>
            </div>
            <div class="text-center w-full">
                <h3 class="text-xl font-bold text-gray-900 mb-2" id="loading-title">Processing...</h3>
                <p class="text-sm text-gray-600 mb-2" id="loading-message">Please wait while we process your request</p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <p class="text-xs text-blue-800 font-medium">
                        💡 <strong>Notice:</strong> First generation may be slower as templates are being optimized.
                        Subsequent generations will be instant!
                    </p>
                </div>
                <div class="space-y-2" id="progress-steps">
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span id="current-step">Initializing...</span>
                        <span id="step-counter">1/5</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-500 ease-out" id="progress-bar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(loadingOverlay);
}

function showLoading(title, message, progressSteps = []) {
    createLoadingOverlay();
    
    // Update content
    document.getElementById('loading-title').textContent = title;
    document.getElementById('loading-message').textContent = message;
    
    // Show overlay
    loadingOverlay.classList.remove('hidden');
    
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
