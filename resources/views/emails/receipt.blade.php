<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $invoice->invoice_number }}</title>
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
            background-color: #059669 !important;
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
        .receipt-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #059669;
        }
        .client-info {
            background: #eff6ff;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .payment-confirmation {
            background: #f0fdf4;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 2px solid #bbf7d0;
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
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
        .success-icon {
            font-size: 48px;
            color: #059669;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header" style="background-color: #059669; color: #ffffff; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="margin: 0 0 10px 0; font-size: 28px; font-weight: bold; color: #ffffff;">Payment Receipt</h1>
        <p style="margin: 0; font-size: 16px; color: #ffffff;">MySuperTax Professional Services</p>
    </div>

    <div class="content">
        <div class="payment-confirmation">
            <div class="success-icon">✓</div>
            <h2 style="color: #059669; margin: 0 0 10px 0;">Payment Received Successfully!</h2>
            <p style="margin: 0; font-size: 16px;">Thank you for your payment. Your invoice has been marked as paid.</p>
        </div>

        <p>Dear {{ $invoice->client->user->first_name ?? 'Valued Client' }},</p>

        <p>We are delighted to confirm that we have successfully received your payment for Invoice {{ $invoice->invoice_number }}. We sincerely appreciate your prompt payment and continued trust in our services.</p>

        <p>This email serves as your official payment receipt and confirmation of the completed transaction.</p>

        <div class="receipt-details">
            <h3>Payment Details</h3>
            <table>
                <tr>
                    <td><strong>Invoice Number:</strong></td>
                    <td>{{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td><strong>Invoice Title:</strong></td>
                    <td>{{ $invoice->title }}</td>
                </tr>
                <tr>
                    <td><strong>Payment Date:</strong></td>
                    <td>{{ $invoice->paid_at ? $invoice->paid_at->format('F j, Y g:i A') : 'Just now' }}</td>
                </tr>
                <tr>
                    <td><strong>Amount Paid:</strong></td>
                    <td class="amount">${{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Payment Status:</strong></td>
                    <td style="color: #059669; font-weight: bold;">PAID</td>
                </tr>
            </table>
        </div>

        <div class="client-info">
            <h3>Client Information</h3>
            <p><strong>Name:</strong> {{ $invoice->client->user->first_name ?? '' }} {{ $invoice->client->user->last_name ?? '' }}</p>
            <p><strong>Email:</strong> {{ $invoice->client->user->email ?? $invoice->send_to_email }}</p>
        </div>

        <h3>Services Paid For</h3>
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

        <div style="text-align: right; margin: 20px 0; padding: 15px; background: #f0fdf4; border-radius: 8px;">
            <p><strong>Subtotal:</strong> ${{ number_format($invoice->subtotal, 2) }}</p>
            <p><strong>Tax ({{ $invoice->tax_rate }}%):</strong> ${{ number_format($invoice->tax_amount, 2) }}</p>
            <p class="amount"><strong>Total Paid: ${{ number_format($invoice->total_amount, 2) }}</strong></p>
        </div>

        @if($invoice->comments)
        <div style="margin: 20px 0; padding: 15px; background: #f3f4f6; border-radius: 8px;">
            <h4>Service Notes:</h4>
            <p>{{ $invoice->comments }}</p>
        </div>
        @endif

        <p>We truly value your business and are grateful for the opportunity to serve your tax preparation needs. Your trust in our expertise means everything to us, and we remain committed to providing you with exceptional service.</p>

        <p>Please retain this receipt for your records. Should you have any questions about this payment, require additional documentation, or need assistance with any other tax-related matters, we are here to help.</p>

        <p>Thank you once again for choosing MySuperTax as your trusted tax preparation partner. We look forward to continuing to serve you with excellence.</p>

        <p>With sincere appreciation,<br>
        <strong>The MySuperTax Professional Team</strong></p>
    </div>

    <div class="footer">
        <p><strong>MySuperTax Professional Services</strong></p>
        <p><strong>Office Address:</strong> TX-61265, USA</p>
        <p><strong>Phone:</strong> +1 315-307-2751 | <strong>Email:</strong> help@mysupertax.com</p>
        <p>For any queries or support regarding this payment receipt, please contact us using the above details.</p>
        <p style="margin-top: 10px; font-style: italic;">This is an automated receipt. Please keep this for your records.</p>
    </div>
</body>
</html>