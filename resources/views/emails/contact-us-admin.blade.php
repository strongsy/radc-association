@component('mail::message')
# New Contact Form Submission

You’ve received a new contact form submission:

## Name:
{{ $name }}

## Email:
{{ $email }}

## Message:
{{ $message }}

Please respond to this inquiry within 72 hours.

@component('mail::button', ['url' => config('app.url') . '/mail'])
Emails
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
