@extends('emails.layout')

@section('title', 'Team Invitation - ' . $invitation->team->name)

@section('header-title', 'USEP Publication System')
@section('header-subtitle', 'Team Invitation')

@section('content')
    <p>Hello,</p>
    
    <p>You have been invited to join the <strong>{{ $invitation->team->name }}</strong> team!</p>
    
    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::registration()))
    <div class="info-box">
        <p><strong>Getting Started:</strong></p>
        <p style="margin-top: 8px;">If you do not have an account, please use "Sign in with Google" on the login page to create an account with your USeP email. After creating an account, you may click the invitation acceptance button below to accept the team invitation.</p>
        <p style="margin-top: 12px;">If you already have an account, you may accept this invitation by clicking the button below:</p>
    </div>
    @else
    <div class="info-box">
        <p>You may accept this invitation by clicking the button below:</p>
    </div>
    @endif
    
    <div class="btn-center">
        <a href="{{ $acceptUrl }}" class="btn">Accept Invitation</a>
    </div>
    
    <p style="margin-top: 24px; font-size: 14px; color: #6b7280;">
        If you did not expect to receive an invitation to this team, you may safely discard this email.
    </p>
@endsection

@section('footer')
    <p><strong>USEP Publication & Citation System</strong></p>
    <p>This is an automated invitation. Please do not reply to this email.</p>
    <p>If you need assistance, please contact your system administrator.</p>
@endsection
