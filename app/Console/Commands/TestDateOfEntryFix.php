<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\User;

class TestDateOfEntryFix extends Command
{
    protected $signature = 'test:date-of-entry-fix';
    protected $description = 'Test the date of entry US fix';

    public function handle()
    {
        $this->info('Testing date of entry US fix...');
        
        // Find a client or create test data
        $client = Client::first();
        
        if (!$client) {
            $this->info('No client found, creating test data...');
            
            // Create a test user and client
            $user = User::factory()->create([
                'role' => 'client',
                'first_name' => 'Test',
                'last_name' => 'Client'
            ]);
            
            $client = Client::create([
                'user_id' => $user->id,
                'visa_status' => 'h1b',
                'date_of_entry_us' => '2020-03-15'
            ]);
            
            $this->info("Created test client {$client->id}");
        } else {
            // Update existing client with test date
            $client->update([
                'visa_status' => 'f1',
                'date_of_entry_us' => '2019-08-20'
            ]);
        }
        
        // Test the prepareClientDataForForm method
        $controller = new \App\Http\Controllers\Client\InformationController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('prepareClientDataForForm');
        $method->setAccessible(true);
        
        $clientData = $method->invoke($controller, $client);
        
        $this->info('Client data prepared for form:');
        $this->line('Date of entry US: ' . ($clientData['personal']['date_of_entry_us'] ?? 'null'));
        $this->line('Type: ' . gettype($clientData['personal']['date_of_entry_us'] ?? null));
        $this->line('Visa status: ' . ($clientData['personal']['visa_status'] ?? 'null'));
        
        // Test JSON serialization
        $this->info('JSON serialization test:');
        $json = json_encode([
            'date_of_entry_us' => $clientData['personal']['date_of_entry_us'],
            'visa_status' => $clientData['personal']['visa_status']
        ], JSON_PRETTY_PRINT);
        $this->line($json);
        
        // Test date format validation
        $dateOfEntry = $clientData['personal']['date_of_entry_us'] ?? '';
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateOfEntry)) {
            $this->info('✅ Date format is correct (YYYY-MM-DD)');
        } elseif (empty($dateOfEntry)) {
            $this->info('ℹ️  Date is empty');
        } else {
            $this->error('❌ Date format is incorrect: ' . $dateOfEntry);
        }
        
        // Test raw database value
        $this->info('Raw database values:');
        $this->line('Raw date_of_entry_us: ' . ($client->getRawOriginal('date_of_entry_us') ?? 'null'));
        $this->line('Accessor date_of_entry_us: ' . ($client->date_of_entry_us ?? 'null'));
        
        $this->info('Test completed!');
    }
}