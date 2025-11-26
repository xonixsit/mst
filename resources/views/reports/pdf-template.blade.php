<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 40px 30px;
            size: A4;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #2d3748;
            line-height: 1.6;
            font-size: 12px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 25px;
            margin: -40px -30px 30px -30px;
            text-align: center;
        }
        
        .company-logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        
        .company-tagline {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 20px;
        }
        
        .report-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .generated-info {
            font-size: 12px;
            opacity: 0.8;
        }
        
        .filters-section {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .filters-title {
            font-size: 14px;
            font-weight: bold;
            color: #4a5568;
            margin: 0 0 12px 0;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
            display: inline-block;
        }
        
        .filter-item {
            display: inline-block;
            margin-right: 25px;
            margin-bottom: 8px;
        }
        
        .filter-label {
            font-weight: 600;
            color: #2d3748;
        }
        
        .filter-value {
            color: #4a5568;
        }
        
        .summary-section {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        
        .summary-title {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 20px 0;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .summary-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            align-items: stretch;
        }
        
        .summary-item {
            flex: 1;
            min-width: 120px;
            text-align: center;
            padding: 15px 10px;
            margin: 0 5px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }
        
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .summary-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
        }
        
        .section {
            margin-bottom: 35px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
            margin: 0 0 15px 0;
            padding: 12px 0 8px 0;
            border-bottom: 3px solid #667eea;
            background: linear-gradient(90deg, #f7fafc 0%, transparent 100%);
            padding-left: 15px;
            margin-left: -15px;
            margin-right: -15px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .data-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .data-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .data-table tr:hover {
            background-color: #e6fffa;
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active { background-color: #c6f6d5; color: #22543d; }
        .status-inactive { background-color: #fed7d7; color: #742a2a; }
        .status-pending { background-color: #fef5e7; color: #744210; }
        .status-approved { background-color: #c6f6d5; color: #22543d; }
        .status-rejected { background-color: #fed7d7; color: #742a2a; }
        .status-paid { background-color: #c6f6d5; color: #22543d; }
        
        .currency {
            font-weight: 600;
            color: #2d3748;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            line-height: 40px;
            font-size: 10px;
            margin: 0 -30px -40px -30px;
        }
        
        .page-number:before {
            content: "Page " counter(page) " of " counter(pages);
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #718096;
            font-style: italic;
            background-color: #f7fafc;
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            margin: 15px 0;
        }
        
        .highlight-row {
            background-color: #e6fffa !important;
            border-left: 4px solid #38b2ac;
        }
        
        .total-row {
            background-color: #f0fff4 !important;
            font-weight: bold;
            border-top: 2px solid #38a169;
        }
        
        .data-table tbody tr:last-child {
            border-bottom: 2px solid #667eea;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-logo">MySuperTax</div>
        <div class="company-tagline">Professional Tax Consulting Services</div>
        <div class="report-title">{{ $title }}</div>
        <div class="generated-info">Generated on {{ $generated_at }}</div>
    </div>

    @if(isset($filters) && array_filter($filters))
    <div class="filters-section">
        <div class="filters-title">Report Filters</div>
        @if($filters['date_range'])
            <div class="filter-item">
                <span class="filter-label">Date Range:</span> 
                <span class="filter-value">
                    @switch($filters['date_range'])
                        @case('3months')
                            Last 3 Months
                            @break
                        @case('6months')
                            Last 6 Months
                            @break
                        @case('12months')
                            Last 12 Months
                            @break
                        @default
                            Custom Range
                    @endswitch
                </span>
            </div>
        @endif
        @if($filters['start_date'])
            <div class="filter-item">
                <span class="filter-label">Start Date:</span> 
                <span class="filter-value">{{ $filters['start_date'] }}</span>
            </div>
        @endif
        @if($filters['end_date'])
            <div class="filter-item">
                <span class="filter-label">End Date:</span> 
                <span class="filter-value">{{ $filters['end_date'] }}</span>
            </div>
        @endif
        @if(isset($filters['status']) && $filters['status'])
            <div class="filter-item">
                <span class="filter-label">Status:</span> 
                <span class="filter-value">{{ ucfirst($filters['status']) }}</span>
            </div>
        @endif
    </div>
    @endif

    @if(isset($summary))
    <div class="summary-section">
        <div class="summary-title">Summary</div>
        <div class="summary-grid">
            @foreach($summary as $key => $value)
                <div class="summary-item">
                    <div class="summary-value">
                        @if(strpos($key, 'amount') !== false || strpos($key, 'revenue') !== false)
                            {{ number_format($value, 2) }}
                        @else
                            {{ is_numeric($value) ? number_format($value) : $value }}
                        @endif
                    </div>
                    <div class="summary-label">{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(isset($clients))
    <div class="section">
        <div class="section-title">Client Details</div>
        @if(count($clients) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client Name</th>
                    <th>Email Address</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th class="text-center">Invoices</th>
                    <th class="text-center">Documents</th>
                    <th class="text-right">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                <tr>
                    <td><strong>#{{ $client['id'] }}</strong></td>
                    <td>{{ $client['name'] }}</td>
                    <td>{{ $client['email'] }}</td>
                    <td>
                        <span class="status-badge status-{{ $client['status'] }}">
                            {{ ucfirst($client['status']) }}
                        </span>
                    </td>
                    <td>{{ $client['created_at'] }}</td>
                    <td class="text-center">{{ $client['invoices_count'] }}</td>
                    <td class="text-center">{{ $client['documents_count'] }}</td>
                    <td class="text-right currency">${{ number_format($client['total_invoice_amount'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">No client data available for the selected criteria.</div>
        @endif
    </div>
    @endif

    @if(isset($invoices))
    <div class="section">
        <div class="section-title">Invoice Details</div>
        @if(count($invoices) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Client Name</th>
                    <th class="text-right">Amount</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                <tr>
                    <td><strong>{{ $invoice['invoice_number'] }}</strong></td>
                    <td>{{ $invoice['client_name'] }}</td>
                    <td class="text-right currency">${{ number_format($invoice['amount'], 2) }}</td>
                    <td>
                        <span class="status-badge status-{{ $invoice['status'] }}">
                            {{ ucfirst($invoice['status']) }}
                        </span>
                    </td>
                    <td>{{ $invoice['created_at'] }}</td>
                    <td>{{ $invoice['due_date'] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">No invoice data available for the selected criteria.</div>
        @endif
    </div>
    @endif

    @if(isset($documents))
    <div class="section">
        <div class="section-title">Document Details</div>
        @if(count($documents) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Document Name</th>
                    <th>Type</th>
                    <th>Client</th>
                    <th>Status</th>
                    <th>Uploaded</th>
                    <th class="text-right">Size</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $document)
                <tr>
                    <td><strong>{{ $document['name'] }}</strong></td>
                    <td>{{ ucwords(str_replace('_', ' ', $document['type'])) }}</td>
                    <td>{{ $document['client_name'] }}</td>
                    <td>
                        <span class="status-badge status-{{ $document['status'] }}">
                            {{ ucfirst($document['status']) }}
                        </span>
                    </td>
                    <td>{{ $document['uploaded_at'] }}</td>
                    <td class="text-right">{{ number_format($document['file_size'] / 1024, 2) }} KB</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">No document data available for the selected criteria.</div>
        @endif
    </div>
    @endif

    @if(isset($messages))
    <div class="section">
        <div class="section-title">Message Details</div>
        @if(count($messages) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Subject</th>
                    <th>Sent At</th>
                    <th class="text-center">Read Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($messages as $message)
                <tr>
                    <td>{{ $message['client_name'] }}</td>
                    <td><strong>{{ $message['subject'] }}</strong></td>
                    <td>{{ $message['sent_at'] }}</td>
                    <td class="text-center">
                        <span class="status-badge {{ $message['is_read'] ? 'status-approved' : 'status-pending' }}">
                            {{ $message['is_read'] ? 'Read' : 'Unread' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">No message data available for the selected criteria.</div>
        @endif
    </div>
    @endif

    @if(isset($monthly_breakdown))
    <div class="section">
        <div class="section-title">Monthly Financial Breakdown</div>
        @if(count($monthly_breakdown) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-center">Total Invoices</th>
                    <th class="text-right">Total Amount</th>
                    <th class="text-right">Paid Amount</th>
                    <th class="text-right">Pending Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthly_breakdown as $month => $data)
                <tr>
                    <td><strong>{{ $month }}</strong></td>
                    <td class="text-center">{{ number_format($data['total_invoices']) }}</td>
                    <td class="text-right currency">${{ number_format($data['total_amount'], 2) }}</td>
                    <td class="text-right currency">${{ number_format($data['paid_amount'], 2) }}</td>
                    <td class="text-right currency">${{ number_format($data['pending_amount'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">No monthly breakdown data available for the selected criteria.</div>
        @endif
    </div>
    @endif
</body>
</html>