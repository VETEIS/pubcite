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
        // But only log in debug mode to avoid spam
        if (empty($token)) {
            if (config('app.debug')) {
                Log::debug('reCAPTCHA verification: No token provided', [
                    'should_display' => $this->shouldDisplay(),
                    'environment' => app()->environment()
                ]);
            }
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
        $enabled = Config::get('recaptcha.enabled', false);
        $skipInLocal = Config::get('recaptcha.skip_in_local', true);
        $siteKey = Config::get('recaptcha.site_key', '');
        $environment = app()->environment();
        
        // Log for debugging (only in debug mode)
        if (config('app.debug')) {
            Log::debug('reCAPTCHA display check', [
                'enabled' => $enabled,
                'skip_in_local' => $skipInLocal,
                'environment' => $environment,
                'has_site_key' => !empty($siteKey),
                'site_key_length' => strlen($siteKey)
            ]);
        }
        
        if (!$enabled) {
            return false;
        }

        // Don't display in local if configured to skip
        if ($skipInLocal && in_array($environment, ['local', 'testing'])) {
            return false;
        }

        return !empty($siteKey);
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

