@extends('emails.layout')

@section('title', 'New Request Requires Your Signature - ' . $request->request_code)

@section('header-title', 'USEP Publication System')
@section('header-subtitle', 'Signature Required')

@section('content')
    <p>Dear {{ $signatoryName }},</p>
    
    <p>A new <strong>{{ $request->type }}</strong> request has been submitted and requires your signature as <strong>{{ ucwords(str_replace('_', ' ', $signatoryType)) }}</strong>.</p>
    
    <div class="alert alert-warning">
        <p class="alert-title">⚠️ Action Required</p>
        <p class="alert-content">Please review and sign this request at your earliest convenience.</p>
    </div>
    
    <div class="request-code-badge">
        Request Code: {{ $request->request_code }}
    </div>
    
    <table class="details-table">
        <tr>
            <td class="label">Request Type:</td>
            <td class="value">{{ $request->type }}</td>
        </tr>
        <tr>
            <td class="label">Submitted By:</td>
            <td class="value">{{ $request->user->name ?? 'Unknown User' }}</td>
        </tr>
        <tr>
            <td class="label">Submission Date:</td>
            <td class="value">{{ $request->requested_at->format('F j, Y \a\t g:i A') }}</td>
        </tr>
        <tr>
            <td class="label">Current Status:</td>
            <td class="value"><span class="status-badge status-pending">{{ ucfirst($request->status) }}</span></td>
        </tr>
        <tr>
            <td class="label">Your Role:</td>
            <td class="value">{{ ucwords(str_replace('_', ' ', $signatoryType)) }}</td>
        </tr>
    </table>
    
    <div class="btn-center">
        <a href="{{ route('signing.index') }}" class="btn">Review & Sign Request</a>
    </div>
    
    <div class="info-box" style="margin-top: 24px;">
        <p><strong>Important Notes:</strong></p>
        <ul style="margin: 8px 0 0 0; padding-left: 20px;">
            <li>Please review all documents carefully before signing</li>
            <li>Ensure all required information is complete and accurate</li>
            <li>Contact the system administrator if you have any questions</li>
        </ul>
    </div>
@endsection

@section('footer')
    <p><strong>USEP Publication & Citation System</strong></p>
    <p>This is an automated notification from the Publication & Citation System.</p>
    <p>Please do not reply to this email. If you need assistance, contact your system administrator.</p>
@endsection
