@component('mail::message')
# Hello {{ $name }},

Thank you for getting in touch with us. Here's a copy of your message:

## Your Message:
{{ $message }}

We appreciate you reaching out to us and will respond to your query as soon as possible.

Best regards,

Thanks,<br>
{{ config('app.name') }}
@endcomponent
