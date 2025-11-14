<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestClientRegistration extends Command
{
    protected $signature = 'test:client-registration';
    protected $description = 'Test client registration and email verification flow';

    public function handle()
    {
        $this->info('Testing client registration and email verification...');
        
        // Clean up any existing test user
        User::where('email', 'testclient@example.com')->delete();
        
        // Create a test user
        $user = User::create([
            'name' => 'Test Client',
            'email' => 'testclient@example.com',
            'password' => bcrypt('password123'),
            'role' => 'client'
        ]);
        
        $this->info("✓ User created with ID: {$user->id}");
        $this->info("✓ Email verified: " . ($user->email_verified_at ? 'Yes' : 'No'));
        
        // Test sending verification email
        try {
            $user->sendEmailVerificationNotification();
            $this->info('✓ Email verification notification sent successfully');
            
        } catch (\Exception $e) {
            $this->error("✗ Failed to send verification email: " . $e->getMessage());
            return 1;
        }
        
        // Test email verification URL generation
        try {
            $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'client.verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->email)]
            );
            
            $this->info('✓ Verification URL generated successfully');
            $this->line("Verification URL: {$verificationUrl}");
            
        } catch (\Exception $e) {
            $this->error("✗ Failed to generate verification URL: " . $e->getMessage());
            return 1;
        }
        
        // Test manual verification
        try {
            $user->markEmailAsVerified();
            $user->refresh();
            
            $this->info('✓ Email marked as verified: ' . ($user->email_verified_at ? 'Yes' : 'No'));
            
        } catch (\Exception $e) {
            $this->error("✗ Failed to mark email as verified: " . $e->getMessage());
            return 1;
        }
        
        $this->info('');
        $this->info('🎉 All email verification tests passed!');
        $this->info('');
        $this->info('Email configuration summary:');
        $this->line('- MAIL_MAILER: ' . config('mail.default'));
        $this->line('- MAIL_HOST: ' . config('mail.mailers.smtp.host'));
        $this->line('- MAIL_PORT: ' . config('mail.mailers.smtp.port'));
        $this->line('- MAIL_FROM_ADDRESS: ' . config('mail.from.address'));
        
        return 0;
    }
}