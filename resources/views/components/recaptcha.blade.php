@php
    $recaptchaService = app(\App\Services\RecaptchaService::class);
    $shouldDisplay = $recaptchaService->shouldDisplay();
    $siteKey = $recaptchaService->getSiteKey();
    $enabled = config('recaptcha.enabled', false);
    $skipInLocal = config('recaptcha.skip_in_local', true);
    $env = app()->environment();
    // Use a stable ID based on site key to enable caching
    $recaptchaId = 'recaptcha-widget-' . md5($siteKey);
@endphp

@if($shouldDisplay && !empty($siteKey))
    <!-- Hidden input to indicate widget was rendered -->
    <input type="hidden" name="recaptcha_widget_rendered" value="0" id="recaptcha-widget-rendered">
    
    <!-- Define callback BEFORE reCAPTCHA script loads to ensure it's available -->
    <script>
        (function() {
            'use strict';
            
            // Production-ready auto-submit callback
            // Prevents double submission and handles all edge cases
            let isSubmitting = false;
            const DEBUG = {{ config('app.debug') ? 'true' : 'false' }};
            
            function log(message, data) {
                if (DEBUG) {
                    console.log('[reCAPTCHA] ' + message, data || '');
                }
            }
            
            function logError(message, error) {
                if (DEBUG) {
                    console.error('[reCAPTCHA] ' + message, error || '');
                }
            }
            
            // Define callback in global scope IMMEDIATELY (before reCAPTCHA loads)
            window.recaptchaAutoSubmitCallback = function(token) {
                // Prevent double submission
                if (isSubmitting) {
                    log('Submission already in progress, ignoring callback');
                    return;
                }
                
                if (!token) {
                    logError('Callback triggered without token');
                    return;
                }
                
                log('Callback triggered with token');
                
                // Mark widget as rendered when user completes it
                const renderedInput = document.getElementById('recaptcha-widget-rendered');
                if (renderedInput) {
                    renderedInput.value = '1';
                }
                
                // Find login form
                let form = document.getElementById('login-form');
                if (!form) {
                    const recaptchaContainer = document.getElementById('recaptcha-container');
                    if (recaptchaContainer) {
                        form = recaptchaContainer.closest('form');
                    }
                }
                
                if (!form || form.id !== 'login-form') {
                    logError('Login form not found');
                    return;
                }
                
                // Validate form fields
                const email = form.querySelector('input[name="email"]');
                const password = form.querySelector('input[name="password"]');
                
                if (!email || !password) {
                    logError('Email or password field not found');
                    return;
                }
                
                const emailValue = email.value.trim();
                const passwordValue = password.value.trim();
                
                if (!emailValue || !passwordValue) {
                    log('Email or password is empty, skipping auto-submit');
                    return;
                }
                
                // Mark as submitting to prevent double submission
                isSubmitting = true;
                
                // Show loading indicator
                const recaptchaContainer = document.getElementById('recaptcha-container');
                if (recaptchaContainer) {
                    recaptchaContainer.style.opacity = '0.6';
                    recaptchaContainer.style.pointerEvents = 'none';
                    recaptchaContainer.setAttribute('aria-busy', 'true');
                }
                
                // Disable form to prevent manual submission
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                }
                
                // Wait for reCAPTCHA to add the response field to the form
                setTimeout(function() {
                    try {
                        const formData = new FormData(form);
                        
                        // Ensure the reCAPTCHA token is included
                        const recaptchaResponse = form.querySelector('textarea[name="g-recaptcha-response"]');
                        if (recaptchaResponse && recaptchaResponse.value) {
                            formData.set('g-recaptcha-response', recaptchaResponse.value);
                        } else if (token) {
                            formData.set('g-recaptcha-response', token);
                        } else {
                            logError('No reCAPTCHA token available');
                            resetFormState();
                            return;
                        }
                        
                        // Get CSRF token
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                         form.querySelector('input[name="_token"]')?.value;
                        
                        if (!csrfToken) {
                            logError('CSRF token not found');
                            resetFormState();
                            // Fallback to regular form submission
                            form.submit();
                            return;
                        }
                        
                        log('Submitting form via fetch API');
                        
                        // Set timeout for fetch request (30 seconds)
                        const controller = new AbortController();
                        const timeoutId = setTimeout(function() {
                            controller.abort();
                        }, 30000);
                        
                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'text/html, application/xhtml+xml'
                            },
                            credentials: 'same-origin',
                            redirect: 'manual',
                            signal: controller.signal
                        })
                        .then(function(response) {
                            clearTimeout(timeoutId);
                            log('Response received', { status: response.status, type: response.type, url: response.url });
                            
                            // Handle explicit redirects (302, 303, 307, 308) - Laravel sends these with Location header
                            if (response.status >= 300 && response.status < 400) {
                                const locationHeader = response.headers.get('Location');
                                
                                if (locationHeader) {
                                    // Use the Location header from Laravel (it knows the correct dashboard)
                                    let redirectUrl = locationHeader;
                                    if (locationHeader.startsWith('/')) {
                                        redirectUrl = window.location.origin + locationHeader;
                                    }
                                    log('Following redirect from Location header', redirectUrl);
                                    window.location.href = redirectUrl;
                                    return;
                                } else if (response.url && response.url !== form.action && !response.url.includes('/login')) {
                                    // Fallback to response URL if Location header not available and it's not the login page
                                    log('Using response URL as redirect', response.url);
                                    window.location.href = response.url;
                                    return;
                                } else {
                                    // No redirect info - navigate to generic dashboard and let server handle role-based redirect
                                    log('No redirect info, navigating to dashboard (server will handle role-based redirect)');
                                    window.location.href = '/dashboard';
                                    return;
                                }
                            }
                            
                            // Handle status 0 (browser handled redirect or network issue)
                            // When status is 0, login was likely successful - try to navigate to dashboard
                            if (response.status === 0 || response.type === 'opaqueredirect') {
                                log('Status 0 detected, attempting to navigate to dashboard');
                                // Navigate to generic dashboard and let server handle role-based redirect
                                window.location.href = '/dashboard';
                                return;
                            }
                            
                            // Handle successful responses (200 OK) - might be error page or success
                            if (response.ok) {
                                return response.text().then(function(html) {
                                    // Check for error messages in response
                                    const hasErrors = html.includes('validation-errors') || 
                                                     html.includes('error') ||
                                                     html.includes('The provided credentials') ||
                                                     html.includes('These credentials do not match') ||
                                                     html.includes('No account found') ||
                                                     html.includes('reCAPTCHA verification failed') ||
                                                     html.includes('Sign in to your account');
                                    
                                    if (hasErrors) {
                                        // Error detected - reload to show error messages
                                        log('Error detected in response, reloading page');
                                        window.location.reload();
                                    } else {
                                        // Success - login was successful, navigate to dashboard
                                        log('Success response detected, navigating to dashboard');
                                        // Navigate to generic dashboard and let server handle role-based redirect
                                        window.location.href = '/dashboard';
                                    }
                                }).catch(function(error) {
                                    logError('Error parsing response', error);
                                    // On error parsing, assume success and navigate to dashboard
                                    window.location.href = '/dashboard';
                                });
                            } else {
                                // Non-200 status - might be error, but try dashboard anyway
                                log('Non-200 status received', response.status);
                                // Navigate to generic dashboard - if login failed, Laravel will redirect back to login
                                window.location.href = '/dashboard';
                            }
                        })
                        .catch(function(error) {
                            clearTimeout(timeoutId);
                            
                            if (error.name === 'AbortError') {
                                logError('Request timeout');
                            } else {
                                logError('Fetch error', error);
                            }
                            
                            resetFormState();
                            
                            // Fallback to regular form submission
                            log('Falling back to regular form submission');
                            form.submit();
                        });
                    } catch (error) {
                        logError('Exception during form submission', error);
                        resetFormState();
                        form.submit();
                    }
                }, 300);
                
                function resetFormState() {
                    isSubmitting = false;
                    if (recaptchaContainer) {
                        recaptchaContainer.style.opacity = '1';
                        recaptchaContainer.style.pointerEvents = 'auto';
                        recaptchaContainer.removeAttribute('aria-busy');
                    }
                    if (submitButton) {
                        submitButton.disabled = false;
                    }
                }
            };
            
            log('Callback function defined in global scope');
        })();
    </script>
    
    <div class="flex items-center justify-center w-full" id="recaptcha-container" style="min-height: 78px; display: flex; align-items: center; justify-content: center; margin: 0 auto; padding: 0; box-sizing: border-box; overflow: hidden; position: relative;">
        <!-- Loading State -->
        <div id="recaptcha-loading" class="absolute inset-0 flex items-center justify-center" style="min-height: 78px; z-index: 10;">
            <span class="text-base text-white font-bold">Loading reCAPTCHA Sign in...</span>
        </div>
        <!-- reCAPTCHA Widget -->
        <div class="g-recaptcha" data-sitekey="{{ $siteKey }}" data-callback="recaptchaAutoSubmitCallback" id="{{ $recaptchaId }}" style="transform: scale(1.0); transform-origin: center center; width: 304px !important; height: 78px !important; margin: 0 !important; padding: 0 !important; opacity: 0; transition: opacity 0.3s ease-in-out;"></div>
    </div>
    
    <style>
        #recaptcha-container {
            width: 100% !important;
        }
        #recaptcha-container .g-recaptcha,
        #recaptcha-container .g-recaptcha > div,
        #recaptcha-container .g-recaptcha > div > div,
        #recaptcha-container iframe {
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            box-sizing: border-box !important;
        }
        #recaptcha-container .g-recaptcha {
            width: 304px !important;
            height: 78px !important;
            transform: scale(1.0) !important;
            transform-origin: center center !important;
        }
    </style>
    
    <script>
        (function() {
            const RECAPTCHA_ID = '{{ $recaptchaId }}';
            const SITE_KEY = '{{ $siteKey }}';
            const CACHE_KEY = 'recaptcha_rendered_' + RECAPTCHA_ID;
            
            // Callback is already defined in global scope above (before this script runs)
            // Just verify it exists (only log in debug mode)
            const DEBUG = {{ config('app.debug') ? 'true' : 'false' }};
            if (typeof window.recaptchaAutoSubmitCallback !== 'function') {
                if (DEBUG) {
                    console.error('[reCAPTCHA] ERROR: Callback function not found in global scope!');
                }
            } else if (DEBUG) {
                console.log('[reCAPTCHA] Callback function verified in global scope');
            }
            
            // Load reCAPTCHA script if not already loaded
            if (!document.querySelector('script[src*="recaptcha/api.js"]')) {
                const script = document.createElement('script');
                script.src = 'https://www.google.com/recaptcha/api.js';
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
            }
            
            // Hide loading state and show reCAPTCHA
            function hideLoadingState() {
                const loadingElement = document.getElementById('recaptcha-loading');
                const container = document.getElementById(RECAPTCHA_ID);
                if (loadingElement) {
                    loadingElement.style.display = 'none';
                }
                if (container) {
                    container.style.opacity = '1';
                }
            }
            
            // Show loading state
            function showLoadingState() {
                const loadingElement = document.getElementById('recaptcha-loading');
                const container = document.getElementById(RECAPTCHA_ID);
                if (loadingElement) {
                    loadingElement.style.display = 'flex';
                }
                if (container) {
                    container.style.opacity = '0';
                }
            }
            
            // Check if reCAPTCHA widget is already rendered and valid
            function isRecaptchaRendered() {
                const container = document.getElementById(RECAPTCHA_ID);
                if (!container) return false;
                
                // Check for iframe (actual reCAPTCHA widget)
                const iframe = container.querySelector('iframe');
                if (!iframe) return false;
                
                // Check if widget ID exists in grecaptcha (if available)
                if (typeof grecaptcha !== 'undefined' && grecaptcha.getResponse) {
                    try {
                        // Try to get response - if it throws or returns empty, widget might be invalid
                        const response = grecaptcha.getResponse(RECAPTCHA_ID);
                        // Widget exists if we can call getResponse without error
                        return true;
                    } catch (e) {
                        // Widget ID doesn't exist in grecaptcha registry
                        return false;
                    }
                }
                
                // If grecaptcha not loaded yet, but iframe exists, assume it's rendered
                return true;
            }
            
            // Initialize reCAPTCHA
            function initRecaptcha() {
                const recaptchaContainer = document.getElementById('recaptcha-container');
                const container = recaptchaContainer ? recaptchaContainer.querySelector('.g-recaptcha') : null;
                if (!container) return;
                
                // Set the ID if not already set
                if (!container.id) {
                    container.id = RECAPTCHA_ID;
                }
                
                // Check if already rendered - use multiple checks for reliability
                if (isRecaptchaRendered()) {
                    // Ensure hidden input exists
                    ensureHiddenInput();
                    // Hide loading state since it's already rendered
                    hideLoadingState();
                    return; // Already rendered, skip
                }
                
                // Check sessionStorage cache
                const cached = sessionStorage.getItem(CACHE_KEY);
                if (cached === 'true' && container.querySelector('iframe')) {
                    // Cached and iframe exists, just ensure hidden input
                    ensureHiddenInput();
                    // Hide loading state
                    hideLoadingState();
                    return;
                }
                
                // Show loading state while waiting for reCAPTCHA to load
                showLoadingState();
                
                // Wait for grecaptcha to be available
                if (typeof grecaptcha === 'undefined') {
                    // Check again after a delay
                    setTimeout(initRecaptcha, 100);
                    return;
                }
                
                // Render reCAPTCHA
                grecaptcha.ready(function() {
                    // Double-check before rendering
                    if (isRecaptchaRendered()) {
                        ensureHiddenInput();
                        hideLoadingState();
                        return;
                    }
                    
                    // Check if container still exists and doesn't have iframe
                    if (!container || container.querySelector('iframe')) {
                        hideLoadingState();
                        return;
                    }
                    
                    try {
                        // Verify callback is available (only log in debug mode)
                        if (typeof window.recaptchaAutoSubmitCallback !== 'function') {
                            if (DEBUG) {
                                console.error('[reCAPTCHA] ERROR: Callback function not found!');
                            }
                        } else if (DEBUG) {
                            console.log('[reCAPTCHA] Rendering widget with callback function available');
                        }
                        
                        grecaptcha.render(container, {
                            'sitekey': SITE_KEY,
                            'theme': 'light',
                            'callback': window.recaptchaAutoSubmitCallback,
                            'expired-callback': function() {
                                // Reset the rendered input if reCAPTCHA expires
                                const renderedInput = document.getElementById('recaptcha-widget-rendered');
                                if (renderedInput) {
                                    renderedInput.value = '0';
                                }
                                // Don't clear cache on expire, widget is still rendered
                            }
                        });
                        // Cache that we've rendered it
                        sessionStorage.setItem(CACHE_KEY, 'true');
                        
                        // Wait for the iframe to appear and be fully loaded, then hide loading
                        const checkInterval = setInterval(function() {
                            const iframe = container.querySelector('iframe');
                            if (iframe && iframe.contentDocument && iframe.contentDocument.readyState === 'complete') {
                                // Widget is fully loaded
                                clearInterval(checkInterval);
                                // Small delay to ensure widget is visible
                                setTimeout(function() {
                                    hideLoadingState();
                                }, 100);
                            } else if (iframe && iframe.offsetWidth > 0 && iframe.offsetHeight > 0) {
                                // Iframe exists and has dimensions, widget is likely ready
                                clearInterval(checkInterval);
                                setTimeout(function() {
                                    hideLoadingState();
                                }, 200);
                            }
                        }, 100);
                        
                        // Timeout after 10 seconds - hide loading anyway (widget might be blocked)
                        setTimeout(function() {
                            clearInterval(checkInterval);
                            hideLoadingState();
                        }, 10000);
                    } catch (e) {
                        // Clear cache on error
                        sessionStorage.removeItem(CACHE_KEY);
                        // Remove the hidden input if rendering fails
                        const renderedInput = document.getElementById('recaptcha-widget-rendered');
                        if (renderedInput) {
                            renderedInput.remove();
                        }
                        // Hide loading state on error
                        hideLoadingState();
                    }
                });
            }
            
            // Ensure hidden input exists for form submission
            function ensureHiddenInput() {
                const renderedInput = document.getElementById('recaptcha-widget-rendered');
                if (!renderedInput) {
                    const recaptchaContainer = document.getElementById('recaptcha-container');
                    if (recaptchaContainer) {
                        const form = recaptchaContainer.closest('form');
                        if (form) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'recaptcha_widget_rendered';
                            input.value = '0';
                            input.id = 'recaptcha-widget-rendered';
                            form.insertBefore(input, form.firstChild);
                        }
                    }
                }
            }
            
            // Initialize on page load
            function setupRecaptcha() {
                // Small delay to allow DOM to settle
                setTimeout(initRecaptcha, 300);
            }
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setupRecaptcha);
            } else {
                setupRecaptcha();
            }
            
            // Re-initialize on Turbo navigation (but check cache first)
            document.addEventListener('turbo:load', function() {
                // Check if widget persists across navigation
                setTimeout(function() {
                    if (!isRecaptchaRendered()) {
                        setupRecaptcha();
                    } else {
                        ensureHiddenInput();
                    }
                }, 100);
            });
            document.addEventListener('turbo:render', function() {
                setTimeout(function() {
                    if (!isRecaptchaRendered()) {
                        setupRecaptcha();
                    } else {
                        ensureHiddenInput();
                    }
                }, 100);
            });
        })();
    </script>
@else
    <!-- Placeholder div to maintain layout when reCAPTCHA is not displayed -->
    <div style="min-height: 65px;"></div>
    @if(config('app.debug'))
        <!-- reCAPTCHA Debug Info:
        enabled={{ $enabled ? 'true' : 'false' }}
        skip_in_local={{ $skipInLocal ? 'true' : 'false' }}
        env={{ $env }}
        has_site_key={{ !empty($siteKey) ? 'true' : 'false' }}
        site_key_length={{ strlen($siteKey) }}
        should_display={{ $shouldDisplay ? 'true' : 'false' }}
        -->
    @endif
@endif


