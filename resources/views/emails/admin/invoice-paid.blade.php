@extends('emails.layout')

@section('content')
<h2>Payment Received 💰</h2>

<p>Hello Admin,</p>

<p>Great news! A client has made a payment for their invoice.</p>

<div class="info-box success">
    <h3 style="margin-top: 0; color: #2d3748;">Payment Details</h3>
    <p><strong>Client:</strong> {{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</p>
    <p><strong>Client Email:</strong> {{ $invoice->client->email }}</p>
    <p><strong>Invoice Number:</strong> #{{ $invoice->invoice_number }}</p>
    <p><strong>Amount Paid:</strong> ${{ number_format($invoice->total_amount, 2) }}</p>
    <p><strong>Payment Date:</strong> {{ $invoice->paid_at->format('F j, Y g:i A') }}</p>
    <p><strong>Invoice Date:</strong> {{ $invoice->created_at->format('F j, Y') }}</p>
</div>

<div class="highlight success">
    <h4 style="margin-top: 0;">💳 Payment Processed</h4>
    <p>The payment has been successfully processed and the invoice status has been updated to "Paid". The client has been automatically notified of the payment confirmation.</p>
</div>

<h3>Services Paid For</h3>
<div style="background-color: #f7fafc; padding: 20px; border-radius: 6px; margin: 20px 0;">
    @foreach($invoice->items as $item)
    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e2e8f0;">
        <div>
            <strong>{{ $item->service_name }}</strong>
            <br><small>Qty: {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}</small>
        </div>
        <div style="text-align: right;">
            <strong>${{ number_format($item->quantity * $item->unit_price, 2) }}</strong>
        </div>
    </div>
    @endforeach
    
    <div style="padding-top: 15px; margin-top: 15px; border-top: 2px solid #667eea;">
        <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 600;">
            <span>Total Paid:</span>
            <span>${{ number_format($invoice->total_amount, 2) }}</span>
        </div>
    </div>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="button">View Invoice Details</a>
</div>

<h3>Next Steps</h3>
<p>Now that payment has been received:</p>
<ul>
    <li>📋 Continue with tax preparation services</li>
    <li>📧 Client has been notified of payment confirmation</li>
    <li>💼 Update project status if needed</li>
    <li>📊 Payment will be reflected in financial reports</li>
</ul>

<h3>Client Account Summary</h3>
<div style="background-color: #f0fff4; padding: 15px; border-radius: 6px; border-left: 4px solid #48bb78; margin: 20px 0;">
    <p><strong>Account Status:</strong> Payment up to date</p>
    <p><strong>Client Since:</strong> {{ $invoice->client->created_at->format('F Y') }}</p>
    <p><strong>Total Invoices:</strong> {{ $invoice->client->invoices()->count() }}</p>
    <p><strong>Total Paid:</strong> ${{ number_format($invoice->client->invoices()->where('status', 'paid')->sum('total_amount'), 2) }}</p>
</div>

<p>Thank you for providing excellent service that results in satisfied clients and timely payments.</p>

<p>Best regards,<br>
<strong>MySuperTax System</strong></p>
@endsection