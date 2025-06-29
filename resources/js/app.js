import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;

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
            console.log('Review modal opened:', this.reviewOpen, this.data);
        } catch (error) {
            console.error('Error opening review modal:', error);
        }
    },
    
    closeReview() {
        try {
            this.reviewOpen = false;
            document.body.classList.remove('overflow-hidden');
            console.log('Review modal closed');
        } catch (error) {
            console.error('Error closing review modal:', error);
        }
    },
    
    openStatus(data) {
        try {
            this.data = { ...data };
            this.statusOpen = true;
            this.reviewOpen = false; // Close other modal
            document.body.classList.add('overflow-hidden');
            console.log('Status modal opened:', this.statusOpen, this.data);
        } catch (error) {
            console.error('Error opening status modal:', error);
        }
    },
    
    closeStatus() {
        try {
            this.statusOpen = false;
            document.body.classList.remove('overflow-hidden');
            console.log('Status modal closed');
        } catch (error) {
            console.error('Error closing status modal:', error);
        }
    }
});

// Initialize Alpine
Alpine.start();

// Debug: Check if Alpine and store are working
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, Alpine available:', !!window.Alpine);
    console.log('Store available:', !!Alpine.store('adminModal'));
    console.log('Store state:', Alpine.store('adminModal'));
});
