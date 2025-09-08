<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrivacyEnforcement
{
    /**
     * Handle an incoming request.
     * 
     * This middleware provides server-side validation as a backup to client-side enforcement.
     * It checks if the user has accepted privacy terms in their session.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to welcome page, privacy routes, and API routes
        if ($request->is('/') || 
            $request->is('privacy/*') || 
            $request->is('api/*') ||
            $request->is('test*') ||
            $request->is('debug*')) {
            return $next($request);
        }

        // Check if user has accepted privacy (stored in session)
        $privacyAccepted = session('privacy_accepted', false);
        
        // If privacy not accepted, redirect to welcome page
        if (!$privacyAccepted) {
            // For AJAX requests, return JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Privacy statement must be accepted',
                    'redirect' => route('welcome')
                ], 403);
            }
            
            // For regular requests, redirect to welcome page
            return redirect('/?privacy_required=1&intended=' . urlencode($request->url()));
        }

        return $next($request);
    }
}
