<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

class GoogleController extends Controller
{

    public function checkPrivacyBeforeGoogle(Request $request)
    {
        // Set privacy session if not already set
        if (session('privacy_accepted') !== true) {
            session(['privacy_accepted' => true]);
        }
        
        return response()->json(['status' => 'success', 'redirect_url' => route('google.login')]);
    }

    public function redirectToGoogle()
    {
        // Check if privacy was accepted in session (from welcome page)
        if (session('privacy_accepted') !== true) {
            return redirect()->route('welcome')->withErrors(['privacy' => 'You must accept the privacy policy before logging in. Please visit the homepage first.']);
        }

        // Configure Guzzle client with SSL settings
        $httpClient = $this->createHttpClient();
        
        /** @var \Laravel\Socialite\Two\GoogleProvider $provider */
        $provider = Socialite::driver('google');
        $provider->setHttpClient($httpClient);
        
        return $provider
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        // Configure Guzzle client with SSL settings
        $httpClient = $this->createHttpClient();
        
        /** @var \Laravel\Socialite\Two\GoogleProvider $provider */
        $provider = Socialite::driver('google');
        $provider->setHttpClient($httpClient);
        
        try {
            $googleUser = $provider
                ->stateless()
                ->user();
        } catch (RequestException $exception) {
            if ($this->shouldRetryWithoutSsl($exception)) {
                Log::warning('Google OAuth SSL verification failed, retrying without verification (local only)', [
                    'error' => $exception->getMessage(),
                ]);

                $provider->setHttpClient($this->createHttpClient(true));

                $googleUser = $provider
                    ->stateless()
                    ->user();
            } else {
                throw $exception;
            }
        }

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
            // SECURITY FIX: Don't log email addresses for security
            Log::info('Privacy acceptance recorded for Google user');
        }

        // Only set Google profile picture if user doesn't already have a custom one
        if ($googleUser->getAvatar() && !$user->profile_photo_path) {
            $avatarUrl = $googleUser->getAvatar();
            // SECURITY FIX: Don't log email addresses or URLs for security
            Log::info('Google profile picture found, setting as default');
            
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
            
            Log::info('Profile picture set from Google', [
                'user_id' => $user->id,
            ]);
        } elseif ($user->profile_photo_path) {
            Log::info('User already has custom profile picture, keeping existing photo', [
                'user_id' => $user->id,
            ]);
        } else {
            Log::info('No Google profile picture available', [
                'user_email' => $googleUser->getEmail(),
            ]);
        }

        // Check if admin is trying to login on mobile
        if ($user->role === 'admin' && $this->isMobileDevice()) {
            return redirect()->route('login')->withErrors(['email' => 'Admin accounts must be accessed from desktop devices for full functionality. Please use a desktop computer to login.']);
        }

        Auth::login($user, true);

        session()->forget('url.intended');

        Log::info('Google login redirect', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_email' => $user->email,
            'intended_url' => session('url.intended'),
            'redirecting_to' => $user->role === 'admin' ? 'admin.dashboard' : ($user->role === 'signatory' ? 'signing.index' : 'dashboard')
        ]);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'signatory') {
            return redirect()->route('signing.index');
        } else {
            // Regular user role
            return redirect()->route('dashboard');
        }
    }

    /**
     * Create HTTP client with proper SSL configuration
     */
    private function createHttpClient(bool $forceDisableVerification = false): Client
    {
        $options = [];
 
        if ($forceDisableVerification) {
            $options[RequestOptions::VERIFY] = false;
            return new Client($options);
        }

        // For local development, handle SSL certificate issues
        if (app()->environment('local')) {
            // Try to use system CA bundle first
            $caBundlePaths = [
                base_path('cacert.pem'), // If you download and place it in project root
                storage_path('app/cacert.pem'), // Alternative location
                'C:\\php\\extras\\ssl\\cacert.pem', // Common Windows XAMPP location
                'C:\\xampp\\php\\extras\\ssl\\cacert.pem', // XAMPP location
                ini_get('curl.cainfo'), // PHP ini setting
                getenv('SSL_CERT_FILE'), // Environment variable
            ];
 
            $caBundleFound = false;
            foreach ($caBundlePaths as $path) {
                if ($path && file_exists($path)) {
                    $realPath = realpath($path) ?: $path;
                    $options[RequestOptions::VERIFY] = $realPath;
                    $caBundleFound = true;
                    Log::info('Using CA bundle for SSL verification', ['path' => $realPath]);
                    break;
                }
            }
 
            // If no CA bundle found, disable SSL verification for local development only
            // WARNING: This should NEVER be used in production
            if (!$caBundleFound) {
                $options[RequestOptions::VERIFY] = false;
                Log::warning('SSL verification disabled for local development. CA bundle not found.');
            }
        } else {
            // Production: Always verify SSL
            $options[RequestOptions::VERIFY] = true;
        }
 
        return new Client($options);
    }

    /**
     * Determine if we should retry without SSL verification.
     */
    private function shouldRetryWithoutSsl(RequestException $exception): bool
    {
        if (!app()->environment('local')) {
            return false;
        }

        $message = $exception->getMessage();

        return str_contains($message, 'cURL error 60');
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
