/**
 * Loading Manager - Unified loading state management
 * Prevents double submissions and provides consistent loading UI
 */
class LoadingManager {
    constructor() {
        this.activeLoaders = new Set();
        this.loadingOverlay = null;
        this.createLoadingOverlay();
    }

    /**
     * Create the main loading overlay
     */
    createLoadingOverlay() {
        if (this.loadingOverlay) return;

        this.loadingOverlay = document.createElement('div');
        this.loadingOverlay.id = 'global-loading-overlay';
        this.loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden';
        this.loadingOverlay.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl px-8 py-6 flex flex-col items-center max-w-sm mx-4">
                <div class="relative">
                    <div class="w-12 h-12 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <h3 class="text-lg font-semibold text-gray-900" id="loading-title">Processing...</h3>
                    <p class="text-sm text-gray-600 mt-1" id="loading-message">Please wait while we process your request</p>
                </div>
            </div>
        `;
        document.body.appendChild(this.loadingOverlay);
    }

    /**
     * Show loading state for a specific operation
     * @param {string} operationId - Unique identifier for the operation
     * @param {Object} options - Loading options
     */
    show(operationId, options = {}) {
        const {
            title = 'Processing...',
            message = 'Please wait while we process your request',
            showOverlay = true,
            disableButtons = true,
            buttonSelector = null
        } = options;

        // Prevent duplicate loaders
        if (this.activeLoaders.has(operationId)) {
            console.warn(`Loading operation '${operationId}' is already active`);
            return;
        }

        this.activeLoaders.add(operationId);

        // Show overlay if requested
        if (showOverlay) {
            this.loadingOverlay.querySelector('#loading-title').textContent = title;
            this.loadingOverlay.querySelector('#loading-message').textContent = message;
            this.loadingOverlay.classList.remove('hidden');
        }

        // Disable buttons if requested
        if (disableButtons) {
            this.disableButtons(buttonSelector);
        }

        // Prevent page interactions
        document.body.style.pointerEvents = 'none';
        document.body.style.userSelect = 'none';
    }

    /**
     * Hide loading state for a specific operation
     * @param {string} operationId - Unique identifier for the operation
     */
    hide(operationId) {
        if (!this.activeLoaders.has(operationId)) {
            console.warn(`Loading operation '${operationId}' was not active`);
            return;
        }

        this.activeLoaders.delete(operationId);

        // Hide overlay if no other operations are active
        if (this.activeLoaders.size === 0) {
            this.loadingOverlay.classList.add('hidden');
            document.body.style.pointerEvents = 'auto';
            document.body.style.userSelect = 'auto';
        }

        // Re-enable buttons
        this.enableButtons();
    }

    /**
     * Disable buttons to prevent double submission
     * @param {string} selector - CSS selector for buttons to disable
     */
    disableButtons(selector = null) {
        const buttons = selector ? 
            document.querySelectorAll(selector) : 
            document.querySelectorAll('button[type="submit"], button[onclick*="generateDocx"], button[onclick*="submit"]');

        buttons.forEach(button => {
            if (!button.disabled) {
                button.dataset.originalDisabled = button.disabled;
                button.dataset.originalText = button.textContent;
                button.disabled = true;
                button.classList.add('opacity-50', 'cursor-not-allowed');
                
                // Add loading text if it's a submit button
                if (button.type === 'submit' || button.textContent.includes('Submit')) {
                    button.textContent = 'Submitting...';
                } else if (button.textContent.includes('Generate')) {
                    button.textContent = 'Generating...';
                }
            }
        });
    }

    /**
     * Re-enable all disabled buttons
     */
    enableButtons() {
        const buttons = document.querySelectorAll('button[data-original-disabled]');
        buttons.forEach(button => {
            button.disabled = button.dataset.originalDisabled === 'true';
            button.textContent = button.dataset.originalText || button.textContent;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
            delete button.dataset.originalDisabled;
            delete button.dataset.originalText;
        });
    }

    /**
     * Check if any loading operations are active
     * @returns {boolean}
     */
    isLoading() {
        return this.activeLoaders.size > 0;
    }

    /**
     * Get list of active operations
     * @returns {Array}
     */
    getActiveOperations() {
        return Array.from(this.activeLoaders);
    }
}

// Create global instance
window.loadingManager = new LoadingManager();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LoadingManager;
}
