<?php

namespace App\Livewire\Profile;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Log;
use Laravel\Jetstream\Http\Livewire\LogoutOtherBrowserSessionsForm as JetstreamLogoutOtherBrowserSessionsForm;

class LogoutOtherBrowserSessionsForm extends JetstreamLogoutOtherBrowserSessionsForm
{
    /**
     * Confirm that the user would like to log out from other browser sessions.
     *
     * @return void
     */
    public function confirmLogout()
    {
        Log::info('LogoutOtherBrowserSessionsForm: confirmLogout called', [
            'current_confirmingLogout' => $this->confirmingLogout
        ]);
        
        try {
            parent::confirmLogout();
            Log::info('LogoutOtherBrowserSessionsForm: confirmLogout completed successfully', [
                'confirmingLogout' => $this->confirmingLogout,
                'after_parent_call' => true
            ]);
            
            // Force a re-render to ensure the modal shows
            $this->dispatch('modal-should-show', ['property' => 'confirmingLogout', 'value' => true]);
        } catch (\Exception $e) {
            Log::error('LogoutOtherBrowserSessionsForm: confirmLogout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Log out from other browser sessions.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @return void
     */
    public function logoutOtherBrowserSessions(StatefulGuard $guard)
    {
        Log::info('LogoutOtherBrowserSessionsForm: logoutOtherBrowserSessions called', [
            'user_id' => auth()->id(),
            'has_password' => !empty($this->password)
        ]);
        
        try {
            parent::logoutOtherBrowserSessions($guard);
            Log::info('LogoutOtherBrowserSessionsForm: logoutOtherBrowserSessions completed successfully');
        } catch (\Exception $e) {
            Log::error('LogoutOtherBrowserSessionsForm: logoutOtherBrowserSessions failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        Log::debug('LogoutOtherBrowserSessionsForm: render called', [
            'sessions_count' => count($this->sessions),
            'confirmingLogout' => $this->confirmingLogout
        ]);
        
        return view('profile.logout-other-browser-sessions-form');
    }
}
