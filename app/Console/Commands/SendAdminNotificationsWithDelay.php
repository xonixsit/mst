<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Client;
use App\Models\Message;
use App\Models\Document;
use App\Services\AdminNotificationService;

class SendAdminNotificationsWithDelay extends Command
{
    protected $signature = 'send:admin-notifications-delayed';
    protected $description = 'Send admin notifications with delays to avoid rate limiting';

    public function handle()
    {
        $this->info('Sending Admin Notifications with Delays...');
        
        $adminNotificationService = app(AdminNotificationService::class);
        
        // Check admin users
        $admins = $adminNotificationService->getAdminUsers();
        if ($admins->isEmpty()) {
            $this->error('No admin users found.');
            return;
        }
        
        $this->info("Sending to: {$admins->first()->email}");
        
        // Create test client
        $uniqueEmail = 'test.client.' . time() . '@example.com';
        $testClient = User::create([
            'role' => 'client',
            'first_name' => 'Test',
            'last_name' => 'Client',
            'email' => $uniqueEmail,
            'password' => bcrypt('password123'),
            'email_verified_at' => now()
        ]);
        
        $client = Client::create([
            'user_id' => $testClient->id,
            'status' => 'active'
        ]);
        
        // 1. Client Registration (immediate)
        $this->info("\n1. Sending Client Registration notification...");
        $adminNotificationService->notifyClientRegistered($testClient);
        $this->info('✅ Sent - Client Registration');
        sleep(3); // 3 second delay
        
        // 2. Document Upload
        $this->info("\n2. Sending Document Upload notification...");
        $document = Document::create([
            'client_id' => $client->id,
            'name' => 'w2-form.pdf',
            'original_name' => 'w2-form.pdf',
            'file_path' => 'documents/w2-form.pdf',
            'file_size' => 512000,
            'mime_type' => 'application/pdf',
            'document_type' => 'w2',
            'status' => 'pending',
            'uploaded_by' => $testClient->id
        ]);
        
        $adminNotificationService->notifyDocumentUploaded($document);
        $this->info('✅ Sent - Document Upload');
        sleep(3); // 3 second delay
        
        // 3. Message from Client
        $this->info("\n3. Sending Message notification...");
        $admin = $admins->first();
        $message = Message::create([
            'client_id' => $client->id,
            'sender_id' => $testClient->id,
            'recipient_id' => $admin->id,
            'subject' => 'Question about Tax Deductions',
            'body' => 'Hi, I have a question about which expenses I can deduct for my home office. Can you please help?',
            'priority' => 'normal'
        ]);
        
        $adminNotificationService->notifyMessageSent($message);
        $this->info('✅ Sent - Message from Client');
        sleep(3); // 3 second delay
        
        // 4. Review Request
        $this->info("\n4. Sending Review Request notification...");
        $reviewData = [
            'sections' => ['personal', 'spouse', 'employee'],
            'message' => 'Please review my tax information. I want to make sure everything is accurate before filing.',
            'priority' => 'high'
        ];
        
        $adminNotificationService->notifyReviewRequested($client, $reviewData);
        $this->info('✅ Sent - Review Request');
        sleep(3); // 3 second delay
        
        // 5. Invoice Payment
        $this->info("\n5. Sending Invoice Payment notification...");
        $invoice = \App\Models\Invoice::create([
            'client_id' => $client->id,
            'invoice_number' => 'INV-PAID-' . time(),
            'title' => 'Tax Preparation Services 2024',
            'send_to_email' => $testClient->email,
            'invoice_year' => now()->year,
            'subtotal' => 750.00,
            'tax_rate' => 8.25,
            'tax_amount' => 61.88,
            'total_amount' => 811.88,
            'status' => 'paid',
            'paid_at' => now()
        ]);
        
        $adminNotificationService->notifyInvoicePaid($invoice);
        $this->info('✅ Sent - Invoice Payment');
        
        $this->info("\n🎉 All 5 admin notifications sent with delays!");
        $this->info("Check xonixsitsolutions@gmail.com inbox in the next few minutes.");
        $this->info("Subject lines to look for:");
        $this->line("• New Client Registration: Test Client");
        $this->line("• Document Upload: w2-form.pdf");
        $this->line("• New Message: Question about Tax Deductions");
        $this->line("• Review Request from Test Client");
        $this->line("• Invoice Payment: INV-PAID-{$invoice->invoice_number}");
        
        // Cleanup
        $this->info("\nCleaning up test data...");
        Message::where('sender_id', $testClient->id)->delete();
        Document::where('client_id', $client->id)->delete();
        \App\Models\Invoice::where('client_id', $client->id)->delete();
        $client->delete();
        $testClient->delete();
        $this->info('✅ Test data cleaned up');
    }
}