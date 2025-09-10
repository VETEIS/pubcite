<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desktop Access Required - PubCite</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #fef7f0 0%, #fef3c7 100%);
            min-height: 100vh;
            color: #374151;
        }
        
        .container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 1rem;
        }
        
        .card {
            flex: 1;
            width: 100%;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .logo {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 2px solid #7c2d12;
            overflow: hidden;
        }
        
        .logo-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #7c2d12;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        
        .header p {
            color: #7c2d12;
            text-align: center;
        }
        
        .icon-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .icon {
            width: 4rem;
            height: 4rem;
            background: #dbeafe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        
        .icon svg {
            width: 2rem;
            height: 2rem;
            color: #2563eb;
        }
        
        .content h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.75rem;
            text-align: center;
        }
        
        .content p {
            color: #6b7280;
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .reasons {
            margin-bottom: 1.5rem;
        }
        
        .reason-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        
        .reason-icon {
            width: 1.5rem;
            height: 1.5rem;
            background: #dcfce7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 0.125rem;
        }
        
        .reason-icon svg {
            width: 0.75rem;
            height: 0.75rem;
            color: #16a34a;
        }
        
        .reason-text {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .buttons {
            margin-bottom: 1.5rem;
        }
        
        .btn {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            margin-bottom: 0.75rem;
        }
        
        .btn-primary {
            background: #7c2d12;
            color: white;
        }
        
        .btn-primary:hover {
            background: #991b1b;
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
        }
        
        .btn-testing {
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }
        
        .btn-testing:hover {
            background: #bfdbfe;
        }
        
        .footer-info {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-info p {
            font-size: 0.75rem;
            color: #9ca3af;
            text-align: center;
        }
        
        .footer {
            text-align: center;
            margin-top: 1rem;
            padding-top: 1rem;
        }
        
        .footer p {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <!-- Logo and Header -->
            <div class="header">
                <div class="logo">
                    <img src="/images/spjrd.png" alt="SPJRD Logo" class="logo-image">
                </div>
                <h1>PubCite</h1>
                <p>Desktop Access Required</p>
            </div>

            <!-- Desktop Icon -->
            <div class="icon-container">
                
            </div>

            <!-- Explanation -->
            <div class="content">
                <h2>Better Experience on Desktop</h2>
                <p>Our application is optimized for desktop and tablet devices to provide the best experience for document submission and review processes.</p>
            </div>

            <!-- Reasons List -->
            <div class="reasons">
                <div class="reason-item">
                    <div class="reason-icon">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="reason-text">Complex form layouts with multiple tabs</p>
                </div>
                <div class="reason-item">
                    <div class="reason-icon">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="reason-text">File upload and document management</p>
                </div>
                <div class="reason-item">
                    <div class="reason-icon">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="reason-text">Detailed data entry and review processes</p>
                </div>
                <div class="reason-item">
                    <div class="reason-icon">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="reason-text">Better security and data validation</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="buttons">
                <button onclick="tryAgain()" class="btn btn-primary">Try Again</button>
                <button onclick="contactSupport()" class="btn btn-secondary">Contact Support</button>
            </div>

            <!-- Additional Info -->
            <div class="footer-info">
                <p>For the best experience, please access this application from a desktop computer or tablet device.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Publication Unit System &copy; {{ date('Y') }}</p>
        </div>
    </div>

    <script>
        function tryAgain() {
            // Check if user agent indicates mobile device
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const isSmallScreen = window.innerWidth < 1024;
            
            if (isMobile || isSmallScreen) {
                // Still on mobile, show message
                alert('Please access this application from a desktop computer or tablet for the best experience.');
            } else {
                // User switched to desktop, redirect to intended page
                const urlParams = new URLSearchParams(window.location.search);
                const redirect = urlParams.get('redirect') || '/dashboard';
                window.location.replace(redirect);
            }
        }

        function contactSupport() {
            window.location.href = 'mailto:pubcite@gmail.com?subject=Mobile Access Request&body=Hi, I would like to request mobile access to the PubCite system.';
        }

        // Auto-redirect if user resizes to desktop size
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                if (window.innerWidth >= 1024) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const redirect = urlParams.get('redirect') || '/dashboard';
                    window.location.replace(redirect);
                }
            }, 100);
        });
    </script>
</body>
</html>
