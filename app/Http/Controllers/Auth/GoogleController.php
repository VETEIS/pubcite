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
            ]
        );

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
}
