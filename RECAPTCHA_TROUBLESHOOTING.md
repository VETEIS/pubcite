# reCAPTCHA Troubleshooting Guide

## Issue: reCAPTCHA Not Showing

If reCAPTCHA is not appearing, check the following:

### 1. Check Your .env File

Make sure these are set correctly:
```env
RECAPTCHA_ENABLED=true
RECAPTCHA_SITE_KEY=your_actual_site_key_here
RECAPTCHA_SECRET_KEY=your_actual_secret_key_here
RECAPTCHA_SKIP_IN_LOCAL=false  # Set to false if you want to test in local environment
```

### 2. Clear Config Cache

After updating .env, run:
```bash
php artisan config:clear
php artisan cache:clear
```

### 3. Check Environment

If you're in **local** environment and `RECAPTCHA_SKIP_IN_LOCAL=true`, reCAPTCHA will be hidden.

To test in local environment:
- Set `RECAPTCHA_SKIP_IN_LOCAL=false` in your .env
- Or test on a production/staging server

### 4. View Source Debug Info

If `APP_DEBUG=true`, you'll see a comment in the HTML source showing:
- Whether reCAPTCHA is enabled
- Whether it's skipping in local
- Current environment
- Whether site key exists

Look for: `<!-- reCAPTCHA Debug Info: ... -->` in the page source.

### 5. Check Browser Console

Open browser DevTools (F12) and check:
- Console for any JavaScript errors
- Network tab to see if `recaptcha/api.js` is loading
- Elements tab to see if `#recaptcha-container` exists in the DOM

### 6. Verify Domain in Google reCAPTCHA Console

Make sure your domain is added in Google reCAPTCHA admin:
- For local: `localhost` or `127.0.0.1`
- For production: your actual domain

### 7. Test reCAPTCHA Display

To quickly test if reCAPTCHA should display, you can temporarily add this to any Blade template:

```blade
@php
    $recaptchaService = app(\App\Services\RecaptchaService::class);
@endphp

<div style="background: yellow; padding: 10px; margin: 10px;">
    <strong>reCAPTCHA Debug:</strong><br>
    Should Display: {{ $recaptchaService->shouldDisplay() ? 'YES' : 'NO' }}<br>
    Site Key: {{ $recaptchaService->getSiteKey() ? 'SET' : 'NOT SET' }}<br>
    Enabled: {{ config('recaptcha.enabled') ? 'YES' : 'NO' }}<br>
    Skip in Local: {{ config('recaptcha.skip_in_local') ? 'YES' : 'NO' }}<br>
    Environment: {{ app()->environment() }}
</div>

<x-recaptcha />
```

### Common Issues:

1. **Config not cleared**: Always run `php artisan config:clear` after changing .env
2. **Local environment**: Set `RECAPTCHA_SKIP_IN_LOCAL=false` to test locally
3. **Wrong domain**: Make sure your domain is registered in Google reCAPTCHA console
4. **Empty site key**: Double-check the site key in .env matches Google console
5. **Turbo navigation**: reCAPTCHA should re-initialize on Turbo navigation (already handled)

