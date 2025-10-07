// Dynamic loading system with progress tracking
(function() {
    'use strict';
    
    let loadingOverlay = null;
    let progressInterval = null;
    
    function createLoadingOverlay(title = 'Processing...', message = 'Please wait...', progressSteps = []) {
        if (loadingOverlay) {
            loadingOverlay.remove();
            loadingOverlay = null;
        }
        
        loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'loading-overlay';
        loadingOverlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            font-family: Arial, sans-serif;
        `;
        
        const progressHTML = progressSteps.length > 0 ? `
            <div style="margin-top: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 12px; color: #666;">
                    <span id="current-step">Initializing...</span>
                    <span id="step-counter">1/${progressSteps.length}</span>
                </div>
                <div style="width: 100%; background: #f0f0f0; border-radius: 10px; height: 8px; overflow: hidden;">
                    <div id="progress-bar" style="
                        background: linear-gradient(90deg, #8B1538, #A91B47);
                        height: 100%;
                        width: 0%;
                        border-radius: 10px;
                        transition: width 0.5s ease;
                    "></div>
                </div>
            </div>
        ` : '';
        
        loadingOverlay.innerHTML = `
            <div style="
                background: white;
                padding: 30px;
                border-radius: 15px;
                text-align: center;
                box-shadow: 0 8px 32px rgba(0,0,0,0.3);
                max-width: 450px;
                width: 90%;
                transform: scale(0.95);
                opacity: 0;
                transition: all 0.3s ease;
            " id="loading-card">
                <div style="
                    width: 60px;
                    height: 60px;
                    border: 4px solid #f3f3f3;
                    border-top: 4px solid #8B1538;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                    margin: 0 auto 20px;
                "></div>
                <h3 style="margin: 0 0 10px; color: #333; font-size: 20px; font-weight: 600;" id="loading-title">${title}</h3>
                <p style="margin: 0; color: #666; font-size: 14px; line-height: 1.4;" id="loading-message">${message}</p>
                ${progressHTML}
            </div>
            <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        `;
        
        document.body.appendChild(loadingOverlay);
        
        // Animate in
        setTimeout(() => {
            const card = document.getElementById('loading-card');
            if (card) {
                card.style.transform = 'scale(1)';
                card.style.opacity = '1';
            }
        }, 10);
    }
    
    function showLoading(title = 'Processing...', message = 'Please wait...', progressSteps = [], useRealProgress = false) {
        console.log('showLoading called with:', title, message, progressSteps, useRealProgress);
        createLoadingOverlay(title, message, progressSteps);
        
        if (loadingOverlay) {
            loadingOverlay.style.display = 'flex';
        }
        
        if (useRealProgress && progressSteps.length > 0) {
            // Use real progress tracking via Server-Sent Events
            startRealProgressTracking();
        } else if (progressSteps.length > 0) {
            // Show honest loading without fake progress steps
            updateProgress(0, progressSteps);
            
            // Show a simple animated progress bar that doesn't pretend to track specific steps
            const progressBar = document.getElementById('progress-bar');
            if (progressBar) {
                // Animate progress bar to show activity without specific step tracking
                let progress = 0;
                progressInterval = setInterval(() => {
                    progress += Math.random() * 10; // Random increments to show activity
                    if (progress > 90) progress = 90; // Never reach 100% until actually complete
                    progressBar.style.width = `${progress}%`;
                }, 200);
            }
        }
    }
    
    function startRealProgressTracking() {
        const currentStepEl = document.getElementById('current-step');
        const stepCounter = document.getElementById('step-counter');
        const progressBar = document.getElementById('progress-bar');
        
        if (!currentStepEl || !stepCounter || !progressBar) {
            console.error('Progress elements not found');
            return;
        }
        
        // Initialize progress
        currentStepEl.textContent = 'Starting submission...';
        stepCounter.textContent = '0/6';
        progressBar.style.width = '0%';
        
        // Connect to Server-Sent Events
        const eventSource = new EventSource('/progress/stream');
        let currentStep = 0;
        const totalSteps = 6;
        
        eventSource.onmessage = function(event) {
            try {
                const data = JSON.parse(event.data);
                console.log('Progress update:', data);
                
                if (data.type === 'progress') {
                    currentStep++;
                    const progress = (currentStep / totalSteps) * 100;
                    
                    // Update progress display
                    currentStepEl.textContent = data.message;
                    stepCounter.textContent = `${currentStep}/${totalSteps}`;
                    progressBar.style.width = `${progress}%`;
                } else if (data.type === 'complete') {
                    // Mark as complete
                    currentStep = totalSteps;
                    currentStepEl.textContent = 'Request submitted successfully!';
                    stepCounter.textContent = `${totalSteps}/${totalSteps}`;
                    progressBar.style.width = '100%';
                    
                    // Close the connection
                    eventSource.close();
                } else if (data.type === 'timeout') {
                    currentStepEl.textContent = 'Processing completed';
                    eventSource.close();
                }
            } catch (e) {
                console.error('Error parsing progress data:', e);
            }
        };
        
        eventSource.onerror = function(event) {
            console.error('SSE connection error:', event);
            currentStepEl.textContent = 'Processing...';
            eventSource.close();
        };
        
        // Store event source for cleanup
        window.progressEventSource = eventSource;
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
        if (progressInterval) {
            clearInterval(progressInterval);
            progressInterval = null;
        }
        
        // Close SSE connection if it exists
        if (window.progressEventSource) {
            window.progressEventSource.close();
            window.progressEventSource = null;
        }
        
        if (loadingOverlay) {
            loadingOverlay.remove();
            loadingOverlay = null;
        }
    }
    
    // Make functions globally available
    window.showLoading = showLoading;
    window.hideLoading = hideLoading;
    window.updateProgress = updateProgress;
    
    // Auto-hide on page navigation
    document.addEventListener('turbo:before-cache', hideLoading);
    document.addEventListener('turbo:load', hideLoading);
    
})();