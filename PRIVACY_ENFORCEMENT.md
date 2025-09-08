# Privacy Enforcement Implementation

This document explains the privacy enforcement system across all PubCite webapp pages.

## Overview

The privacy enforcement system ensures that users must accept the USeP Data Privacy Statement before accessing any webapp functionality. The system uses a **hybrid approach** with both client-side and server-side validation for maximum security and user experience.

## Architecture

### Client-Side Enforcement (Primary)
- **Storage**: `localStorage.getItem('privacyAccepted')`
- **Component**: `resources/views/components/privacy-enforcer.blade.php`
- **Purpose**: Immediate user feedback and smooth UX

### Server-Side Enforcement (Backup)
- **Storage**: `session('privacy_accepted')`
- **Middleware**: `app/Http/Middleware/PrivacyEnforcement.php`
- **Purpose**: Security validation and protection against client-side bypass

### Synchronization
- When user accepts privacy, both client and server storage are updated
- Server endpoint: `POST /privacy/accept`
- Fallback: Client-side enforcement works even if server sync fails

## Implementation

### 1. Privacy Enforcer Component

The `resources/views/components/privacy-enforcer.blade.php` component handles client-side privacy checking.

### 2. Adding to Pages

To add privacy enforcement to any webapp page, include the component at the top:

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

### 3. Pages Currently Protected

- ✅ Dashboard (`resources/views/dashboard.blade.php`)
- ✅ Profile (`resources/views/profile/show.blade.php`)
- ✅ Signing (`resources/views/signing/index.blade.php`)
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

#### Admin Pages
- `resources/views/admin/users.blade.php`
- `resources/views/admin/settings.blade.php`
- `resources/views/admin/user-create.blade.php`
- `resources/views/admin/user-edit.blade.php`

#### Other Pages
- `resources/views/policy.blade.php`
- `resources/views/terms.blade.php`

## How It Works

1. **First Visit**: User sees privacy modal on welcome page
2. **Acceptance**: User clicks "I Agree" and acceptance is stored in both localStorage and server session
3. **Subsequent Visits**: Privacy modal is skipped for accepted users
4. **Webapp Access**: All webapp pages check for privacy acceptance
5. **No Acceptance**: Users are redirected to welcome page with privacy modal
6. **Server Validation**: Server-side middleware provides backup security

## Technical Details

### Client-Side
- **Storage**: `localStorage.getItem('privacyAccepted') === 'true'`
- **Check**: Consistent across all components
- **Redirect**: Sends users to `/` (welcome page)
- **Timing**: Checks on DOM load and page visibility change
- **Exclusions**: Welcome page itself is excluded from checks

### Server-Side
- **Storage**: `session('privacy_accepted')`
- **Middleware**: `PrivacyEnforcement` (can be enabled for additional security)
- **API Support**: Returns JSON responses for AJAX requests
- **Exclusions**: Welcome page, privacy routes, API routes, test routes

### Synchronization
- **Endpoint**: `POST /privacy/accept`
- **Method**: AJAX call with CSRF token
- **Fallback**: Client-side enforcement continues if server sync fails

## Security Features

- **Dual Validation**: Both client and server-side checks
- **CSRF Protection**: Server endpoint protected with CSRF tokens
- **Bypass Protection**: Server-side validation prevents client-side bypass
- **Session Tracking**: Server tracks acceptance timestamp
- **Error Handling**: Graceful fallback if server sync fails

## Testing

1. Clear localStorage: `localStorage.removeItem('privacyAccepted')`
2. Clear server session: `session()->forget('privacy_accepted')`
3. Visit any webapp page
4. Should redirect to welcome page with privacy modal
5. Accept privacy terms
6. Visit webapp pages - should work normally
7. Test server sync: Check `/test-privacy` endpoint

## Maintenance

- Privacy acceptance is stored in both localStorage and server session
- Users must re-accept if they clear browser data
- Server-side tracking provides audit trail
- Privacy modal shows on every new browser/session
- System is resilient to client-side tampering
