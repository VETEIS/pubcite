<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Enhanced Security Headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), accelerometer=()');
        
        // Additional Security Headers
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        
        // Content Security Policy - Development vs Production
        if (app()->environment('local', 'development')) {
            // Development CSP - More permissive for Vite, Alpine, and external services
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://192.168.56.1:5173 http://localhost:5173 https://unpkg.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
                   "style-src 'self' 'unsafe-inline' http://192.168.56.1:5173 http://localhost:5173 https://fonts.bunny.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
                   "font-src 'self' https://fonts.bunny.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
                   "img-src 'self' data: https: http:; " .
                   "connect-src 'self' http://192.168.56.1:5173 http://localhost:5173 ws://192.168.56.1:5173 ws://localhost:5173 https:; " .
                   "frame-ancestors 'none';";
        } else {
            // Production CSP - Allow necessary external resources
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://unpkg.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
                   "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
                   "font-src 'self' https://fonts.bunny.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
                   "img-src 'self' data: https: https://lh3.googleusercontent.com https://lh4.googleusercontent.com https://lh5.googleusercontent.com https://lh6.googleusercontent.com; " .
                   "connect-src 'self' https:; " .
                   "frame-ancestors 'none';";
        }
        
        $response->headers->set('Content-Security-Policy', $csp);

        // Cache Control Headers
        if ($request->is('*.css') || $request->is('*.js') || $request->is('*.png') || $request->is('*.jpg') || $request->is('*.jpeg') || $request->is('*.gif') || $request->is('*.svg') || $request->is('*.ico')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        } else {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        // Remove potentially dangerous headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
