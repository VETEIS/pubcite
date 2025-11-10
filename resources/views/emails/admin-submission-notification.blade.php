@extends('emails.layout')

@section('title', 'New Request Received - ' . $request->request_code)

@section('header-title', 'USEP Publication System')
@section('header-subtitle', 'New ' . $request->type . ' Request')

@section('content')
    <p>Hello Admin,</p>
    
    <p>A new <strong>{{ strtolower($request->type) }}</strong> request has been submitted and requires your review.</p>
    
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
            <td class="label">User:</td>
            <td class="value">{{ $user->name }}</td>
        </tr>
        <tr>
            <td class="label">Email:</td>
            <td class="value">{{ $user->email }}</td>
        </tr>
        <tr>
            <td class="label">Request Type:</td>
            <td class="value">{{ $request->type }}</td>
        </tr>
    </table>
    
    <div class="btn-center">
        <a href="{{ route('admin.dashboard') }}" class="btn">Review Request</a>
    </div>
    
    <p style="margin-top: 24px; font-size: 14px; color: #6b7280;">
        Please review this request in the admin dashboard at your earliest convenience.
    </p>
@endsection

@section('footer')
    <p><strong>USEP Publication & Citation System</strong></p>
    <p>This is an automated notification from the Publication Unit system.</p>
    <p>Please do not reply to this email.</p>
@endsection
