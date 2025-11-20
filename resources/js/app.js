import './bootstrap';
import '@hotwired/turbo';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

// Register Alpine plugins before Livewire initializes
Alpine.plugin(focus);
window.Alpine = Alpine;

// Configure Turbo to ignore Livewire requests
document.addEventListener('turbo:before-fetch-request', (event) => {
    // Exclude Livewire requests from Turbo
    if (event.detail.fetchOptions && event.detail.fetchOptions.headers) {
        const headers = event.detail.fetchOptions.headers;
        if (headers['X-Livewire'] || headers['x-livewire']) {
            event.preventDefault();
            return false;
        }
    }
});

// Turbo error handling for network issues
document.addEventListener('turbo:before-fetch-request', (event) => {
    // Add error handling for prefetch requests
    if (event.detail.fetchOptions && event.detail.fetchOptions.method === 'GET') {
        event.detail.fetchOptions.signal = AbortSignal.timeout(10000); // 10 second timeout
    }
});

document.addEventListener('turbo:fetch-request-error', (event) => {
    // Silently handle prefetch errors to prevent console spam
    if (event.detail.fetchOptions && event.detail.fetchOptions.method === 'GET') {
        event.preventDefault(); // Prevent error from bubbling up
    }
});

// Turbo form submission handling
document.addEventListener('turbo:submit-start', (event) => {
    const form = event.target;
    
    // Check if this is a form submission that should show loading
    if (form.matches('form[method="POST"]')) {
        const action = form.action;
        
        // Only show loading if not already shown by custom handlers
        const loadingOverlay = document.getElementById('loading-overlay');
        const isAlreadyLoading = loadingOverlay && !loadingOverlay.classList.contains('hidden');
        
        // Show loading for specific form submissions (only if not already shown)
        if (!isAlreadyLoading && (action.includes('submit') || action.includes('nudge') || action.includes('logout'))) {
            if (window.showLoading) {
                let title = 'Processing...';
                let message = 'Please wait while we process your request';
                let steps = ['Processing...'];
                
                if (action.includes('submit')) {
                    title = 'Submitting Request...';
                    message = 'Please wait while we process your submission';
                    steps = [
                        'Processing your request...'
                    ];
                } else if (action.includes('nudge')) {
                    title = 'Sending Nudge...';
                    message = 'Notifying admin about your request';
                    steps = ['Processing...'];
                } else if (action.includes('logout')) {
                    title = 'Logging Out...';
                    message = 'Please wait while we log you out';
                    steps = ['Processing...'];
                }
                
                window.showLoading(title, message, steps);
            }
        }
    }
});

document.addEventListener('turbo:submit-end', (event) => {
    // Hide loading screen when form submission completes
    if (window.hideLoading) {
        window.hideLoading();
    }
});

// Handle Turbo navigation to show success notifications
document.addEventListener('turbo:load', (event) => {
    // Check for success/error notifications in the page
    const successNotification = document.getElementById('success-notification');
    const errorNotification = document.getElementById('error-notification');
    
    if (successNotification && successNotification.textContent.trim()) {
        // Show success notification using global notification system
        if (window.notificationManager) {
            window.notificationManager.success(successNotification.textContent.trim());
        }
        // Clear the hidden notification
        successNotification.textContent = '';
    }
    
    if (errorNotification && errorNotification.textContent.trim()) {
        // Show error notification using global notification system
        if (window.notificationManager) {
            window.notificationManager.error(errorNotification.textContent.trim());
        }
        // Clear the hidden notification
        errorNotification.textContent = '';
    }
});

// Global Alpine store for admin modal management
Alpine.store('adminModal', {
    reviewOpen: false,
    statusOpen: false,
    data: {},
    
    init() {
        // Ensure the store is properly initialized
        this.reviewOpen = false;
        this.statusOpen = false;
        this.data = {};
    },
    
    openReview(data) {
        try {
            this.data = { ...data };
            this.reviewOpen = true;
            this.statusOpen = false; // Close other modal
            document.body.classList.add('overflow-hidden');
        } catch (error) {
            // Error opening review modal
        }
    },
    
    closeReview() {
        try {
            this.reviewOpen = false;
            document.body.classList.remove('overflow-hidden');
        } catch (error) {
            // Error closing review modal
        }
    },
    
    openStatus(data) {
        try {
            this.data = { ...data };
            this.statusOpen = true;
            this.reviewOpen = false; // Close other modal
            document.body.classList.add('overflow-hidden');
        } catch (error) {
            // Error opening status modal
        }
    },
    
    closeStatus() {
        try {
            this.statusOpen = false;
            document.body.classList.remove('overflow-hidden');
        } catch (error) {
            // Error closing status modal
        }
    }
});

// Let Livewire handle Alpine initialization
// Alpine.start() is called by Livewire automatically on DOMContentLoaded
// Do NOT call Alpine.start() manually here to avoid conflicts

// Ensure Focus plugin is registered with Livewire's Alpine instance
// Livewire may use its own Alpine instance, so we register the plugin here too
document.addEventListener('livewire:init', () => {
    console.log('Livewire initialized');
    if (window.Alpine && typeof window.Alpine.plugin === 'function') {
        // Register Focus plugin with Livewire's Alpine instance
        window.Alpine.plugin(focus);
    }
});

// Track Livewire component initialization
document.addEventListener('livewire:initialized', () => {
    console.log('Livewire components initialized');
});

// Track component updates
document.addEventListener('livewire:update', () => {
    console.log('Livewire component updated');
});

// Track component errors
document.addEventListener('livewire:error', (event) => {
    console.error('Livewire error:', event.detail);
});
