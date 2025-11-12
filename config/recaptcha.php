<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Google reCAPTCHA v2.
    | Get your keys from: https://www.google.com/recaptcha/admin/create
    |
    */

    'enabled' => env('RECAPTCHA_ENABLED', false),
    
    'site_key' => env('RECAPTCHA_SITE_KEY', ''),
    
    'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
    
    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA API Endpoint
    |--------------------------------------------------------------------------
    */
    'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
    
    /*
    |--------------------------------------------------------------------------
    | Skip reCAPTCHA in Development
    |--------------------------------------------------------------------------
    | When set to true, reCAPTCHA validation will be skipped in local/testing environments
    */
    'skip_in_local' => env('RECAPTCHA_SKIP_IN_LOCAL', true),
];

