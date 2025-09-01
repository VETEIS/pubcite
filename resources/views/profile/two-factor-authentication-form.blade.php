<div class="space-y-6">
    <div class="space-y-4">
        <h4 class="text-lg font-medium text-gray-900 flex items-center gap-2">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    {{ __('Finish enabling two factor authentication.') }}
                @else
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    {{ __('You have enabled two factor authentication.') }}
                @endif
            @else
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                {{ __('You have not enabled two factor authentication.') }}
            @endif
        </h4>

        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-blue-800">
                    {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
                </p>
            </div>
        </div>
    </div>

    @if ($this->enabled)
        @if ($showingQrCode)
            <div class="space-y-4">
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <p class="text-sm text-yellow-800 font-medium">
                                @if ($showingConfirmation)
                                    {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.') }}
                                @else
                                    {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center">
                    <div class="p-4 bg-white border border-gray-200 rounded-lg">
                        {!! $this->user->twoFactorQrCodeSvg() !!}
                    </div>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">{{ __('Setup Key') }}:</span> 
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm font-mono">{{ decrypt($this->user->two_factor_secret) }}</code>
                    </p>
                </div>

                @if ($showingConfirmation)
                    <div class="space-y-2">
                        <x-label for="code" value="{{ __('Verification Code') }}" class="text-sm font-medium text-gray-700" />
                        <x-input id="code" type="text" name="code" class="block w-full max-w-xs" inputmode="numeric" autofocus autocomplete="one-time-code"
                            wire:model="code"
                            wire:keydown.enter="confirmTwoFactorAuthentication" 
                            placeholder="Enter 6-digit code" />
                        <x-input-error for="code" class="mt-2" />
                    </div>
                @endif
            </div>
        @endif

        @if ($showingRecoveryCodes)
            <div class="space-y-4">
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <p class="text-sm text-green-800">
                            {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-2 max-w-xl p-4 font-mono text-sm bg-gray-100 rounded-lg border border-gray-200">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div class="bg-white px-3 py-2 rounded border border-gray-200">{{ $code }}</div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
        @if (! $this->enabled)
            <x-confirms-password wire:then="enableTwoFactorAuthentication">
                <x-button type="button" wire:loading.attr="disabled" class="bg-maroon-600 hover:bg-maroon-700 focus:ring-maroon-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    {{ __('Enable Two-Factor Authentication') }}
                </x-button>
            </x-confirms-password>
        @else
            @if ($showingRecoveryCodes)
                <x-confirms-password wire:then="regenerateRecoveryCodes">
                    <x-secondary-button class="bg-blue-600 hover:bg-blue-700 text-white">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        {{ __('Regenerate Recovery Codes') }}
                    </x-secondary-button>
                </x-confirms-password>
            @elseif ($showingConfirmation)
                <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                    <x-button type="button" class="me-3 bg-green-600 hover:bg-green-700 focus:ring-green-500" wire:loading.attr="disabled">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('Confirm') }}
                    </x-button>
                </x-confirms-password>
            @else
                <x-confirms-password wire:then="showRecoveryCodes">
                    <x-secondary-button class="bg-blue-600 hover:bg-blue-700 text-white">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{ __('Show Recovery Codes') }}
                    </x-secondary-button>
                </x-confirms-password>
            @endif

            @if ($showingConfirmation)
                <x-confirms-password wire:then="disableTwoFactorAuthentication">
                    <x-secondary-button wire:loading.attr="disabled">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        {{ __('Cancel') }}
                    </x-secondary-button>
                </x-confirms-password>
            @else
                <x-confirms-password wire:then="disableTwoFactorAuthentication">
                    <x-danger-button wire:loading.attr="disabled">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                        </svg>
                        {{ __('Disable Two-Factor Authentication') }}
                    </x-danger-button>
                </x-confirms-password>
            @endif
        @endif
    </div>
</div>
