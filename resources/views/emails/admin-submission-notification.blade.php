<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Request Submitted</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 0;
            background: #f5f5f5;
        }
        .header {
            background-color: #8B2635;
            color: white;
            padding: 18px 20px 12px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #fff;
            padding: 18px 20px 16px 20px;
            border-radius: 0 0 8px 8px;
        }
        .main-message {
            font-size: 1.08em;
            font-weight: bold;
            color: #8B2635;
            margin-bottom: 10px;
            text-align: center;
        }
        .request-code {
            background-color: #8B2635;
            color: white;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            margin: 10px 0 12px 0;
            letter-spacing: 1px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .details-table td {
            padding: 6px 8px 6px 0;
            font-size: 15px;
            vertical-align: top;
        }
        .details-table .label {
            font-weight: bold;
            color: #8B2635;
            width: 90px;
        }
        .details-table .value {
            color: #222;
        }
        .btn {
            display: inline-block;
            background-color: #8B2635;
            color: white !important;
            padding: 10px 22px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0 0 0;
            font-size: 15px;
        }
        .footer {
            text-align: center;
            margin-top: 16px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 13px;
        }
        @media (min-width: 480px) {
            .details-table {
                margin-bottom: 0;
            }
            .details-table tr {
                display: flex;
            }
            .details-table td {
                flex: 1 1 50%;
                padding-right: 16px;
                padding-bottom: 0;
            }
            .details-table .label {
                width: auto;
                min-width: 90px;
            }
        }
        @media (max-width: 600px) {
            .header, .content { padding-left: 6px; padding-right: 6px; }
            .btn { width: 100%; box-sizing: border-box; }
            .details-table td { padding-right: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0; font-size: 1.2em;">USEP Publication</h2>
        <div style="font-size: 1em; margin-top: 2px;">New {{ $request->type }} Request</div>
    </div>
    <div class="content">
        <div class="main-message">
            New {{ strtolower($request->type) }} request received.
        </div>
        <div class="request-code">Request Code: {{ $request->request_code }}</div>
        <table class="details-table">
            <tr>
                <td class="label">Status:</td>
                <td class="value">Pending Review</td>
                <td class="label">Submitted:</td>
                <td class="value">{{ $request->requested_at->format('M d, Y g:i A') }}</td>
            </tr>
            <tr>
                <td class="label">User:</td>
                <td class="value" colspan="3">{{ $user->name }} ({{ $user->email }})</td>
            </tr>
        </table>
        <a href="{{ route('dashboard') }}" class="btn">Review Request</a>
        <div style="margin-top:10px; font-size:13px; color:#555;">
            Please review this request in the admin dashboard.
        </div>
        <div class="footer">
            <p>This is an automated notification from the Publication Unit system.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html> 