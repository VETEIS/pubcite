<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Publication Request - {{ $request->request_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #8B0000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #8B0000;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #8B0000;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .field {
            margin-bottom: 10px;
        }
        .field-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 3px;
        }
        .field-value {
            padding: 5px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 3px;
            min-height: 20px;
        }
        .grid {
            display: table;
            width: 100%;
        }
        .grid-row {
            display: table-row;
        }
        .grid-cell {
            display: table-cell;
            padding: 5px;
            vertical-align: top;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">USeP Publications Unit</div>
        <div class="title">Publication Request Form</div>
        <div class="subtitle">Request Code: {{ $request->request_code }}</div>
        <div class="subtitle">Date: {{ $request->requested_at->format('F d, Y \a\t g:i A') }}</div>
    </div>

    <div class="section">
        <div class="section-title">Applicant Information</div>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-cell" style="width: 50%;">
                    <div class="field">
                        <div class="field-label">Name:</div>
                        <div class="field-value">{{ $user->name }}</div>
                    </div>
                </div>
                <div class="grid-cell" style="width: 50%;">
                    <div class="field">
                        <div class="field-label">Email:</div>
                        <div class="field-value">{{ $user->email }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Request Details</div>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-cell" style="width: 50%;">
                    <div class="field">
                        <div class="field-label">Request Type:</div>
                        <div class="field-value">{{ ucfirst($request->type) }}</div>
                    </div>
                </div>
                <div class="grid-cell" style="width: 50%;">
                    <div class="field">
                        <div class="field-label">Status:</div>
                        <div class="field-value">{{ ucfirst($request->status) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($formData)
        <div class="section">
            <div class="section-title">Form Data</div>
            @foreach($formData as $key => $value)
                <div class="field">
                    <div class="field-label">{{ ucwords(str_replace('_', ' ', $key)) }}:</div>
                    <div class="field-value">{{ $value }}</div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="footer">
        <p>This document was generated automatically by the USeP Publications Unit system.</p>
        <p>For any questions, please contact the Publications Unit.</p>
    </div>
</body>
</html> 