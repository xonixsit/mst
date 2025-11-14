@extends('emails.layout')

@section('content')
<h2>Payment Received - Thank You! 💳</h2>

<p>Dear {{ $invoice->client->first_name }} {{ $invoice->client->last_name }},</p>

<p>We have successfully received your payment for invoice #{{ $invoice->invoice_number }}. Thank you for your prompt payment!</p>

<div class="info-box success">
    <h3 style="margin-top: 0; color: #2d3748;">Payment Confirmation</h3>
    <p><strong>Invoice Number:</strong> #{{ $invoice->invoice_number }}</p>
    <p><strong>Payment Date:</strong> {{ $invoice->paid_at->format('F j, Y g:i A') }}</p>
    <p><strong>Amount Paid:</strong> ${{ number_format($invoice->total_amount, 2) }}</p>
    <p><strong>Payment Status:</strong> <span style="color: #48bb78; font-weight: 600;">Paid in Full</span></p>
</div>

<div class="highlight success">
    <h4 style="margin-top: 0;">✅ Payment Processed Successfully</h4>
    <p>Your payment has been processed and applied to your account. You should see this transaction reflected in your payment method within 1-2 business days.</p>
</div>

<h3>Services Paid For</h3>
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
        </div>
    </div>
    @endforeach
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('client.dashboard') }}" class="button">View Payment History</a>
</div>

<h3>What's Next?</h3>
<p>Now that your payment is complete:</p>
<ul>
    <li>📄 We'll continue processing your tax return</li>
    <li>📧 You'll receive updates on the progress</li>
    <li>📋 We'll notify you when your return is ready for review</li>
    <li>🎯 Final filing will be completed according to your timeline</li>
</ul>

<div class="info-box">
    <p><strong>Receipt:</strong> This email serves as your payment receipt. Please keep it for your records. A detailed receipt is also available in your client dashboard.</p>
</div>

<p>Thank you for choosing MySuperTax for your tax consulting needs. We appreciate your business and trust in our services.</p>

<p>Best regards,<br>
<strong>MySuperTax Accounting Team</strong></p>
@endsection