@extends('emails.layout')

@section('content')
<h2>New Client Registration 👤</h2>

<p>Hello Admin,</p>

<p>A new client has registered on the MySuperTax platform and requires your attention.</p>

<div class="info-box">
    <h3 style="margin-top: 0; color: #2d3748;">Client Details</h3>
    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Registration Date:</strong> {{ $user->created_at->format('F j, Y g:i A') }}</p>
    <p><strong>Account Status:</strong> {{ $user->email_verified_at ? 'Email Verified' : 'Pending Email Verification' }}</p>
</div>

<div class="highlight">
    <h4 style="margin-top: 0;">📋 Next Steps Required</h4>
    <p>Please review the new client registration and take the following actions:</p>
    <ul style="margin: 10px 0;">
        <li>Assign a tax professional to this client</li>
        <li>Review client information completeness</li>
        <li>Send welcome communication if needed</li>
        <li>Set up initial consultation if required</li>
    </ul>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('admin.clients.index') }}" class="button">View Client List</a>
</div>

<h3>Client Profile Status</h3>
<p>The client will need to complete their profile information including:</p>
<ul>
    <li>Personal and contact details</li>
    <li>Tax document uploads</li>
    <li>Employment information</li>
    <li>Asset and expense details</li>
</ul>

<div class="info-box warning">
    <p><strong>Action Required:</strong> New clients should be contacted within 24 hours of registration to ensure a smooth onboarding experience.</p>
</div>

<p>This notification was sent to help you stay informed about new client registrations and maintain excellent customer service.</p>

<p>Best regards,<br>
<strong>MySuperTax System</strong></p>
@endsection