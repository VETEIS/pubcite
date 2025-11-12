@php
    $recaptchaService = app(\App\Services\RecaptchaService::class);
@endphp

@if($recaptchaService->shouldDisplay())
    <div class="mb-4" id="recaptcha-container">
        <div class="g-recaptcha" data-sitekey="{{ $recaptchaService->getSiteKey() }}"></div>
        @error('g-recaptcha-response')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

