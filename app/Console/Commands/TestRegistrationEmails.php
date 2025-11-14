<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ClientWelcomeNotification;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestRegistrationEmails extends Command
{
    protected $signature = 'test:registration-emails {client_email} {admin_email}';
    protected $description = 'Test registration emails by sending to real email addresses';

    public function __construct(
        private AdminNotificationService $adminNotificationService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $clientEmail = $this->argument('client_email');
        $adminEmail = $this->argument('admin_email');
        
        $this->info("Testing registration emails...");
        $this->info("Client email: {$clientEmail}");
        $this->info("Admin email: {$adminEmail}");
        
        // Clean up any existing test users
        User::where('email', $clientEmail)->delete();
        User::where('email', $adminEmail)->delete();
        
        try {
            // Create admin user first
            $adminUser = User::create([
                'name' => 'Test Admin',
                'email' => $adminEmail,
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            
            $this->info("✓ Admin user created: {$adminUser->name}");
            
            // Create client user
            $clientUser = User::create([
                'name' => 'Test Client',
                'email' => $clientEmail,
                'password' => Hash::make('password123'),
                'role' => 'client',
            ]);
            
            $this->info("✓ Client user created: {$clientUser->name}");
            
            // Send email verification notification
            $clientUser->sendEmailVerificationNotification();
            $this->info("✓ Email verification sent to client");
            
            // Send welcome notification to client
            $clientUser->notify(new ClientWelcomeNotification($clientUser));
            $this->info("✓ Welcome email sent to client");
            
            // Send admin notification about new client registration
            $this->adminNotificationService->notifyClientRegistered($clientUser);
            $this->info("✓ Admin notification sent about new client registration");
            
            $this->info("");
            $this->info("🎉 Registration email test completed successfully!");
            $this->info("");
            $this->info("Emails sent:");
            $this->line("1. Email verification → {$clientEmail}");
            $this->line("2. Welcome email → {$clientEmail}");
            $this->line("3. New client alert → {$adminEmail}");
            $this->info("");
            $this->info("Check your email inboxes to verify the emails were received.");
            
            // Process the queue to send emails immediately
            $this->info("");
            $this->info("Processing email queue...");
            $this->call('queue:work', ['--once' => true]);
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Failed to send registration emails: " . $e->getMessage());
            return 1;
        }
    }
}