<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\User;
use App\Notifications\InvoiceCreatedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DebugInvoiceEmail extends Command
{
    protected $signature = 'debug:invoice-email {invoice_id?}';
    protected $description = 'Debug invoice email sending';

    public function handle()
    {
        $invoiceId = $this->argument('invoice_id');
        
        if ($invoiceId) {
            $invoice = Invoice::with(['client.user', 'items'])->find($invoiceId);
        } else {
            $invoice = Invoice::with(['client.user', 'items'])->first();
        }
        
        if (!$invoice) {
            $this->error('No invoice found');
            return 1;
        }

        $this->info("=== Invoice Debug Info ===");
        $this->info("Invoice ID: {$invoice->id}");
        $this->info("Invoice Number: {$invoice->invoice_number}");
        $this->info("Client ID: {$invoice->client_id}");
        
        if ($invoice->client) {
            $this->info("Client exists: YES");
            $this->info("Client User ID: {$invoice->client->user_id}");
            
            if ($invoice->client->user) {
                $this->info("User exists: YES");
                $this->info("User Email: {$invoice->client->user->email}");
                $this->info("User Name: {$invoice->client->user->first_name} {$invoice->client->user->last_name}");
                
                // Test notification
                try {
                    $this->info("\n=== Testing Notification ===");
                    $invoice->client->user->notify(new InvoiceCreatedNotification($invoice));
                    $this->info("✅ Notification sent successfully");
                } catch (\Exception $e) {
                    $this->error("❌ Notification failed: " . $e->getMessage());
                    $this->error("Stack trace: " . $e->getTraceAsString());
                }
                
                // Test direct mail
                try {
                    $this->info("\n=== Testing Direct Mail ===");
                    Mail::to($invoice->client->user->email)->send(new \App\Mail\InvoiceMail($invoice));
                    $this->info("✅ Direct mail sent successfully");
                } catch (\Exception $e) {
                    $this->error("❌ Direct mail failed: " . $e->getMessage());
                    $this->error("Stack trace: " . $e->getTraceAsString());
                }
                
            } else {
                $this->error("User does NOT exist for this client");
            }
        } else {
            $this->error("Client does NOT exist for this invoice");
        }
        
        // Check mail configuration
        $this->info("\n=== Mail Configuration ===");
        $this->info("Mail Driver: " . config('mail.default'));
        $this->info("Mail Host: " . config('mail.mailers.smtp.host'));
        $this->info("Mail Port: " . config('mail.mailers.smtp.port'));
        $this->info("Mail From: " . config('mail.from.address'));
        
        return 0;
    }
}