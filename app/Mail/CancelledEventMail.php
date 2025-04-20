<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CancelledEventMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Event $event, public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cancelled Event',
        );
    }

    public function content(): Content
    {

        return new Content(
            markdown: 'emails.cancelled-event',
        );

    }

    public function attachments(): array
    {
        return [];
    }
}
