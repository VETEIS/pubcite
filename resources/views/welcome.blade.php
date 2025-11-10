<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-turbo="false">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="turbo-visit-control" content="reload">
        <meta name="turbo-cache-control" content="no-preview">

        <title>{{ config('app.name', 'PubCite') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Preload hero art for faster paint - desktop only -->
        <link rel="preload" as="image" href="/images/art.webp" media="(min-width: 641px)" fetchpriority="high">
        
        <!-- Preload mobile hero logo to prevent layout shifts - mobile only -->
        <link rel="preload" as="image" href="/images/publication_logo.webp" media="(max-width: 640px)" fetchpriority="high">

        <!-- Scripts -->
            @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Styles -->
    <style>
        html { scroll-behavior: smooth; }
        [x-cloak] { display: none !important; }
        .hero-hidden { display: none !important; }
        
        /* Mobile-specific responsive design */
        @media (max-width: 640px) {
            /* Hero section full viewport */
            .main-content {
                padding-top: 0 !important;
            }
            #hero {
                height: 100vh !important;
                height: 100dvh !important;
                min-height: 100vh !important;
                min-height: 100dvh !important;
                padding: 0 !important;
            }
            
            /* Hide elements on mobile */
            .mobile-hidden {
                display: none !important;
            }
            
            /* Mobile-only orientation lock */
            body {
                orientation: portrait !important;
            }
            
            /* Mobile journal group adjustments */
            .journal-group {
                gap: 0.25rem !important;
                justify-content: center !important;
            }
            
            .journal-item {
                padding: 0.25rem !important;
            }
            
            .journal-circle {
                width: 4rem !important;
                height: 4rem !important;
            }
            
            .journal-image {
                width: 2.5rem !important;
                height: 2.5rem !important;
            }
            
            .journal-text {
                font-size: 0.75rem !important;
                line-height: 1.2 !important;
            }
            
            /* Mobile text truncation */
            .journal-text {
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                max-width: 100% !important;
            }
            
            /* Mobile hero logo above title */
            .mobile-hero-logo {
                display: block !important;
                margin: 0 auto 0.75rem auto !important;
                max-width: 280px !important;
                height: auto !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Mobile hero content fade-in effect */
            .mobile-hero-content[x-cloak] {
                display: none !important;
            }
            
            .mobile-hero-content {
                opacity: 0 !important;
                transform: translateY(20px) !important;
                transition: opacity 0.8s ease-out, transform 0.8s ease-out !important;
            }
            
            .mobile-hero-content.fade-in {
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
            
            /* Force mobile logo visibility with more specific selector */
            .text-center .mobile-hero-logo {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Hide navbar on mobile */
            nav {
                display: none !important;
            }
            
            /* Move scroll indicator to top on mobile */
            #scrollHint {
                position: fixed !important;
                top: 20px !important;
                left: 50% !important;
                bottom: auto !important;
                transform: translateX(-50%) !important;
                z-index: 1000 !important;
            }
        }
        
        /* Desktop text display (default) - no truncation needed */
        
        /* Hide mobile hero logo on desktop */
        .mobile-hero-logo {
            display: none !important;
        }
        
        /* Privacy modal overlay - content remains visible */
        .privacy-pending .privacy-modal { display: block !important; }
        
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        .hero-shape {
            position: absolute;
            border-radius: 9999px;
            filter: blur(20px);
            opacity: 0;
            will-change: transform, opacity;
            pointer-events: none;
        }
        .hero-shape.shape-1 { 
            width: 240px; height: 240px; background: #ef4444; 
            top: -40px; left: -40px; 
            animation: drift1 18s .2s infinite; 
        }
        .hero-shape.shape-2 { 
            width: 320px; height: 320px; background: #f59e0b; 
            bottom: -60px; right: 10%; 
            animation: drift2 22s .8s infinite; 
        }
        .hero-shape.shape-3 { 
            width: 200px; height: 200px; background: #10b981; 
            top: 10%; right: -60px; 
            animation: drift3 20s 1.2s infinite; 
        }
        @keyframes drift1 {
            0%   { transform: translate(0, 0) scale(1); }
            25%  { transform: translate(35vw, -10vh) scale(1.04); }
            50%  { transform: translate(12vw, 18vh) scale(1.01); }
            75%  { transform: translate(-22vw, -8vh) scale(0.98); }
            100% { transform: translate(0, 0) scale(1); }
        }
        @keyframes drift2 {
            0%   { transform: translate(0, 0) scale(1); }
            20%  { transform: translate(-30vw, 12vh) scale(1.02); }
            45%  { transform: translate(28vw, -20vh) scale(1.05); }
            70%  { transform: translate(-18vw, 14vh) scale(0.99); }
            100% { transform: translate(0, 0) scale(1); }
        }
        @keyframes drift3 {
            0%   { transform: translate(0, 0) scale(1); }
            30%  { transform: translate(22vw, 16vh) scale(1.03); }
            55%  { transform: translate(-28vw, -14vh) scale(0.97); }
            85%  { transform: translate(10vw, -18vh) scale(1.02); }
            100% { transform: translate(0, 0) scale(1); }
        }

        #scroll-left,
        #scroll-right,
        #scroll-left-researchers,
        #scroll-right-researchers {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px) !important;
            border: 2px solid rgba(156, 163, 175, 0.3) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            transition: all 0.3s ease !important;
            z-index: 20 !important;
        }
        
        #publishers-container,
        #researchers-container {
            padding: 2rem 1rem !important;
        }
        
        #researchers-container {
            padding-left: 2rem !important;
            padding-right: 2rem !important;
        }
        
        .researcher-card {
            margin: 0.5rem;
        }
        

        #scroll-left,
        #scroll-right,
        #scroll-left-researchers,
        #scroll-right-researchers { 
            transition: transform .2s ease; 
            transform-origin: center;
        }
        #scroll-left:hover,
        #scroll-right:hover,
        #scroll-left-researchers:hover,
        #scroll-right-researchers:hover { 
            transform: translateY(-50%) scale(1.15); 
        }
        
        #backToTop { 
            position: fixed; bottom: 1.25rem; right: 1.25rem; z-index: 60; 
            opacity: 0; transform: translateY(8px); 
            transition: opacity .25s ease, transform .25s ease; 
            pointer-events: none; 
        }
        #backToTop.show { 
            opacity: 1; transform: translateY(0); 
            pointer-events: auto; 
        }

        #scrollHint { 
            position: fixed; left: 50%; bottom: 2.5rem; z-index: 70; 
            transform: translateX(-50%) translateY(8px); 
            opacity: 0; transition: opacity .3s ease, transform .3s ease; 
            pointer-events: none; will-change: transform, opacity; 
        }
        #scrollHint.show { 
            opacity: 1; transform: translateX(-50%) translateY(0); 
        }
        @keyframes hintBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(6px); }
        }

        #scrollHint .mouse {
            width: 22px; height: 34px; 
            border: 2px solid rgba(255,255,255,0.85);
            border-radius: 14px; position: relative;
        }
        #scrollHint .wheel {
            width: 3px; height: 7px; 
            background: rgba(255,255,255,0.9); border-radius: 2px;
            position: absolute; left: 50%; top: 7px; 
            transform: translateX(-50%);
            animation: wheelSlide 1.6s ease-in-out infinite;
        }
        @keyframes wheelSlide {
            0%   { transform: translateX(-50%) translateY(0); opacity: 1; }
            70%  { transform: translateX(-50%) translateY(12px); opacity: 0.2; }
            100% { opacity: 0; }
        }
        #scrollHint .chev { 
            width: 18px; height: 18px; 
            color: rgba(255,255,255,0.85); 
            animation: chevFade 1.8s ease-in-out infinite; 
        }
        #scrollHint .chev.chev-2 { 
            opacity: .5; animation-delay: .25s; 
        }
        @keyframes chevFade {
            0%   { opacity: .1; transform: translateY(-2px); }
            30%  { opacity: .7; }
            60%  { opacity: .4; transform: translateY(2px); }
            100% { opacity: .1; transform: translateY(6px); }
        }

        .hero-title { 
            letter-spacing: -0.01em; 
            text-shadow: 0 4px 22px rgba(255,255,255,0.25); 
            display: inline-block; position: relative; 
        }
        .hero-title::after { 
            content: ""; display: block; height: 3px; 
            border-radius: 9999px; margin-top: 10px; 
            background: linear-gradient(to right, rgba(255,255,255,0), rgba(255,255,255,0.9), rgba(255,255,255,0)); 
        }

        #hero { contain: paint; }
        .scroll-target { scroll-margin-top: 5rem; }

        .reveal-section { 
            opacity: 0; transform: translateY(16px); 
            transition: opacity .5s ease, transform .5s ease; 
        }
        .reveal-section.visible { 
            opacity: 1; transform: translateY(0); 
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .calendar-details-container {
            overflow-x: hidden; overflow-y: auto;
        }
        
        .hero-art-glow {
            filter: drop-shadow(0 0 1px rgba(255,255,255,0.8)) drop-shadow(0 0 4px rgba(255,255,255,0.3)) drop-shadow(0 15px 40px rgba(0,0,0,0.25));
            transition: filter 0.3s ease, transform 0.3s ease;
        }
        
        a.group:hover .hero-art-glow,
        .group:hover .hero-art-glow {
            filter: drop-shadow(0 0 3px rgba(255,255,255,1)) drop-shadow(0 0 12px rgba(255,255,255,0.7)) drop-shadow(0 0 24px rgba(255,255,255,0.5)) drop-shadow(0 0 32px rgba(255,255,255,0.3)) drop-shadow(0 25px 60px rgba(0,0,0,0.4));
            transform: scale(1.08);
        }
        
        [x-cloak] {
            display: none !important;
        }
        
        
        .group:hover . {
            animation-play-state: paused;
        }
        
        .journal-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            z-index: 10;
            min-width: 2.5rem;
            text-align: center;
        }
        
        .marked-date-card {
            margin: 0.5rem 0; transform-origin: center;
        }

        .glassmorphism-modal {
            position: fixed; inset: 0; z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            padding: 1rem; background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
            opacity: 0; visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .glassmorphism-modal.show {
            opacity: 1; visibility: visible;
        }

        .glassmorphism-card {
            position: relative; max-width: 60vw; width: 60vw; max-height: 90vh;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.3);
            overflow: hidden;
        }

        .glassmorphism-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
            animation: liquidFlow 8s ease-in-out infinite;
            background-size: 200% 200%;
        }

        .glassmorphism-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            border-radius: 24px;
            pointer-events: none;
            z-index: 2;
        }

        .glassmorphism-content {
            position: relative;
            z-index: 3;
            padding: 2rem;
            color: rgba(255, 255, 255, 0.95);
            overflow-y: auto;
            max-height: calc(90vh - 4rem);
        }

        .glassmorphism-content h2,
        .glassmorphism-content h3 {
            color: rgba(255, 255, 255, 0.95);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .glassmorphism-content p {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .glassmorphism-content .bg-white\/20 p {
            color: inherit;
        }

        .glassmorphism-button {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.95);
            transition: all 0.3s ease;
        }

        .glassmorphism-button:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .glassmorphism-button.secondary {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .glassmorphism-button.secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        @keyframes liquidFlow {
            0%, 100% { background-position: 0% 50%; background-size: 200% 200%; }
            50% { background-position: 100% 50%; background-size: 200% 200%; }
        }

        body.modal-open {
            overflow: hidden;
            padding-right: 0 !important;
            position: fixed;
            width: 100%;
        }
        html { overflow-y: scroll; }

        .glassmorphism-content::-webkit-scrollbar { width: 6px; }
        .glassmorphism-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1); border-radius: 3px; 
        }
        .glassmorphism-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3); border-radius: 3px; 
        }
        .glassmorphism-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
    </head>
    <body style="background: linear-gradient(135deg, #7c2d12 0%, #991b1b 50%, #7c2d12 100%);">
        <div class="min-h-screen relative font-sans text-gray-900 antialiased" style="background: linear-gradient(135deg, #7c2d12 0%, #991b1b 50%, #7c2d12 100%);">

            <!-- Privacy Modal -->
            <div id="privacy-modal" class="fixed inset-0 bg-black/20 backdrop-blur-sm overflow-y-auto h-full w-full z-[9999] transition-opacity duration-300 {{ session('privacy_accepted') ? 'opacity-0 pointer-events-none' : 'opacity-100' }}">
                <div class="min-h-screen flex items-center justify-center p-4">
                    <div class="w-full max-w-2xl">
                        <!-- Main Card - Compact Layout -->
                        <div class="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl overflow-hidden border border-white/20 transform transition-all duration-300 {{ session('privacy_accepted') ? 'scale-95 opacity-0' : 'scale-100 opacity-100' }}">
                            <div class="flex flex-col lg:flex-row">
                                <!-- Left Side - Header Only -->
                                <div class="bg-gradient-to-br from-maroon-600 to-maroon-700 px-6 py-6 flex flex-col items-center justify-center text-center lg:w-2/5 relative overflow-hidden">
                                    <!-- Hero Art Background -->
                                    <div class="absolute inset-0 pointer-events-none">
                                        <img src="/images/privacy_art.webp" alt="Hero Art" class="w-full h-full object-cover opacity-30" />
                                    </div>
                                    <div class="relative z-10">
                                        <h1 class="text-2xl font-bold text-white whitespace-nowrap">PubCite</h1>
                                        <h2 class="text-lg font-bold text-white mb-1 whitespace-nowrap">Data Privacy Agreement</h2>
                                        <p class="text-maroon-100 text-xs whitespace-nowrap">University of Southeastern Philippines</p>
                                        <p class="text-maroon-100 text-xs whitespace-nowrap">Publication Unit System 2025</p>
                                    </div>
                                </div>

                                <!-- Right Side - Content & Actions -->
                                <div class="px-6 py-6 flex flex-col justify-center lg:w-3/5">
                                    <div class="mb-6">
                                         <p class="text-gray-700 leading-relaxed mb-4 text-sm text-justify" style="text-indent: 2em;">
                                             By continuing, you agree to the
                                            <a href="https://www.usep.edu.ph/usep-data-privacy-statement/" 
                                               target="_blank" 
                                               class="text-maroon-600 hover:text-maroon-700 underline font-semibold transition-colors duration-200">
                                                DATA PRIVACY
                                            </a> 
                                            statement of the University of Southeastern Philippines and acknowledge that your personal information will be collected and processed in accordance with the Data Privacy Act of 2012 (R.A. 10173).
                                        </p>
                                        <div class="bg-maroon-50 border border-maroon-200 rounded-lg p-3">
                                            <div class="flex items-start space-x-2">
                                                <svg class="w-4 h-4 text-maroon-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <p class="text-xs text-maroon-800 font-medium">
                                                    This agreement is required to access the application. You can review the complete privacy policy by clicking the link above.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex flex-col sm:flex-row gap-3">
                                        <button type="button" id="privacy-modal-decline" class="flex-1 bg-gray-100 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300/50 transition-all duration-200 font-medium text-sm">
                                            <div class="flex items-center justify-center space-x-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                <span>Decline</span>
                                            </div>
                                        </button>
                                        <button type="button" id="privacy-modal-accept" class="flex-1 bg-gradient-to-r from-maroon-600 to-maroon-700 text-white py-3 px-4 rounded-lg hover:from-maroon-700 hover:to-maroon-800 focus:outline-none focus:ring-2 focus:ring-maroon-500/50 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02] text-sm">
                                            <div class="flex items-center justify-center space-x-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span>Accept</span>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- How It Works Modal -->
            <div id="howItWorksModal" class="glassmorphism-modal hidden">
                <div class="glassmorphism-card">
                    <div class="glassmorphism-content">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl sm:text-3xl font-bold">How PubCite Works</h2>
                            <button onclick="hideHowItWorks()" class="glassmorphism-button p-2 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
            </div>

                        <div class="space-y-8">
                            <!-- Step 1 -->
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-maroon-600 text-white rounded-full flex items-center justify-center font-bold text-lg">1</div>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Sign In with Your USeP Account</h3>
                                    <p class="text-gray-600">Use your USeP Google account (@usep.edu.ph) to securely access the system. No need to create a separate account!</p>
                                </div>
                            </div>
                            
                            <!-- Step 2 -->
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-maroon-600 text-white rounded-full flex items-center justify-center font-bold text-lg">2</div>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Choose Your Incentive Type</h3>
                                    <p class="text-gray-600">Select between <strong>Publication Incentives</strong> (for published research papers) or <strong>Citation Incentives</strong> (for citations received).</p>
                                </div>
                            </div>
                            
                            <!-- Step 3 -->
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-maroon-600 text-white rounded-full flex items-center justify-center font-bold text-lg">3</div>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Upload Your Documents</h3>
                                    <p class="text-gray-600">Attach your research papers, publication proofs, or citation records. The system securely stores and submit all your documents for review.</p>
                                </div>
                            </div>
                            
                            <!-- Step 4 -->
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-maroon-600 text-white rounded-full flex items-center justify-center font-bold text-lg">4</div>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Track Your Application</h3>
                                    <p class="text-gray-600">Monitor your application progress in real-time through your dashboard. Get instant updates when your application is reviewed or approved.</p>
                                </div>
                            </div>
                            
                            <!-- Step 5 -->
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-maroon-600 text-white rounded-full flex items-center justify-center font-bold text-lg">5</div>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Receive Your Incentive</h3>
                                    <p class="text-gray-600">Once approved, download your official documents and receive your publication or citation incentive through the university's standard process.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8 grid md:grid-cols-2 gap-6">
                            <div class="p-6 bg-white/10 backdrop-blur-sm rounded-xl border border-white/30 shadow-lg">
                                <h4 class="text-lg font-semibold text-white mb-2">📚 Publication Incentives</h4>
                                <p class="text-gray-900 text-sm">For faculty who have published research papers in indexed journals. Submit your publication details and supporting documents.</p>
                            </div>
                            
                            <div class="p-6 bg-white/10 backdrop-blur-sm rounded-xl border border-white/30 shadow-lg">
                                <h4 class="text-lg font-semibold text-white mb-2">📊 Citation Incentives</h4>
                                <p class="text-gray-900 text-sm">For researchers whose work has been cited by other scholars. Provide citation records and impact metrics.</p>
                            </div>
                        </div>
                        
                        <div class="mt-6 p-6 bg-white/10 backdrop-blur-sm rounded-xl border border-white/30 shadow-lg">
                            <h4 class="text-lg font-semibold text-white mb-2">✍️ Signatory Feature</h4>
                            <p class="text-gray-900">For signatories, download documents for external signing and upload the signed versions. You can revert signed documents within 24 hours if needed.</p>
                        </div>
                        
                        <div class="mt-6 p-6 bg-white/10 backdrop-blur-sm rounded-xl border border-white/30 shadow-lg">
                            <h4 class="text-lg font-semibold text-white mb-2">🔒 Secure & Private</h4>
                            <p class="text-gray-900">Your documents are stored securely with encryption, and only authorized personnel can access your applications. All file operations are logged for audit purposes.</p>
                        </div>
                        
                        <div class="mt-8 pt-8 border-t border-white/20">
                            <div class="text-center">
                                <h3 class="text-xl font-bold mb-4">Ready to Get Started?</h3>
                                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 font-semibold rounded-lg bg-maroon-700 hover:bg-maroon-800 text-white transition-colors border border-maroon-500 hover:border-maroon-600 shadow-lg hover:shadow-xl">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                        </svg>
                                        Sign In with Google
                                    </a>
                                </div>
                                <p class="text-sm text-white/80 mt-3 text-center">
                                    Use your USeP Google account to access the system
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Navigation -->
            <nav class="bg-maroon-800/95 backdrop-blur-sm border-b border-maroon-900 fixed top-0 left-0 w-full z-50 shadow-lg">
                <div class="px-6">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center gap-3">
                            <img src="/images/usep.png" alt="USEP Logo" class="h-10 w-10 object-contain rounded-full" />
                            <span class="text-white text-lg font-semibold tracking-wide whitespace-nowrap">PubCite</span>
                            @if (\Illuminate\Support\Facades\View::exists('components.breadcrumbs'))
                            <span class="flex items-center justify-center ml-2">
                                    @include('components.breadcrumbs', ['crumbs' => $breadcrumbs ?? null, 'inline' => true])
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 sm:gap-4" x-data="{ 
                            quickLinksOpen: false, 
                            announcementsOpen: false,
                            toggleQuickLinks() {
                                this.quickLinksOpen = !this.quickLinksOpen;
                                if (this.quickLinksOpen) this.announcementsOpen = false;
                            },
                            toggleAnnouncements() {
                                this.announcementsOpen = !this.announcementsOpen;
                                if (this.announcementsOpen) {
                                    this.quickLinksOpen = false;
                                    window.loadAnnouncements();
                                }
                            },
                            closeAll() {
                                this.quickLinksOpen = false;
                                this.announcementsOpen = false;
                            }
                        }" @click.away="closeAll()">

                        <!-- Quick Links Dropdown -->
                        <div class="relative mobile-hidden">
                            <button @click="toggleQuickLinks()" type="button" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg font-semibold text-sm text-white/90 hover:text-white hover:bg-white/10 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-1.414 1.414a4 4 0 01-5.656 0l-1.414-1.414a4 4 0 010-5.656M10.172 13.828a4 4 0 010-5.656l1.414-1.414a4 4 0 015.656 0l1.414 1.414a4 4 0 010 5.656" /></svg>
                                Quick Links
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="quickLinksOpen" x-cloak class="absolute right-0 mt-2 w-64 bg-white/95 backdrop-blur border border-white/40 rounded-lg shadow-xl overflow-hidden z-50">
                                <div class="py-1">
                                    <a href="https://journal.usep.edu.ph/index.php/Southeastern_Philippines_Journal/index" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 px-3 py-2 text-sm text-maroon-900 hover:bg-maroon-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7" /><path stroke-linecap="round" stroke-linejoin="round" d="M16 3v4M8 3v4M4 11h16" /></svg>
                                        <span>SPJRD</span>
                                    </a>
                                    <a href="https://docs.google.com/spreadsheets/d/1bwf9eZvtI5HO7w0HdMRDujQULfdwKJNU4Ieb535sUdk/edit?gid=451510018#gid=451510018" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 px-3 py-2 text-sm text-maroon-900 hover:bg-maroon-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7" /><path stroke-linecap="round" stroke-linejoin="round" d="M16 3v4M8 3v4M4 11h16" /></svg>
                                        <span>Scopus (Suggested Journals)</span>
                                    </a>
                                    <a href="https://docs.google.com/spreadsheets/d/1_54NTUdRE4y9QVB01p9SHF_cEPllajyyM3siyBFWfRs/edit?gid=451510018#gid=451510018" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 px-3 py-2 text-sm text-maroon-900 hover:bg-maroon-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20l9-5-9-5-9 5 9 5zm0 0V4" /></svg>
                                        <span>Web of Science (Suggested)</span>
                                    </a>
                                    <a href="https://docs.google.com/spreadsheets/d/1XT-2QD6ZYK4Vl5JPWGoDAFFGu0j6SYXhxQbcvidIrAI/edit?gid=572855311#gid=572855311" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 px-3 py-2 text-sm text-maroon-900 hover:bg-maroon-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8" /></svg>
                                        <span>ACI (Suggested)</span>
                                    </a>
                                    <a href="https://docs.google.com/spreadsheets/d/1qeRfbWQVB2fodnirzIK5Znql5nliLAPVtK4xXRS5xSY/edit?gid=451510018#gid=451510018" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 px-3 py-2 text-sm text-maroon-900 hover:bg-maroon-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2" /><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8" /></svg>
                                        <span>Peer Reviewed</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Announcements Dropdown -->
                        <div class="relative mobile-hidden">
                            <button @click="toggleAnnouncements()" type="button" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg font-semibold text-sm text-white/90 hover:text-white hover:bg-white/10 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                Announcements
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="announcementsOpen" x-cloak class="absolute right-0 mt-2 w-80 bg-white/95 backdrop-blur border border-white/40 rounded-lg shadow-xl overflow-hidden z-50">
                                <div class="py-1">
                                    <div class="px-3 py-2 border-b border-gray-100">
                                        <h4 class="text-sm font-semibold text-maroon-900">Latest Updates</h4>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto" id="announcements-content">
                                        <!-- Content will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navbar actions -->
                        <div class="hidden md:flex items-center gap-2">
                            @guest
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg font-semibold text-sm text-white bg-white/10 border border-white/20 hover:bg-white/20 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H3m6-6l-6 6 6 6M12 3h6a2 2 0 012 2v14a2 2 0 01-2 2h-6" />
                                </svg>
                                Sign In
                            </a>
                            @endguest
                                </div>

                        @if (\Illuminate\Support\Facades\Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg font-semibold text-sm text-white bg-white/10 border border-white/20 hover:bg-white/20 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75L12 3l9 6.75V20a1.5 1.5 0 01-1.5 1.5H15a1.5 1.5 0 01-1.5-1.5v-4.5H10.5V20A1.5 1.5 0 019 21.5H4.5A1.5 1.5 0 013 20V9.75z" />
                                    </svg>
                                    Dashboard
                                </a>
                            @endauth
                        @endif
                            </div>
                        </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="main-content relative z-10 pt-8 sm:pt-16 bg-transparent" x-cloak x-init="$el.removeAttribute('x-cloak')">
                <main>
                <!-- Hero Section -->
                <section id="hero" class="relative overflow-hidden bg-gradient-to-br from-maroon-900 via-maroon-800 to-maroon-700 py-8 sm:py-16 flex items-center" style="min-height: 100vh; min-height: calc(100vh - 4rem); min-height: calc(100dvh - 4rem);" x-cloak x-init="$el.removeAttribute('x-cloak')">
                    <div class="hero-shape"></div>
                    <div class="hero-shape"></div>
                    <div class="hero-shape"></div>
                    <div class="hero-shape"></div>
                    <div class="hero-shape"></div>
                    <div class="max-w-7xl mx-auto px-6 relative z-10">
                        <div class="grid md:grid-cols-2 gap-8 items-center">
                            <div class="text-center">
                                <!-- Mobile-only hero logo -->
                                <img src="/images/publication_logo.webp" alt="Publication Logo" class="mobile-hero-logo" id="mobile-hero-logo">
                                
                                <!-- Mobile hero content that fades in after logo loads -->
                                <div class="mobile-hero-content" id="mobile-hero-content" x-cloak x-init="$el.removeAttribute('x-cloak')">
                                    <h1 class="hero-title text-2xl sm:text-4xl md:text-6xl font-extrabold text-white mb-6 leading-tight tracking-tight">
                                        USeP Publication Unit
                                    </h1>
                                <h2 class="text-sm sm:text-xl md:text-2xl font-normal text-white/90 mb-8 leading-relaxed">
                                    Suggested List of Indexed University Journals
                                </h2>
                                
                                <div class="mb-6">
                                    <!--<h3 class="text-md font-medium text-white mb-3">Suggested Journals:</h3>-->
                                    <div class="journal-group flex gap-1 justify-center items-stretch sm:gap-2">
                                        <a href="https://docs.google.com/spreadsheets/d/1bwf9eZvtI5HO7w0HdMRDujQULfdwKJNU4Ieb535sUdk/edit?gid=451510018#gid=451510018" target="_blank" rel="noopener noreferrer" class="journal-item group flex-1 h-24 sm:h-36 flex flex-col items-center justify-center gap-1 hover:scale-105 transition-all duration-300 min-w-0 px-1">
                                            <div class="journal-circle relative w-16 h-16 sm:w-32 sm:h-32 rounded-full bg-white backdrop-blur-sm border border-white/20 shadow-lg transition-all duration-300 hover:bg-white/20 hover:border-white/30" style="aspect-ratio: 1/1;">
                                                <div class="journal-badge" id="scopus-count" data-target="{{ (int) \App\Models\Setting::get('scopus_publications_count', '0') }}">0</div>
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <img src="/images/scopus.webp" alt="Scopus" class="journal-image h-10 w-10 sm:h-24 sm:w-24 object-contain filter drop-shadow-lg group-hover:drop-shadow-2xl transition-all duration-300" />
                                                </div>
                                            </div>
                                            <span class="journal-text text-white font-medium text-xs text-center leading-tight">Scopus</span>
                                        </a>
                                        
                                        <a href="https://docs.google.com/spreadsheets/d/1_54NTUdRE4y9QVB01p9SHF_cEPllajyyM3siyBFWfRs/edit?gid=451510018#gid=451510018" target="_blank" rel="noopener noreferrer" class="journal-item group flex-1 h-24 sm:h-36 flex flex-col items-center justify-center gap-1 hover:scale-105 transition-all duration-300 min-w-0 px-1">
                                            <div class="journal-circle relative w-16 h-16 sm:w-32 sm:h-32 rounded-full bg-white backdrop-blur-sm border border-white/20 shadow-lg transition-all duration-300 hover:bg-white/20 hover:border-white/30" style="aspect-ratio: 1/1;">
                                                <div class="journal-badge" id="wos-count" data-target="{{ (int) \App\Models\Setting::get('wos_publications_count', '0') }}">0</div>
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <img src="/images/wos.webp" alt="Web of Science" class="journal-image h-10 w-10 sm:h-24 sm:w-24 object-contain filter drop-shadow-lg group-hover:drop-shadow-2xl transition-all duration-300" />
                                                </div>
                                            </div>
                                            <span class="journal-text text-white font-medium text-xs text-center leading-tight">Web of Science</span>
                                        </a>
                                        
                                        <a href="https://docs.google.com/spreadsheets/d/1XT-2QD6ZYK4Vl5JPWGoDAFFGu0j6SYXhxQbcvidIrAI/edit?gid=572855311#gid=572855311" target="_blank" rel="noopener noreferrer" class="journal-item group flex-1 h-24 sm:h-36 flex flex-col items-center justify-center gap-1 hover:scale-105 transition-all duration-300 min-w-0 px-1">
                                            <div class="journal-circle relative w-16 h-16 sm:w-32 sm:h-32 rounded-full bg-white backdrop-blur-sm border border-white/20 shadow-lg transition-all duration-300 hover:bg-white/20 hover:border-white/30" style="aspect-ratio: 1/1;">
                                                <div class="journal-badge" id="aci-count" data-target="{{ (int) \App\Models\Setting::get('aci_publications_count', '0') }}">0</div>
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <img src="/images/aci.webp" alt="ACI" class="journal-image h-8 w-8 sm:h-20 sm:w-20 object-contain filter drop-shadow-lg group-hover:drop-shadow-2xl transition-all duration-300" />
                                                </div>
                                            </div>
                                            <span class="journal-text text-white font-medium text-xs text-center leading-tight">Scopus-ACI</span>
                                        </a>
                                        
                                        <a href="https://docs.google.com/spreadsheets/d/1qeRfbWQVB2fodnirzIK5Znql5nliLAPVtK4xXRS5xSY/edit?gid=451510018#gid=451510018" target="_blank" rel="noopener noreferrer" class="journal-item group flex-1 h-24 sm:h-36 flex flex-col items-center justify-center gap-1 hover:scale-105 transition-all duration-300 min-w-0 px-1">
                                            <div class="journal-circle relative w-16 h-16 sm:w-32 sm:h-32 rounded-full bg-white backdrop-blur-sm border border-white/20 shadow-lg transition-all duration-300 hover:bg-white/20 hover:border-white/30" style="aspect-ratio: 1/1;">
                                                <div class="journal-badge" id="peer-count" data-target="{{ (int) \App\Models\Setting::get('peer_publications_count', '0') }}">0</div>
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <img src="/images/peer.webp" alt="Peer Review" class="journal-image h-10 w-10 sm:h-24 sm:w-24 object-contain filter drop-shadow-lg group-hover:drop-shadow-2xl transition-all duration-300" />
                                                </div>
                                            </div>
                                            <span class="journal-text text-white font-medium text-xs text-center leading-tight">Peer Reviewed</span>
                                        </a>
                                    </div>
                                </div>
                                @guest
                                    <div class="flex flex-col sm:flex-row gap-4 items-center sm:items-start max-w-3xl">
                                        <button onclick="showHowItWorks()" class="mobile-hidden inline-flex items-center justify-center px-8 py-4 border-2 border-white/30 text-white font-semibold rounded-full hover:bg-white/20 hover:-translate-y-1 transition-all duration-300 flex-1">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                            See How It Works
                                        </button>
                                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-maroon-900 font-semibold rounded-full hover:bg-maroon-50 hover:-translate-y-1 transition-all duration-300 shadow-lg hover:shadow-xl flex-1">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                            Apply For Incentives
                                        </a>
                                    </div>
                                @else
                                    <div class="flex flex-col sm:flex-row gap-4 items-start">
                                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur border border-white/30 text-white font-semibold rounded-lg hover:bg-white/30 transition duration-200">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 0 18 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            You're logged in as {{ \Illuminate\Support\Facades\Auth::user()->name }}
                                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full {{ \Illuminate\Support\Facades\Auth::user()->role === 'admin' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white' }}">
                                                {{ \Illuminate\Support\Facades\Auth::user()->role === 'admin' ? 'Admin' : 'User' }}
                                            </span>
                                        </a>
                                             </div>
                                @endguest
                                </div> <!-- End mobile-hero-content -->
                                         </div>
                            <div class="mobile-hidden lg:flex justify-end relative pointer-events-none" aria-hidden="true">
                                <div class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 pointer-events-none" style="width: 60vh; height: 60vh; background: radial-gradient(closest-side, rgba(255,255,255,0.28), rgba(255,255,255,0.12), transparent 70%); filter: blur(16px); border-radius: 9999px;"></div>
                                <a href="https://journal.usep.edu.ph/index.php/Southeastern_Philippines_Journal/index" target="_blank" rel="noopener noreferrer" class="cursor-pointer transition-all duration-300 group pointer-events-auto">
                                    <img src="/images/art.webp" alt="Hero Art" class="h-[60vh] md:h-[70vh] w-auto object-contain select-none hero-art-glow transition-all duration-300" />
                                </a>
                                             </div>
                                         </div>
                                             </div>
                </section>

                <!-- Our Services Section -->
                <section id="services" class="py-12 bg-white/80 reveal-section scroll-target relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-maroon-300 to-transparent"></div>
                    
                    <div class="absolute inset-0 pointer-events-none opacity-15">
                        <svg class="w-full h-full" viewBox="0 0 1200 400" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="wave1" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#8b0000"/>
                                    <stop offset="100%" stop-color="#b22222"/>
                                </linearGradient>
                            </defs>
                            <path d="M0,200 Q300,150 600,200 T1200,200 L1200,400 L0,400 Z" fill="url(#wave1)"/>
                            <path d="M0,250 Q400,200 800,250 T1200,250 L1200,400 L0,400 Z" fill="url(#wave1)" opacity="0.7"/>
                        </svg>
                                         </div>
                    <div class="max-w-7xl mx-auto px-6">
                        <!-- Services Section -->
                        <div class="text-center mb-8">
                                    <div class="flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-maroon-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Our Services</h2>
                                             </div>
                                         </div>

                        <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-4 sm:p-8 shadow-lg border border-white/50">

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div class="flex flex-col items-center text-center p-4 sm:p-6 bg-white/50 rounded-xl border border-white/60 hover:bg-white/70 transition-all duration-300 hover:-translate-y-1">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                    </div>
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-3">Publication & Citation Incentives</h3>
                                            <p class="text-gray-600 leading-relaxed">Submit applications for publication and citation incentives to support your research contributions and academic achievements with streamlined processing.</p>
                            </div>

                                <div class="flex flex-col items-center text-center p-4 sm:p-6 bg-white/50 rounded-xl border border-white/60 hover:bg-white/70 transition-all duration-300 hover:-translate-y-1">
                                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                        </div>
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-3">Real-time Status Tracking</h3>
                                            <p class="text-gray-600 leading-relaxed">Monitor the progress of your submitted applications and requests with detailed status updates and transparent communication throughout the process.</p>
                                         </div>

                                <div class="flex flex-col items-center text-center p-4 sm:p-6 bg-white/50 rounded-xl border border-white/60 hover:bg-white/70 transition-all duration-300 hover:-translate-y-1">
                                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                             </div>
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-3">News & Events Hub</h3>
                                            <p class="text-gray-600 leading-relaxed">Stay informed with the latest news, upcoming events, workshops, and important announcements from the Publications Unit and research community.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- USeP Researchers Section -->
                <section class="py-12 bg-white/90 reveal-section relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-maroon-300 to-transparent"></div>
                    
                    <div class="absolute inset-0 pointer-events-none opacity-15">
                        <svg class="w-full h-full" viewBox="0 0 1200 400" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="wave2" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#c05050"/>
                                    <stop offset="100%" stop-color="#d16c6c"/>
                                </linearGradient>
                            </defs>
                            <path d="M0,180 Q250,130 500,180 T1200,180 L1200,400 L0,400 Z" fill="url(#wave2)"/>
                            <path d="M0,220 Q350,170 700,220 T1200,220 L1200,400 L0,400 Z" fill="url(#wave2)" opacity="0.6"/>
                                </svg>
                            </div>
                    <div class="max-w-7xl mx-auto px-6">
                        <div class="text-center mb-8">
                            <div class="flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-maroon-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">USeP Researchers</h2>
                            </div>
                        </div>

                        <div class="relative">
                            <button id="scroll-left-researchers" class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 bg-white/90 backdrop-blur border border-gray-200 rounded-full p-4 md:p-3 shadow-lg transition-transform duration-200 opacity-0" onclick="scrollResearchers('left')">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            
                            <button id="scroll-right-researchers" class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 bg-white/90 backdrop-blur border border-gray-200 rounded-full p-4 md:p-3 shadow-lg transition-transform duration-200 opacity-0" onclick="scrollResearchers('right')">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <!-- Researchers -->
                            <div id="researchers-container" class="flex gap-6 overflow-x-auto scrollbar-hide pb-4 pt-4 px-8 bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/50">
                                <!-- Loading state -->
                                <div id="researchers-loading" class="flex-shrink-0 w-full flex items-center justify-center py-12">
                                    <div class="text-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-maroon-600 mx-auto mb-4"></div>
                                        <p class="text-gray-600 text-sm">Loading researchers...</p>
                                    </div>
                                </div>
                                
                                <!-- Empty state -->
                                <div id="researchers-empty" class="flex-shrink-0 w-full flex items-center justify-center py-12 hidden">
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-600 text-sm">No researchers available at the moment.</p>
                                    </div>
                                </div>
                                
                                <!-- Error state -->
                                <div id="researchers-error" class="flex-shrink-0 w-full flex items-center justify-center py-12 hidden">
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-600 text-sm">Failed to load researchers.</p>
                                    </div>
                                </div>
                                
                                <!-- Dynamic researcher cards will be loaded here -->
                                    
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Calendar Section (JavaScript will replace this) -->
                <section class="py-12 bg-white/80 reveal-section relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-maroon-300 to-transparent"></div>
                    <div class="max-w-7xl mx-auto px-6">
                        <div class="text-center mb-8">
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Calendar</h2>
                </div>
                </div>
                </section>
        </main>

        <div class="relative z-30">
            <x-footer />
        </div>
        </div>
        </div>

        <!-- Back to Top Button -->
        <button id="backToTop" class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-maroon-600 text-white shadow-lg hover:bg-maroon-700 focus:outline-none" aria-label="Back to top">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
        </button>

        <!-- Scroll Down Hint -->
        <div id="scrollHint" class="flex items-center gap-3 text-white px-4 py-2 rounded-full border border-white/20 shadow-xl">
            <div class="mouse relative">
                <div class="wheel"></div>
            </div>
            <div class="flex items-center gap-1">
                <svg class="chev" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                <svg class="chev chev-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <span class="text-xs tracking-wide">Scroll</span>
        </div>
    </body>
</html>

<script>
function debounce(fn, delay) {
    let timer = null;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
    }



function scrollResearchers(direction) {
    const container = document.getElementById('researchers-container');
    const scrollLeft = document.getElementById('scroll-left-researchers');
    const scrollRight = document.getElementById('scroll-right-researchers');

    if (direction === 'left') {
        container.scrollBy({ left: -300, behavior: 'smooth' }); // Scroll by 300px
        if (container.scrollLeft <= 0) {
            scrollLeft.style.opacity = '0';
        } else {
            scrollLeft.style.opacity = '1';
        }
        if (container.scrollLeft > 0) {
            scrollRight.style.opacity = '1';
        }
    } else {
        container.scrollBy({ left: 300, behavior: 'smooth' }); // Scroll by 300px
        if (container.scrollLeft >= container.scrollWidth - container.clientWidth) {
            scrollRight.style.opacity = '0';
        } else {
            scrollRight.style.opacity = '1';
        }
        if (container.scrollLeft < container.scrollWidth - container.clientWidth) {
            scrollLeft.style.opacity = '1';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Mobile-only viewport height fix - desktop completely unaffected
    function setMobileViewportHeight() {
        if (window.innerWidth < 768) { // Mobile only
            const heroSection = document.getElementById('hero');
            if (heroSection) {
                const vh = window.innerHeight * 0.01;
                document.documentElement.style.setProperty('--vh', `${vh}px`);
                heroSection.style.minHeight = `calc(var(--vh, 1vh) * 100 - 4rem)`;
            }
        }
    }
    
    // Initialize mobile viewport height
    setMobileViewportHeight();
    
    // Update viewport height on mobile resize and orientation change
    window.addEventListener('resize', setMobileViewportHeight);
    window.addEventListener('orientationchange', function() {
        if (window.innerWidth < 768) { // Mobile only
            setTimeout(setMobileViewportHeight, 100);
        }
    });
    
    // Privacy Modal Logic - Robust against multiple user sessions
    const privacyModal = document.getElementById('privacy-modal');
    const privacyAcceptBtn = document.getElementById('privacy-modal-accept');
    const privacyDeclineBtn = document.getElementById('privacy-modal-decline');
    const heroSection = document.getElementById('hero');
    const body = document.body;

    // Initialize privacy modal state
    initializePrivacyModal();
    
    function initializePrivacyModal() {
        // Don't clear sessionStorage on page load - let it persist for login flow
        
        // Listen for storage changes (user logout/login in another tab)
        window.addEventListener('storage', handleStorageChange);
        
        // Check server-side privacy acceptance status
        checkPrivacyStatus();
    }
    
    function handleStorageChange(event) {
        // If privacy acceptance was cleared in another tab, recheck status
        if (event.key === 'privacy_accepted' && event.newValue === null) {
            checkPrivacyStatus();
        }
    }
    
    // Note: sessionStorage persists across page loads for login flow
    
    async function checkPrivacyStatus() {
        try {
            const response = await fetch('/privacy/status', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.privacy_accepted) {
                    // Privacy already accepted - set sessionStorage and hide modal
                    sessionStorage.setItem('privacy_accepted', 'true');
                    closePrivacyModal();
                    animateJournalCounts();
                    
                    // Preload announcements in the background
                    setTimeout(() => {
                        // Preload guest announcements (for guest layout)
                        if (window.guestAnnouncements && typeof window.guestAnnouncements.preload === 'function') {
                            window.guestAnnouncements.preload();
                        }
                        // Preload landing announcements (for welcome page)
                        if (window.landingAnnouncements && typeof window.landingAnnouncements.preload === 'function') {
                            window.landingAnnouncements.preload();
                        }
                    }, 100);
                } else {
                    // Privacy not accepted - show modal
                    showPrivacyModal();
                }
            } else {
                // Fallback: show modal if we can't determine status
                showPrivacyModal();
            }
        } catch (error) {
            // Fallback: show modal on error
            showPrivacyModal();
        }
    }

    // Privacy modal handlers
    function closePrivacyModal() {
        // Fade out the modal smoothly
        privacyModal.style.opacity = '0';
        privacyModal.style.pointerEvents = 'none';
        
        // Fade out the modal card
        const modalCard = privacyModal.querySelector('.bg-white\\/95');
        if (modalCard) {
            modalCard.style.transform = 'scale(0.95)';
            modalCard.style.opacity = '0';
        }
        
        // Hide modal after animation completes
        setTimeout(() => {
            privacyModal.classList.add('hidden');
        }, 300);
    }
    
    function showPrivacyModal() {
        // Show modal and start fade in
        privacyModal.classList.remove('hidden');
        privacyModal.style.opacity = '1';
        privacyModal.style.pointerEvents = 'auto';
        
        // Fade in the modal card
        const modalCard = privacyModal.querySelector('.bg-white\\/95');
        if (modalCard) {
            modalCard.style.transform = 'scale(1)';
            modalCard.style.opacity = '1';
        }
    }

    privacyAcceptBtn.addEventListener('click', async function() {
        // Disable button to prevent multiple clicks
        privacyAcceptBtn.disabled = true;
        privacyAcceptBtn.textContent = 'Accepting...';
        
        try {
            // Accept privacy on server-side
            const response = await fetch('/privacy/accept', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({accepted: true})
            });
            
            if (response.ok) {
                // Success - set both server session and client sessionStorage
                sessionStorage.setItem('privacy_accepted', 'true');
                closePrivacyModal();
                animateJournalCounts();
                
                // Preload announcements in the background
                setTimeout(() => {
                    // Preload guest announcements (for guest layout)
                    if (window.guestAnnouncements && typeof window.guestAnnouncements.preload === 'function') {
                        window.guestAnnouncements.preload();
                    }
                    // Preload landing announcements (for welcome page)
                    if (window.landingAnnouncements && typeof window.landingAnnouncements.preload === 'function') {
                        window.landingAnnouncements.preload();
                    }
                }, 100);
            } else {
                // Error - re-enable button and show error
                privacyAcceptBtn.disabled = false;
                privacyAcceptBtn.textContent = 'Accept';
                console.error('Failed to accept privacy policy');
            }
        } catch (error) {
            // Error - re-enable button
            privacyAcceptBtn.disabled = false;
            privacyAcceptBtn.textContent = 'Accept';
            console.error('Error accepting privacy policy:', error);
        }
    });

    privacyDeclineBtn.addEventListener('click', function() {
        // Redirect to external page or show message
        window.location.href = 'https://www.usep.edu.ph/';
    });


    // Animate journal count badges
    function animateJournalCounts() {
        const counterIds = ['scopus-count', 'wos-count', 'aci-count', 'peer-count'];
        
        const counters = counterIds.map(id => {
            const element = document.getElementById(id);
            if (!element) return null;
            
            // Get target value from data-target attribute (set from admin settings)
            const target = parseInt(element.getAttribute('data-target')) || 0;
            return { id, target, element };
        }).filter(c => c !== null);

        counters.forEach((counter, index) => {
            const element = counter.element;
            if (!element) return;

            const duration = 2000; // 2 seconds
            const startTime = Date.now() + (index * 200); // Stagger by 200ms each
            const target = counter.target;

            function updateCount() {
                const now = Date.now();
                const elapsed = now - startTime;
                
                if (elapsed < 0) {
                    requestAnimationFrame(updateCount);
                    return;
                }

                const progress = Math.min(elapsed / duration, 1);
                const easeOut = 1 - Math.pow(1 - progress, 3); // Ease out cubic
                const current = Math.floor(easeOut * target);

                element.textContent = current.toLocaleString();

                if (progress < 1) {
                    requestAnimationFrame(updateCount);
                }
            }

            updateCount();
        });
    }

    const researchersContainer = document.getElementById('researchers-container');
    const researchersScrollLeft = document.getElementById('scroll-left-researchers');
    const researchersScrollRight = document.getElementById('scroll-right-researchers');

    function updateResearchersScrollIndicators() {
        if (!researchersContainer || !researchersScrollLeft || !researchersScrollRight) return;
        
        const maxScrollLeft = researchersContainer.scrollWidth - researchersContainer.clientWidth;
        const isAtStart = researchersContainer.scrollLeft <= 2; // small tolerance
        const isAtEnd = researchersContainer.scrollLeft >= (maxScrollLeft - 2);
        const hasOverflow = researchersContainer.scrollWidth > researchersContainer.clientWidth;


        if (hasOverflow && !isAtStart) {
            researchersScrollLeft.style.opacity = '1';
            researchersScrollLeft.style.pointerEvents = 'auto';
        } else {
            researchersScrollLeft.style.opacity = '0';
            researchersScrollLeft.style.pointerEvents = 'none';
        }

        if (hasOverflow && !isAtEnd) {
            researchersScrollRight.style.opacity = '1';
            researchersScrollRight.style.pointerEvents = 'auto';
        } else {
            researchersScrollRight.style.opacity = '0';
            researchersScrollRight.style.pointerEvents = 'none';
        }
    }

    updateResearchersScrollIndicators();
    researchersContainer.addEventListener('scroll', updateResearchersScrollIndicators);
    window.addEventListener('resize', updateResearchersScrollIndicators);
    
    // Calendar
    try {
        const announcementsHeader = Array.from(document.querySelectorAll('h2')).find(el => el.textContent && el.textContent.trim() === 'Calendar');
        if (announcementsHeader) {
            const section = announcementsHeader.closest('section');
            if (section) {
                section.innerHTML = `
                    <!-- Modern section separator -->
                    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-maroon-300 to-transparent"></div>
                    
                    <!-- Subtle wave background pattern -->
                    <div class="absolute inset-0 pointer-events-none opacity-15">
                        <svg class="w-full h-full" viewBox="0 0 1200 400" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="wave5" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#b22222"/>
                                    <stop offset="100%" stop-color="#c05050"/>
                                </linearGradient>
                            </defs>
                            <path d="M0,120 Q300,70 600,120 T1200,120 L1200,400 L0,400 Z" fill="url(#wave5)"/>
                            <path d="M0,160 Q400,110 800,160 T1200,160 L1200,400 L0,400 Z" fill="url(#wave5)" opacity="0.3"/>
                        </svg>
                    </div>
                    
                    <div class="max-w-7xl mx-auto px-6">
                        <!-- Section Header -->
                        <div class="text-center mb-12">
                            <div class="flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-maroon-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">USeP Calendar</h2>
                            </div>
                        </div>

                        <!-- Single Calendar Card with Two Columns -->
                        <div class="bg-gradient-to-br from-white/80 to-white/60 backdrop-blur-sm rounded-2xl p-8 shadow-xl border border-white/50 relative overflow-hidden">
                            <!-- Decorative background elements -->
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-maroon-100/30 to-transparent rounded-full -translate-y-16 translate-x-16"></div>
                            <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-blue-100/30 to-transparent rounded-full translate-y-12 -translate-x-12"></div>
                            
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 relative z-10">
                                <!-- Left Column: Calendar -->
                                <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 border border-white/60 shadow-lg">
                                    <div id="calendarHeader" class="flex items-center justify-between mb-6">
                                        <div id="calendarMonth" class="text-xl font-bold text-gray-900"></div>
                                        <div class="flex items-center gap-2">
                                            <button class="p-2 rounded-lg bg-white/50 hover:bg-white/70 transition-colors border border-white/60">
                                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                                </svg>
                                            </button>
                                            <button class="p-2 rounded-lg bg-white/50 hover:bg-white/70 transition-colors border border-white/60">
                                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-gray-600 mb-3 select-none">
                                        <div class="p-2 rounded-lg bg-gray-50">Sun</div>
                                        <div class="p-2 rounded-lg bg-gray-50">Mon</div>
                                        <div class="p-2 rounded-lg bg-gray-50">Tue</div>
                                        <div class="p-2 rounded-lg bg-gray-50">Wed</div>
                                        <div class="p-2 rounded-lg bg-gray-50">Thu</div>
                                        <div class="p-2 rounded-lg bg-gray-50">Fri</div>
                                        <div class="p-2 rounded-lg bg-gray-50">Sat</div>
                                    </div>
                                    
                                    <div id="calendarGrid" class="grid grid-cols-7 gap-1 text-center text-sm"></div>
                                    
                                    <div id="calendarLegend" class="mt-6 pt-4 flex flex-wrap gap-4 text-xs text-gray-600 relative">
                                        <!-- Modern legend separator -->
                                        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-maroon-200 to-transparent"></div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block w-3 h-3 rounded-full bg-gradient-to-r from-blue-400 to-blue-500 shadow-sm"></span>
                                            <span>Today</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block w-3 h-3 rounded-full bg-gradient-to-r from-amber-400 to-amber-500 shadow-sm"></span>
                                            <span>Marked Date</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: Marked Dates -->
                                <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 border border-white/60 shadow-lg">
                                    <div class="flex items-center mb-4">
                                        <svg class="w-6 h-6 text-maroon-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h10" />
                                        </svg>
                                        <h3 class="text-xl font-bold text-gray-900">Marked Dates</h3>
                                    </div>
                                    <div class="max-h-[300px] calendar-details-container" id="calendarDetails">
                                        <div class="text-center text-gray-500 py-8">
                                            <div class="relative">
                                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <!-- Decorative dots around the calendar icon -->
                                                <div class="absolute -top-2 -right-2 w-3 h-3 bg-maroon-200 rounded-full"></div>
                                                <div class="absolute -bottom-2 -left-2 w-2 h-2 bg-blue-200 rounded-full"></div>
                                                <div class="absolute top-1/2 -right-4 w-1.5 h-1.5 bg-green-200 rounded-full"></div>
                                                <div class="absolute top-1/2 -left-4 w-1.5 h-1.5 bg-purple-200 rounded-full"></div>
                                            </div>
                                            <p class="text-sm font-medium text-gray-400">No marked dates for this month</p>
                                            <p class="text-xs text-gray-300 mt-1">Events will appear here when added by administrators</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Calendar state
                let currentDate = new Date();
                let currentYear = currentDate.getFullYear();
                let currentMonth = currentDate.getMonth();

                // Render calendar and details
                const monthEl = section.querySelector('#calendarMonth');
                const gridEl = section.querySelector('#calendarGrid');
                const detailsEl = section.querySelector('#calendarDetails');
                const marks = @json(json_decode(\App\Models\Setting::get('calendar_marks', '[]'), true));

                function renderCalendar() {
                    const today = new Date();
                    const firstDay = new Date(currentYear, currentMonth, 1);
                    const startDay = firstDay.getDay();
                    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
                    
                    monthEl.textContent = new Date(currentYear, currentMonth).toLocaleString('default', { month: 'long', year: 'numeric' });
                    gridEl.innerHTML = '';
                    
                    // Add padding for first day of month
                    for (let i = 0; i < startDay; i++) {
                        const pad = document.createElement('div');
                        pad.className = 'p-2';
                        gridEl.appendChild(pad);
                    }
                    
                    const parsedMarks = (Array.isArray(marks) ? marks : []).map(m => ({
                        date: m && m.date ? new Date(m.date + 'T00:00:00') : null,
                        note: m && m.note ? String(m.note) : ''
                    })).filter(m => m.date);
                    
                    const marksByDay = new Map();
                    parsedMarks.forEach(m => {
                        if (m.date.getFullYear() === currentYear && m.date.getMonth() === currentMonth) {
                            const d = m.date.getDate();
                            if (!marksByDay.has(d)) marksByDay.set(d, []);
                            marksByDay.get(d).push(m);
                        }
                    });
                    
                    for (let d = 1; d <= daysInMonth; d++) {
                        const cell = document.createElement('div');
                        cell.className = 'p-3 rounded-lg border border-white/60 bg-white/30 hover:bg-white/50 transition-all duration-200 cursor-pointer';
                        const isToday = d === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear();
                        const hasMarks = marksByDay.has(d);
                        
                        if (isToday && hasMarks) {
                            // Today is also a marked date - use half blue, half amber gradient
                            cell.className = 'p-3 rounded-lg bg-gradient-to-br from-blue-400 via-blue-300 to-amber-400 text-white font-bold shadow-lg hover:shadow-xl transition-all duration-200 cursor-pointer ring-2 ring-blue-200/50';
                            
                            // Add click event for marked dates
                            cell.addEventListener('click', () => {
                                const clickedDate = new Date(currentYear, currentMonth, d);
                                highlightMarkedDate(clickedDate);
                            });
                        } else if (isToday) {
                            cell.className = 'p-3 rounded-lg bg-gradient-to-br from-blue-400 to-blue-500 text-white font-bold shadow-lg hover:shadow-xl transition-all duration-200 cursor-pointer';
                        } else if (hasMarks) {
                            cell.className = 'p-3 rounded-lg bg-gradient-to-br from-amber-400 to-amber-500 text-white font-bold shadow-lg hover:shadow-xl transition-all duration-200 cursor-pointer ring-2 ring-amber-200/50';
                            
                            // Add click event for marked dates
                            cell.addEventListener('click', () => {
                                const clickedDate = new Date(currentYear, currentMonth, d);
                                highlightMarkedDate(clickedDate);
                            });
                        }
                        
                        cell.textContent = d;
                        gridEl.appendChild(cell);
                    }
                    
                    // Render details list
                    const monthMarks = parsedMarks.filter(m => m.date.getFullYear() === currentYear && m.date.getMonth() === currentMonth)
                        .sort((a,b) => a.date - b.date);
                    
                    if (monthMarks.length === 0) {
                        detailsEl.innerHTML = `
                            <div class="text-center text-gray-500 py-8">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm">No marked dates for this month</p>
                            </div>
                        `;
                                         } else {
                        detailsEl.innerHTML = monthMarks.map((m, index) => {
                            const ds = m.date.toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' });
                            return `
                                <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 mb-4 border border-white/60 hover:bg-white/80 transition-all duration-200 shadow-sm hover:shadow-md marked-date-card" data-date="${m.date.toISOString().split('T')[0]}" style="margin: 0.5rem 0;">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-amber-500 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm font-bold text-gray-900 mb-1">${ds}</div>
                                            <div class="text-sm text-gray-600 leading-relaxed">${m.note ? m.note : 'No description provided'}</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }).join('');
                    }
                }

                // Function to highlight marked date card
                function highlightMarkedDate(clickedDate) {
                    const dateString = clickedDate.toISOString().split('T')[0];
                    const card = detailsEl.querySelector(`[data-date="${dateString}"]`);
                    
                    if (card) {
                        // Add shake animation
                        card.style.animation = 'shake 0.5s ease-in-out';
                        card.style.transform = 'scale(1.05)';
                        
                        // Check if card is visible in the container
                        const container = detailsEl;
                        const cardRect = card.getBoundingClientRect();
                        const containerRect = container.getBoundingClientRect();
                        
                        const isCardVisible = (
                            cardRect.top >= containerRect.top &&
                            cardRect.bottom <= containerRect.bottom
                        );
                        
                        // Only scroll if card is not visible
                        if (!isCardVisible) {
                            card.scrollIntoView({ 
                                behavior: 'smooth', 
                                block: 'center',
                                inline: 'nearest'
                            });
                        }
                        
                        // Reset after animation
                        setTimeout(() => {
                            card.style.animation = '';
                            card.style.transform = '';
                        }, 500);
                    }
                }

                // Navigation buttons
                const prevBtn = section.querySelector('button:first-of-type');
                const nextBtn = section.querySelector('button:last-of-type');
                
                prevBtn.addEventListener('click', () => {
                    currentMonth--;
                    if (currentMonth < 0) {
                        currentMonth = 11;
                        currentYear--;
                    }
                    renderCalendar();
                });
                
                nextBtn.addEventListener('click', () => {
                    currentMonth++;
                    if (currentMonth > 11) {
                        currentMonth = 0;
                        currentYear++;
                    }
                    renderCalendar();
                });

                    // Initial render
    renderCalendar();



    // How It Works Modal Functions (Global Scope)
    window.showHowItWorks = function() {
        const modal = document.getElementById('howItWorksModal');
        
        modal.classList.remove('hidden');
        modal.classList.add('show');
        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';
    };

    window.hideHowItWorks = function() {
        const modal = document.getElementById('howItWorksModal');
        
        modal.classList.remove('show');
        modal.classList.add('hidden');
        document.body.classList.remove('modal-open');
        document.body.style.overflow = 'auto';
    };


    document.getElementById('howItWorksModal').addEventListener('click', function(e) {
        if (e.target === this) {
            window.hideHowItWorks();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.hideHowItWorks();
        }
    });
            }
        }
    } catch (e) {
        // Silent fail for calendar rebuild
    }

    // Randomized hero shapes animation
    const hero = document.getElementById('hero');
    const shapes = hero ? hero.querySelectorAll('.hero-shape') : [];
    const colors = ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#a855f7', '#f97316', '#22c55e'];
    function rand(min, max) { return Math.random() * (max - min) + min; }
    function pick(arr) { return arr[Math.floor(Math.random() * arr.length)]; }
    function edgePoint(vw, vh, side) {
        switch (side) {
            case 'left':   return { x: -0.15 * vw, y: rand(-0.1 * vh, 1.1 * vh) };
            case 'right':  return { x: 1.15 * vw,  y: rand(-0.1 * vh, 1.1 * vh) };
            case 'top':    return { x: rand(-0.1 * vw, 1.1 * vw), y: -0.15 * vh };
            case 'bottom': return { x: rand(-0.1 * vw, 1.1 * vw), y: 1.15 * vh };
            default:       return { x: -0.15 * vw, y: 0 };
        }
    }
    function oppositeSide(side) {
        const map = { left: 'right', right: 'left', top: 'bottom', bottom: 'top' };
        return map[side];
    }
    function scheduleColorSwap(el) {
        // Smoothly transition colors over time
        el.style.transition = 'background-color 1500ms ease, filter 1500ms ease, opacity 1500ms ease';
        const swap = () => {
            el.style.backgroundColor = pick(colors);
            el.style.filter = `blur(${rand(16, 28)}px)`;
            el.style.opacity = rand(0.18, 0.35).toFixed(2);
            el._colorTimer = setTimeout(swap, rand(3000, 7000));
        };
        el._colorTimer = setTimeout(swap, rand(1500, 3500));
    }
    function animateShape(el) {
        if (!hero) return;
        const heroRect = hero.getBoundingClientRect();
        const vw = heroRect.width;
        const vh = heroRect.height;
        const size = rand(140, 380);
        const duration = rand(14000, 26000); // ms
        const delay = rand(0, 800); // ms
        const startSide = pick(['left', 'right', 'top', 'bottom']);
        const endSide = Math.random() < 0.6 ? oppositeSide(startSide) : pick(['left', 'right', 'top', 'bottom'].filter(s => s !== startSide));
        const start = edgePoint(vw, vh, startSide);
        const end = edgePoint(vw, vh, endSide);
        const mid = { x: (start.x + end.x) / 2 + rand(-0.1 * vw, 0.1 * vw), y: (start.y + end.y) / 2 + rand(-0.1 * vh, 0.1 * vh) };

        el.style.width = `${size}px`;
        el.style.height = `${size}px`;
        // Avoid changing backgroundColor/filter on every frame; set once per cycle
        el.style.backgroundColor = pick(colors);
        el.style.transform = `translate(${start.x}px, ${start.y}px)`; // ensure offscreen before starting
        el.style.opacity = '0';

        // Kick off a color swap loop if not already running
        if (!el._colorTimer) scheduleColorSwap(el);

        el.animate([
            { transform: `translate(${start.x}px, ${start.y}px) scale(1)`, opacity: 0 },
            { transform: `translate(${mid.x}px, ${mid.y}px) scale(${rand(0.95,1.08).toFixed(2)})`, opacity: 0.28 },
            { transform: `translate(${end.x}px, ${end.y}px) scale(1)`, opacity: 0 }
        ], {
            duration,
            delay,
            iterations: 1,
            easing: 'ease-in-out',
            fill: 'forwards'
        }).onfinish = () => {
            animateShape(el);
        };
    }
    function setupHeroShapes(targetCount = 7) {
        const heroEl = document.getElementById('hero');
        if (!heroEl) return;
        let current = heroEl.querySelectorAll('.hero-shape');
        const missing = targetCount - current.length;
        for (let i = 0; i < missing; i++) {
            const d = document.createElement('div');
            d.className = 'hero-shape';
            heroEl.prepend(d);
        }
        current = heroEl.querySelectorAll('.hero-shape');
        current.forEach((el, idx) => {
            if (el.getAnimations) {
                try { el.getAnimations().forEach(a => a.cancel()); } catch (_) {}
            }
            if (el._colorTimer) { try { clearTimeout(el._colorTimer); } catch (_) {} el._colorTimer = null; }
            setTimeout(() => animateShape(el), idx * 250);
        });
        // Ensure two shapes appear immediately in viewport
        const immediate = Array.from(current).slice(0, 2);
        immediate.forEach(el => animateShapeImmediate(el));
    }

    // Initialize hero shapes now and on browser restore/visibility
    setupHeroShapes();

    // Visibility enforcement: ensure at least 2 shapes visible inside hero
    function rectsIntersect(a, b) {
        return !(b.left > a.right || b.right < a.left || b.top > a.bottom || b.bottom < a.top);
    }
    function countVisibleShapes() {
        const heroEl = document.getElementById('hero');
        if (!heroEl) return 0;
        const heroRect = heroEl.getBoundingClientRect();
        let count = 0;
        heroEl.querySelectorAll('.hero-shape').forEach(el => {
            const r = el.getBoundingClientRect();
            const op = parseFloat(getComputedStyle(el).opacity || '0');
            if (op > 0.12 && rectsIntersect(heroRect, r)) count += 1;
        });
        return count;
    }
    function ensureMinVisible() {
        const visible = countVisibleShapes();
        if (visible < 2) {
            const heroEl = document.getElementById('hero');
            if (!heroEl) return;
            const shapesList = Array.from(heroEl.querySelectorAll('.hero-shape'));
            let needed = 2 - visible;
            for (const el of shapesList) {
                if (needed <= 0) break;
                const op = parseFloat(getComputedStyle(el).opacity || '0');
                if (op < 0.05) {
                    animateShapeImmediate(el);
                    needed -= 1;
                }
            }
        }
    }
    function animateShapeImmediate(el) {
        const heroEl = document.getElementById('hero');
        if (!heroEl) return;
        const heroRect = heroEl.getBoundingClientRect();
        const vw = heroRect.width;
        const vh = heroRect.height;
        const size = rand(160, 340);
        const midX = rand(0.2 * vw, 0.8 * vw);
        const midY = rand(0.2 * vh, 0.8 * vh);
        const end = edgePoint(vw, vh, pick(['left','right','top','bottom']));
        el.style.width = `${size}px`;
        el.style.height = `${size}px`;
        el.style.backgroundColor = pick(colors);
        el.style.transform = `translate(${midX}px, ${midY}px)`;
        if (el.getAnimations) { try { el.getAnimations().forEach(a => a.cancel()); } catch(_){} }
        if (el._colorTimer) { try { clearTimeout(el._colorTimer); } catch(_){} el._colorTimer = null; }
        scheduleColorSwap(el);
        el.animate([
            { transform: `translate(${midX}px, ${midY}px) scale(1)`, opacity: 0 },
            { transform: `translate(${midX}px, ${midY}px) scale(${rand(0.97,1.06).toFixed(2)})`, opacity: 0.28 },
            { transform: `translate(${end.x}px, ${end.y}px) scale(1)`, opacity: 0 }
        ], {
            duration: rand(12000, 20000),
            delay: 0,
            iterations: 1,
            easing: 'ease-in-out',
            fill: 'forwards'
        }).onfinish = () => animateShape(el);
    }
    if (window.__ensureVisibleTimer) { try { clearInterval(window.__ensureVisibleTimer); } catch(_){} }
    window.__ensureVisibleTimer = setInterval(ensureMinVisible, 600);
    window.addEventListener('pageshow', () => { setupHeroShapes(); ensureMinVisible(); });
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            // Use requestIdleCallback if available to start asap without blocking
            const reinit = () => { setupHeroShapes(); ensureMinVisible(); };
            if (window.requestIdleCallback) { requestIdleCallback(reinit, { timeout: 200 }); } else { reinit(); }
        }
    });

    // Back to Top visibility and initial hint state
    const backToTop = document.getElementById('backToTop');
    const scrollHint = document.getElementById('scrollHint');
    const onScroll = () => {
        if (window.scrollY > 300) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }

        // Show hint only near top
        if (scrollHint) {
            if (window.scrollY < 40) {
                scrollHint.classList.add('show');
            } else {
                scrollHint.classList.remove('show');
            }
        }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    // Set initial state on load
    onScroll();
    backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    // Reveal sections on scroll
    const revealSections = document.querySelectorAll('.reveal-section');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });
    revealSections.forEach(sec => revealObserver.observe(sec));

    // Override anchor default to ensure exact offset for services
    const toServices = document.getElementById('scrollToServices');
    if (toServices) {
        toServices.addEventListener('click', (e) => {
            const target = document.getElementById('services');
            if (target) {
                e.preventDefault();
                const y = target.getBoundingClientRect().top + window.pageYOffset - 80;
                window.scrollTo({ top: y, behavior: 'smooth' });
            }
        });
    }

    // Swipe gestures for researchers
    let touchStartX = 0;
    let touchEndX = 0;
    if (researchersContainer) {
        researchersContainer.addEventListener('touchstart', (e) => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
        researchersContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            const delta = touchEndX - touchStartX;
            if (Math.abs(delta) > 50) {
                scrollResearchers(delta > 0 ? 'left' : 'right');
            }
        }, { passive: true });
    }

    // Search/filter for researchers
    const researcherSearch = document.getElementById('researcherSearch');
    if (researcherSearch) {
        const filterResearchers = debounce(() => {
            const q = researcherSearch.value.toLowerCase().trim();
            const cards = document.querySelectorAll('#researchers-container .researcher-card');
            cards.forEach((card) => {
                const name = (card.getAttribute('data-name') || card.querySelector('h3')?.textContent || '').toLowerCase();
                const tags = (card.getAttribute('data-tags') || card.textContent || '').toLowerCase();
                const match = !q || name.includes(q) || tags.includes(q);
                card.style.display = match ? '' : 'none';
            });
        }, 150);
        researcherSearch.addEventListener('input', filterResearchers);
    }

    // Mobile hero content fade-in - simplified since logo is preloaded
    function initMobileHeroFadeIn() {
        if (window.innerWidth < 768) { // Mobile only
            const content = document.getElementById('mobile-hero-content');
            
            if (content) {
                // Since logo is preloaded, we can fade in content immediately
                // Add a small delay to ensure smooth visual effect
                setTimeout(function() {
                    content.classList.add('fade-in');
                }, 100);
            }
        }
    }
    
    // Initialize mobile hero fade-in
    initMobileHeroFadeIn();

    // Legacy function for backward compatibility
    window.loadAnnouncements = function() {
        if (window.landingAnnouncements) {
            window.landingAnnouncements.load();
        }
    }

    // Legacy function for backward compatibility
    function formatTimeAgo(dateString) {
        if (window.landingAnnouncements) {
            return window.landingAnnouncements.formatTimeAgo(dateString);
        }
        return 'Recently';
    }

});
</script>

<!-- Landing Page Announcements JavaScript -->
<script src="{{ asset('js/landing-announcements.js') }}"></script>

<!-- Landing Page Researchers JavaScript -->
<script src="{{ asset('js/landing-researchers.js') }}"></script>

<!-- Researcher Profile Modal -->
<div id="researcherProfileModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden backdrop-blur-sm">
    <div class="relative top-20 mx-auto p-5 w-full max-w-md">
        <div class="relative bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
            <!-- Close button -->
            <button onclick="closeResearcherModal()" class="absolute top-4 right-4 z-10 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <!-- Modal content -->
            <div class="p-8 pt-10">
                <!-- Profile picture -->
                <div id="modal-researcher-photo" class="flex justify-center mb-6">
                    <!-- Photo will be inserted here by JavaScript -->
                </div>
                
                <!-- Full name -->
                <h2 id="modal-researcher-name" class="text-2xl font-bold text-gray-900 mb-8 text-center"></h2>
                
                <!-- Buttons -->
                <div class="flex flex-col gap-3">
                    <!-- SCOPUS Button - Orange (#E9710C) -->
                    <a id="modal-scopus-btn" href="#" target="_blank" rel="noopener noreferrer" 
                       class="w-full inline-flex items-center justify-center gap-2.5 px-6 py-3.5 text-white font-semibold text-sm uppercase tracking-widest rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 ease-in-out"
                       style="background: linear-gradient(135deg, #E9710C 0%, #D65A00 100%);"
                       onmouseover="this.style.background='linear-gradient(135deg, #F08020 0%, #E9710C 100%)'"
                       onmouseout="this.style.background='linear-gradient(135deg, #E9710C 0%, #D65A00 100%)'">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                        SCOPUS
                    </a>
                    
                    <!-- ORCID Button - Lime Green (#A6CE39) -->
                    <a id="modal-orcid-btn" href="#" target="_blank" rel="noopener noreferrer" 
                       class="w-full inline-flex items-center justify-center gap-2.5 px-6 py-3.5 text-white font-semibold text-sm uppercase tracking-widest rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 ease-in-out"
                       style="background: linear-gradient(135deg, #A6CE39 0%, #8FB82E 100%);"
                       onmouseover="this.style.background='linear-gradient(135deg, #B8D94A 0%, #A6CE39 100%)'"
                       onmouseout="this.style.background='linear-gradient(135deg, #A6CE39 0%, #8FB82E 100%)'">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm.34 5.577c1.939 0 3.511 1.57 3.511 3.511 0 1.939-1.572 3.511-3.511 3.511-1.939 0-3.511-1.572-3.511-3.511 0-1.941 1.572-3.511 3.511-3.511zm-4.988 13.5c.5-.75 1.5-1.5 2.5-1.5s2 .75 2.5 1.5h-5zm9.988 0c.5-.75 1.5-1.5 2.5-1.5s2 .75 2.5 1.5h-5z"/>
                        </svg>
                        ORCID
                    </a>
                    
                    <!-- WOS Button - Blue (#0066CC) -->
                    <a id="modal-wos-btn" href="#" target="_blank" rel="noopener noreferrer" 
                       class="w-full inline-flex items-center justify-center gap-2.5 px-6 py-3.5 text-white font-semibold text-sm uppercase tracking-widest rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 ease-in-out"
                       style="background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);"
                       onmouseover="this.style.background='linear-gradient(135deg, #1A7AE6 0%, #0066CC 100%)'"
                       onmouseout="this.style.background='linear-gradient(135deg, #0066CC 0%, #0052A3 100%)'">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                        Web of Science
                    </a>
                    
                    <!-- Google Scholar Button - Google Blue (#4285F4) -->
                    <a id="modal-google-scholar-btn" href="#" target="_blank" rel="noopener noreferrer" 
                       class="w-full inline-flex items-center justify-center gap-2.5 px-6 py-3.5 text-white font-semibold text-sm uppercase tracking-widest rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 ease-in-out"
                       style="background: linear-gradient(135deg, #4285F4 0%, #3367D6 100%);"
                       onmouseover="this.style.background='linear-gradient(135deg, #5A95F5 0%, #4285F4 100%)'"
                       onmouseout="this.style.background='linear-gradient(135deg, #4285F4 0%, #3367D6 100%)'">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5.242 13.769L0 9.5 12 0l12 9.5-5.242 4.269C17.548 11.249 14.978 9.5 12 9.5c-2.977 0-5.548 1.748-6.758 4.269zM12 10a7 7 0 1 0 0 14 7 7 0 0 0 0-14z"/>
                        </svg>
                        Google Scholar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> 