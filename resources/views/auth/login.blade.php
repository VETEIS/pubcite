<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-maroon-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <div class="flex items-center justify-center mt-6">
                <x-button class="w-full justify-center">
                    {{ __('Sign in') }}
                </x-button>
            </div>

            <div class="flex items-center justify-center mt-6">
                <div class="w-full border-t border-gray-300"></div>
                <div class="px-4 text-sm text-gray-500">or</div>
                <div class="w-full border-t border-gray-300"></div>
            </div>

            <div class="flex justify-center mt-4">
                <a href="{{ route('google.login') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-maroon-500">
                    <img src="/images/google-logo.png" alt="Google logo" class="w-5 h-5 mr-2">
                    Sign in with Google
                </a>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    {{ __('Don\'t have an account?') }}
                    <a href="{{ route('register') }}" class="font-medium text-maroon-600 hover:text-maroon-500">
                        {{ __('Register here') }}
                    </a>
                </p>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>

<x-footer />
