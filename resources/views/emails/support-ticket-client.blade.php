<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Support Ticket Update</title>
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
        <h1>🎫 Support Ticket Update</h1>
        <p>MySuperTax Support</p>
    </div>
    
    <div class="content">
        <p>Hello {{ $recipient->first_name }},</p>
        
        @if($type === 'created')
            <p>Your support ticket has been created successfully. Our team will review it and respond as soon as possible.</p>
        @elseif($type === 'updated')
            <p>There has been an update to your support ticket.</p>
        @elseif($type === 'reply')
            <p>Our support team has replied to your ticket.</p>
        @elseif($type === 'assigned')
            <p>Your support ticket has been assigned to a team member for resolution.</p>
        @endif
        
        <div class="ticket-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0;">{{ $ticketModel->ticket_number }}</h3>
                <span class="status status-{{ $ticketModel->status }}">{{ ucfirst(str_replace('_', ' ', $ticketModel->status)) }}</span>
            </div>
            
            <h4 style="margin: 10px 0;">{{ $ticketModel->subject }}</h4>
            
            <div style="color: #666; font-size: 14px; margin-bottom: 15px;">
                <strong>Category:</strong> {{ ucfirst($ticketModel->category) }}<br>
                <strong>Priority:</strong> {{ ucfirst($ticketModel->priority) }}<br>
                <strong>Created:</strong> {{ $ticketModel->created_at->format('F j, Y \a\t g:i A') }}<br>
                @if($ticketModel->assignedTo)
                    <strong>Assigned to:</strong> {{ $ticketModel->assignedTo->name }}
                @endif
            </div>
        </div>
        
        @if($type === 'created')
            <p><strong>What happens next?</strong></p>
            <ul>
                <li>Our support team will review your ticket</li>
                <li>You'll receive updates via email as we work on your request</li>
                <li>You can reply to add more information or ask questions</li>
            </ul>
        @endif
        
        <p>You can view the full ticket details and add replies through your client portal.</p>
        
        <a href="{{ config('app.url') }}/client/support-tickets/{{ $ticketModel->id }}" class="button">
            View Ticket Details
        </a>
        
        <p><strong>Need immediate assistance?</strong></p>
        <ul>
            <li><a href="{{ config('app.url') }}/client/support-tickets">View All Your Tickets</a></li>
            <li><a href="{{ config('app.url') }}/client/dashboard">Go to Dashboard</a></li>
        </ul>
    </div>
    
    <div class="footer">
        <p>Best regards,<br><strong>MySuperTax Support Team</strong><br>
        This is an automated notification. Please use the client portal to reply.</p>
    </div>
</body>
</html>