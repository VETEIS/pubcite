<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request Submitted Successfully</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 0;
            background: #f9f9f9;
        }
        .header {
            background-color: #8B2635;
            color: white;
            padding: 16px 10px 10px 10px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #fff;
            padding: 18px 16px 10px 16px;
            border-radius: 0 0 8px 8px;
        }
        .request-code {
            background-color: #8B2635;
            color: white;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 12px 0 8px 0;
        }
        .details {
            background-color: #f9f9f9;
            padding: 10px 12px;
            border-radius: 6px;
            margin: 10px 0 8px 0;
            border-left: 4px solid #8B2635;
            font-size: 15px;
        }
        .status {
            font-weight: bold;
            color: #f59e0b;
            font-size: 15px;
        }
        .btn {
            display: inline-block;
            background-color: #8B2635;
            color: white !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            margin: 12px 0 0 0;
            font-size: 15px;
        }
        .footer {
            text-align: center;
            margin-top: 18px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 13px;
        }
        @media (max-width: 600px) {
            .header, .content { padding-left: 6px; padding-right: 6px; }
            .btn { width: 100%; box-sizing: border-box; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0; font-size: 1.2em;">USEP Publication</h2>
        <div style="font-size: 1em; margin-top: 2px;">{{ $request->type }} Request Submitted</div>
    </div>
    <div class="content">
        <div class="request-code">Request Code: {{ $request->request_code }}</div>
        <div class="details">
            <div><strong>Status:</strong> <span class="status">Pending Review</span></div>
            <div><strong>Submitted:</strong> {{ $request->requested_at->format('M d, Y g:i A') }}</div>
        </div>
        <a href="{{ route('dashboard') }}" class="btn">View Dashboard</a>
        <div style="margin-top:10px; font-size:13px; color:#555;">
            Please save your request code for future reference.
        </div>
        <div style="margin-top:10px; font-size:12px; color:#888;">
            Your request will be reviewed by the Publication Unit. You will receive updates on the status of your request.
        </div>
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html> 