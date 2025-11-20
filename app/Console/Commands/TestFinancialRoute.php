<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\User;
use App\Http\Controllers\Admin\ClientController;

class TestFinancialRoute extends Command
{
    protected $signature = 'test:financial-route {client_id}';
    protected $description = 'Test financial summary route';

    public function handle()
    {
        $clientId = $this->argument('client_id');
        $client = Client::find($clientId);
        
        if (!$client) {
            $this->error("Client with ID {$clientId} not found");
            return;
        }
        
        $this->info("Testing financial summary route for client ID: {$clientId}");
        
        try {
            // Simulate being logged in as admin
            $admin = User::where('role', 'admin')->first();
            if (!$admin) {
                $this->error("No admin user found");
                return;
            }
            
            auth()->login($admin);
            $this->info("Logged in as admin: " . $admin->email);
            
            // Test the controller method directly
            $controller = app(ClientController::class);
            $response = $controller->financialSummary($client);
            
            $this->info("Response status: " . $response->getStatusCode());
            $this->info("Response content: " . $response->getContent());
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . " Line: " . $e->getLine());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
    }
}