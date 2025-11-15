<?php

namespace App\Mail;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SubmissionNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $request;
    public $user;
    public $isAdminNotification;

    /**
     * Create a new message instance.
     */
    public $tries = 3; // Retry up to 3 times on failure
    public $backoff = [60, 300, 900]; // Exponential backoff: 1min, 5min, 15min
    public $timeout = 30; // Timeout after 30 seconds (faster failure)
    
    public function __construct(Request $request, $user, $isAdminNotification = false)
    {
        $this->request = $request;
        $this->user = $user;
        $this->isAdminNotification = $isAdminNotification;
        
        // Set queue configuration for better performance
        $this->onQueue('emails');
        $this->delay(now()->addSeconds(1)); // Small delay to ensure request is fully processed
    }
    
    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Email sending failed after retries', [
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
        $subject = $this->isAdminNotification 
            ? 'New ' . strtolower($this->request->type) . ' Request Received'
            : "Your {$this->request->type} Request Submitted Successfully - {$this->request->request_code}";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->isAdminNotification ? 'emails.admin-submission-notification' : 'emails.user-submission-notification',
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