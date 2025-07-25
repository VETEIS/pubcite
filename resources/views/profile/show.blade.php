<x-app-layout>
    <div class="h-[calc(100vh-4rem)] flex items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-6xl h-[calc(90vh-4rem)] flex items-center justify-center">
            <div class="bg-white/30 backdrop-blur-md border border-white/40 overflow-hidden shadow-xl sm:rounded-lg p-0 relative h-full flex flex-col">
                <div class="h-full flex flex-col overflow-y-auto">
                    <div class="flex-1 flex flex-col p-6 min-w-[260px] max-w-2xl mx-auto w-full">
                        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                            @livewire('profile.update-profile-information-form')
                            <x-section-border />
                        @endif
                        <div class="mt-10 sm:mt-0">
                            @livewire('profile.update-password-form')
                        </div>
                        <x-section-border />
                        <div class="mt-10 sm:mt-0">
                            @livewire('profile.two-factor-authentication-form')
                        </div>
                        <x-section-border />
                        <div class="mt-10 sm:mt-0">
                            @livewire('profile.logout-other-browser-sessions-form')
                        </div>
                        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                            <x-section-border />
                            <div class="mt-10 sm:mt-0">
                                @livewire('profile.delete-user-form')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
