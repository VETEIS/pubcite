<?php

namespace App\Mail;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Notification email for when a request (publication or citation) status changes.
 */
class StatusChangeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    public $user;
    public $newStatus;
    public $adminComment;

    public function __construct(Request $request, $user, $newStatus, $adminComment = null)
    {
        $this->request = $request;
        $this->user = $user;
        $this->newStatus = $newStatus;
        $this->adminComment = $adminComment;
    }

    public function build()
    {
        return $this->subject('Your Request Status Has Changed - ' . $this->request->request_code)
            ->view('emails.status-change-notification');
    }
} 