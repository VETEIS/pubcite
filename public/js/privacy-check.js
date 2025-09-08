/**
 * Global Privacy Check for PubCite
 * This script ensures users have accepted the data privacy statement
 * before accessing any webapp pages.
 */

// Check if privacy was accepted (consistent with other components)
function checkPrivacyAccepted() {
    return localStorage.getItem('privacyAccepted') === 'true';
}

// Redirect to welcome page if privacy not accepted
function enforcePrivacyAgreement() {
    // Skip check for welcome page itself
    if (window.location.pathname === '/' || window.location.pathname === '/welcome') {
        return;
    }
    
    if (!checkPrivacyAccepted()) {
        // Show notification
        if (typeof showNotification === 'function') {
            showNotification('You must accept the Data Privacy Statement to access this page.', 'warning');
        } else {
            alert('You must accept the Data Privacy Statement to access this page. Redirecting to welcome page...');
        }
        
        // Redirect to welcome page
        window.location.href = '/';
    }
}

// Run privacy check when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    enforcePrivacyAgreement();
});

// Also check on page visibility change (in case user switches tabs)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        enforcePrivacyAgreement();
    }
});

// Export functions for global use
window.checkPrivacyAccepted = checkPrivacyAccepted;
window.enforcePrivacyAgreement = enforcePrivacyAgreement;
