<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Force refresh user data from database
        $user = User::find(Auth::id());
        
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Access denied. Admin privileges required.');
        }
        
        return $next($request);
    }
} 