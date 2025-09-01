<?php

namespace App\Livewire;

use App\Models\Signature;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SignatureManager extends Component
{
    use WithFileUploads;

    public $signatures;
    public $newSignature;
    public $label = '';

    protected $rules = [
        'newSignature' => 'required|file|mimes:png,jpg,jpeg|max:5120', // 5MB max
        'label' => 'nullable|string|max:120',
    ];

    protected $messages = [
        'newSignature.required' => 'Please select a signature file.',
        'newSignature.mimes' => 'Signature must be a PNG, JPG, or JPEG file.',
        'newSignature.max' => 'Signature file must be less than 5MB.',
        'label.max' => 'Label must be less than 120 characters.',
    ];

    public function mount()
    {
        $this->loadSignatures();
    }

    public function updatedNewSignature()
    {
        if ($this->newSignature) {
            // File was selected, trigger a refresh
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'File selected: ' . $this->newSignature->getClientOriginalName()
            ]);
        }
    }

    public function loadSignatures()
    {
        $this->signatures = Signature::forUser(Auth::id())
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function uploadSignature()
    {
        $this->validate();

        try {
            $user = Auth::user();
            $path = $this->newSignature->store("signatures/{$user->id}", 'local');

            Signature::create([
                'user_id' => $user->id,
                'label' => $this->label,
                'path' => $path,
                'mime_type' => $this->newSignature->getMimeType(),
            ]);

            // Reset form
            $this->reset(['newSignature', 'label']);
            
            // Reload signatures
            $this->loadSignatures();
            
            // Show success message using global notification system
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Signature uploaded successfully!'
            ]);
            
        } catch (\Exception $e) {
            // Show error message using global notification system
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to upload signature. Please try again.'
            ]);
        }
    }

    public function deleteSignature($signatureId)
    {
        try {
            $signature = Signature::findOrFail($signatureId);
            
            if ($signature->user_id !== Auth::id()) {
                abort(404);
            }

            // Delete the file from storage
            if (Storage::disk('local')->exists($signature->path)) {
                Storage::disk('local')->delete($signature->path);
            }

            // Soft delete the record
            $signature->delete();

            // Reload signatures
            $this->loadSignatures();
            
            // Show success message using global notification system
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Signature deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            // Show error message using global notification system
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to delete signature. Please try again.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.signature-manager');
    }
}
