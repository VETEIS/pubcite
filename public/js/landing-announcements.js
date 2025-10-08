/**
 * Landing Page Announcements
 * Clean, maintainable JavaScript for displaying announcements on landing page
 */
class LandingAnnouncements {
    constructor() {
        this.apiUrl = '/admin/announcements';
        this.contentElement = document.getElementById('announcements-content');
        
        this.init();
    }

    /**
     * Initialize the announcements display
     */
    init() {
        // Re-find the content element in case it was replaced by Turbo
        this.contentElement = document.getElementById('announcements-content');
        if (!this.contentElement) {
            console.warn('Announcements content element not found');
            return;
        }
    }

    /**
     * Load and display announcements
     */
    async load() {
        this.showLoadingState();
        
        try {
            const announcements = await this.fetchAnnouncements();
            this.render(announcements);
        } catch (error) {
            this.showErrorState();
        }
    }

    /**
     * Fetch announcements from server
     */
    async fetchAnnouncements() {
        const timestamp = new Date().getTime();
        const response = await fetch(`${this.apiUrl}?t=${timestamp}`, {
            method: 'GET',
            cache: 'no-cache',
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        return data.announcements || [];
    }

    /**
     * Render announcements in the UI
     */
    render(announcements) {
        if (announcements.length === 0) {
            this.showEmptyState();
            return;
        }

        this.contentElement.innerHTML = announcements.map(announcement => 
            this.createAnnouncementHTML(announcement)
        ).join('');
    }

    /**
     * Create HTML for a single announcement
     */
    createAnnouncementHTML(announcement) {
        return `
            <div class="px-3 py-2 hover:bg-maroon-50">
                <div class="text-sm font-medium text-maroon-900">${this.escapeHtml(announcement.title || 'Untitled')}</div>
                <div class="text-xs text-gray-600 mt-1">${this.escapeHtml(announcement.description || 'No description')}</div>
                <div class="text-xs text-gray-500 mt-1">${this.formatTimeAgo(announcement.created_at)}</div>
            </div>
        `;
    }

    /**
     * Show loading state
     */
    showLoadingState() {
        this.contentElement.innerHTML = `
            <div class="px-3 py-4 text-center text-gray-500">
                <div class="animate-spin w-4 h-4 border-2 border-gray-300 border-t-gray-600 rounded-full mx-auto mb-2"></div>
                <div class="text-xs">Loading announcements...</div>
            </div>
        `;
    }

    /**
     * Show empty state
     */
    showEmptyState() {
        this.contentElement.innerHTML = `
            <div class="px-3 py-4 text-center text-gray-500">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <div class="text-xs">No announcements available</div>
            </div>
        `;
    }

    /**
     * Show error state
     */
    showErrorState() {
        this.contentElement.innerHTML = `
            <div class="px-3 py-4 text-center text-gray-500">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-xs">Unable to load announcements</div>
            </div>
        `;
    }

    /**
     * Format time ago
     */
    formatTimeAgo(dateString) {
        if (!dateString) return 'Recently';
        
        const date = new Date(dateString);
        const now = new Date();
        const diffInMinutes = Math.floor((now - date) / (1000 * 60));
        
        if (diffInMinutes < 1) return 'Just now';
        if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
        if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
        return date.toLocaleDateString();
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize when DOM is ready (both regular page loads and Turbo navigation)
function initializeLandingAnnouncements() {
    // Only initialize if we're on the landing page and container exists
    const container = document.getElementById('announcements-content');
    if (container) {
        if (!window.landingAnnouncements) {
            // Create new instance
            window.landingAnnouncements = new LandingAnnouncements();
        } else {
            // Re-initialize existing instance (for Turbo navigation)
            window.landingAnnouncements.init();
        }
    }
}

// Initialize on regular page load
document.addEventListener('DOMContentLoaded', initializeLandingAnnouncements);

// Initialize on Turbo navigation
document.addEventListener('turbo:load', initializeLandingAnnouncements);
