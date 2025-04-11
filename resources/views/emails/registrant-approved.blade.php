@component('mail::message')
# Hello {{ $name }},

Good news! Your account has been approved.

## Your login details are:
Email {{ $email }}
Temporary Password {{ $password }}

@component('mail::button', ['url' => ['url' => config('app.url') . '/login']])
   Log In
@endcomponent

Please change your password as soon as possible.

Best regards,

Thanks,<br>
{{ config('app.name') }}
@endcomponent
