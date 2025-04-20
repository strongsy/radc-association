<?php

namespace App\Jobs;

use App\Mail\CancelledEventMail;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class CancelledEventEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $eventId;

    public function __construct(int $eventId)
    {
        $this->eventId = $eventId;
    }

    public function handle()
    {
        // Re-fetch the event from the DB with attendees
        $event = Event::withTrashed()->with('attendees')->findOrFail($this->eventId);

        if ($event->attendees && $event->attendees->count()) {
            foreach ($event->attendees as $user) {
                Mail::to($user->email)->queue(new CancelledEventMail($event, $user));
            }
        }
    }
}
