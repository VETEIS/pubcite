# Privacy Enforcement Implementation

This document explains how to implement privacy enforcement across all PubCite webapp pages.

## Overview

The privacy enforcement system ensures that users must accept the USeP Data Privacy Statement before accessing any webapp functionality. Users who haven't accepted the privacy terms will be redirected to the welcome page.

## Implementation

### 1. Privacy Enforcer Component

The `resources/views/components/privacy-enforcer.blade.php` component handles privacy checking for all webapp pages.

### 2. Adding to Pages

To add privacy enforcement to any webapp page, simply include the component at the top of the page:

```blade
<x-app-layout>
    {{-- Privacy Enforcer --}}
    <x-privacy-enforcer />
    
    {{-- Your page content --}}
    <div>
        <!-- Page content here -->
    </div>
</x-app-layout>
```

### 3. Pages Already Protected

- ✅ Dashboard (`resources/views/dashboard.blade.php`)
- ✅ Admin Dashboard (`resources/views/admin/dashboard.blade.php`)
- ✅ Welcome Page (shows privacy modal instead of redirecting)

### 4. Pages That Need Protection

Add `<x-privacy-enforcer />` to the following pages:

#### Authentication Pages
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/auth/forgot-password.blade.php`
- `resources/views/auth/reset-password.blade.php`

#### Application Pages
- `resources/views/publications/incentive-application.blade.php`
- `resources/views/citations/incentive-application.blade.php`
- `resources/views/publications/request.blade.php`
- `resources/views/citations/request.blade.php`

#### Profile Pages
- `resources/views/profile/show.blade.php`

#### Admin Pages
- `resources/views/admin/users.blade.php`
- `resources/views/admin/settings.blade.php`
- `resources/views/admin/user-create.blade.php`
- `resources/views/admin/user-edit.blade.php`

#### Other Pages
- `resources/views/signing/index.blade.php`
- `resources/views/policy.blade.php`
- `resources/views/terms.blade.php`

## How It Works

1. **First Visit**: User sees privacy modal on welcome page
2. **Acceptance**: User clicks "I Agree" and acceptance is stored in localStorage
3. **Subsequent Visits**: Privacy modal is skipped for accepted users
4. **Webapp Access**: All webapp pages check for privacy acceptance
5. **No Acceptance**: Users are redirected to welcome page with privacy modal

## Technical Details

- **Storage**: Uses `localStorage.getItem('privacyAccepted')`
- **Check**: Compares value to `'true'`
- **Redirect**: Sends users to `/` (welcome page)
- **Timing**: Checks on DOM load and page visibility change
- **Exclusions**: Welcome page itself is excluded from checks

## Testing

1. Clear localStorage: `localStorage.removeItem('privacyAccepted')`
2. Visit any webapp page
3. Should redirect to welcome page with privacy modal
4. Accept privacy terms
5. Visit webapp pages - should work normally

## Maintenance

- Privacy acceptance is stored locally in browser
- Users must re-accept if they clear browser data
- No server-side tracking of privacy acceptance
- Privacy modal shows on every new browser/session
