<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

class NewRegistrantMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected array $data;

    public function __construct(array $data)
    {
        $requiredKeys = ['name', 'email', 'community', 'membership', 'affiliation'];

        foreach ($requiredKeys as $key) {
            if (! array_key_exists($key, $data)) {
                throw new InvalidArgumentException("Missing required key: {$key}");
            }
        }

        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        $subject = $this->data['subject'] ?? 'New Registrant Submission';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-registrant',
            with: [
                'name' => $this->data['name'] ?? 'Unknown',
                'email' => $this->data['email'] ?? 'Unknown',
                'community' => $this->data['community'] ?? 'Unknown',
                'membership' => $this->data['membership'] ?? 'Unknown',
                'affiliation' => $this->data['affiliation'] ?? 'Unknown',
            ]);
    }

    public function attachments(): array
    {
        return [];
    }
}
