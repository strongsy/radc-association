@component('mail::message')
# New Registration Form Submission

You’ve received a new registration form submission:

## Name:
{{ $name }}

## Email:
{{ $email }}

## Community:
{{ $community }}

## Membership:
{{ $membership }}

## Affiliation:
{{ $affiliation }}

Please action this registration request within 72 hours.

@component('mail::button', ['url' => config('app.url') . '/registrants'])
Registrants List
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
