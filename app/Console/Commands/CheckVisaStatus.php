<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class CheckVisaStatus extends Command
{
    protected $signature = 'check:visa-status';
    protected $description = 'Check visa status values in database';

    public function handle()
    {
        $this->info('Checking visa status values in database...');
        
        // Get all distinct visa status values
        $visaStatuses = DB::table('clients')
            ->whereNotNull('visa_status')
            ->distinct()
            ->pluck('visa_status');
            
        $this->info('Found visa status values:');
        foreach ($visaStatuses as $status) {
            $this->line("- '{$status}'");
        }
        
        // Get clients with visa status
        $clients = Client::whereNotNull('visa_status')->get(['id', 'visa_status']);
        
        $this->info("\nClients with visa status:");
        foreach ($clients as $client) {
            $visaStatus = $client->visa_status instanceof \App\Enums\VisaStatus 
                ? $client->visa_status->value 
                : $client->visa_status;
            $this->line("Client ID: {$client->id}, Visa Status: '{$visaStatus}'");
        }
        
        // Check for problematic values
        $validValues = [
            'citizen', 'permanent_resident', 'h1b', 'h4', 'f1', 'f2', 
            'j1', 'j2', 'l1', 'l2', 'o1', 'o2', 'e1', 'e2', 'tn', 
            'b1_b2', 'k1', 'k3', 'other'
        ];
        
        $invalidValues = $visaStatuses->reject(function ($status) use ($validValues) {
            return in_array($status, $validValues);
        });
        
        if ($invalidValues->isNotEmpty()) {
            $this->error("\nInvalid visa status values found:");
            foreach ($invalidValues as $invalid) {
                $this->error("- '{$invalid}'");
            }
        } else {
            $this->info("\nAll visa status values are valid!");
        }
    }
}