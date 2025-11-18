<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\User;
use Illuminate\Console\Command;

class DebugClientData extends Command
{
    protected $signature = 'debug:client-data';
    protected $description = 'Debug client data to find duplicates';

    public function handle()
    {
        $this->info('Debugging client data...');

        // Get all clients with their users
        $clients = Client::with('user')->get();
        
        $this->info("Total clients: " . $clients->count());

        // Group by user_id to find duplicates
        $clientsByUser = $clients->groupBy('user_id');
        
        $this->info("Unique user_ids: " . $clientsByUser->count());

        // Find duplicates
        $duplicates = $clientsByUser->filter(function ($group) {
            return $group->count() > 1;
        });

        if ($duplicates->count() > 0) {
            $this->error("Found duplicate clients for the same user:");
            
            foreach ($duplicates as $userId => $clientGroup) {
                $user = $clientGroup->first()->user;
                $this->warn("User: {$user->name} ({$user->email}) has {$clientGroup->count()} client records:");
                
                foreach ($clientGroup as $client) {
                    $this->line("  - Client ID: {$client->id}, Status: {$client->status}, Created: {$client->created_at}");
                }
            }
        } else {
            $this->info("No duplicate clients found.");
        }

        // Check for clients without users
        $clientsWithoutUsers = Client::whereDoesntHave('user')->get();
        
        if ($clientsWithoutUsers->count() > 0) {
            $this->warn("Found {$clientsWithoutUsers->count()} clients without user accounts:");
            foreach ($clientsWithoutUsers as $client) {
                $this->line("  - Client ID: {$client->id}, user_id: {$client->user_id}");
            }
        }

        // Check for users with client role but no client record
        $clientUsers = User::where('role', 'client')->get();
        $usersWithoutClients = $clientUsers->filter(function ($user) {
            return !$user->client;
        });

        if ($usersWithoutClients->count() > 0) {
            $this->warn("Found {$usersWithoutClients->count()} client users without client records:");
            foreach ($usersWithoutClients as $user) {
                $this->line("  - User ID: {$user->id}, Name: {$user->name}, Email: {$user->email}");
            }
        }

        return 0;
    }
}