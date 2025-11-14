@extends('emails.layout')

@section('content')
<h2>New Message from Your Tax Professional 💬</h2>

<p>Dear {{ $message->recipient->name }},</p>

<p>You have received a new message from your tax professional regarding your account.</p>

<div class="info-box">
    <h3 style="margin-top: 0; color: #2d3748;">Message Details</h3>
    <p><strong>From:</strong> {{ $message->sender->name }}</p>
    <p><strong>Subject:</strong> {{ $message->subject }}</p>
    <p><strong>Date:</strong> {{ $message->created_at->format('F j, Y g:i A') }}</p>
    @if($message->priority && $message->priority !== 'normal')
    <p><strong>Priority:</strong> <span style="color: #ed8936; font-weight: 600;">{{ ucfirst($message->priority) }}</span></p>
    @endif
</div>

<h3>Message Content</h3>
<div style="background-color: #f7fafc; padding: 20px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #667eea;">
    <p style="white-space: pre-wrap; margin: 0;">{{ $message->body }}</p>
</div>

@if($message->attachments && count($message->attachments) > 0)
<h3>Attachments</h3>
<div style="background-color: #fef5e7; padding: 15px; border-radius: 6px; margin: 20px 0;">
    <p><strong>📎 This message includes {{ count($message->attachments) }} attachment(s):</strong></p>
    <ul style="margin: 10px 0;">
        @foreach($message->attachments as $attachment)
        <li>{{ $attachment['name'] }} ({{ $attachment['size'] }})</li>
        @endforeach
    </ul>
    <p><small>Please log in to your dashboard to download attachments.</small></p>
</div>
@endif

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('client.messages.show', $message->id) }}" class="button">View & Reply to Message</a>
</div>

@if($message->priority === 'urgent')
<div class="highlight error">
    <h4 style="margin-top: 0;">🚨 Urgent Message</h4>
    <p>This message has been marked as urgent. Please review and respond as soon as possible to avoid any delays in your tax preparation process.</p>
</div>
@endif

<h3>Quick Actions</h3>
<p>From your client dashboard, you can:</p>
<ul>
    <li>💬 Reply to this message</li>
    <li>📁 Upload additional documents if requested</li>
    <li>📅 Schedule a consultation if needed</li>
    <li>📋 Check the status of your tax return</li>
</ul>

<div class="info-box">
    <p><strong>Response Time:</strong> We typically respond to client messages within 24 hours during business days. For urgent matters, please call our office directly.</p>
</div>

<p>Thank you for using our secure messaging system. This helps us maintain clear communication and keep all your tax-related discussions organized.</p>

<p>Best regards,<br>
<strong>MySuperTax Communication Team</strong></p>
@endsection