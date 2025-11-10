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

