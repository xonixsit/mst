<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class TestEmailCommand extends Command
{
    protected $signature = 'email:test {email}';
    protected $description = 'Test email configuration by sending a test email';

    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            Mail::raw('This is a test email from MySuperTax platform to verify email configuration.', function (Message $message) use ($email) {
                $message->to($email)
                        ->subject('MySuperTax - Email Configuration Test');
            });
            
            $this->info("Test email sent successfully to: {$email}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to send email: " . $e->getMessage());
            return 1;
        }
    }
}