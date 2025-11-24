<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request, RecaptchaService $recaptchaService)
    {
        Log::info('Login attempt received', [
            'has_email' => $request->has('email'),
            'has_password' => $request->has('password'),
            'has_recaptcha' => $request->has('g-recaptcha-response'),
            'is_ajax' => $request->ajax(),
            'user_agent' => $request->userAgent()
        ]);
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => 'sometimes|string',
        ]);

        // Verify reCAPTCHA only if widget was actually rendered on the page
        $recaptchaToken = $request->input('g-recaptcha-response');
        $widgetRendered = $request->has('recaptcha_widget_rendered');
        
        // Only validate if widget was supposed to be displayed AND was actually rendered
        if ($recaptchaService->shouldDisplay() && $widgetRendered) {
            // Widget was displayed, so token is required
            if (empty($recaptchaToken)) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'Please complete the reCAPTCHA verification.'
                ]);
            }
            if (!$recaptchaService->verify($recaptchaToken, $request->ip())) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.'
                ]);
            }
        } else if ($recaptchaService->shouldDisplay() && !$widgetRendered) {
            // Widget should have been displayed but wasn't rendered - log for debugging
            if (config('app.debug')) {
                Log::warning('reCAPTCHA widget should have been displayed but was not rendered', [
                    'should_display' => $recaptchaService->shouldDisplay(),
                    'widget_rendered' => $widgetRendered,
                    'has_token' => !empty($recaptchaToken)
                ]);
            }
            // Don't block login if widget didn't render (could be a frontend issue)
            // But verify token if one was provided
            if (!empty($recaptchaToken) && !$recaptchaService->verify($recaptchaToken, $request->ip())) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.'
                ]);
            }
        } else if (!empty($recaptchaToken)) {
            // Token was sent even though widget shouldn't display - verify it anyway
            if (!$recaptchaService->verify($recaptchaToken, $request->ip())) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.'
                ]);
            }
        }

        // Check if privacy was accepted in session (from welcome page)
        if (session('privacy_accepted') !== true) {
            throw ValidationException::withMessages([
                'email' => 'You must accept the privacy policy before logging in. Please visit the homepage first.'
            ]);
        }

        // Restrict to @usep.edu.ph emails only
        if (!str_ends_with($request->email, '@usep.edu.ph')) {
            throw ValidationException::withMessages([
                'email' => 'Only @usep.edu.ph email addresses are allowed.'
            ]);
        }

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // SECURITY FIX: Don't log email addresses for security
            Log::info('Login attempt with non-existent email');
            throw ValidationException::withMessages([
                'email' => 'No account found with this email address. Please use "Sign in with Google" to create an account with your USeP email.'
            ]);
        }

        // Verify password for existing user
        if (!Hash::check($request->password, $user->password)) {
            // SECURITY FIX: Don't log email addresses for security
            Log::info('Password mismatch for user');
            throw ValidationException::withMessages([
                'password' => 'The provided credentials do not match our records. If you previously used Google login, please use the "Sign in with Google" button.'
            ]);
        }
        
        // SECURITY FIX: Don't log email addresses for security
        Log::info('Password verified for user');

        // Update privacy acceptance timestamp (privacy was already accepted on welcome page)
        if (!$user->hasAcceptedPrivacy()) {
            $user->update(['privacy_accepted_at' => now()]);
            // SECURITY FIX: Don't log email addresses for security
            Log::info('Privacy acceptance recorded for user');
        }

        Auth::login($user, $request->boolean('remember'));
        // SECURITY FIX: Don't log email addresses for security
        Log::info('User logged in successfully');

        // Clear any existing intended URL to prevent redirect issues
        session()->forget('url.intended');

        // Check if admin is trying to login on mobile
        if ($user->role === 'admin' && $this->isMobileDevice($request)) {
            Auth::logout(); // Log out the admin user
            throw ValidationException::withMessages([
                'email' => 'Admin accounts must be accessed from desktop devices for full functionality. Please use a desktop computer to login.'
            ]);
        }

        // Redirect users to their appropriate dashboard based on role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'signatory') {
            return redirect()->route('signing.index');
        } else {
            // Regular user role
            return redirect()->route('dashboard');
        }
    }

    public function acceptPrivacy(Request $request)
    {
        $request->validate([
            'accepted' => 'required|boolean'
        ]);

        if ($request->accepted) {
            // Update user's privacy acceptance timestamp
            $user = Auth::user();
            if ($user && !$user->hasAcceptedPrivacy()) {
                $user->update(['privacy_accepted_at' => now()]);
                Log::info('Privacy acceptance recorded for user: ' . $user->email);
            }
            
            // Set session flag for this browser session
            session(['privacy_accepted' => true]);
            
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 400);
    }

    public function getPrivacyStatus(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            // User is logged in - check their privacy acceptance
            $privacyAccepted = $user->hasAcceptedPrivacy() || session('privacy_accepted', false);
        } else {
            // User is not logged in - check session only
            $privacyAccepted = session('privacy_accepted', false);
        }
        
        return response()->json([
            'privacy_accepted' => $privacyAccepted,
            'user_logged_in' => $user ? true : false
        ]);
    }

    /**
     * Check if the request is from a mobile device
     */
    private function isMobileDevice(Request $request): bool
    {
        $userAgent = $request->header('User-Agent');
        $mobileKeywords = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 
            'BlackBerry', 'Windows Phone', 'webOS', 'Opera Mini', 
            'IEMobile', 'Mobile Safari'
        ];

        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }
} 