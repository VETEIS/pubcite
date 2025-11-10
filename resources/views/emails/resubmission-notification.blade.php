@extends('emails.layout')

@section('title', 'Resubmission Required - ' . $request->request_code)

@section('header-title', 'USEP Publication System')
@section('header-subtitle', 'Resubmission Required')

@section('content')
    <p>Dear {{ $user->name }},</p>
    
    <p>Your <strong>{{ $request->type }}</strong> request requires resubmission. The Center Manager has reviewed your request and determined that the files need to be resubmitted.</p>
    
    <div class="request-code-badge">
        Request Code: {{ $request->request_code }}
    </div>
    
    <div class="alert alert-warning">
        <p class="alert-title">⚠️ Action Required</p>
        <p class="alert-content">All files associated with this request have been removed. Please resubmit your request with the corrected files.</p>
    </div>
    
    <div class="info-box">
        <p><strong>Reason for Resubmission:</strong></p>
        <p style="margin-top: 8px;">{{ $reason }}</p>
    </div>
    
    <table class="details-table">
        <tr>
            <td class="label">Request Type:</td>
            <td class="value">{{ $request->type }}</td>
        </tr>
        <tr>
            <td class="label">Request Code:</td>
            <td class="value">{{ $request->request_code }}</td>
        </tr>
        <tr>
            <td class="label">Original Submission:</td>
            <td class="value">{{ $request->requested_at->format('F j, Y \a\t g:i A') }}</td>
        </tr>
        <tr>
            <td class="label">Status:</td>
            <td class="value"><span class="status-badge status-pending">Pending Resubmission</span></td>
        </tr>
    </table>
    
    <p><strong>Next Steps:</strong></p>
    <ul>
        <li>Review the reason provided above</li>
        <li>Prepare your corrected files</li>
        <li>Resubmit your request through the system</li>
        <li>Ensure all required information is complete and accurate</li>
    </ul>
    
    <div class="btn-center">
        <a href="{{ route('dashboard') }}" class="btn">View Your Requests</a>
    </div>
    
    <p style="margin-top: 24px; font-size: 14px; color: #6b7280;">
        If you have any questions about this resubmission request, please contact the Publication Unit or your Center Manager.
    </p>
@endsection

@section('footer')
    <p><strong>USEP Publication & Citation System</strong></p>
    <p>This is an automated notification. Please do not reply to this email.</p>
    <p>If you need assistance, please contact your system administrator or the Publication Unit.</p>
@endsection

