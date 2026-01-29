<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Two-Factor Authentication Code</title>
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
        .code-box {
            background: white;
            border: 2px solid #667eea;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 5px;
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
        <h1>Two-Factor Authentication</h1>
        <p>MySuperTax Security Code</p>
    </div>
    
    <div class="content">
        <p>Hello {{ $user->first_name }},</p>
        
        <p>Your two-factor authentication code is:</p>
        
        <div class="code-box">
            <div class="code">{{ $code }}</div>
        </div>
        
        <p><strong>This code will expire in 10 minutes.</strong></p>
        
        <p>Please enter this code on the login page to complete your authentication.</p>
        
        <p>If you did not request this code, please ignore this email or contact support if you have concerns about your account security.</p>
    </div>
    
    <div class="footer">
        <p>Best regards,<br><strong>MySuperTax Team</strong></p>
    </div>
</body>
</html>