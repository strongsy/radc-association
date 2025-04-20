<?php

namespace App\Notifications;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEventNotification extends Notification implements ShouldQueue
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
        $creator = $this->event->user?->name ?? 'Anonymous';

        $unsubscribeUrl = url('/unsubscribe/'.$notifiable->unsubscribe_token);

        return (new MailMessage)
            ->subject('Event Cancelled: '.$this->event->title)
            ->markdown('emails.notifications.new-event', [
                'event' => $this->event,
                'user' => $notifiable,  // User variable passed here
                'date' => $date,
                'time' => $time,
                'creator' => $creator,
                'url' => $unsubscribeUrl,
            ])
            ->with([
                'user' => $notifiable, // Explicitly pass the user to the global layout
            ]);
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
