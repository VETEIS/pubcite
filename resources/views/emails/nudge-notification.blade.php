@extends('emails.layout')

@section('title', 'Request Nudge - ' . $request->request_code)

@section('header-title', 'USEP Publication System')
@section('header-subtitle', 'Request Reminder')

@section('content')
    <p>Hello Admin,</p>
    
    <p>The user <strong>{{ $user->name }}</strong> has sent a reminder about their pending <strong>{{ strtolower($request->type) }}</strong> request.</p>
    
    <div class="request-code-badge">
        Request Code: {{ $request->request_code }}
    </div>
    
    <table class="details-table">
        <tr>
            <td class="label">User Name:</td>
            <td class="value">{{ $user->name }}</td>
        </tr>
        <tr>
            <td class="label">User Email:</td>
            <td class="value">{{ $user->email }}</td>
        </tr>
        <tr>
            <td class="label">Request Type:</td>
            <td class="value">{{ $request->type }}</td>
        </tr>
        <tr>
            <td class="label">Status:</td>
            <td class="value"><span class="status-badge status-pending">{{ ucfirst($request->status) }}</span></td>
        </tr>
        <tr>
            <td class="label">Submitted:</td>
            <td class="value">{{ $request->requested_at->format('F j, Y \a\t g:i A') }}</td>
        </tr>
    </table>
    
    <div class="alert alert-info">
        <p class="alert-title">ℹ️ Reminder</p>
        <p class="alert-content">The user is awaiting review of this request. Please review it in the admin dashboard when convenient.</p>
    </div>
    
    <div class="btn-center">
        <a href="{{ route('admin.dashboard') }}" class="btn">Review Request</a>
    </div>
@endsection

@section('footer')
    <p><strong>USEP Publication & Citation System</strong></p>
    <p>This is an automated notification. Please do not reply to this email.</p>
    <p>If you need assistance, please contact your system administrator.</p>
@endsection
