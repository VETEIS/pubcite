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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to welcome page and privacy acceptance route
        if ($request->is('/') || $request->is('privacy/accept')) {
            return $next($request);
        }

        // Check if user has accepted privacy (stored in session)
        $privacyAccepted = session('privacy_accepted', false);
        
        // If privacy not accepted, redirect to welcome page with modal
        if (!$privacyAccepted) {
            return redirect('/?privacy_required=1&intended=' . urlencode($request->url()));
        }

        return $next($request);
    }
}
