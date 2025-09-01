<form wire:submit.prevent="updatePassword" class="space-y-6">
    <div class="space-y-2">
        <x-label for="current_password" value="{{ __('Current Password') }}" class="text-sm font-medium text-gray-700" />
        <x-input id="current_password" type="password" class="mt-1 block w-full" wire:model="state.current_password" autocomplete="current-password" placeholder="Enter your current password" />
        <x-input-error for="current_password" class="mt-2" />
    </div>

    <div class="space-y-2">
        <x-label for="password" value="{{ __('New Password') }}" class="text-sm font-medium text-gray-700" />
        <x-input id="password" type="password" class="mt-1 block w-full" wire:model="state.password" autocomplete="new-password" placeholder="Enter your new password" />
        <x-input-error for="password" class="mt-2" />
    </div>

    <div class="space-y-2">
        <x-label for="password_confirmation" value="{{ __('Confirm New Password') }}" class="text-sm font-medium text-gray-700" />
        <x-input id="password_confirmation" type="password" class="mt-1 block w-full" wire:model="state.password_confirmation" autocomplete="new-password" placeholder="Confirm your new password" />
        <x-input-error for="password_confirmation" class="mt-2" />
    </div>

    <!-- Password Requirements -->
    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm text-blue-800 font-medium mb-2">{{ __('Password Requirements:') }}</p>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li class="flex items-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        At least 8 characters long
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Mix of uppercase and lowercase letters
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Include numbers and special characters
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
        <x-action-message class="text-sm text-green-600 font-medium" on="saved">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('Password updated successfully.') }}
            </div>
        </x-action-message>

        <x-button class="bg-maroon-600 hover:bg-maroon-700 focus:ring-maroon-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ __('Update Password') }}
        </x-button>
    </div>
</form>
