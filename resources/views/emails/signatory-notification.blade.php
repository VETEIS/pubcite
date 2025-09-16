<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Request Requires Your Signature</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #dc2626;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 10px;
        }
        .title {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #6b7280;
            font-size: 14px;
        }
        .content {
            margin-bottom: 30px;
        }
        .request-info {
            background: #f3f4f6;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        .info-label {
            font-weight: 600;
            color: #374151;
        }
        .info-value {
            color: #6b7280;
        }
        .cta-button {
            display: inline-block;
            background: #dc2626;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
        }
        .cta-button:hover {
            background: #b91c1c;
        }
        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .urgent {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .urgent-text {
            color: #92400e;
            font-weight: 600;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Publication & Citation System</div>
            <div class="title">New Request Requires Your Signature</div>
            <div class="subtitle">As {{ ucwords(str_replace('_', ' ', $signatoryType)) }}, your signature is required</div>
        </div>

        <div class="content">
            <p>Dear {{ $signatoryName }},</p>
            
            <p>A new <strong>{{ $request->type }}</strong> request has been submitted and requires your signature as <strong>{{ ucwords(str_replace('_', ' ', $signatoryType)) }}</strong>.</p>

            <div class="urgent">
                <p class="urgent-text">⚠️ Action Required: Please review and sign this request at your earliest convenience.</p>
            </div>

            <div class="request-info">
                <div class="info-row">
                    <span class="info-label">Request Code:</span>
                    <span class="info-value">{{ $request->request_code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Request Type:</span>
                    <span class="info-value">{{ $request->type }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Submitted By:</span>
                    <span class="info-value">{{ $request->user->name ?? 'Unknown User' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Submission Date:</span>
                    <span class="info-value">{{ $request->requested_at->format('F j, Y \a\t g:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Current Status:</span>
                    <span class="info-value">{{ ucfirst($request->status) }}</span>
                </div>
            </div>

            <p>To review and sign this request, please log into the system and navigate to your signature requests dashboard.</p>

            <div style="text-align: center;">
                <a href="{{ route('signing.index') }}" class="cta-button">Review & Sign Request</a>
            </div>

            <p><strong>Important Notes:</strong></p>
            <ul>
                <li>Please review all documents carefully before signing</li>
                <li>Ensure all required information is complete and accurate</li>
                <li>Contact the system administrator if you have any questions</li>
            </ul>
        </div>

        <div class="footer">
            <p>This is an automated notification from the Publication & Citation System.</p>
            <p>Please do not reply to this email. If you need assistance, contact your system administrator.</p>
        </div>
    </div>
</body>
</html>
