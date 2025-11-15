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

class SignatoryNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $request;
    public $signatoryType;
    public $signatoryName;

    /**
     * Create a new message instance.
     */
    public $tries = 3; // Retry up to 3 times on failure
    public $backoff = [60, 300, 900]; // Exponential backoff: 1min, 5min, 15min
    public $timeout = 30; // Timeout after 30 seconds (faster failure)
    
    public function __construct(Request $request, string $signatoryType, string $signatoryName)
    {
        $this->request = $request;
        $this->signatoryType = $signatoryType;
        $this->signatoryName = $signatoryName;
        
        // Set queue configuration for better performance
        $this->onQueue('emails');
        $this->delay(now()->addSeconds(2)); // Small delay to ensure request is fully processed
    }
    
    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Signatory email sending failed after retries', [
            'request_id' => $this->request->id,
            'signatory_type' => $this->signatoryType,
            'signatory_name' => $this->signatoryName,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $position = ucwords(str_replace('_', ' ', $this->signatoryType));
        
        return new Envelope(
            subject: "New {$this->request->type} Request Requires Your Signature - {$this->request->request_code}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.signatory-notification',
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
