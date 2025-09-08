{{-- Privacy Enforcer Component --}}
{{-- Include this component in all webapp pages to enforce privacy agreement --}}

<script>
// Privacy enforcement for webapp pages
document.addEventListener('DOMContentLoaded', function() {
    // Skip check for welcome page itself
    if (window.location.pathname === '/' || window.location.pathname === '/welcome') {
        return;
    }
    
    // Check if privacy was accepted (consistent with welcome page logic)
    const privacyAccepted = localStorage.getItem('privacyAccepted') === 'true';
    
    if (!privacyAccepted) {
        @if(config('app.debug'))
            console.log('Privacy not accepted, redirecting to welcome page...');
        @endif
        
        // Show user-friendly message
        if (typeof showNotification === 'function') {
            showNotification('You must accept the Data Privacy Statement to access this page. Redirecting...', 'warning');
        } else {
            // Fallback alert
            alert('You must accept the Data Privacy Statement to access this page. Redirecting to welcome page...');
        }
        
        // Redirect to welcome page after a short delay
        setTimeout(function() {
            window.location.href = '{{ route("welcome") }}';
        }, 1500);
    }
});

// Also check on page visibility change (in case user switches tabs)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        const privacyAccepted = localStorage.getItem('privacyAccepted') === 'true';
        if (!privacyAccepted && window.location.pathname !== '/' && window.location.pathname !== '/welcome') {
            window.location.href = '{{ route("welcome") }}';
        }
    }
});
</script>
