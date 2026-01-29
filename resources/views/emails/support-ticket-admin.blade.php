<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Support Ticket Notification</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .ticket-box {
            background: white;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .client-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .priority {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .priority-urgent { background: #ffebee; color: #c62828; }
        .priority-high { background: #fff3e0; color: #ef6c00; }
        .priority-medium { background: #e8f5e8; color: #2e7d32; }
        .priority-low { background: #f3e5f5; color: #7b1fa2; }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-open { background: #fff3cd; color: #856404; }
        .status-in_progress { background: #d1ecf1; color: #0c5460; }
        .status-resolved { background: #d4edda; color: #155724; }
        .status-closed { background: #f8d7da; color: #721c24; }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎫 Support Ticket {{ ucfirst($type) }}</h1>
        <p>MySuperTax Support System</p>
    </div>
    
    <div class="content">
        <p>Hello {{ $recipient->first_name }},</p>
        
        @if($type === 'created')
            <p>A new support ticket has been created and requires your attention.</p>
        @elseif($type === 'updated')
            <p>A support ticket has been updated.</p>
        @elseif($type === 'reply')
            <p>A new reply has been added to a support ticket.</p>
        @elseif($type === 'assigned')
            <p>A support ticket has been assigned to you.</p>
        @endif
        
        @if($client)
        <div class="client-info">
            <strong>Client:</strong> {{ $client->first_name }} {{ $client->last_name }}<br>
            <strong>Email:</strong> {{ $client->email ?? 'N/A' }}<br>
            <strong>Client ID:</strong> {{ $client->id }}
        </div>
        @endif
        
        <div class="ticket-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0;">{{ $ticketModel->ticket_number }}</h3>
                <div>
                    <span class="priority priority-{{ $ticketModel->priority }}">{{ ucfirst($ticketModel->priority) }}</span>
                    <span class="status status-{{ $ticketModel->status }}">{{ ucfirst(str_replace('_', ' ', $ticketModel->status)) }}</span>
                </div>
            </div>
            
            <h4 style="margin: 10px 0;">{{ $ticketModel->subject }}</h4>
            
            <div style="color: #666; font-size: 14px; margin-bottom: 15px;">
                <strong>Category:</strong> {{ ucfirst($ticketModel->category) }}<br>
                <strong>Created:</strong> {{ $ticketModel->created_at->format('F j, Y \a\t g:i A') }}<br>
                @if($ticketModel->assignedTo)
                    <strong>Assigned to:</strong> {{ $ticketModel->assignedTo->name }}
                @endif
            </div>
            
            <div style="white-space: pre-wrap; background: #f8f9fa; padding: 15px; border-radius: 5px;">{{ $ticketModel->description }}</div>
        </div>
        
        <p>Please log in to your admin dashboard to view the full ticket details and take action.</p>
        
        <a href="{{ config('app.url') }}/admin/support-tickets/{{ $ticketModel->id }}" class="button">
            View Ticket Details
        </a>
        
        <p><strong>Quick Actions:</strong></p>
        <ul>
            <li><a href="{{ config('app.url') }}/admin/support-tickets">View All Tickets</a></li>
            @if($client)
                <li><a href="{{ config('app.url') }}/admin/clients/{{ $client->id }}">View Client Profile</a></li>
            @endif
        </ul>
    </div>
    
    <div class="footer">
        <p>This is an automated notification from MySuperTax Support System.<br>
        Please do not reply to this email directly.</p>
    </div>
</body>
</html>