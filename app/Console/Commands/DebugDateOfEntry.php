<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\User;

class DebugDateOfEntry extends Command
{
    protected $signature = 'debug:date-of-entry {client_id?}';
    protected $description = 'Debug date of entry US field';

    public function handle()
    {
        $clientId = $this->argument('client_id');
        
        if (!$clientId) {
            // Find a client with date_of_entry_us data
            $client = Client::whereNotNull('date_of_entry_us')->first();
            if (!$client) {
                $this->error('No client with date_of_entry_us found');
                return;
            }
        } else {
            $client = Client::find($clientId);
            if (!$client) {
                $this->error("Client with ID {$clientId} not found");
                return;
            }
        }
        
        $this->info("Debugging date_of_entry_us for client: {$client->id}");
        
        // Check raw database value
        $rawValue = $client->getRawOriginal('date_of_entry_us');
        $this->info("Raw database value: " . ($rawValue ?? 'null'));
        
        // Check accessor value
        $accessorValue = $client->date_of_entry_us;
        $this->info("Accessor value: " . ($accessorValue ?? 'null'));
        $this->info("Accessor value type: " . gettype($accessorValue));
        
        // Test the prepareClientDataForForm method
        $controller = new \App\Http\Controllers\Client\InformationController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('prepareClientDataForForm');
        $method->setAccessible(true);
        
        $clientData = $method->invoke($controller, $client);
        
        $this->info("Value in form data: " . ($clientData['personal']['date_of_entry_us'] ?? 'null'));
        $this->info("Form data type: " . gettype($clientData['personal']['date_of_entry_us'] ?? null));
        
        // Test JSON serialization
        $this->info("\nJSON serialization test:");
        $personalData = $clientData['personal'];
        $this->line(json_encode(['date_of_entry_us' => $personalData['date_of_entry_us']], JSON_PRETTY_PRINT));
        
        // Test setting a new date
        $this->info("\nTesting date setting...");
        $testDate = '2020-01-15';
        $client->date_of_entry_us = $testDate;
        $client->save();
        
        $client->refresh();
        $this->info("After setting {$testDate}:");
        $this->info("Raw value: " . ($client->getRawOriginal('date_of_entry_us') ?? 'null'));
        $this->info("Accessor value: " . ($client->date_of_entry_us ?? 'null'));
        
        // Test form data again
        $clientData = $method->invoke($controller, $client);
        $this->info("Form data after update: " . ($clientData['personal']['date_of_entry_us'] ?? 'null'));
    }
}