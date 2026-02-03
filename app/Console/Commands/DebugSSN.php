<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;

class DebugSSN extends Command
{
    protected $signature = 'debug:ssn {client_id}';
    protected $description = 'Debug SSN retrieval for a client';

    public function handle()
    {
        $clientId = $this->argument('client_id');
        $client = Client::find($clientId);
        
        if (!$client) {
            $this->error("Client {$clientId} not found");
            return 1;
        }
        
        $this->info("=== SSN Debug for Client {$clientId} ===");
        $this->info("SSN (decrypted): " . ($client->ssn ?: 'NULL'));
        $this->info("Raw SSN (encrypted): " . ($client->getAttributes()['ssn'] ?: 'NULL'));
        $this->info("Has SSN: " . ($client->ssn ? 'YES' : 'NO'));
        
        // Test what gets sent to frontend
        $clientArray = $client->toArray();
        $this->info("SSN in toArray(): " . ($clientArray['ssn'] ?? 'NOT_PRESENT'));
        
        return 0;
    }
}