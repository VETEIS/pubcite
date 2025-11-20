<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Get Vite dev server URLs for CSP
     * Dynamically detects local network IPs for flexible development
     */
    private function getViteDevUrls(Request $request): array
    {
        $urls = ['http://localhost:5173', 'http://127.0.0.1:5173'];
        
        // Get IP from request host (if accessing via local network IP)
        $host = $request->getHost();
        if ($host && $host !== 'localhost' && $host !== '127.0.0.1') {
            // Check if it's a local network IP
            if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                // It's a private/reserved IP (local network)
                $urls[] = "http://{$host}:5173";
            }
        }
        
        // Try to get server IP
        if (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] !== '127.0.0.1') {
            $serverIp = $_SERVER['SERVER_ADDR'];
            if (filter_var($serverIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                $urls[] = "http://{$serverIp}:5173";
            }
        }
        
        // Also check REMOTE_ADDR for client's local network IP
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $clientIp = $_SERVER['REMOTE_ADDR'];
            if ($clientIp !== '127.0.0.1' && filter_var($clientIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                $urls[] = "http://{$clientIp}:5173";
            }
        }
        
        return array_unique($urls);
    }

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
        // SECURITY FIX: Removed overly restrictive COEP header that breaks application
        // COEP 'require-corp' blocks legitimate resources and breaks functionality
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        
        // Content Security Policy - Development vs Production
        if (app()->environment('local', 'development')) {
            // Development CSP - More permissive for Vite, Alpine, and external services
            // Dynamically detect Vite dev server URLs from request and allow http: as fallback for any local IP
            $viteUrls = $this->getViteDevUrls($request);
            $viteUrlsString = implode(' ', $viteUrls);
            $viteWsUrlsString = implode(' ', array_map(fn($url) => str_replace('http://', 'ws://', $url), $viteUrls));
            
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' {$viteUrlsString} http: https://unpkg.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.google.com https://www.gstatic.com; " .
                   "style-src 'self' 'unsafe-inline' {$viteUrlsString} http: https://fonts.bunny.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.google.com https://www.gstatic.com; " .
                   "font-src 'self' https://fonts.bunny.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
                   "img-src 'self' data: https: http:; " .
                   "connect-src 'self' {$viteUrlsString} {$viteWsUrlsString} http: ws: wss: https: https://www.google.com; " .
                   "frame-src 'self' https://www.google.com; " .
                   "frame-ancestors 'none';";
        } else {
            // Production CSP - Allow necessary external resources
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://unpkg.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.google.com https://www.gstatic.com; " .
                   "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.google.com https://www.gstatic.com; " .
                   "font-src 'self' https://fonts.bunny.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
                   "img-src 'self' data: https: https://lh3.googleusercontent.com https://lh4.googleusercontent.com https://lh5.googleusercontent.com https://lh6.googleusercontent.com; " .
                   "connect-src 'self' https: https://www.google.com; " .
                   "frame-src 'self' https://www.google.com; " .
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
