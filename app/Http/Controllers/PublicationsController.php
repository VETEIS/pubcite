<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as UserRequest;

class PublicationsController extends Controller
{
    public function create()
    {
        return view('publications.request');
    }

    public function store(Request $request)
    {
        $request->validate([
            'incentive_field' => 'required',
            'recommendation_field' => 'required',
            'terminal_field' => 'required',
        ]);

        UserRequest::create([
            'user_id' => Auth::id(),
            'request_code' => 'REQ-' . now()->format('Ymd-His'),
            'type' => 'Publication',
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Publication request submitted successfully!');
    }

    public function adminUpdate(Request $httpRequest, \App\Models\Request $request)
    {
        $httpRequest->validate([
            'status' => 'required|in:pending,endorsed,rejected',
        ]);
        $request->status = $httpRequest->input('status');
        $request->save();
        return back()->with('success', 'Request status updated successfully.');
    }
} 