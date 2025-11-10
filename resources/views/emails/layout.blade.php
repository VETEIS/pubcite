<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'USEP Publication System')</title>
    <style>
        /* Reset styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
        }
        
        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            height: 100% !important;
            background-color: #f5f7fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Container */
        .email-wrapper {
            width: 100%;
            background-color: #f5f7fa;
            padding: 20px 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #D50B00 0%, #b30800 100%);
            color: #ffffff;
            padding: 32px 24px;
            text-align: center;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .email-header .subtitle {
            margin: 8px 0 0 0;
            font-size: 14px;
            opacity: 0.95;
            font-weight: 400;
        }
        
        /* Content */
        .email-content {
            padding: 32px 24px;
        }
        
        .email-content p {
            margin: 0 0 16px 0;
            color: #374151;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .email-content p:last-child {
            margin-bottom: 0;
        }
        
        /* Request Code Badge */
        .request-code-badge {
            background: linear-gradient(135deg, #D50B00 0%, #b30800 100%);
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin: 24px 0;
            display: block;
        }
        
        /* Info Box */
        .info-box {
            background-color: #f9fafb;
            border-left: 4px solid #D50B00;
            border-radius: 6px;
            padding: 16px 20px;
            margin: 20px 0;
        }
        
        .info-box p {
            margin: 0;
            font-size: 15px;
            color: #4b5563;
        }
        
        .info-box strong {
            color: #1f2937;
            font-weight: 600;
        }
        
        /* Alert Boxes */
        .alert {
            border-radius: 8px;
            padding: 16px 20px;
            margin: 20px 0;
            border-left: 4px solid;
        }
        
        .alert-warning {
            background-color: #fef3c7;
            border-color: #f59e0b;
            color: #92400e;
        }
        
        .alert-info {
            background-color: #dbeafe;
            border-color: #3b82f6;
            color: #1e40af;
        }
        
        .alert-success {
            background-color: #d1fae5;
            border-color: #10b981;
            color: #065f46;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            border-color: #ef4444;
            color: #991b1b;
        }
        
        .alert-title {
            font-weight: 600;
            margin: 0 0 8px 0;
            font-size: 15px;
        }
        
        .alert-content {
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
        }
        
        /* Details Table */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #f9fafb;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .details-table tr {
            border-bottom: 1px solid #e5e7eb;
        }
        
        .details-table tr:last-child {
            border-bottom: none;
        }
        
        .details-table td {
            padding: 12px 16px;
            font-size: 15px;
        }
        
        .details-table .label {
            font-weight: 600;
            color: #6b7280;
            width: 40%;
        }
        
        .details-table .value {
            color: #1f2937;
            font-weight: 500;
        }
        
        /* Button */
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #D50B00 0%, #b30800 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 24px 0;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(213, 11, 0, 0.2);
        }
        
        .btn:hover {
            background: linear-gradient(135deg, #b30800 0%, #D50B00 100%);
            box-shadow: 0 4px 8px rgba(213, 11, 0, 0.3);
        }
        
        .btn-center {
            text-align: center;
        }
        
        /* Footer */
        .email-footer {
            background-color: #f9fafb;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .email-footer p {
            margin: 0 0 8px 0;
            font-size: 13px;
            color: #6b7280;
            line-height: 1.5;
        }
        
        .email-footer p:last-child {
            margin-bottom: 0;
        }
        
        /* List */
        .email-content ul {
            margin: 16px 0;
            padding-left: 24px;
            color: #374151;
        }
        
        .email-content ul li {
            margin: 8px 0;
            font-size: 15px;
            line-height: 1.6;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-endorsed {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .status-completed {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0 10px;
                border-radius: 8px;
            }
            
            .email-header {
                padding: 24px 20px;
            }
            
            .email-header h1 {
                font-size: 20px;
            }
            
            .email-content {
                padding: 24px 20px;
            }
            
            .request-code-badge {
                font-size: 16px;
                padding: 10px 16px;
            }
            
            .btn {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }
            
            .details-table td {
                display: block;
                width: 100% !important;
                padding: 8px 16px;
            }
            
            .details-table .label {
                font-weight: 700;
                margin-bottom: 4px;
            }
            
            .details-table tr {
                border-bottom: 2px solid #e5e7eb;
            }
        }
        
        /* Dark mode support (for email clients that support it) */
        @media (prefers-color-scheme: dark) {
            .email-container {
                background-color: #1f2937;
            }
            
            .email-content {
                color: #f9fafb;
            }
            
            .email-content p {
                color: #e5e7eb;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <h1>@yield('header-title', 'USEP Publication System')</h1>
                @hasSection('header-subtitle')
                    <p class="subtitle">@yield('header-subtitle')</p>
                @endif
            </div>
            
            <!-- Content -->
            <div class="email-content">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <div class="email-footer">
                @yield('footer', '
                    <p><strong>USEP Publication & Citation System</strong></p>
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>If you need assistance, please contact your system administrator.</p>
                ')
            </div>
        </div>
    </div>
</body>
</html>

