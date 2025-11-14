@extends('emails.layout')

@section('content')
<h2>New Invoice Created 📄</h2>

<p>Dear {{ $invoice->client->first_name }} {{ $invoice->client->last_name }},</p>

<p>A new invoice has been created for your tax consulting services. Please review the details below:</p>

<div class="info-box">
    <h3 style="margin-top: 0; color: #2d3748;">Invoice Details</h3>
    <p><strong>Invoice Number:</strong> #{{ $invoice->invoice_number }}</p>
    <p><strong>Date:</strong> {{ $invoice->created_at->format('F j, Y') }}</p>
    <p><strong>Due Date:</strong> {{ $invoice->due_date->format('F j, Y') }}</p>
    <p><strong>Total Amount:</strong> ${{ number_format($invoice->total_amount, 2) }}</p>
    <p><strong>Status:</strong> <span style="color: #ed8936; font-weight: 600;">{{ ucfirst($invoice->status) }}</span></p>
</div>

<h3>Services Included</h3>
<div style="background-color: #f7fafc; padding: 20px; border-radius: 6px; margin: 20px 0;">
    @foreach($invoice->items as $item)
    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e2e8f0;">
        <div>
            <strong>{{ $item->description }}</strong>
            @if($item->notes)
                <br><small style="color: #718096;">{{ $item->notes }}</small>
            @endif
        </div>
        <div style="text-align: right;">
            <strong>${{ number_format($item->amount, 2) }}</strong>
            <br><small>Qty: {{ $item->quantity }}</small>
        </div>
    </div>
    @endforeach
    
    <div style="padding-top: 15px; margin-top: 15px; border-top: 2px solid #667eea;">
        <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 600;">
            <span>Total Amount:</span>
            <span>${{ number_format($invoice->total_amount, 2) }}</span>
        </div>
    </div>
</div>

@if($invoice->notes)
<div class="highlight">
    <h4 style="margin-top: 0;">📝 Additional Notes</h4>
    <p>{{ $invoice->notes }}</p>
</div>
@endif

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('client.dashboard') }}" class="button">View Invoice Details</a>
</div>

<h3>Payment Information</h3>
<p>Please review your invoice and make payment by the due date to avoid any late fees. You can:</p>
<ul>
    <li>💳 Pay online through your client dashboard</li>
    <li>🏦 Make a bank transfer (details in your dashboard)</li>
    <li>📞 Contact us to discuss payment options</li>
</ul>

<div class="info-box warning">
    <p><strong>Important:</strong> This invoice is due on {{ $invoice->due_date->format('F j, Y') }}. Late payments may incur additional fees as outlined in our service agreement.</p>
</div>

<p>If you have any questions about this invoice or need to discuss payment arrangements, please don't hesitate to contact us.</p>

<p>Thank you for your business!</p>

<p>Best regards,<br>
<strong>MySuperTax Billing Team</strong></p>
@endsection