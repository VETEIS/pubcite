<form wire:submit.prevent="updateProfileInformation" class="space-y-6">
    <!-- Name -->
    <div class="space-y-2">
        <x-label for="name" value="{{ __('Full Name') }}" class="text-sm font-medium text-gray-700" />
        <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" placeholder="Enter your full name" />
        <x-input-error for="name" class="mt-2" />
    </div>

    <!-- Email -->
    <div class="space-y-2">
        <x-label for="email" value="{{ __('Email Address') }}" class="text-sm font-medium text-gray-700" />
        <div class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-700">
            {{ Auth::user()->email }}
        </div>
        <p class="text-xs text-gray-500 mt-1">Email address cannot be changed for security reasons.</p>
        <!-- Hidden email input for Livewire state - required for form submission -->
        <input type="hidden" wire:model="state.email" />

        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
            <div class="mt-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <div>
                        <p class="text-sm text-yellow-800 font-medium">{{ __('Your email address is unverified.') }}</p>
                        <button type="button" class="text-sm text-yellow-700 hover:text-yellow-900 underline mt-1" wire:click.prevent="sendEmailVerification">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </div>
                </div>
            </div>

            @if ($this->verificationLinkSent)
                <div class="mt-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-green-800 font-medium">{{ __('A new verification link has been sent to your email address.') }}</p>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Form Actions -->
    <div class="flex items-center justify-end pt-6 border-t border-gray-200">
        <x-button wire:loading.attr="disabled" class="bg-maroon-600 hover:bg-maroon-700 focus:ring-maroon-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ __('Save Changes') }}
        </x-button>
    </div>

    <script>
        // Listen for Livewire 'saved' event from the profile update form
        // Use Livewire hook to ensure it's set up after Livewire is ready
        document.addEventListener('livewire:init', () => {
            Livewire.on('saved', () => {
                if (window.notificationManager) {
                    window.notificationManager.success('Profile information saved successfully.');
                }
            });
        });
        
        // Fallback for when Livewire is already initialized
        if (window.Livewire) {
            Livewire.on('saved', () => {
                if (window.notificationManager) {
                    window.notificationManager.success('Profile information saved successfully.');
                }
            });
        }
    </script>
</form>
