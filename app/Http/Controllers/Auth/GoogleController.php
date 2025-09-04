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
            \Log::info('Google profile picture found', [
                'user_email' => $googleUser->getEmail(),
                'avatar_url' => $googleUser->getAvatar(),
                'previous_photo' => $user->profile_photo_path
            ]);
            
            $user->profile_photo_path = $googleUser->getAvatar();
            $user->save();
            
            \Log::info('Profile picture updated successfully', [
                'user_id' => $user->id,
                'new_photo' => $user->profile_photo_path
            ]);
        } else {
            \Log::info('No Google profile picture available', [
                'user_email' => $googleUser->getEmail()
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
