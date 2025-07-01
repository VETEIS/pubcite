<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request Status Update</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 0; background: #f9f9f9; }
        .header { background-color: #8B2635; color: white; padding: 16px 10px 10px 10px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background-color: #fff; padding: 18px 16px 10px 16px; border-radius: 0 0 8px 8px; }
        .request-code { background-color: #8B2635; color: white; padding: 10px; border-radius: 6px; text-align: center; font-size: 18px; font-weight: bold; margin: 12px 0 8px 0; }
        .status { font-weight: bold; font-size: 16px; padding: 4px 12px; border-radius: 12px; display: inline-block; margin: 6px 0; }
        .status-pending { background: #f59e0b; color: #fff; }
        .status-endorsed { background: #22c55e; color: #fff; }
        .status-rejected { background: #ef4444; color: #fff; }
        .btn { display: inline-block; background-color: #8B2635; color: white !important; padding: 10px 20px; text-decoration: none; border-radius: 6px; margin: 12px 0 0 0; font-size: 15px; }
        .footer { text-align: center; margin-top: 18px; padding-top: 10px; border-top: 1px solid #eee; color: #666; font-size: 13px; }
        @media (max-width: 600px) { .header, .content { padding-left: 6px; padding-right: 6px; } .btn { width: 100%; box-sizing: border-box; } }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0; font-size: 1.2em;">USEP Publications</h2>
        <div style="font-size: 1em; margin-top: 2px;">Request Status Update</div>
    </div>
    <div class="content">
        <div class="request-code">Request Code: {{ $request->request_code }}</div>
        <div style="margin: 10px 0 8px 0;">
            <span class="status status-{{ strtolower($newStatus) }}">{{ ucfirst($newStatus) }}</span>
        </div>
        @if($adminComment)
        <div style="background:#f3f4f6; border-left:4px solid #8B2635; padding:8px 12px; border-radius:6px; margin-bottom:8px; font-size:14px;">
            <strong>Admin Comment:</strong><br>{{ $adminComment }}
        </div>
        @endif
        <a href="{{ route('dashboard') }}" class="btn">View Your Request</a>
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html> 