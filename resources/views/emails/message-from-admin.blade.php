<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Message from MySuperTax</title>
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
        .message-box {
            background: white;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .priority {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .priority-high { background: #ffebee; color: #c62828; }
        .priority-normal { background: #e8f5e8; color: #2e7d32; }
        .priority-low { background: #f3e5f5; color: #7b1fa2; }
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
        <h1>💬 New Message from MySuperTax</h1>
        <p>Your Tax Professional Team</p>
    </div>
    
    <div class="content">
        <p>Hello {{ $recipient->first_name }},</p>
        
        <p>You have received a new message from your tax professional at MySuperTax.</p>
        
        <div class="message-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0;">{{ $messageModel->subject }}</h3>
                <span class="priority priority-{{ $messageModel->priority }}">{{ ucfirst($messageModel->priority) }} Priority</span>
            </div>
            
            <div style="color: #666; font-size: 14px; margin-bottom: 15px;">
                From: {{ $sender->name }}<br>
                Sent on {{ $messageModel->created_at->format('F j, Y \a\t g:i A') }}
            </div>
            
            <div style="white-space: pre-wrap;">{{ $messageModel->body }}</div>
        </div>
        
        <p>Please log in to your client portal to view the full conversation and respond to this message.</p>
        
        <a href="{{ config('app.url') }}/client/messages/{{ $messageModel->id }}" class="button">
            View Message & Reply
        </a>
        
        <p><strong>Need Help?</strong></p>
        <ul>
            <li><a href="{{ config('app.url') }}/client/messages">View All Messages</a></li>
            <li><a href="{{ config('app.url') }}/client/dashboard">Go to Dashboard</a></li>
        </ul>
    </div>
    
    <div class="footer">
        <p>Best regards,<br><strong>MySuperTax Team</strong><br>
        This is an automated notification. Please log in to reply.</p>
    </div>
</body>
</html>