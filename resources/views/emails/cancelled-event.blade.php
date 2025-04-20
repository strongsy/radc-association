@php
use Carbon\Carbon;

// Extract just the date and just the time
$datePart = Carbon::parse($event->date)->toDateString();  // e.g., "2025-04-19"
$timePart = Carbon::parse($event->time)->format('H:i:s'); // e.g., "20:46:59"

// Combine and parse into a Carbon datetime
$datetime = Carbon::parse($datePart . ' ' . $timePart);

// UK format: Monday, 8th April 2025 at 7:30 PM
$formattedDateTime = $datetime->translatedFormat('l jS F Y \a\t g:i A');
@endphp

@component('mail::message')
# Event Cancelled

Hi {{ $user->first_name }},

We regret to inform you that the event **{{ $event->title }}** scheduled on **{{ $formattedDateTime }}** has been cancelled.

We apologize for the inconvenience.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
