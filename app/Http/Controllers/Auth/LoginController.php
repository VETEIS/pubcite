<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

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
            // User doesn't exist - don't create account automatically
            Log::info('Login attempt with non-existent email: ' . $request->email);
            throw ValidationException::withMessages([
                'email' => 'No account found with this email address. Please use "Sign in with Google" to create an account with your USeP email.'
            ]);
        }

        // Verify password for existing user
        if (!Hash::check($request->password, $user->password)) {
            Log::info('Password mismatch for user: ' . $request->email);
            throw ValidationException::withMessages([
                'password' => 'The provided credentials do not match our records. If you previously used Google login, please use the "Sign in with Google" button.'
            ]);
        }
        
        Log::info('Password verified for user: ' . $request->email);

        // Update privacy acceptance timestamp (privacy was already accepted on welcome page)
        if (!$user->hasAcceptedPrivacy()) {
            $user->update(['privacy_accepted_at' => now()]);
            Log::info('Privacy acceptance recorded for user: ' . $request->email);
        }

        Auth::login($user, $request->boolean('remember'));
        Log::info('User logged in successfully: ' . $request->email);

        // Clear any existing intended URL to prevent redirect issues
        session()->forget('url.intended');

        // Check if admin is trying to login on mobile
        if ($user->role === 'admin' && $this->isMobileDevice($request)) {
            Auth::logout(); // Log out the admin user
            throw ValidationException::withMessages([
                'email' => 'Admin accounts must be accessed from desktop devices for full functionality. Please use a desktop computer to login.'
            ]);
        }

        // Redirect admin users to admin dashboard, regular users to user dashboard
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
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