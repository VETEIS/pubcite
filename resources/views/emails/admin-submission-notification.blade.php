<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Request Submitted</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #8B2635;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .request-code {
            background-color: #8B2635;
            color: white;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }
        .details {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #8B2635;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background-color: #8B2635;
            color: white !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
        .urgent {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0; font-size: 1.2em;">USEP Publications</h2>
        <div style="font-size: 1em; margin-top: 2px;">New {{ $request->type }} Request</div>
    </div>
    <div class="content">
        <div class="request-code">Request Code: {{ $request->request_code }}</div>
        <div class="details">
            <div><strong>Status:</strong> <span class="status">Pending Review</span></div>
            <div><strong>Submitted:</strong> {{ $request->requested_at->format('M d, Y g:i A') }}</div>
            <div><strong>User:</strong> {{ $user->name }} ({{ $user->email }})</div>
        </div>
        <a href="{{ route('dashboard') }}" class="btn">Review Request</a>
        <div style="margin-top:10px; font-size:13px; color:#555;">
            Please review this request in the admin dashboard.
        </div>
        <div class="footer">
            <p>This is an automated notification from the Publications Unit system.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html> 