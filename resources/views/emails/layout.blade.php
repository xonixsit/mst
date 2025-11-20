<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'MySuperTax' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .content p {
            margin-bottom: 16px;
            color: #4a5568;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        .info-box {
            background-color: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background-color: #f7fafc;
            padding: 30px;
            text-align: center;
            color: #718096;
            font-size: 14px;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 30px 0;
        }
        .highlight {
            background-color: #fef5e7;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #f6ad55;
            margin: 20px 0;
        }
        .success {
            background-color: #f0fff4;
            border-left-color: #48bb78;
        }
        .warning {
            background-color: #fffaf0;
            border-left-color: #ed8936;
        }
        .error {
            background-color: #fed7d7;
            border-left-color: #f56565;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>MySuperTax</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Professional Tax Consulting Services</p>
        </div>
        
        <div class="content">
            @yield('content')
        </div>
        
        <div class="footer">
            <p>
                <strong>MySuperTax</strong><br>
                Professional My Super Tax<br>
                <a href="mailto:support@mst.xonixs.com">support@mst.xonixs.com</a>
            </p>
            <div class="divider"></div>
            <p>
                This email was sent to you because you are a registered user of MySuperTax.<br>
                If you have any questions, please don't hesitate to contact our support team.
            </p>
        </div>
    </div>
</body>
</html>