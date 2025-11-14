@extends('emails.layout')

@section('content')
<h2>Document Approved ✅</h2>

<p>Dear {{ $document->client->first_name }} {{ $document->client->last_name }},</p>

<p>Great news! Your document has been reviewed and approved by our tax professionals.</p>

<div class="info-box success">
    <h3 style="margin-top: 0; color: #2d3748;">Document Details</h3>
    <p><strong>Document Name:</strong> {{ $document->name }}</p>
    <p><strong>Document Type:</strong> {{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</p>
    <p><strong>Upload Date:</strong> {{ $document->created_at->format('F j, Y g:i A') }}</p>
    <p><strong>Approved Date:</strong> {{ $document->updated_at->format('F j, Y g:i A') }}</p>
    @if($document->tax_year)
    <p><strong>Tax Year:</strong> {{ $document->tax_year }}</p>
    @endif
</div>

<div class="highlight success">
    <h4 style="margin-top: 0;">🎉 Status: Approved</h4>
    <p>Your document has been successfully reviewed and meets all requirements for tax preparation. Our team has verified the information and it will be included in your tax filing process.</p>
</div>

@if($document->notes)
<h3>Reviewer Notes</h3>
<div style="background-color: #f7fafc; padding: 20px; border-radius: 6px; margin: 20px 0;">
    <p>{{ $document->notes }}</p>
</div>
@endif

<h3>What Happens Next?</h3>
<p>Now that your document is approved:</p>
<ol>
    <li><strong>Processing:</strong> The document will be included in your tax preparation</li>
    <li><strong>Review:</strong> Our tax professionals will incorporate this information into your return</li>
    <li><strong>Updates:</strong> You'll receive notifications about any additional requirements</li>
    <li><strong>Completion:</strong> We'll notify you when your tax return is ready for review</li>
</ol>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('client.documents') }}" class="button">View All Documents</a>
</div>

<h3>Need to Upload More Documents?</h3>
<p>If you have additional documents to submit for your tax preparation, you can:</p>
<ul>
    <li>📁 Upload them through your client dashboard</li>
    <li>📧 Email them to your assigned tax professional</li>
    <li>💬 Use our secure messaging system</li>
</ul>

<div class="info-box">
    <p><strong>Tip:</strong> Keep your original documents in a safe place. While we securely store digital copies, it's always good practice to maintain your own records.</p>
</div>

<p>Thank you for providing the necessary documentation. This helps us ensure accurate and timely completion of your tax return.</p>

<p>Best regards,<br>
<strong>MySuperTax Document Review Team</strong></p>
@endsection