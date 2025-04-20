@component('mail::message')
# Hi {{ $user->first_name }},

We're sorry to inform you that the event you were attending, **{{ $event->title }}**, has been cancelled.

📍 **Location:** {{ $event->location }}

📅 **Date:** {{ $date }}

⏰ **Time:** {{ $time }}

This event was created by **{{ $creator }}**.

We apologize for the inconvenience.

Thanks,<br>
{{ config('app.name') }}

<p style="margin-top: 1.5rem; font-size: 12px; color: #aaa;">
No longer want to receive these emails? <a href="{{ $unsubscribeUrl }}">Unsubscribe</a>.
</p>
@endcomponent
