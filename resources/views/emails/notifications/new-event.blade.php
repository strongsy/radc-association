{{--@php
use Carbon\Carbon;

// Extract just the date and just the time
$datePart = Carbon::parse($event->date)->toDateString();  // e.g., "2025-04-19"
$timePart = Carbon::parse($event->time)->format('H:i:s'); // e.g., "20:46:59"

$date = Carbon::parse($event->date)->format('l jS F Y');
$time = Carbon::parse($event->time)->format('H:i');

@endphp--}}

@component('mail::message')
# A new event has been created!

Hello {{ $user->first_name ?? '' }},

We're excited to let you know that a new event, **{{ $event->title }}**, has just been created!

This event was created by **{{ $creator ?? '' }}**.

📍 Location Address: **{{ $event->location ?? '' }}**

Map: <a href="{{ 'https://www.google.com/maps?q=' . urlencode($event->location ?? '') }}" target="_blank" rel="noopener noreferrer" class="text-blue-500 underline">
    View on Google Maps
</a>

📅 Date: **{{ $date ?? '' }}**

⏰ Time: **{{ $time ?? '' }}**


To find out more or RSVP, please click the button below to view the event.

@component('mail::button', ['url' => config('app.url') . '/events'])
View Event
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@endcomponent
