<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;

class TestClientData extends Command
{
    protected $signature = 'test:client-data {client_id}';
    protected $description = 'Test what data is sent to frontend for client edit';

    public function handle()
    {
        $clientId = $this->argument('client_id');
        $client = Client::with(['user', 'spouse', 'employees', 'projects', 'assets', 'expenses'])->find($clientId);
        
        if (!$client) {
            $this->error("Client {$clientId} not found");
            return 1;
        }
        
        // Simulate what the admin controller does
        $clientArray = $client->toArray();
        
        // Handle SSN masking for security
        if ($client->ssn) {
            $clientArray['ssn_masked'] = '***-**-' . substr($client->ssn, -4);
            $clientArray['has_ssn'] = true;
            // Send actual SSN to admin for editing
            $clientArray['ssn'] = $client->ssn;
        } else {
            $clientArray['ssn_masked'] = null;
            $clientArray['has_ssn'] = false;
        }
        
        $this->info("=== Client Data for Frontend ===");
        $this->info("SSN: " . ($clientArray['ssn'] ?? 'NOT_SET'));
        $this->info("SSN Masked: " . ($clientArray['ssn_masked'] ?? 'NOT_SET'));
        $this->info("Has SSN: " . ($clientArray['has_ssn'] ? 'YES' : 'NO'));
        
        return 0;
    }
}