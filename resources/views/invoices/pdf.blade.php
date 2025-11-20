<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .invoice-number {
            font-size: 16px;
            color: #666;
        }
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .bill-to, .invoice-details {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .invoice-details {
            text-align: right;
        }
        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            color: #2563eb;
        }
        .client-info p, .details p {
            margin: 3px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        .items-table th {
            background-color: #f8fafc;
            border: 1px solid #e5e7eb;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        .items-table td {
            border: 1px solid #e5e7eb;
            padding: 12px;
        }
        .items-table .text-right {
            text-align: right;
        }
        .totals {
            width: 300px;
            margin-left: auto;
            margin-top: 20px;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .totals .total-row {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #2563eb;
            background-color: #f8fafc;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-sent {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-draft {
            background-color: #f3f4f6;
            color: #374151;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .comments {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8fafc;
            border-left: 4px solid #2563eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ config('app.name') }}</div>
        <div class="invoice-title">INVOICE</div>
        <div class="invoice-number"># {{ $invoice->invoice_number }}</div>
    </div>

    <div class="invoice-info">
        <div class="bill-to">
            <div class="section-title">Bill To:</div>
            <div class="client-info">
                <p><strong>{{ $invoice->client->user ? $invoice->client->user->first_name . ' ' . $invoice->client->user->last_name : 'Unknown Client' }}</strong></p>
                @if($invoice->client->user)
                    <p>{{ $invoice->client->user->email }}</p>
                @endif
                @if($invoice->client->phone)
                    <p>{{ $invoice->client->phone }}</p>
                @endif
                @if($invoice->client->street_no)
                    <p>{{ $invoice->client->street_no }} {{ $invoice->client->apartment_no }}</p>
                    <p>{{ $invoice->client->city }}, {{ $invoice->client->state }} {{ $invoice->client->zip_code }}</p>
                @endif
            </div>
        </div>
        
        <div class="invoice-details">
            <div class="section-title">Invoice Details:</div>
            <div class="details">
                <p><strong>Invoice Date:</strong> {{ $invoice->created_at->format('F d, Y') }}</p>
                @if($invoice->invoice_year)
                    <p><strong>Tax Year:</strong> {{ $invoice->invoice_year }}</p>
                @endif
                @if($invoice->sent_at)
                    <p><strong>Sent Date:</strong> {{ $invoice->sent_at->format('F d, Y') }}</p>
                @endif
                <!-- <p><strong>Status:</strong> 
                    <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                </p> -->
                @if($invoice->paid_at)
                    <p><strong>Paid Date:</strong> {{ $invoice->paid_at->format('F d, Y') }}</p>
                @endif
            </div>
        </div>
    </div>

    @if($invoice->title)
        <div style="margin: 20px 0;">
            <div class="section-title">{{ $invoice->title }}</div>
        </div>
    @endif

    @if($invoice->comments)
        <div class="comments">
            <strong>Notes:</strong><br>
            {{ $invoice->comments }}
        </div>
    @endif

    <table class="items-table">
        <thead>
            <tr>
                <th>Service Description</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Rate</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->service_name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">${{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>Tax ({{ $invoice->tax_rate }}%):</td>
                <td class="text-right">${{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total:</td>
                <td class="text-right">${{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($invoice->status === 'paid')
        <div style="margin-top: 30px; padding: 15px; background-color: #dcfce7; border: 1px solid #16a34a; border-radius: 5px; text-align: center;">
            <strong style="color: #166534;">✓ PAID - Thank you for your payment!</strong>
        </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>