<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Document;

class CheckDocuments extends Command
{
    protected $signature = 'check:documents';
    protected $description = 'Check documents in database';

    public function handle()
    {
        $documents = Document::with('client.user')->get();
        
        $this->info('Total documents: ' . $documents->count());
        
        foreach ($documents as $doc) {
            $clientName = 'N/A';
            $clientExists = 'No';
            $userExists = 'No';
            
            if ($doc->client) {
                $clientExists = 'Yes';
                if ($doc->client->user) {
                    $userExists = 'Yes';
                    $clientName = $doc->client->user->first_name . ' ' . $doc->client->user->last_name;
                }
            }
            
            $this->line("- {$doc->name} (Client ID: {$doc->client_id}, Client exists: {$clientExists}, User exists: {$userExists}, Client: {$clientName})");
        }
        
        // Check clients table
        $this->info("\nClients in database:");
        $clients = \App\Models\Client::with('user')->get();
        foreach ($clients as $client) {
            $userName = $client->user ? ($client->user->first_name . ' ' . $client->user->last_name) : 'No User';
            $this->line("- Client ID: {$client->id}, User ID: {$client->user_id}, User: {$userName}");
        }
        
        return 0;
    }
}