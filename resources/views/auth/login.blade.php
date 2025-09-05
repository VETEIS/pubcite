<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back!</h1>
            <p class="text-gray-600">Sign in to your account to continue</p>
        </div>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-maroon-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" class="text-base text-gray-900 font-medium drop-shadow-sm" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" class="text-base text-gray-900 font-medium drop-shadow-sm" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox rounded text-maroon-800 shadow-sm focus:ring-maroon-800" name="remember">
                    <span class="ml-2 text-gray-900 font-medium text-sm">Remember me</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="underline text-maroon-800 hover:text-maroon-900 font-medium text-sm" href="{{ route('password.request') }}">
                        Forgot your password?
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
                <p class="text-base text-gray-900 font-medium drop-shadow-sm">
                    {{ __('Don\'t have an account?') }}
                    <a href="{{ route('register') }}" class="font-semibold text-maroon-800 hover:text-maroon-800 underline">
                        {{ __('Register here') }}
                    </a>
                </p>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
