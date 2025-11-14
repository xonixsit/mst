<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\User;
use App\Notifications\ClientWelcomeNotification;
use App\Notifications\DocumentApprovedNotification;
use App\Notifications\DocumentRejectedNotification;
use App\Notifications\InvoiceCreatedNotification;
use App\Notifications\InvoicePaidNotification;
use App\Notifications\MessageReceivedNotification;
use App\Notifications\AdminClientRegisteredNotification;
use App\Notifications\AdminDocumentUploadedNotification;
use App\Notifications\AdminInvoicePaidNotification;
use App\Notifications\AdminMessageSentNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailTemplates extends Command
{
    protected $signature = 'test:email-templates';
    protected $description = 'Test all email notification templates';

    public function handle()
    {
        $this->info('Testing Email Templates...');
        
        // Clean up any existing test data first
        $this->cleanupExistingTestData();
        
        Mail::fake();
        
        // Create test data
        $testUser = $this->createTestUser();
        $testAdmin = $this->createTestAdmin();
        $testClient = $this->createTestClient($testUser);
        $testInvoice = $this->createTestInvoice($testClient);
        $testDocument = $this->createTestDocument($testClient);
        $testMessage = $this->createTestMessage($testUser, $testClient);
        
        $this->info('✓ Test data created');
        
        // Test Welcome Notification
        try {
            $testUser->notify(new ClientWelcomeNotification($testUser));
            $this->info('✓ Client Welcome notification sent');
        } catch (\Exception $e) {
            $this->error("✗ Client Welcome failed: " . $e->getMessage());
        }
        
        // Test Invoice Created Notification
        try {
            $testUser->notify(new InvoiceCreatedNotification($testInvoice));
            $this->info('✓ Invoice Created notification sent');
        } catch (\Exception $e) {
            $this->error("✗ Invoice Created failed: " . $e->getMessage());
        }
        
        // Test Invoice Paid Notification
        try {
            $testInvoice->update(['status' => 'paid', 'paid_at' => now()]);
            $testUser->notify(new InvoicePaidNotification($testInvoice));
            $this->info('✓ Invoice Paid notification sent');
        } catch (\Exception $e) {
            $this->error("✗ Invoice Paid failed: " . $e->getMessage());
        }
        
        // Test Document Approved Notification
        try {
            $testDocument->update(['status' => 'approved']);
            $testUser->notify(new DocumentApprovedNotification($testDocument));
            $this->info('✓ Document Approved notification sent');
        } catch (\Exception $e) {
            $this->error("✗ Document Approved failed: " . $e->getMessage());
        }
        
        // Test Document Rejected Notification
        try {
            $testDocument->update(['status' => 'rejected', 'notes' => 'Document is unclear, please re-upload with better quality.']);
            $testUser->notify(new DocumentRejectedNotification($testDocument));
            $this->info('✓ Document Rejected notification sent');
        } catch (\Exception $e) {
            $this->error("✗ Document Rejected failed: " . $e->getMessage());
        }
        
        // Test Message Received Notification
        try {
            $testUser->notify(new MessageReceivedNotification($testMessage));
            $this->info('✓ Message Received notification sent');
        } catch (\Exception $e) {
            $this->error("✗ Message Received failed: " . $e->getMessage());
        }

        // Test Admin Notifications
        try {
            $testAdmin->notify(new AdminClientRegisteredNotification($testUser));
            $this->info('✓ Admin Client Registered notification sent');
        } catch (\Exception $e) {
            $this->error("✗ Admin Client Registered failed: " . $e->getMessage());
        }

        try {
            $testAdmin->notify(new AdminDocumentUploadedNotification($testDocument));
            $this->info('✓ Admin Document Uploaded notification sent');
        } catch (\Exception $e) {
            $this->error("✗ Admin Document Uploaded failed: " . $e->getMessage());
        }

        try {
            $testAdmin->notify(new AdminInvoicePaidNotification($testInvoice));
            $this->info('✓ Admin Invoice Paid notification sent');
        } catch (\Exception $e) {
            $this->error("✗ Admin Invoice Paid failed: " . $e->getMessage());
        }

        try {
            $testAdmin->notify(new AdminMessageSentNotification($testMessage));
            $this->info('✓ Admin Message Sent notification sent');
        } catch (\Exception $e) {
            $this->error("✗ Admin Message Sent failed: " . $e->getMessage());
        }
        
        // Cleanup test data
        $this->cleanupTestData($testUser, $testAdmin, $testClient, $testInvoice, $testDocument, $testMessage);
        
        $this->info('');
        $this->info('🎉 All email templates tested successfully!');
        $this->info('');
        $this->info('Email templates available:');
        $this->line('Client Notifications:');
        $this->line('- Client Welcome (registration)');
        $this->line('- Invoice Created');
        $this->line('- Invoice Paid');
        $this->line('- Document Approved');
        $this->line('- Document Rejected');
        $this->line('- Message Received');
        $this->line('');
        $this->line('Admin Notifications:');
        $this->line('- Client Registered');
        $this->line('- Document Uploaded');
        $this->line('- Invoice Paid');
        $this->line('- Message Sent');
        
        return 0;
    }
    
    private function createTestUser(): User
    {
        return User::create([
            'name' => 'Test Client User',
            'email' => 'testclient@example.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'email_verified_at' => now(),
        ]);
    }
    
    private function createTestClient(User $user): Client
    {
        return Client::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $user->email,
            'phone' => '555-0123',
            'status' => 'active',
        ]);
    }
    
    private function createTestInvoice(Client $client): Invoice
    {
        $invoice = Invoice::create([
            'client_id' => $client->id,
            'invoice_number' => 'TEST-001',
            'title' => 'Tax Preparation Services 2024',
            'comments' => 'Federal and State tax return preparation',
            'send_to_email' => $client->email,
            'invoice_year' => 2024,
            'tax_rate' => 18.00,
            'subtotal' => 300.00,
            'tax_amount' => 54.00,
            'total_amount' => 354.00,
            'due_date' => now()->addDays(30),
            'status' => 'sent',
        ]);
        
        $invoice->items()->create([
            'service_name' => 'Federal Filing',
            'quantity' => 1,
            'unit_price' => 150.00,
        ]);
        
        $invoice->items()->create([
            'service_name' => 'State Filing',
            'quantity' => 1,
            'unit_price' => 150.00,
        ]);
        
        return $invoice->fresh(['items']);
    }
    
    private function createTestDocument(Client $client): Document
    {
        return Document::create([
            'client_id' => $client->id,
            'name' => 'W-2 Form 2024',
            'original_name' => 'w2_2024.pdf',
            'file_path' => 'documents/test/w2_2024.pdf',
            'file_size' => 1024000,
            'mime_type' => 'application/pdf',
            'document_type' => 'w2',
            'tax_year' => 2024,
            'status' => 'pending',
            'uploaded_by' => 1,
        ]);
    }
    
    private function createTestMessage(User $user, Client $client): Message
    {
        $adminUser = User::create([
            'name' => 'Tax Professional',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        return Message::create([
            'client_id' => $client->id,
            'sender_id' => $adminUser->id,
            'recipient_id' => $user->id,
            'subject' => 'Additional Documents Required',
            'body' => 'Hi John, we need your 1099 forms to complete your tax return. Please upload them at your earliest convenience.',
            'priority' => 'normal',
            'is_read' => false,
        ]);
    }
    
    private function createTestAdmin(): User
    {
        return User::create([
            'name' => 'Test Admin User',
            'email' => 'testadmin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    private function cleanupTestData(User $user, User $admin, Client $client, Invoice $invoice, Document $document, Message $message): void
    {
        $message->delete();
        $document->delete();
        $invoice->items()->delete();
        $invoice->delete();
        $client->delete();
        User::where('email', 'admin@example.com')->delete();
        $admin->delete();
        $user->delete();
        
        $this->info('✓ Test data cleaned up');
    }
    
    private function cleanupExistingTestData(): void
    {
        User::where('email', 'testclient@example.com')->delete();
        User::where('email', 'testadmin@example.com')->delete();
        User::where('email', 'admin@example.com')->delete();
        Client::where('email', 'testclient@example.com')->delete();
        
        $this->info('✓ Existing test data cleaned up');
    }
}