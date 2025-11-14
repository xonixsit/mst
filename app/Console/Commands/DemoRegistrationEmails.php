<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ClientWelcomeNotification;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;

class DemoRegistrationEmails extends Command
{
    protected $signature = 'demo:registration-emails {client_email} {admin_email}';
    protected $description = 'Demo registration emails by showing email content and logging';

    public function __construct(
        private AdminNotificationService $adminNotificationService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $clientEmail = $this->argument('client_email');
        $adminEmail = $this->argument('admin_email');
        
        $this->info("=== MySuperTax Registration Email Demo ===");
        $this->info("Client email: {$clientEmail}");
        $this->info("Admin email: {$adminEmail}");
        $this->info("");
        
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
            
            // Create client user
            $clientUser = User::create([
                'name' => 'Test Client',
                'email' => $clientEmail,
                'password' => Hash::make('password123'),
                'role' => 'client',
            ]);
            
            $this->info("✓ Test users created successfully");
            $this->info("");
            
            // Show email verification content
            $this->showEmailVerificationDemo($clientUser);
            
            // Show client welcome email content
            $this->showClientWelcomeDemo($clientUser);
            
            // Show admin notification content
            $this->showAdminNotificationDemo($clientUser, $adminUser);
            
            // Actually send the emails (will be logged)
            $this->info("📧 Sending emails (logged to storage/logs/laravel.log)...");
            
            // Send email verification
            $clientUser->sendEmailVerificationNotification();
            
            // Send welcome notification to client
            $clientUser->notify(new ClientWelcomeNotification($clientUser));
            
            // Send admin notification
            $this->adminNotificationService->notifyClientRegistered($clientUser);
            
            $this->info("✓ All emails queued and logged successfully!");
            $this->info("");
            $this->info("📁 Check storage/logs/laravel.log to see the actual email content");
            $this->info("🚀 In production, these emails will be sent via SMTP to the recipients");
            
            // Clean up
            $clientUser->delete();
            $adminUser->delete();
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Demo failed: " . $e->getMessage());
            return 1;
        }
    }
    
    private function showEmailVerificationDemo(User $user): void
    {
        $this->info("📧 EMAIL 1: Email Verification");
        $this->line("To: {$user->email}");
        $this->line("Subject: Verify Email Address - MySuperTax");
        $this->line("Content: Email verification link with secure token");
        $this->line("Purpose: Verify client's email address before account activation");
        $this->info("");
    }
    
    private function showClientWelcomeDemo(User $user): void
    {
        $this->info("📧 EMAIL 2: Client Welcome");
        $this->line("To: {$user->email}");
        $this->line("Subject: Welcome to MySuperTax - Your Tax Consulting Journey Begins!");
        
        // Render the actual email content
        try {
            $emailContent = View::make('emails.client-welcome', [
                'user' => $user,
                'subject' => 'Welcome to MySuperTax'
            ])->render();
            
            $this->line("Content: Professional welcome email with:");
            $this->line("  • Account details and registration confirmation");
            $this->line("  • Next steps for completing profile");
            $this->line("  • Security information and privacy assurance");
            $this->line("  • Support contact information");
            $this->line("  • Dashboard access link");
            
        } catch (\Exception $e) {
            $this->line("Content: Welcome email template (render error: {$e->getMessage()})");
        }
        
        $this->info("");
    }
    
    private function showAdminNotificationDemo(User $client, User $admin): void
    {
        $this->info("📧 EMAIL 3: Admin New Client Alert");
        $this->line("To: {$admin->email}");
        $this->line("Subject: New Client Registration: {$client->name} - MySuperTax");
        
        try {
            $emailContent = View::make('emails.admin.client-registered', [
                'user' => $client,
                'subject' => "New Client Registration: {$client->name}"
            ])->render();
            
            $this->line("Content: Admin notification with:");
            $this->line("  • New client details and registration info");
            $this->line("  • Action items for client onboarding");
            $this->line("  • Assignment and follow-up recommendations");
            $this->line("  • Quick access to client management");
            $this->line("  • SLA reminders for client contact");
            
        } catch (\Exception $e) {
            $this->line("Content: Admin notification template (render error: {$e->getMessage()})");
        }
        
        $this->info("");
    }
}