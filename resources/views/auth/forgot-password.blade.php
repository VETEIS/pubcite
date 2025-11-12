<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <!-- Back to Sign In Button -->
        <div class="absolute top-4 right-4">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Sign In
            </a>
        </div>

        <div class="text-center mb-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-1">Reset Password</h1>
            <p class="text-gray-600">Enter your email to receive a reset link</p>
        </div>

        <div class="mb-3 text-base text-gray-900 font-medium drop-shadow-sm">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        @session('status')
            <div class="mb-3 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <x-validation-errors class="mb-3" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" class="text-base text-gray-900 font-medium drop-shadow-sm" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-3 space-y-3">
                <x-recaptcha />
                <x-button class="justify-center items-center" style="width: 256px; margin: 0 auto; display: flex; box-sizing: border-box; border-top-left-radius: 0; border-top-right-radius: 0;">
                    <svg class="mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 1em; height: 1em; vertical-align: middle;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span style="line-height: 1;">{{ __('Email Reset Link') }}</span>
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
