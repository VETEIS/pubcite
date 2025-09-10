<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{

    public function redirectToGoogle()
    {
        // Check if privacy was accepted in session (from welcome page)
        if (session('privacy_accepted') !== true) {
            return redirect()->route('welcome')->withErrors(['privacy' => 'You must accept the privacy policy before logging in. Please visit the homepage first.']);
        }

        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        if (!str_ends_with($googleUser->getEmail(), '@usep.edu.ph')) {
            return redirect()->route('login')->withErrors(['email' => 'Only @usep.edu.ph email addresses are allowed.']);
        }

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => bcrypt(uniqid()),
                'role' => 'user',
                'auth_provider' => 'google',
                'privacy_accepted_at' => now(),
            ]
        );

        // Update privacy acceptance for existing users
        if (!$user->hasAcceptedPrivacy()) {
            $user->update(['privacy_accepted_at' => now()]);
            \Log::info('Privacy acceptance recorded for Google user: ' . $googleUser->getEmail());
        }

        if ($googleUser->getAvatar()) {
            $avatarUrl = $googleUser->getAvatar();
            \Log::info('Google profile picture found', [
                'user_email' => $googleUser->getEmail(),
                'avatar_url' => $avatarUrl,
                'previous_photo' => $user->profile_photo_path,
                'environment' => app()->environment()
            ]);
            
            // Ensure HTTPS for Google profile pictures
            if (str_contains($avatarUrl, 'googleusercontent.com')) {
                $avatarUrl = str_replace('http://', 'https://', $avatarUrl);
                // Add size parameter if not present
                if (!str_contains($avatarUrl, '=')) {
                    $avatarUrl .= '=s96-c';
                }
            }
            
            $user->profile_photo_path = $avatarUrl;
            $user->save();
            
            \Log::info('Profile picture updated successfully', [
                'user_id' => $user->id,
                'new_photo' => $user->profile_photo_path,
                'processed_url' => $avatarUrl
            ]);
        } else {
            \Log::info('No Google profile picture available', [
                'user_email' => $googleUser->getEmail(),
                'google_user_data' => $googleUser->user
            ]);
        }

        // Check if admin is trying to login on mobile
        if ($user->role === 'admin' && $this->isMobileDevice()) {
            return redirect()->route('login')->withErrors(['email' => 'Admin accounts must be accessed from desktop devices for full functionality. Please use a desktop computer to login.']);
        }

        Auth::login($user, true);

        session()->forget('url.intended');

        \Log::info('Google login redirect', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_email' => $user->email,
            'intended_url' => session('url.intended'),
            'redirecting_to' => $user->role === 'admin' ? 'admin.dashboard' : 'dashboard'
        ]);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('dashboard');
        }
    }

    /**
     * Check if the request is from a mobile device
     */
    private function isMobileDevice(): bool
    {
        $userAgent = request()->header('User-Agent');
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
