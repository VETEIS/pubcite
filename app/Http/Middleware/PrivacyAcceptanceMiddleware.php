<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PrivacyAcceptanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip privacy check for certain routes
        $skipRoutes = [
            'privacy.accept',
            'privacy.status',
            'login',
            'logout',
            'welcome'
        ];
        
        if (in_array($request->route()?->getName(), $skipRoutes)) {
            return $next($request);
        }
        
        $user = Auth::user();
        
        // Check if privacy has been accepted
        $privacyAccepted = false;
        
        if ($user) {
            // User is logged in - check their privacy acceptance
            $privacyAccepted = $user->hasAcceptedPrivacy() || session('privacy_accepted', false);
        } else {
            // User is not logged in - check session only
            $privacyAccepted = session('privacy_accepted', false);
        }
        
        // If privacy not accepted and not on welcome page, redirect to welcome
        if (!$privacyAccepted && !$request->is('/')) {
            return redirect('/')->with('privacy_required', true);
        }
        
        return $next($request);
    }
}
