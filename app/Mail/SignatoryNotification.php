<?php

namespace App\Mail;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SignatoryNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $request;
    public $signatoryType;
    public $signatoryName;

    /**
     * Create a new message instance.
     */
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
