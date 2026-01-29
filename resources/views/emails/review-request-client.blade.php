@component('mail::message')
# Review Request Confirmation

Hello {{ $client->user->first_name }},

Thank you for submitting your review request. We have received your request and our tax professionals will review your information shortly.

## Request Details

**Priority:** {{ ucfirst($priority) }}  
**Submitted At:** {{ now()->format('F j, Y \a\t g:i A') }}

@if(!empty($sections))
**Sections Submitted for Review:**
@foreach($sections as $section)
- {{ ucfirst(str_replace('_', ' ', $section)) }}
@endforeach
@else
**All sections** submitted for review
@endif

@if($message)
## Your Message
{{ $message }}
@endif

## What Happens Next?

1. Our tax professionals will review your submitted information
2. You will receive an email notification when the review is complete
3. Any feedback or required changes will be communicated to you
4. You can continue to update your information if needed

@component('mail::button', ['url' => $clientUrl])
View Your Information
@endcomponent

## Need Help?

If you have any questions or need to make changes to your information, please don't hesitate to contact us or log into your account.

Best regards,  
**MySuperTax Team**
@endcomponent