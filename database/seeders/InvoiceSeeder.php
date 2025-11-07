<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // Get some clients to create invoices for
        $clients = Client::take(5)->get();

        if ($clients->isEmpty()) {
            $this->command->info('No clients found. Please run ClientSeeder first.');
            return;
        }

        foreach ($clients as $client) {
            // Create 1-3 invoices per client
            $invoiceCount = rand(1, 3);
            
            for ($i = 0; $i < $invoiceCount; $i++) {
                $invoice = Invoice::create([
                    'client_id' => $client->id,
                    'invoice_number' => Invoice::generateInvoiceNumber(),
                    'title' => 'Tax Preparation Services ' . (date('Y') - $i),
                    'comments' => $i === 0 ? 'Standard tax preparation and filing services.' : null,
                    'send_to_email' => $client->email,
                    'invoice_year' => date('Y') - $i,
                    'tax_rate' => 18.00,
                    'status' => $this->getRandomStatus(),
                    'created_at' => now()->subDays(rand(1, 90)),
                ]);

                // Add random invoice items
                $services = [
                    ['name' => 'Federal Filing', 'price' => 150.00],
                    ['name' => 'State Filing', 'price' => 75.00],
                    ['name' => 'Sch C Planning', 'price' => 125.00],
                    ['name' => 'Stock Transaction', 'price' => 25.00],
                ];

                $itemCount = rand(2, 4);
                $selectedServices = collect($services)->random($itemCount);

                foreach ($selectedServices as $service) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'service_name' => $service['name'],
                        'quantity' => rand(1, 2),
                        'unit_price' => $service['price'],
                    ]);
                }

                // Calculate totals
                $invoice->load('items');
                $invoice->calculateTotals();
                $invoice->save();

                // Set sent/paid dates for non-draft invoices
                if ($invoice->status === 'sent') {
                    $invoice->update(['sent_at' => $invoice->created_at->addDays(1)]);
                } elseif ($invoice->status === 'paid') {
                    $invoice->update([
                        'sent_at' => $invoice->created_at->addDays(1),
                        'paid_at' => $invoice->created_at->addDays(rand(5, 30)),
                    ]);
                }
            }
        }

        $this->command->info('Invoice seeder completed successfully.');
    }

    private function getRandomStatus(): string
    {
        $statuses = ['draft', 'sent', 'paid', 'overdue'];
        $weights = [10, 20, 50, 20]; // Higher chance for paid invoices
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $statuses[$index];
            }
        }
        
        return 'draft';
    }
}