<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Client;
use App\Notifications\InvoiceCreatedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestInvoiceEmail extends Command
{
    protected $signature = 'test:invoice-email {email}';
    protected $description = 'Test invoice email sending';

    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            // Find the first invoice or create a test one
            $invoice = Invoice::with(['client.user', 'items'])->first();
            
            if (!$invoice) {
                $this->error('No invoices found in database. Please create an invoice first.');
                return 1;
            }

            $this->info("Testing email to: {$email}");
            $this->info("Using invoice: #{$invoice->invoice_number}");
            
            // Test direct mail sending
            Mail::to($email)->send(new \App\Mail\InvoiceMail($invoice));
            
            $this->info('✅ Email sent successfully via Mail facade');
            
            // Test notification system
            $testUser = new \App\Models\User();
            $testUser->email = $email;
            $testUser->first_name = 'Test';
            $testUser->last_name = 'User';
            
            $testUser->notify(new InvoiceCreatedNotification($invoice));
            
            $this->info('✅ Notification sent successfully');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Email failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}