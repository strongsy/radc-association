<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewRegistrantMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected array $data;

    public function __construct(array $data)
    {
       $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Registrant Submission',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-registrant',
            with: [
                'name' => $this->data['name'],
                'email' => $this->data['email'],
                'community' => $this->data['community'],
                'membership' => $this->data['membership'],
                'affiliation' => $this->data['affiliation'],
            ]);
    }

    public function attachments(): array
    {
        return [];
    }
}
