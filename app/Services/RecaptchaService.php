<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class RecaptchaService
{
    /**
     * Verify reCAPTCHA response token
     *
     * @param string|null $token The reCAPTCHA response token
     * @param string|null $ipAddress The user's IP address
     * @return bool
     */
    public function verify(?string $token, ?string $ipAddress = null): bool
    {
        // Skip validation if reCAPTCHA is disabled
        if (!Config::get('recaptcha.enabled', false)) {
            return true;
        }

        // Skip in local environment if configured
        if (Config::get('recaptcha.skip_in_local', true) && app()->environment('local', 'testing')) {
            return true;
        }

        // If no token provided, validation fails
        if (empty($token)) {
            Log::warning('reCAPTCHA verification failed: No token provided');
            return false;
        }

        $secretKey = Config::get('recaptcha.secret_key');
        
        if (empty($secretKey)) {
            Log::error('reCAPTCHA secret key is not configured');
            return false;
        }

        try {
            $response = Http::asForm()->post(Config::get('recaptcha.verify_url'), [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => $ipAddress ?? request()->ip(),
            ]);

            $result = $response->json();

            if (!isset($result['success']) || $result['success'] !== true) {
                $errorCodes = $result['error-codes'] ?? [];
                Log::warning('reCAPTCHA verification failed', [
                    'error_codes' => $errorCodes,
                    'ip' => $ipAddress ?? request()->ip(),
                ]);
                return false;
            }

            // Optional: Check score for v3 (we're using v2, so this is just for future compatibility)
            if (isset($result['score']) && $result['score'] < 0.5) {
                Log::warning('reCAPTCHA score too low', ['score' => $result['score']]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error', [
                'message' => $e->getMessage(),
                'ip' => $ipAddress ?? request()->ip(),
            ]);
            // Fail open in case of network issues (you may want to fail closed)
            return false;
        }
    }

    /**
     * Check if reCAPTCHA should be displayed
     *
     * @return bool
     */
    public function shouldDisplay(): bool
    {
        if (!Config::get('recaptcha.enabled', false)) {
            return false;
        }

        // Don't display in local if configured to skip
        if (Config::get('recaptcha.skip_in_local', true) && app()->environment('local', 'testing')) {
            return false;
        }

        return !empty(Config::get('recaptcha.site_key'));
    }

    /**
     * Get the site key
     *
     * @return string
     */
    public function getSiteKey(): string
    {
        return Config::get('recaptcha.site_key', '');
    }
}

