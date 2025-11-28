<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\User;

class TestSpouseDateFix extends Command
{
    protected $signature = 'test:spouse-date-fix';
    protected $description = 'Test the spouse date of birth fix';

    public function handle()
    {
        $this->info('Testing spouse date of birth fix...');
        
        // Find a client with spouse data or create test data
        $client = Client::whereHas('spouse')->first();
        
        if (!$client) {
            $this->info('No client with spouse found, creating test data...');
            
            // Create a test user and client
            $user = User::factory()->create([
                'role' => 'client',
                'first_name' => 'Test',
                'last_name' => 'Client'
            ]);
            
            $client = Client::create([
                'user_id' => $user->id,
                'marital_status' => 'married'
            ]);
            
            // Create spouse with date of birth
            $spouse = $client->spouse()->create([
                'first_name' => 'Test',
                'last_name' => 'Spouse',
                'date_of_birth' => '1990-05-15',
                'email' => 'test.spouse@example.com'
            ]);
            
            $this->info("Created test client {$client->id} with spouse {$spouse->id}");
        }
        
        // Test the prepareClientDataForForm method
        $controller = new \App\Http\Controllers\Client\InformationController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('prepareClientDataForForm');
        $method->setAccessible(true);
        
        $clientData = $method->invoke($controller, $client);
        
        $this->info('Client data prepared for form:');
        $this->line('Spouse date_of_birth: ' . ($clientData['spouse']['date_of_birth'] ?? 'null'));
        $this->line('Type: ' . gettype($clientData['spouse']['date_of_birth'] ?? null));
        
        // Test JSON serialization
        $this->info('JSON serialization test:');
        $json = json_encode($clientData['spouse']);
        $this->line($json);
        
        // Test date format validation
        $dateOfBirth = $clientData['spouse']['date_of_birth'] ?? '';
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateOfBirth)) {
            $this->info('✅ Date format is correct (YYYY-MM-DD)');
        } else {
            $this->error('❌ Date format is incorrect: ' . $dateOfBirth);
        }
        
        $this->info('Test completed!');
    }
}