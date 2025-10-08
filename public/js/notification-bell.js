// Consolidated Notification Bell System
// Prevents multiple instances and fixes Alpine.js errors

(function() {
    'use strict';
    
    // Only create once globally
    if (window.notificationBell) {
        return;
    }
    
    window.notificationBell = function() {
        return {
            showDropdown: false,
            notifications: [],
            unreadCount: 0,
            loading: false,
            
            init() {
                this.loadNotifications();
                // Poll for new notifications every 10 seconds
                setInterval(() => this.checkForNewNotifications(), 10000);
            },
            
            toggleNotifications() {
                this.showDropdown = !this.showDropdown;
                if (this.showDropdown) {
                    this.loadNotifications();
                }
            },
            
            async loadNotifications() {
                this.loading = true;
                try {
                    const response = await fetch('/admin/notifications');
                    const data = await response.json();
                    this.notifications = Array.isArray(data.items) ? data.items : [];
                    this.unreadCount = data.unread || 0;
                } catch (error) {
                    // Silent fail for notifications
                    console.warn('Failed to load notifications:', error);
                    this.notifications = [];
                    this.unreadCount = 0;
                } finally {
                    this.loading = false;
                }
            },

            async checkForNewNotifications() {
                try {
                    const response = await fetch('/admin/notifications');
                    const data = await response.json();
                    const newUnreadCount = data.unread || 0;
                    
                    if (newUnreadCount !== this.unreadCount) {
                        this.unreadCount = newUnreadCount;
                        this.notifications = data.items || [];
                    }
                } catch (error) {
                    // Silent fail for notification check
                }
            },

            async markAsRead(notificationId) {
                try {
                    await fetch(`/admin/notifications/${notificationId}/mark-read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    // Update local state
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification) {
                        notification.read_at = new Date().toISOString();
                    }
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                } catch (error) {
                    // Silent fail for mark as read
                    console.warn('Failed to mark notification as read:', error);
                }
            },

            formatTime(timestamp) {
                if (!timestamp) return '';
                
                const date = new Date(timestamp);
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMs / 3600000);
                const diffDays = Math.floor(diffMs / 86400000);
                
                if (diffMins < 1) return 'Just now';
                if (diffMins < 60) return `${diffMins}m ago`;
                if (diffHours < 24) return `${diffHours}h ago`;
                if (diffDays < 7) return `${diffDays}d ago`;
                
                return date.toLocaleDateString();
            }
        };
    };
    
    // Initialize on DOM ready and handle Turbo navigation
    function initializeNotificationBell() {
        // Ensure notification bell is available
        if (!window.notificationBell) {
            console.warn('Notification bell not properly initialized');
        }
    }
    
    document.addEventListener('DOMContentLoaded', initializeNotificationBell);
    
    // Handle Turbo navigation
    if (window.Turbo) {
        document.addEventListener('turbo:load', initializeNotificationBell);
        document.addEventListener('turbo:render', initializeNotificationBell);
    }
    
})();
