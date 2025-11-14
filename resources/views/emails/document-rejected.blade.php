@extends('emails.layout')

@section('content')
<h2>Document Requires Attention ⚠️</h2>

<p>Dear {{ $document->client->first_name }} {{ $document->client->last_name }},</p>

<p>We've reviewed your submitted document and need some additional information or clarification before we can proceed.</p>

<div class="info-box error">
    <h3 style="margin-top: 0; color: #2d3748;">Document Details</h3>
    <p><strong>Document Name:</strong> {{ $document->name }}</p>
    <p><strong>Document Type:</strong> {{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</p>
    <p><strong>Upload Date:</strong> {{ $document->created_at->format('F j, Y g:i A') }}</p>
    <p><strong>Review Date:</strong> {{ $document->updated_at->format('F j, Y g:i A') }}</p>
    @if($document->tax_year)
    <p><strong>Tax Year:</strong> {{ $document->tax_year }}</p>
    @endif
</div>

<div class="highlight error">
    <h4 style="margin-top: 0;">📋 Action Required</h4>
    <p>This document needs attention before it can be processed for your tax return. Please review the feedback below and take the necessary action.</p>
</div>

@if($document->notes)
<h3>Reviewer Feedback</h3>
<div style="background-color: #fed7d7; padding: 20px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #f56565;">
    <p><strong>What needs to be addressed:</strong></p>
    <p>{{ $document->notes }}</p>
</div>
@endif

<h3>Common Issues & Solutions</h3>
<div style="background-color: #f7fafc; padding: 20px; border-radius: 6px; margin: 20px 0;">
    <ul style="margin: 0;">
        <li><strong>Image Quality:</strong> Ensure the document is clear and all text is readable</li>
        <li><strong>Complete Pages:</strong> Make sure all pages of multi-page documents are included</li>
        <li><strong>Correct Year:</strong> Verify the document is for the correct tax year</li>
        <li><strong>Personal Information:</strong> Ensure your name and details match your account</li>
        <li><strong>File Format:</strong> Use PDF, JPG, or PNG formats for best results</li>
    </ul>
</div>

<h3>Next Steps</h3>
<p>To resolve this issue, please:</p>
<ol>
    <li><strong>Review the feedback</strong> provided by our tax professional</li>
    <li><strong>Make necessary corrections</strong> or obtain additional documentation</li>
    <li><strong>Re-upload the document</strong> through your client dashboard</li>
    <li><strong>Contact us</strong> if you need clarification on the requirements</li>
</ol>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('client.documents') }}" class="button">Upload Corrected Document</a>
</div>

<h3>Need Help?</h3>
<p>If you're unsure about the feedback or need assistance:</p>
<ul>
    <li>💬 Send a message through your client dashboard</li>
    <li>📧 Reply to this email with your questions</li>
    <li>📞 Schedule a call with your assigned tax professional</li>
</ul>

<div class="info-box warning">
    <p><strong>Important:</strong> Resolving document issues promptly helps ensure your tax return is completed on time. If you need an extension or have concerns about deadlines, please contact us immediately.</p>
</div>

<p>We're here to help make this process as smooth as possible. Don't hesitate to reach out if you need any assistance.</p>

<p>Best regards,<br>
<strong>MySuperTax Document Review Team</strong></p>
@endsection