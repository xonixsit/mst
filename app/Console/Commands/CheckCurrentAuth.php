<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\User;

class CheckCurrentAuth extends Command
{
    protected $signature = 'check:auth';
    protected $description = 'Check current authentication and client data';

    public function handle()
    {
        $this->info("All Users:");
        $users = User::all();
        foreach ($users as $user) {
            $this->line("ID: {$user->id}, Email: {$user->email}, Role: {$user->role}");
        }
        
        $this->info("\nAll Clients:");
        $clients = Client::with('user')->get();
        foreach ($clients as $client) {
            $this->line("Client ID: {$client->id}, User ID: {$client->user_id}, User Email: " . ($client->user ? $client->user->email : 'No user'));
        }
        
        $this->info("\nChecking client lookup for user ID 1:");
        $client1 = Client::where('user_id', 1)->first();
        if ($client1) {
            $this->line("Found client ID: {$client1->id}");
        } else {
            $this->error("No client found for user ID 1");
        }
        
        $this->info("\nChecking client lookup for user ID 2:");
        $client2 = Client::where('user_id', 2)->first();
        if ($client2) {
            $this->line("Found client ID: {$client2->id}");
        } else {
            $this->error("No client found for user ID 2");
        }
    }
}