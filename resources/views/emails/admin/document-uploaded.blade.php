@extends('emails.layout')

@section('content')
<h2>New Document Uploaded 📄</h2>

<p>Hello Admin,</p>

<p>A client has uploaded a new document that requires review and processing.</p>

<div class="info-box">
    <h3 style="margin-top: 0; color: #2d3748;">Document Details</h3>
    @if($document->client && $document->client->user)
    <p><strong>Client:</strong> {{ $document->client->user->first_name ?? '' }} {{ $document->client->user->last_name ?? '' }}</p>
    <p><strong>Client Email:</strong> {{ $document->client->user->email ?? 'Not provided' }}</p>
    @else
    <p><strong>Client:</strong> Not available</p>
    <p><strong>Client Email:</strong> Not available</p>
    @endif
    <p><strong>Document Name:</strong> {{ $document->name }}</p>
    <p><strong>Document Type:</strong> {{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</p>
    <p><strong>Upload Date:</strong> {{ $document->created_at->format('F j, Y g:i A') }}</p>
    @if($document->tax_year)
    <p><strong>Tax Year:</strong> {{ $document->tax_year }}</p>
    @endif
    <p><strong>File Size:</strong> {{ number_format($document->file_size / 1024, 2) }} KB</p>
</div>

<div class="highlight warning">
    <h4 style="margin-top: 0;">⏰ Review Required</h4>
    <p>This document is pending review and approval. Please examine the document for:</p>
    <ul style="margin: 10px 0;">
        <li>Document clarity and readability</li>
        <li>Completeness of information</li>
        <li>Correct tax year and client details</li>
        <li>Proper document type classification</li>
    </ul>
</div>

@if($document->client && $document->client->user_id)
<div style="text-align: center; margin: 30px 0;">
    <a href="{{ url('/admin/clients/' . $document->client->id) }}" class="button">Review Document</a>
</div>
@endif

@if($document->client)
<h3>Client Information</h3>
<div style="background-color: #f7fafc; padding: 20px; border-radius: 6px; margin: 20px 0;">
    <p><strong>Client Status:</strong> {{ ucfirst($document->client->status) }}</p>
    <p><strong>Phone:</strong> {{ $document->client->phone ?? 'Not provided' }}</p>
    <p><strong>Total Documents:</strong> {{ $document->client->documents()->count() }} uploaded</p>
</div>
@endif

<h3>Review Actions</h3>
<p>After reviewing the document, you can:</p>
<ul>
    <li>✅ <strong>Approve</strong> - Document meets requirements</li>
    <li>❌ <strong>Reject</strong> - Document needs corrections (provide feedback)</li>
    <li>💬 <strong>Contact Client</strong> - Request additional information</li>
</ul>

<div class="info-box">
    <p><strong>SLA Reminder:</strong> Documents should be reviewed within 2 business days to maintain client satisfaction and project timelines.</p>
</div>

<p>Thank you for maintaining our high standards of document review and client service.</p>

<p>Best regards,<br>
<strong>MySuperTax System</strong></p>
@endsection