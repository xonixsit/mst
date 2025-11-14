@extends('emails.layout')

@section('content')
<h2>Client Message Received 💬</h2>

<p>Hello Admin,</p>

<p>You have received a new message from a client that may require your attention or response.</p>

<div class="info-box">
    <h3 style="margin-top: 0; color: #2d3748;">Message Details</h3>
    <p><strong>From:</strong> {{ $message->sender->name }} ({{ $message->sender->email }})</p>
    <p><strong>To:</strong> {{ $message->recipient->name }}</p>
    <p><strong>Subject:</strong> {{ $message->subject }}</p>
    <p><strong>Date:</strong> {{ $message->created_at->format('F j, Y g:i A') }}</p>
    @if($message->priority && $message->priority !== 'normal')
    <p><strong>Priority:</strong> <span style="color: #ed8936; font-weight: 600;">{{ ucfirst($message->priority) }}</span></p>
    @endif
</div>

@if($message->priority === 'high')
<div class="highlight error">
    <h4 style="margin-top: 0;">🚨 High Priority Message</h4>
    <p>This message has been marked as high priority and may require immediate attention.</p>
</div>
@endif

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
</div>
@endif

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('admin.messages.show', $message->id) }}" class="button">View & Respond</a>
</div>

<h3>Client Context</h3>
<div style="background-color: #f7fafc; padding: 20px; border-radius: 6px; margin: 20px 0;">
    <p><strong>Client Status:</strong> {{ $message->client ? ucfirst($message->client->status) : 'N/A' }}</p>
    <p><strong>Recent Activity:</strong> Last login {{ $message->sender->updated_at->diffForHumans() }}</p>
    <p><strong>Total Messages:</strong> {{ $message->sender->sentMessages()->count() }} sent</p>
</div>

<h3>Response Guidelines</h3>
<p>When responding to client messages:</p>
<ul>
    <li>📞 Respond within 24 hours during business days</li>
    <li>📋 Provide clear, actionable information</li>
    <li>📎 Include relevant documents or links if needed</li>
    <li>🎯 Address all questions in the client's message</li>
</ul>

<div class="info-box warning">
    <p><strong>Response Time:</strong> {{ $message->priority === 'high' ? 'High priority messages should be responded to within 4 hours.' : 'Standard response time is within 24 hours during business days.' }}</p>
</div>

<p>Maintaining prompt and professional communication helps ensure client satisfaction and project success.</p>

<p>Best regards,<br>
<strong>MySuperTax System</strong></p>
@endsection