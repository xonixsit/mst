@component('mail::message')
# New Lead Inquiry

A new lead has submitted a contact form inquiry.

**Contact Information:**

- **Name:** {{ $lead->name }}
- **Email:** {{ $lead->email }}
- **Phone:** {{ $lead->phone }}
- **State:** {{ $lead->state }}
- **City:** {{ $lead->city }}

@component('mail::button', ['url' => config('app.url')])
View in Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
