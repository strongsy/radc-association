<?php

namespace App\Notifications;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CancelledEventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $date = Carbon::parse($this->event->date)->format('l, jS F Y');
        $time = Carbon::parse($this->event->time)->format('H:i');
        $creator = $this->event->user?->name ?? 'the Events Team';

        $unsubscribeUrl = route('unsubscribe', ['token' => $notifiable->unsubscribe_token]);

        $message = new MailMessage;

        // Try multiple ways to pass the data
        return $message
            ->subject('Event Cancelled: '.$this->event->title)
            ->markdown('emails.notifications.cancelled-event', [
                'event' => $this->event,
                'user' => $notifiable,
                'date' => $date,
                'time' => $time,
                'creator' => $creator,
                'unsubscribeUrl' => $unsubscribeUrl,
            ]);
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
