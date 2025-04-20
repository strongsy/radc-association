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

class NewEventMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Event $event;

    public User $user;

    public function __construct(Event $event, User $user)
    {
        $this->event = $event;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Event',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-event',
            with: [
                'event' => $this->event,
                'user' => $this->user, // Make sure to pass the user here
            ]);
    }

    public function attachments(): array
    {
        return [];
    }
}
