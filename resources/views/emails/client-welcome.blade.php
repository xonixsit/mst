@extends('emails.layout')

@section('content')
<h2>Welcome to MySuperTax! 🎉</h2>

<p>Dear {{ $user->name }},</p>

<p>Welcome to MySuperTax! We're thrilled to have you join our community of clients who trust us with their tax consulting needs.</p>

<div class="info-box success">
    <h3 style="margin-top: 0; color: #2d3748;">Your Account Details</h3>
    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Registration Date:</strong> {{ $user->created_at->format('F j, Y') }}</p>
</div>

<h3>What's Next?</h3>
<p>To get started with your tax consulting journey, here are the next steps:</p>

<ol>
    <li><strong>Complete Your Profile:</strong> Fill out your personal information to help us serve you better</li>
    <li><strong>Upload Documents:</strong> Securely upload your tax documents for review</li>
    <li><strong>Schedule Consultation:</strong> Book a meeting with one of our tax professionals</li>
    <li><strong>Track Progress:</strong> Monitor the status of your tax preparation in real-time</li>
</ol>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('client.dashboard') }}" class="button">Access Your Dashboard</a>
</div>

<div class="highlight">
    <h4 style="margin-top: 0;">🔒 Your Security Matters</h4>
    <p>We take your privacy and security seriously. All your documents and personal information are encrypted and stored securely. Our platform is compliant with industry standards to protect your sensitive tax information.</p>
</div>

<h3>Need Help?</h3>
<p>Our support team is here to help you every step of the way:</p>
<ul>
    <li>📧 Email us at <a href="mailto:support@mst.xonixs.com">support@mst.xonixs.com</a></li>
    <li>💬 Use the messaging system in your dashboard</li>
    <li>📚 Check out our help center for common questions</li>
</ul>

<p>Thank you for choosing MySuperTax for your tax consulting needs. We look forward to working with you!</p>

<p>Best regards,<br>
<strong>The MySuperTax Team</strong></p>
@endsection