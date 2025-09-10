<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MobileRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is on a mobile device
        $userAgent = $request->header('User-Agent');
        $isMobile = $this->isMobileDevice($userAgent);
        
        // Check if screen width is small (less than desktop breakpoint)
        $isSmallScreen = $request->has('small_screen') && $request->get('small_screen') === 'true';
        
        // Check if user wants to bypass mobile detection
        $bypassMobile = $request->has('bypass_mobile') && $request->get('bypass_mobile') === '1';
        
        // Skip redirect for certain routes
        $skipRoutes = [
            'mobile.redirect',
            'login',
            'register',
            'password.reset',
            'password.email',
            'password.confirm',
            'verification.notice',
            'verification.verify',
            'two-factor.login',
            'logout',
            'welcome'
        ];
        
        $currentRoute = $request->route() ? $request->route()->getName() : null;
        $currentPath = $request->path();
        
        // Don't redirect if already on mobile redirect page or auth pages
        if (in_array($currentRoute, $skipRoutes) || $currentPath === 'mobile-redirect') {
            return $next($request);
        }
        
        // Don't redirect if user wants to bypass mobile detection
        if ($bypassMobile) {
            return $next($request);
        }
        
        // Only redirect if we're sure it's a mobile device and not already redirected
        if (($isMobile || $isSmallScreen) && !$request->has('mobile_redirected')) {
            $redirectUrl = route('mobile.redirect') . '?redirect=' . urlencode($request->fullUrl()) . '&mobile_redirected=1';
            return redirect($redirectUrl);
        }
        
        return $next($request);
    }
    
    /**
     * Check if the user agent indicates a mobile device
     */
    private function isMobileDevice(string $userAgent): bool
    {
        $mobileKeywords = [
            'Mobile',
            'Android',
            'iPhone',
            'iPad',
            'iPod',
            'BlackBerry',
            'Windows Phone',
            'webOS',
            'Opera Mini',
            'IEMobile',
            'Mobile Safari'
        ];
        
        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
}