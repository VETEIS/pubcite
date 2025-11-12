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
    <div class="flex items-center justify-center" id="recaptcha-container" style="width: 304px; min-height: 65px; display: flex; align-items: center; justify-content: center; margin: 0 auto; padding: 0; box-sizing: border-box; overflow: hidden;">
        <div class="g-recaptcha" data-sitekey="{{ $siteKey }}" id="{{ $recaptchaId }}" style="transform: scale(0.85); transform-origin: center center; width: 358px !important; height: 65px !important; margin: 0 !important; padding: 0 !important;"></div>
    </div>
    
    <style>
        #recaptcha-container {
            width: 304px !important;
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
            width: 358px !important;
            height: 65px !important;
            transform: scale(0.85) !important;
            transform-origin: center center !important;
        }
    </style>
    
    <script>
        (function() {
            const RECAPTCHA_ID = '{{ $recaptchaId }}';
            const SITE_KEY = '{{ $siteKey }}';
            const CACHE_KEY = 'recaptcha_rendered_' + RECAPTCHA_ID;
            
            // Load reCAPTCHA script if not already loaded
            if (!document.querySelector('script[src*="recaptcha/api.js"]')) {
                const script = document.createElement('script');
                script.src = 'https://www.google.com/recaptcha/api.js';
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
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
                    return; // Already rendered, skip
                }
                
                // Check sessionStorage cache
                const cached = sessionStorage.getItem(CACHE_KEY);
                if (cached === 'true' && container.querySelector('iframe')) {
                    // Cached and iframe exists, just ensure hidden input
                    ensureHiddenInput();
                    return;
                }
                
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
                        return;
                    }
                    
                    // Check if container still exists and doesn't have iframe
                    if (!container || container.querySelector('iframe')) {
                        return;
                    }
                    
                    try {
                        grecaptcha.render(container, {
                            'sitekey': SITE_KEY,
                            'theme': 'light',
                            'callback': function(token) {
                                // Mark widget as rendered when user completes it
                                const renderedInput = document.getElementById('recaptcha-widget-rendered');
                                if (renderedInput) {
                                    renderedInput.value = '1';
                                }
                                // Cache the render state
                                sessionStorage.setItem(CACHE_KEY, 'true');
                            },
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
                    } catch (e) {
                        // Clear cache on error
                        sessionStorage.removeItem(CACHE_KEY);
                        // Remove the hidden input if rendering fails
                        const renderedInput = document.getElementById('recaptcha-widget-rendered');
                        if (renderedInput) {
                            renderedInput.remove();
                        }
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

