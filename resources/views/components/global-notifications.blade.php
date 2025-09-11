<script>
(function() {
    'use strict';
    
    // Notification configuration
    const NOTIFICATION_CONFIG = {
        position: 'fixed top-20 right-4 z-[60]',
        baseClasses: 'px-4 py-3 rounded-lg shadow-xl backdrop-blur border transform transition-all duration-500 ease-out',
        success: {
            bg: 'bg-green-600',
            border: 'border-green-500/20',
            icon: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />'
        },
        error: {
            bg: 'bg-red-600',
            border: 'border-red-500/20',
            icon: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />'
        },
        warning: {
            bg: 'bg-yellow-600',
            border: 'border-yellow-500/20',
            icon: '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />'
        },
        info: {
            bg: 'bg-blue-600',
            border: 'border-blue-500/20',
            icon: '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />'
        }
    };
    
    // Create manager once
    if (!window.notificationManager) {
        let activeNotifications = [];
        let notificationCounter = 0;
        
        function createNotification(type, message, duration = 5000) {
            const id = `notification-${++notificationCounter}`;
            const config = NOTIFICATION_CONFIG[type] || NOTIFICATION_CONFIG.info;
            const notification = document.createElement('div');
            notification.id = id;
            notification.className = `${NOTIFICATION_CONFIG.position} ${NOTIFICATION_CONFIG.baseClasses} ${config.bg} ${config.border} text-white opacity-0 translate-x-full`;
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">${config.icon}</svg>
                    <span class="text-sm font-medium">${message}</span>
                    <button onclick="window.notificationManager.dismiss('${id}')" class="ml-2 text-white/80 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>`;
            document.body.appendChild(notification);
            activeNotifications.push({ id, element: notification, timer: null });
            const delay = activeNotifications.length * 100;
            setTimeout(() => {
                notification.classList.remove('opacity-0', 'translate-x-full');
                notification.classList.add('opacity-100', 'translate-x-0');
            }, delay);
            if (duration > 0) {
                const timer = setTimeout(() => dismissNotification(id), duration);
                const found = activeNotifications.find(n => n.id === id);
                if (found) found.timer = timer;
            }
            return id;
        }
        
        function dismissNotification(id) {
            const found = activeNotifications.find(n => n.id === id);
            if (!found) return;
            const { element, timer } = found;
            if (timer) clearTimeout(timer);
            element.classList.add('opacity-0', 'translate-x-full');
            setTimeout(() => {
                if (document.body.contains(element)) document.body.removeChild(element);
                activeNotifications = activeNotifications.filter(n => n.id !== id);
                repositionNotifications();
            }, 500);
        }
        
        function repositionNotifications() {
            activeNotifications.forEach((n, idx) => {
                n.element.style.top = `${20 + (idx * 80)}px`;
            });
        }
        
        window.notificationManager = {
            success: (m, d) => createNotification('success', m, d),
            error: (m, d) => createNotification('error', m, d),
            warning: (m, d) => createNotification('warning', m, d),
            info: (m, d) => createNotification('info', m, d),
            dismiss: dismissNotification,
            dismissAll: () => activeNotifications.slice().forEach(n => dismissNotification(n.id))
        };
    }
    
    // Process session notifications on every navigation
    function processSessionNotifications() {
        try {
            const existing = document.querySelectorAll('#success-notification, #error-notification');
            if (!existing || existing.length === 0) return;
            existing.forEach(el => {
                const isSuccess = el.id.includes('success');
                const msg = (el.textContent || '').trim();
                if (!msg) return;
                window.notificationManager[isSuccess ? 'success' : 'error'](msg);
                el.remove();
            });
        } catch (e) {
            // Silent fail for notification processing
        }
    }
    
    // Hook into lifecycle events (both classic and Turbo)
    document.addEventListener('DOMContentLoaded', processSessionNotifications);
    document.addEventListener('turbo:load', processSessionNotifications);
    // Fallback after small delay (in case content was injected late)
    setTimeout(processSessionNotifications, 300);
})();
</script>
