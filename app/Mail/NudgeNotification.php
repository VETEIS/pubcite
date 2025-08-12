<?php

namespace App\Mail;

use App\Models\Request as UserRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NudgeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    public $user;

    public function __construct(UserRequest $request, $user)
    {
        $this->request = $request;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nudge: Pending ' . $this->request->type . ' Request ' . $this->request->request_code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nudge-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
} 