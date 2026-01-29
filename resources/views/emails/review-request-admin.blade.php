@component('mail::message')
# New Review Request

Hello,

**{{ $clientName }}** has submitted a review request for their tax information.

## Request Details

**Priority:** {{ ucfirst($priority) }}  
**Requested At:** {{ now()->format('F j, Y \a\t g:i A') }}

@if(!empty($sections))
**Sections for Review:**
@foreach($sections as $section)
- {{ ucfirst(str_replace('_', ' ', $section)) }}
@endforeach
@else
**All sections** requested for review
@endif

@if($message)
## Client Message
{{ $message }}
@endif

## Client Information
**Name:** {{ $clientName }}  
**Email:** {{ $client->user->email }}  
**Phone:** {{ $client->phone ?? 'Not provided' }}

@component('mail::button', ['url' => $adminUrl])
Review Client Information
@endcomponent

Please review the client's information and provide feedback as needed.

Best regards,  
**MySuperTax Team**
@endcomponent