<?php

namespace App\Jobs;

use App\Mail\NewEventMail;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NewEventEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Event $event;

    protected $users;

    public function __construct(Event $event, $users)
    {
        $this->event = $event;
        $this->users = $users;
    }

    public function handle(): void
    {

        foreach ($this->users as $user) {
            Mail::to($user->email)->queue(new NewEventMail($this->event, $user));
        }

    }
}
