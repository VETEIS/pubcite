<?php

namespace App\Livewire\Profile;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Jetstream\Contracts\DeletesUsers;
use Laravel\Jetstream\Http\Livewire\DeleteUserForm as JetstreamDeleteUserForm;

class DeleteUserForm extends JetstreamDeleteUserForm
{
    /**
     * Confirm that the user would like to delete their account.
     *
     * @return void
     */
    public function confirmUserDeletion()
    {
        Log::info('DeleteUserForm: confirmUserDeletion called', [
            'current_confirmingUserDeletion' => $this->confirmingUserDeletion
        ]);
        
        try {
            parent::confirmUserDeletion();
            Log::info('DeleteUserForm: confirmUserDeletion completed successfully', [
                'confirmingUserDeletion' => $this->confirmingUserDeletion,
                'after_parent_call' => true
            ]);
            
            // Force a re-render to ensure the modal shows
            $this->dispatch('modal-should-show', ['property' => 'confirmingUserDeletion', 'value' => true]);
        } catch (\Exception $e) {
            Log::error('DeleteUserForm: confirmUserDeletion failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Delete the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Jetstream\Contracts\DeletesUsers  $deleter
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $auth
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function deleteUser(Request $request, DeletesUsers $deleter, StatefulGuard $auth)
    {
        Log::info('DeleteUserForm: deleteUser called', [
            'user_id' => auth()->id(),
            'has_password' => !empty($this->password)
        ]);
        
        try {
            $result = parent::deleteUser($request, $deleter, $auth);
            Log::info('DeleteUserForm: deleteUser completed successfully');
            return $result;
        } catch (\Exception $e) {
            Log::error('DeleteUserForm: deleteUser failed', [
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
        Log::debug('DeleteUserForm: render called', [
            'confirmingUserDeletion' => $this->confirmingUserDeletion
        ]);
        
        return view('profile.delete-user-form');
    }
}
