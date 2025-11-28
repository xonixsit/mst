<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class SendTestEmail extends Command
{
    protected $signature = 'send:test-email {email=xonixsitsolutions@gmail.com}';
    protected $description = 'Send a test email to verify email configuration';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Sending test email to: {$email}");
        
        try {
            Mail::raw('This is a test email from MySuperTax platform to verify email configuration is working correctly.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email from MySuperTax Platform')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info('✅ Test email sent successfully!');
            
            // Show mail configuration
            $this->info("\nCurrent mail configuration:");
            $this->line("Driver: " . config('mail.default'));
            $this->line("From Address: " . config('mail.from.address'));
            $this->line("From Name: " . config('mail.from.name'));
            
            if (config('mail.default') === 'log') {
                $this->info("📧 Email is being logged to storage/logs/laravel.log");
                $this->info("To send real emails, update your .env file with SMTP settings");
            } else {
                $this->info("📧 Email should be delivered to the recipient's inbox");
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email: ' . $e->getMessage());
        }
    }
}