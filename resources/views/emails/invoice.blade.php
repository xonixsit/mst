<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #10b981 !important;
            color: #ffffff !important;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            font-family: Arial, sans-serif;
        }
        .header h1 {
            color: #ffffff !important;
            margin: 0 0 10px 0 !important;
            font-size: 28px !important;
            font-weight: bold !important;
            line-height: 1.2 !important;
        }
        .header p {
            color: #ffffff !important;
            margin: 0 !important;
            font-size: 16px !important;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .invoice-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #10b981;
        }
        .client-info {
            background: #eff6ff;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .payment-info {
            background: #f0fdf4;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border: 1px solid #bbf7d0;
        }
        .custom-message {
            background: #fef3c7;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #f59e0b;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 10px 0;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f3f4f6;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header" style="background-color: #10b981; color: #ffffff; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="margin: 0 0 10px 0; font-size: 28px; font-weight: bold; color: #ffffff;">📄 Invoice {{ $invoice->invoice_number }}</h1>
        <p style="margin: 0; font-size: 16px; color: #ffffff;">MySuperTax Professional Services</p>
    </div>

    <div class="content">
        <p>Dear {{ $invoice->client->user->first_name ?? 'Valued Client' }},</p>

        @if(isset($emailData['message']) && $emailData['message'])
        <div class="custom-message">
            <strong>Personal Message:</strong><br>
            {{ $emailData['message'] }}
        </div>
        @endif

        <p>Please find your invoice details below for our professional tax services.</p>

        <div class="invoice-details">
            <h3>Invoice Information</h3>
            <table>
                <tr>
                    <td><strong>Invoice Number:</strong></td>
                    <td>{{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td><strong>Title:</strong></td>
                    <td>{{ $invoice->title }}</td>
                </tr>
                <tr>
                    <td><strong>Tax Year:</strong></td>
                    <td>{{ $invoice->invoice_year }}</td>
                </tr>
                <tr>
                    <td><strong>Invoice Date:</strong></td>
                    <td>{{ $invoice->created_at->format('F j, Y') }}</td>
                </tr>
                @if(isset($emailData['due_date']) && $emailData['due_date'])
                <tr>
                    <td><strong>Due Date:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($emailData['due_date'])->format('F j, Y') }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="client-info">
            <h3>Client Information</h3>
            <p><strong>Name:</strong> {{ $invoice->client->user->first_name ?? '' }} {{ $invoice->client->user->last_name ?? '' }}</p>
            <p><strong>Email:</strong> {{ $invoice->client->user->email ?? $invoice->send_to_email }}</p>
        </div>

        <h3>Services Provided</h3>
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->service_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>${{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="text-align: right; margin: 20px 0;">
            <p><strong>Subtotal:</strong> ${{ number_format($invoice->subtotal, 2) }}</p>
            <p><strong>Tax ({{ $invoice->tax_rate }}%):</strong> ${{ number_format($invoice->tax_amount, 2) }}</p>
            <p class="amount"><strong>Total Amount: ${{ number_format($invoice->total_amount, 2) }}</strong></p>
        </div>

        <div class="payment-info">
            <h3>💳 Pay Online Securely</h3>
            <p>You can pay this invoice securely online using your preferred payment method:</p>
            <ul>
                <li>Credit/Debit Cards (Visa, MasterCard, Amex)</li>
                <li>Bank Transfer (ACH)</li>
                <li>PayPal</li>
            </ul>
            @if(isset($emailData['payment_link']) && $emailData['payment_link'])
                <a href="{{ $emailData['payment_link'] }}" class="btn">Pay Invoice Online</a>
            @else
                <a href="{{ config('app.url') }}/client/invoices/{{ $invoice->id }}/pay" class="btn">Pay Invoice Online</a>
            @endif
            @if(isset($emailData['due_date']) && $emailData['due_date'])
            <p><strong>Payment Due:</strong> {{ \Carbon\Carbon::parse($emailData['due_date'])->format('F j, Y') }}</p>
            @endif
        </div>

        @if($invoice->comments)
        <div style="margin: 20px 0; padding: 15px; background: #f3f4f6; border-radius: 8px;">
            <h4>Additional Notes:</h4>
            <p>{{ $invoice->comments }}</p>
        </div>
        @endif

        <p>Thank you for choosing MySuperTax for your tax preparation needs. We appreciate your business!</p>

        <p>If you have any questions about this invoice, please don't hesitate to contact us.</p>

        <p>Best regards,<br>
        <strong>MySuperTax Team</strong></p>
    </div>

    <div class="footer">
        <p>MySuperTax Professional Services</p>
        <p>📧 support@mysupertax.com | 📞 (555) 123-4567</p>
        <p>This is an automated email. Please do not reply directly to this message.</p>
    </div>
</body>
</html>