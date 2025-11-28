<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #059669;
            color: white;
            border-radius: 8px;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            font-size: 14px;
        }
        .payment-status {
            text-align: center;
            background-color: #f0fdf4;
            border: 2px solid #bbf7d0;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .payment-status h2 {
            color: #059669;
            margin: 0 0 10px 0;
            font-size: 20px;
        }
        .receipt-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .receipt-info .left, .receipt-info .right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .info-section h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #059669;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .info-table td {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th, .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .items-table th {
            background-color: #059669;
            color: white;
            font-weight: bold;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .totals {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
        }
        .totals .total-row {
            background-color: #059669;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .comments {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .comments h4 {
            margin: 0 0 10px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Receipt</h1>
        <p>MySuperTax Professional Services</p>
    </div>

    <div class="payment-status">
        <h2>PAYMENT RECEIVED</h2>
        <p>This receipt confirms payment for Invoice {{ $invoice->invoice_number }}</p>
        <p><strong>Amount Paid: ${{ number_format($invoice->total_amount, 2) }}</strong></p>
    </div>

    <div class="receipt-info">
        <div class="left">
            <div class="info-section">
                <h3>Payment Information</h3>
                <table class="info-table">
                    <tr>
                        <td>Receipt Number:</td>
                        <td>RCP-{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td>Invoice Number:</td>
                        <td>{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td>Payment Date:</td>
                        <td>{{ $invoice->paid_at ? $invoice->paid_at->format('F j, Y g:i A') : now()->format('F j, Y g:i A') }}</td>
                    </tr>
                    <tr>
                        <td>Payment Status:</td>
                        <td style="color: #059669; font-weight: bold;">PAID</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="right">
            <div class="info-section">
                <h3>Client Information</h3>
                <table class="info-table">
                    <tr>
                        <td>Name:</td>
                        <td>{{ $invoice->client->user->first_name ?? '' }} {{ $invoice->client->user->last_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td>{{ $invoice->client->user->email ?? $invoice->send_to_email }}</td>
                    </tr>
                    @if($invoice->client->phone)
                    <tr>
                        <td>Phone:</td>
                        <td>{{ $invoice->client->phone }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <h3 style="color: #059669; margin-bottom: 15px;">Services Paid For</h3>
    <table class="items-table">
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

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td style="text-align: right;">${{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>Tax ({{ $invoice->tax_rate }}%):</td>
                <td style="text-align: right;">${{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Paid:</td>
                <td style="text-align: right;">${{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($invoice->comments)
    <div class="comments">
        <h4>Service Notes:</h4>
        <p>{{ $invoice->comments }}</p>
    </div>
    @endif

    <div class="footer">
        <p><strong>MySuperTax Professional Services</strong></p>
        <p><strong>Office Address:</strong> TX-61265, USA</p>
        <p><strong>Phone:</strong> +1 315-307-2751 | <strong>Email:</strong> help@mysupertax.com</p>
        <p>Thank you for your payment! Please keep this receipt for your records.</p>
        <p style="margin-top: 10px; font-size: 11px; color: #666;">
            For any queries or support regarding this payment receipt, please contact us using the above details.
        </p>
    </div>
</body>
</html>