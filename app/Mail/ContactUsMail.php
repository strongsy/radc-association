<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Log;

class ContactUsMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected array $data;

    public function __construct(array $data)
    {
        $requiredKeys = ['name', 'email', 'subject', 'message'];

        foreach ($requiredKeys as $key) {
            if (! array_key_exists($key, $data)) {
                throw new \InvalidArgumentException("Missing required key: {$key}");
            }
        }

        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        $subject = $this->data['subject'] ?? 'Thank you for contacting us';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        if (! isset($this->data['name'], $this->data['email'], $this->data['subject'], $this->data['message'])) {
            Log::error('ContactUsMail data is incomplete.', ['data' => $this->data]);
        }

        return new Content(
            markdown: 'emails.new-registrant',
            with: [
                'name' => $this->data['name'] ?? 'Unknown',
                'email' => $this->data['email'] ?? 'Unknown',
                'subject' => $this->data['subject'] ?? 'No Subject',
                'message' => $this->data['message'] ?? 'No Message',
            ]);
    }

    public function attachments(): array
    {
        return [];
    }
}
