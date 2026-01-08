@component('mail::message')
# Two-Factor Authentication Code

Hello {{ $user->first_name }},

Your two-factor authentication code is:

@component('mail::panel')
# {{ $code }}
@endcomponent

This code will expire in 10 minutes.

If you didn't request this code, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
