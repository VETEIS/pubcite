@extends('emails.layout')

@section('title', 'Request Status Update - ' . $request->request_code)

@section('header-title', 'USEP Publication System')
@section('header-subtitle', 'Status Update')

@section('content')
    <p>Dear {{ $request->user->name }},</p>
    
    <p>Your <strong>{{ $request->type }}</strong> request status has been updated.</p>
    
    <div class="request-code-badge">
        Request Code: {{ $request->request_code }}
    </div>
    
    <table class="details-table">
        <tr>
            <td class="label">Request Type:</td>
            <td class="value">{{ $request->type }}</td>
        </tr>
        <tr>
            <td class="label">New Status:</td>
            <td class="value">
                <span class="status-badge status-{{ strtolower($newStatus) }}">
                    {{ ucfirst($newStatus) }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="label">Updated:</td>
            <td class="value">{{ now()->format('F j, Y \a\t g:i A') }}</td>
        </tr>
    </table>
    
    @if($adminComment)
    <div class="info-box">
        <p><strong>Admin Comment:</strong></p>
        <p style="margin-top: 8px;">{{ $adminComment }}</p>
    </div>
    @endif
    
    <div class="btn-center">
        <a href="{{ route('dashboard') }}" class="btn">View Your Request</a>
    </div>
    
    @if(strtolower($newStatus) === 'endorsed')
    <div class="alert alert-success" style="margin-top: 24px;">
        <p class="alert-title">✓ Request Endorsed</p>
        <p class="alert-content">Your request has been endorsed and is now in the signature workflow. You will be notified when signatures are completed.</p>
    </div>
    @elseif(strtolower($newStatus) === 'rejected')
    <div class="alert alert-danger" style="margin-top: 24px;">
        <p class="alert-title">✗ Request Rejected</p>
        <p class="alert-content">Your request has been rejected. Please review the admin comment above and contact the Publication Unit if you have questions.</p>
    </div>
    @endif
@endsection

@section('footer')
    <p><strong>USEP Publication & Citation System</strong></p>
    <p>This is an automated message. Please do not reply to this email.</p>
    <p>If you need assistance, please contact your system administrator or the Publication Unit.</p>
@endsection
