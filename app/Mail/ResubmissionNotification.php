<?php

namespace App\Mail;

use App\Models\Request;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ResubmissionNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $request;
    public $user;
    public $reason;
    public $centerManagerName;

    /**
     * Create a new message instance.
     */
    public $tries = 3; // Retry up to 3 times on failure
    public $backoff = [60, 300, 900]; // Exponential backoff: 1min, 5min, 15min
    public $timeout = 30; // Timeout after 30 seconds (faster failure)
    
    public function __construct(Request $request, User $user, string $reason, string $centerManagerName)
    {
        $this->request = $request;
        $this->user = $user;
        $this->reason = $reason;
        $this->centerManagerName = $centerManagerName;
        
        // Set queue configuration for better performance
        $this->onQueue('emails');
        $this->delay(now()->addSeconds(1));
    }
    
    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Resubmission email sending failed after retries', [
            'request_id' => $this->request->id,
            'user_email' => $this->user->email,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Resubmission Required - {$this->request->request_code}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.resubmission-notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

