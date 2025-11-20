<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clients Export Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #2563eb;
            font-size: 20px;
            margin: 0;
        }
        
        .header p {
            color: #666;
            margin: 3px 0;
            font-size: 10px;
        }
        
        .summary {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .summary h3 {
            margin: 0 0 10px 0;
            color: #1e40af;
            font-size: 14px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-label {
            display: table-cell;
            font-weight: bold;
            padding: 3px 10px 3px 0;
            width: 40%;
        }
        
        .summary-value {
            display: table-cell;
            padding: 3px 0;
        }
        
        .client-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 12px;
        }
        
        .client-header {
            background-color: #f3f4f6;
            color: #1f2937;
            font-size: 13px;
            font-weight: bold;
            padding: 8px 12px;
            margin: -12px -12px 10px -12px;
            border-radius: 4px 4px 0 0;
        }
        
        .client-info {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 4px 10px 4px 0;
            width: 25%;
            vertical-align: top;
            font-size: 10px;
        }
        
        .info-value {
            display: table-cell;
            padding: 4px 0;
            vertical-align: top;
            font-size: 10px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .two-column {
            display: table;
            width: 100%;
        }
        
        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }
        
        .column:last-child {
            padding-right: 0;
            padding-left: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MySuperTax - Clients Export Report</h1>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>Template: {{ ucfirst($template) }} | Total Clients: {{ $clients->count() }}</p>
        <p>Exported by: {{ auth()->user()->name ?? 'System' }}</p>
    </div>

    <!-- Summary Section -->
    <div class="summary">
        <h3>Export Summary</h3>
        <div class="two-column">
            <div class="column">
                <div class="summary-grid">
                    <div class="summary-row">
                        <div class="summary-label">Total Clients:</div>
                        <div class="summary-value">{{ $clients->count() }}</div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label">Active Clients:</div>
                        <div class="summary-value">{{ $clients->where('status', 'active')->count() }}</div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label">Inactive Clients:</div>
                        <div class="summary-value">{{ $clients->where('status', 'inactive')->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="summary-grid">
                    <div class="summary-row">
                        <div class="summary-label">Archived Clients:</div>
                        <div class="summary-value">{{ $clients->where('status', 'archived')->count() }}</div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label">Clients with Spouse:</div>
                        <div class="summary-value">{{ $clients->filter(fn($c) => $c->spouse)->count() }}</div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label">Clients with Assets:</div>
                        <div class="summary-value">{{ $clients->filter(fn($c) => $c->assets->count() > 0)->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients List -->
    @foreach($clients as $index => $client)
        @if($index > 0 && $index % 3 == 0)
            <div class="page-break"></div>
        @endif
        
        <div class="client-section">
            <div class="client-header">
                Client #{{ $index + 1 }}: {{ $client->full_name }} (ID: {{ $client->id }})
            </div>
            
            @if($template === 'basic' || $template === 'comprehensive')
            <div class="client-info">
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $client->email ?? 'Not provided' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Phone:</div>
                    <div class="info-value">{{ $client->phone ?? 'Not provided' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">{{ ucfirst($client->status) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Registration:</div>
                    <div class="info-value">{{ $client->created_at->format('M j, Y') }}</div>
                </div>
            </div>
            @endif
            
            @if($template === 'contact' || $template === 'comprehensive')
            <div class="client-info">
                <div class="info-row">
                    <div class="info-label">Address:</div>
                    <div class="info-value">{{ $client->formatted_address ?? 'Not provided' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Mobile:</div>
                    <div class="info-value">{{ $client->mobile_number ?? 'Not provided' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Work Phone:</div>
                    <div class="info-value">{{ $client->work_number ?? 'Not provided' }}</div>
                </div>
            </div>
            @endif
            
            @if($template === 'financial' || $template === 'comprehensive')
            <div class="client-info">
                <div class="info-row">
                    <div class="info-label">Occupation:</div>
                    <div class="info-value">{{ $client->occupation ?? 'Not provided' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Marital Status:</div>
                    <div class="info-value">{{ ucfirst($client->marital_status ?? 'Not specified') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Assets Count:</div>
                    <div class="info-value">{{ $client->assets->count() }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Expenses Count:</div>
                    <div class="info-value">{{ $client->expenses->count() }}</div>
                </div>
            </div>
            @endif
            
            @if($template === 'comprehensive')
                @if($client->spouse)
                <div class="client-info">
                    <div class="info-row">
                        <div class="info-label">Spouse:</div>
                        <div class="info-value">{{ $client->spouse->full_name }}</div>
                    </div>
                </div>
                @endif
                
                @if($client->visa_status)
                <div class="client-info">
                    <div class="info-row">
                        <div class="info-label">Visa Status:</div>
                        <div class="info-value">{{ $client->visa_status->label() ?? $client->visa_status }}</div>
                    </div>
                </div>
                @endif
            @endif
        </div>
    @endforeach

    <div class="footer">
        <p>This document was generated by MySuperTax Client Management System</p>
        <p>© {{ date('Y') }} MySuperTax. All rights reserved.</p>
    </div>
</body>
</html>