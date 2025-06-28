<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Security: Force HTTPS and prevent mixed content -->
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
        <meta http-equiv="X-Content-Type-Options" content="nosniff">
        <meta http-equiv="X-XSS-Protection" content="1; mode=block">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <!-- Eagle Main Background Overlay (only on white background) -->
        <div class="min-h-screen bg-white relative">
            <div class="pointer-events-none absolute inset-0 z-0 select-none">
                <img src="/images/eagle.jpg" alt="Eagle" class="w-full h-full object-cover opacity-30" draggable="false" />
            </div>

            <x-banner />

            <div class="relative z-10 pt-16">
                @livewire('navigation-menu')

                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            </div>
            
            <!-- Footer - positioned below viewport -->
            <div class="relative z-10">
                <x-footer />
            </div>
        </div>

        @stack('modals')

        <script>window.livewireScriptConfig = { alpine: true }</script>
        @livewireScripts
    </body>
</html>
