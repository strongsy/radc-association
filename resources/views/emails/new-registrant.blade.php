@component('mail::message')
# @lang('New Registration Form Submission')

@lang('You’ve received a new registration form submission:')

@if(!empty($name))
## @lang('Name:')
{{ $name }}
@endif

@if(!empty($email))
## @lang('Email:')
{{ $email }}
@endif

@if(!empty($community))
## @lang('Community:')
{{ $community }}
@endif

@if(!empty($membership))
## @lang('Membership:')
{{ $membership }}
@endif

@if(!empty($affiliation))
## @lang('Affiliation:')
{{ $affiliation }}
@endif

@lang('Please action this registration request within 72 hours.')

@component('mail::button', ['url' => route('registrant.index')])
@lang('Registrants List')
@endcomponent

@lang('Thanks'),
{{ config('app.name') }}

@slot('footer')
You are receiving this email because you signed up for {{ config('app.name') }}. If you no longer wish to receive these emails, please [unsubscribe]({{ config('app.url') }}/unsubscribe).
@endslot
@endcomponent
