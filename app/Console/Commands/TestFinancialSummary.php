<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Services\InvoiceService;

class TestFinancialSummary extends Command
{
    protected $signature = 'test:financial-summary {client_id}';
    protected $description = 'Test financial summary for a client';

    public function handle()
    {
        $clientId = $this->argument('client_id');
        $client = Client::find($clientId);
        
        if (!$client) {
            $this->error("Client with ID {$clientId} not found");
            return;
        }
        
        $this->info("Testing financial summary for client ID: {$clientId}");
        
        try {
            $invoiceService = app(InvoiceService::class);
            $client->load(['invoices', 'assets', 'expenses']);
            
            $this->info("Client loaded successfully");
            $this->info("Invoices count: " . $client->invoices->count());
            $this->info("Assets count: " . $client->assets->count());
            $this->info("Expenses count: " . $client->expenses->count());
            
            $financialSummary = $invoiceService->getClientFinancialSummary($client);
            $this->info("Financial summary generated successfully");
            
            // Add asset and expense summaries
            $financialSummary['total_asset_value'] = $client->assets->sum('cost_of_acquisition');
            $financialSummary['total_business_asset_value'] = $client->assets->sum(function ($asset) {
                return $asset->cost_of_acquisition * ($asset->percentage_used_in_business / 100);
            });
            $financialSummary['total_expense_amount'] = $client->expenses->sum('amount');
            
            $this->info("Summary calculated successfully");
            $this->info(json_encode($financialSummary, JSON_PRETTY_PRINT));
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . " Line: " . $e->getLine());
        }
    }
}