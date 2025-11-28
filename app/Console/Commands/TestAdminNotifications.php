<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Client;
use App\Models\Message;
use App\Models\Document;
use App\Services\AdminNotificationService;
use Illuminate\Support\Facades\Storage;

class TestAdminNotifications extends Command
{
    protected $signature = 'test:admin-notifications';
    protected $description = 'Test all admin notification types';

    public function handle()
    {
        $this->info('Testing Admin Notifications...');
        
        $adminNotificationService = app(AdminNotificationService::class);
        
        // Check if we have admin users
        $admins = $adminNotificationService->getAdminUsers();
        if ($admins->isEmpty()) {
            $this->error('No admin users found. Please create at least one admin or tax_professional user.');
            return;
        }
        
        $this->info("Found {$admins->count()} admin users:");
        foreach ($admins as $admin) {
            $this->line("- {$admin->name} ({$admin->email}) - Role: {$admin->role}");
        }
        
        // Test 1: Client Registration Notification
        $this->info("\n1. Testing Client Registration Notification...");
        $uniqueEmail = 'test.client.' . time() . '@example.com';
        $testClient = User::create([
            'role' => 'client',
            'first_name' => 'Test',
            'last_name' => 'Client',
            'email' => $uniqueEmail,
            'password' => bcrypt('password123'),
            'email_verified_at' => now()
        ]);
        
        try {
            $adminNotificationService->notifyClientRegistered($testClient);
            $this->info('✅ Client registration notification sent');
        } catch (\Exception $e) {
            $this->error('❌ Client registration notification failed: ' . $e->getMessage());
        }
        
        // Create a client record for other tests
        $client = Client::create([
            'user_id' => $testClient->id,
            'status' => 'active'
        ]);
        
        // Test 2: Document Upload Notification
        $this->info("\n2. Testing Document Upload Notification...");
        try {
            // Create a test document
            $document = Document::create([
                'client_id' => $client->id,
                'name' => 'test-document.pdf',
                'original_name' => 'test-document.pdf',
                'file_path' => 'documents/test-document.pdf',
                'file_size' => 1024000,
                'mime_type' => 'application/pdf',
                'document_type' => 'tax_returns',
                'status' => 'pending',
                'uploaded_by' => $testClient->id
            ]);
            
            $adminNotificationService->notifyDocumentUploaded($document);
            $this->info('✅ Document upload notification sent');
        } catch (\Exception $e) {
            $this->error('❌ Document upload notification failed: ' . $e->getMessage());
        }
        
        // Test 3: Message Notification
        $this->info("\n3. Testing Message Notification...");
        try {
            $admin = $admins->first();
            $message = Message::create([
                'client_id' => $client->id,
                'sender_id' => $testClient->id,
                'recipient_id' => $admin->id,
                'subject' => 'Test Message from Client',
                'body' => 'This is a test message to verify admin notifications.',
                'priority' => 'normal'
            ]);
            $message->load('sender', 'recipient');
            
            $adminNotificationService->notifyMessageSent($message);
            $this->info('✅ Message notification sent');
        } catch (\Exception $e) {
            $this->error('❌ Message notification failed: ' . $e->getMessage());
        }
        
        // Test 4: Review Request Notification
        $this->info("\n4. Testing Review Request Notification...");
        try {
            $reviewData = [
                'sections' => ['personal', 'spouse', 'employee'],
                'message' => 'Please review my tax information for accuracy.',
                'priority' => 'high'
            ];
            
            $adminNotificationService->notifyReviewRequested($client, $reviewData);
            $this->info('✅ Review request notification sent');
        } catch (\Exception $e) {
            $this->error('❌ Review request notification failed: ' . $e->getMessage());
        }
        
        // Test 5: Invoice Payment Notification
        $this->info("\n5. Testing Invoice Payment Notification...");
        try {
            // Create a test invoice
            $invoice = \App\Models\Invoice::create([
                'client_id' => $client->id,
                'invoice_number' => 'INV-TEST-' . time(),
                'title' => 'Tax Consultation Services',
                'send_to_email' => $testClient->email,
                'invoice_year' => now()->year,
                'subtotal' => 450.00,
                'tax_rate' => 10.00,
                'tax_amount' => 50.00,
                'total_amount' => 500.00,
                'status' => 'paid',
                'paid_at' => now()
            ]);
            
            $adminNotificationService->notifyInvoicePaid($invoice);
            $this->info('✅ Invoice payment notification sent');
        } catch (\Exception $e) {
            $this->error('❌ Invoice payment notification failed: ' . $e->getMessage());
        }
        
        // Cleanup test data
        $this->info("\n6. Cleaning up test data...");
        try {
            Message::where('sender_id', $testClient->id)->delete();
            Document::where('client_id', $client->id)->delete();
            \App\Models\Invoice::where('client_id', $client->id)->delete();
            $client->delete();
            $testClient->delete();
            $this->info('✅ Test data cleaned up');
        } catch (\Exception $e) {
            $this->error('❌ Cleanup failed: ' . $e->getMessage());
        }
        
        $this->info("\n🎉 Admin notification testing completed!");
        $this->info("Check the admin email inboxes to verify all notifications were received.");
        
        // Show mail configuration
        $this->info("\nCurrent mail configuration:");
        $this->line("Driver: " . config('mail.default'));
        $this->line("From Address: " . config('mail.from.address'));
        $this->line("From Name: " . config('mail.from.name'));
        
        if (config('mail.default') === 'log') {
            $this->info("📧 Emails are being logged to storage/logs/laravel.log");
        }
    }
}