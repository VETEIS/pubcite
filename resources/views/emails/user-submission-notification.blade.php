@extends('emails.layout')

@section('title', 'Request Submitted Successfully - ' . $request->request_code)

@section('header-title', 'USEP Publication System')
@section('header-subtitle', $request->type . ' Request Submitted')

@section('content')
    <p>Dear {{ $user->name }},</p>
    
    <p>Your <strong>{{ $request->type }}</strong> request has been submitted successfully and is now pending review.</p>
    
    <div class="request-code-badge">
        Request Code: {{ $request->request_code }}
    </div>
    
    <table class="details-table">
        <tr>
            <td class="label">Status:</td>
            <td class="value"><span class="status-badge status-pending">Pending Review</span></td>
        </tr>
        <tr>
            <td class="label">Submitted:</td>
            <td class="value">{{ $request->requested_at->format('F j, Y \a\t g:i A') }}</td>
        </tr>
        <tr>
            <td class="label">Request Type:</td>
            <td class="value">{{ $request->type }}</td>
        </tr>
    </table>
    
    <div class="info-box">
        <p><strong>Important:</strong> Please save your request code for future reference. You will receive updates on the status of your request via email.</p>
    </div>
    
    <div class="btn-center">
        <a href="{{ route('dashboard') }}" class="btn">View Your Dashboard</a>
    </div>
    
    <p style="margin-top: 24px; font-size: 14px; color: #6b7280;">
        Your request will be reviewed by the Publication Unit. You will receive email notifications when there are updates to your request status.
    </p>
@endsection

@section('footer')
    <p><strong>USEP Publication & Citation System</strong></p>
    <p>This is an automated message. Please do not reply to this email.</p>
    <p>If you need assistance, please contact your system administrator or the Publication Unit.</p>
@endsection
