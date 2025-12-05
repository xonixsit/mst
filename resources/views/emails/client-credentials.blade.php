@component('mail::message')
# Welcome to MySuperTax

Hello {{ $client->first_name }},

Your tax professional has created an account for you on MySuperTax. You can now login and manage your tax information securely.

## Your Login Credentials

**Username:** `{{ $username }}`  
**Password:** `{{ $password }}`

@component('mail::button', ['url' => $loginUrl])
Login to Your Account
@endcomponent

## Important Security Notes

- Please change your password immediately after your first login
- Never share your credentials with anyone
- Always use a strong, unique password
- If you didn't request this account, please contact your tax professional

## What You Can Do

Once logged in, you can:
- View and update your personal information
- Upload tax documents
- Track your invoices
- Communicate with your tax professional
- Manage your account settings

If you have any questions or need assistance, please contact your tax professional or our support team.

Best regards,  
**MySuperTax Team**
@endcomponent
