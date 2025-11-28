<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SimpleEmailTest extends Command
{
    protected $signature = 'test:simple-email {email}';
    protected $description = 'Send a simple test email';

    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            Mail::raw('This is a test email from MySuperTax platform.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email from MySuperTax');
            });
            
            $this->info("✅ Test email sent to: {$email}");
            $this->info("Check your inbox and spam folder.");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send email: " . $e->getMessage());
        }
    }
}