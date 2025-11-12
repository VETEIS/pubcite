# Google reCAPTCHA Setup Guide

## ✅ Implementation Complete!

reCAPTCHA has been implemented in the following locations:
1. ✅ **Login Form** - Prevents brute force attacks
2. ✅ **Password Reset/Forgot Password Forms** - Prevents abuse
3. ✅ **Publication Request Submission** - Prevents spam (only for final submissions, not drafts)
4. ✅ **Citation Request Submission** - Prevents spam (only for final submissions, not drafts)

## What You Need to Do

### Step 1: Get Google reCAPTCHA Keys

1. **Go to Google reCAPTCHA Admin Console**
   - Visit: https://www.google.com/recaptcha/admin/create
   - Sign in with your Google account

2. **Register a New Site**
   - **Label**: Enter a name (e.g., "PubCite Web App")
   - **reCAPTCHA type**: Select **reCAPTCHA v2** → **"I'm not a robot" Checkbox**
   - **Domains**: Add your domains:
     - `localhost` (for development)
     - `yourdomain.com` (for production)
     - `*.yourdomain.com` (if using subdomains)
   - Accept the reCAPTCHA Terms of Service
   - Click **Submit**

3. **Copy Your Keys**
   - You'll receive:
     - **Site Key** (public, used in frontend)
     - **Secret Key** (private, used in backend)
   - Keep these keys safe!

### Step 2: Add Keys to Your .env File

Add these lines to your `.env` file:

```env
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
RECAPTCHA_ENABLED=true
RECAPTCHA_SKIP_IN_LOCAL=true
```

**Configuration Options:**
- `RECAPTCHA_ENABLED=true` - Enable/disable reCAPTCHA globally
- `RECAPTCHA_SKIP_IN_LOCAL=true` - Skip reCAPTCHA validation in local/testing environments (recommended for development)

### Step 3: Test the Implementation

1. **In Development (Local):**
   - With `RECAPTCHA_SKIP_IN_LOCAL=true`, reCAPTCHA won't be displayed or validated
   - Forms will work normally without the widget

2. **In Production:**
   - Set `RECAPTCHA_SKIP_IN_LOCAL=false` or remove it
   - reCAPTCHA widget will appear on all protected forms
   - Users must complete reCAPTCHA before submitting

## How It Works

- **Automatic Display**: The reCAPTCHA widget automatically appears on forms when enabled
- **Server-Side Validation**: All submissions are validated server-side before processing
- **Error Handling**: Clear error messages if reCAPTCHA verification fails
- **Development Mode**: Can be disabled in local environment for easier testing

## Files Modified

- `config/recaptcha.php` - Configuration file
- `app/Services/RecaptchaService.php` - Service for validation
- `resources/views/components/recaptcha.blade.php` - Reusable component
- Login, password reset, and submission forms - Added reCAPTCHA widget
- Controllers - Added validation logic

## Notes

- reCAPTCHA is **not required for draft saves** (only final submissions)
- Google sign-in is not affected by reCAPTCHA
- The widget automatically hides in local environment when configured

