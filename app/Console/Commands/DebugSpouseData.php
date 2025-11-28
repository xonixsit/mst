<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\User;

class DebugSpouseData extends Command
{
    protected $signature = 'debug:spouse-data {client_id?}';
    protected $description = 'Debug spouse data saving and loading';

    public function handle()
    {
        $clientId = $this->argument('client_id');
        
        if (!$clientId) {
            // Find a client with spouse data
            $client = Client::whereHas('spouse')->first();
            if (!$client) {
                $this->error('No client with spouse data found');
                return;
            }
        } else {
            $client = Client::find($clientId);
            if (!$client) {
                $this->error("Client with ID {$clientId} not found");
                return;
            }
        }
        
        $this->info("Debugging spouse data for client: {$client->id}");
        
        // Check if spouse exists
        if ($client->spouse) {
            $this->info("Spouse data found:");
            $this->table(
                ['Field', 'Value', 'Type'],
                [
                    ['first_name', $client->spouse->first_name, gettype($client->spouse->first_name)],
                    ['last_name', $client->spouse->last_name, gettype($client->spouse->last_name)],
                    ['date_of_birth', $client->spouse->date_of_birth, gettype($client->spouse->date_of_birth)],
                    ['date_of_birth (raw)', $client->spouse->getRawOriginal('date_of_birth'), 'raw'],
                    ['ssn', $client->spouse->ssn ? '[HIDDEN]' : 'null', 'string'],
                    ['email', $client->spouse->email, gettype($client->spouse->email)],
                    ['phone', $client->spouse->phone, gettype($client->spouse->phone)],
                    ['occupation', $client->spouse->occupation, gettype($client->spouse->occupation)],
                ]
            );
            
            // Test JSON serialization
            $this->info("\nJSON serialization test:");
            $spouseArray = $client->spouse->toArray();
            $this->line(json_encode($spouseArray, JSON_PRETTY_PRINT));
            
        } else {
            $this->info("No spouse data found for this client");
            
            // Test creating spouse data
            $this->info("Testing spouse data creation...");
            
            $testData = [
                'first_name' => 'Test',
                'last_name' => 'Spouse',
                'date_of_birth' => '1990-05-15',
                'email' => 'test.spouse@example.com',
                'phone' => '555-123-4567',
                'occupation' => 'Test Occupation'
            ];
            
            try {
                $spouse = $client->spouse()->create($testData);
                $this->info("Spouse created successfully:");
                $this->line("ID: {$spouse->id}");
                $this->line("Date of birth: {$spouse->date_of_birth}");
                $this->line("Date of birth (raw): {$spouse->getRawOriginal('date_of_birth')}");
                
                // Clean up test data
                $spouse->delete();
                $this->info("Test spouse data cleaned up");
                
            } catch (\Exception $e) {
                $this->error("Failed to create spouse: " . $e->getMessage());
            }
        }
        
        // Test the ClientInformationService
        $this->info("\nTesting ClientInformationService...");
        
        $testSpouseData = [
            'spouse' => [
                'first_name' => 'Service',
                'last_name' => 'Test',
                'date_of_birth' => '1985-12-25',
                'email' => 'service.test@example.com'
            ]
        ];
        
        try {
            $service = app(\App\Services\ClientInformationService::class);
            
            // Set client as married first
            $client->update(['marital_status' => 'married']);
            
            $result = $service->updateClientInformation($client, $testSpouseData);
            
            if ($result['success']) {
                $this->info("Service update successful");
                
                // Check the saved data
                $client->refresh();
                if ($client->spouse) {
                    $this->info("Spouse data after service update:");
                    $this->line("Date of birth: {$client->spouse->date_of_birth}");
                    $this->line("Date of birth (raw): {$client->spouse->getRawOriginal('date_of_birth')}");
                }
            } else {
                $this->error("Service update failed: " . json_encode($result['errors']));
            }
            
        } catch (\Exception $e) {
            $this->error("Service test failed: " . $e->getMessage());
        }
    }
}