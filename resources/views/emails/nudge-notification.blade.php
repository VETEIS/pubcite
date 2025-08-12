<p>Hello Admin,</p>
<p>The user <strong>{{ $user->name }}</strong> nudged a pending {{ strtolower($request->type) }} request.</p>
<p>
    <strong>Request Code:</strong> {{ $request->request_code }}<br>
    <strong>User Email:</strong> {{ $user->email }}<br>
    <strong>Status:</strong> {{ ucfirst($request->status) }}
</p>
<p>Please review it in the admin dashboard.</p>
<p>Thank you.</p> 