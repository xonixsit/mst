<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Services\ClientInformationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestExpenseSave extends Command
{
    protected $signature = 'test:expense-save {client_id?}';
    protected $description = 'Test expense saving functionality';

    public function handle(ClientInformationService $service)
    {
        $clientId = $this->argument('client_id');
        
        if (!$clientId) {
            $client = Client::first();
            if (!$client) {
                $this->error('No clients found in database');
                return 1;
            }
            $clientId = $client->id;
        }
        
        $this->info("Testing expense save for client ID: {$clientId}");
        
        // Test data with both camelCase (frontend format) and snake_case
        // IMPORTANT: Include IDs to test UPDATE functionality
        $testData = [
            'expenses' => [
                [
                    'id' => 57,  // Existing database ID - should UPDATE
                    'category' => 'business',
                    'particulars' => 'LAST UPDATED Office Supplies',
                    'taxPayer' => 'John Doe',
                    'spouse' => '',
                    'amount' => 250.00,  // Changed amount
                    'date' => '2024-11-14',
                    'receiptNumber' => 'RCP-2024-001-LAST',  // Changed receipt
                    'deductiblePercentage' => 85,  // Changed percentage
                    'remarks' => 'Last updated test expense'
                ],
                [
                    'id' => 58,  // Existing database ID - should UPDATE
                    'category' => 'medical',
                    'particulars' => 'LAST UPDATED Doctor Visit',
                    'tax_payer' => 'Jane Doe',
                    'spouse' => 'John Doe',
                    'amount' => 400.00,  // Changed amount
                    'expense_date' => '2024-11-10',
                    'receipt_number' => 'RCP-2024-002-LAST',  // Changed receipt
                    'deductible_percentage' => 90,
                    'remarks' => 'Last updated annual checkup'
                ],
                [
                    // No ID - should CREATE a new record
                    'category' => 'education',
                    'particulars' => 'Yet Another NEW Course Fee',
                    'taxPayer' => 'Student Name',
                    'amount' => 1000.00,
                    'date' => '2024-11-17',
                    'receiptNumber' => 'RCP-2024-005',
                    'deductiblePercentage' => 100,
                    'remarks' => 'Yet another new expense record'
                ]
            ]
        ];
        
        $this->info('Test data prepared:');
        $this->line(json_encode($testData, JSON_PRETTY_PRINT));
        
        try {
            $this->info('Attempting to update client with expenses...');
            $client = $service->updateClient($clientId, $testData);
            
            $this->info('Success! Checking saved expenses...');
            $client->load('expenses');
            
            if ($client->expenses->count() > 0) {
                $this->info('Found ' . $client->expenses->count() . ' expense(s):');
                foreach ($client->expenses as $expense) {
                    $this->line("  - {$expense->category}: {$expense->particulars} (\${$expense->amount}) - {$expense->tax_payer}");
                }
            } else {
                $this->warn('No expenses found after save!');
            }
            
            $this->newLine();
            $this->info('Check logs at storage/logs/laravel.log for detailed processing info');
            
        } catch (\Exception $e) {
            $this->error('Failed to save expenses: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}
