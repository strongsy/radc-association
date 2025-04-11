@component('mail::message')
# Hello {{ $name }},

Thank you for registering with us.

If you meet the requirement for registration, you will be notified by email when your account is activated.

We will respond to your registration request as soon as possible.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
