<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="text-center mb-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-1">Sign in to your account</h1>
        </div>

        <x-validation-errors class="mb-3" />

        @session('status')
            <div class="mb-3 font-medium text-sm text-maroon-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" id="login-form" data-turbo="false">
            @csrf
            <input type="hidden" name="privacy_accepted" value="true">
            <button type="submit" id="hidden-submit-btn" style="display: none;" aria-hidden="true"></button>

            <div>
                <x-label for="email" value="{{ __('Email') }}" class="text-base text-gray-900 font-medium drop-shadow-sm" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-3">
                <x-label for="password" value="{{ __('Password') }}" class="text-base text-gray-900 font-medium drop-shadow-sm" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="flex items-center mt-3">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox rounded text-maroon-800 shadow-sm focus:ring-maroon-800" name="remember">
                    <span class="ml-2 text-gray-900 font-medium text-sm">Remember me</span>
                </label>
            </div>

            <div class="mt-4">
                <x-recaptcha />
            </div>

            <div class="flex items-center justify-center mt-4">
                <div class="w-full border-t border-gray-300"></div>
                <div class="px-4 text-sm text-gray-500">or</div>
                <div class="w-full border-t border-gray-300"></div>
            </div>

            <div class="flex justify-center mt-3">
                <button type="button" id="google-login-btn" class="inline-flex items-center justify-center px-4 py-2 bg-maroon-700 border border-transparent rounded-full shadow-sm text-sm font-medium text-white hover:bg-maroon-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon-500">
                    <img src="/images/google-logo.webp" alt="Google logo" class="w-5 h-5 mr-2">
                    Sign in with your USeP Google account
                </button>
            </div>

            
        </form>
    </x-authentication-card>

    <script>
        function initGoogleLogin() {
            const googleLoginBtn = document.getElementById('google-login-btn');
            if (!googleLoginBtn) {
                return;
            }
            
            // Remove existing event listeners by cloning and replacing
            const newBtn = googleLoginBtn.cloneNode(true);
            googleLoginBtn.parentNode.replaceChild(newBtn, googleLoginBtn);
            
            newBtn.addEventListener('click', function() {
                // Check if privacy was accepted in sessionStorage
                const privacyAccepted = sessionStorage.getItem('privacy_accepted') === 'true';
                
                if (privacyAccepted) {
                    // Privacy already accepted, set server session and proceed with Google login
                    fetch('{{ route("google.privacy.check") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            window.location.href = data.redirect_url;
                        } else {
                            window.location.href = '{{ route("welcome") }}';
                        }
                    })
                    .catch(error => {
                        window.location.href = '{{ route("welcome") }}';
                    });
                } else {
                    // Privacy not accepted, redirect to welcome page
                    window.location.href = '{{ route("welcome") }}';
                }
            });
        }
        
        // Initialize on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initGoogleLogin);
        } else {
            initGoogleLogin();
        }
        
        // Re-initialize after form errors (Turbo compatibility)
        document.addEventListener('turbo:load', initGoogleLogin);
        document.addEventListener('turbo:render', initGoogleLogin);
    </script>
</x-guest-layout>
