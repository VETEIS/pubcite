<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogoutController extends Controller
{
    /**
     * Handle user logout and clean up draft sessions
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            $userId = $user->id;
            
            // Clear draft sessions for this user
            $draftSessions = [
                "draft_Publication_{$userId}",
                "draft_Citation_{$userId}",
            ];
            
            foreach ($draftSessions as $sessionKey) {
                if (session()->has($sessionKey)) {
                    session()->forget($sessionKey);
                    Log::info('Cleared draft session on logout', [
                        'user_id' => $userId,
                        'session_key' => $sessionKey
                    ]);
                }
            }
            
            // Clear any other user-specific sessions
            session()->forget('url.intended');
            session()->forget('privacy_accepted');
            
            Log::info('User logout completed with session cleanup', [
                'user_id' => $userId,
                'user_email' => $user->email
            ]);
        }
        
        // Perform the actual logout
        Auth::logout();
        
        // Invalidate the session
        $request->session()->invalidate();
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
