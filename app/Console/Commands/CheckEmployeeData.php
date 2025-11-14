<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;

class CheckEmployeeData extends Command
{
    protected $signature = 'check:employee-data {client_id?}';
    protected $description = 'Check employee data for a client';

    public function handle()
    {
        $clientId = $this->argument('client_id') ?? 2;
        
        $client = Client::with('employees')->find($clientId);
        
        if (!$client) {
            $this->error("Client with ID {$clientId} not found");
            return 1;
        }

        $this->info("Client: {$client->full_name}");
        $this->info("Employee count: {$client->employees->count()}");
        
        if ($client->employees->count() > 0) {
            $this->table(
                ['ID', 'Employer Name', 'Job Title', 'Salary', 'Benefits'],
                $client->employees->map(function ($emp) {
                    return [
                        $emp->id,
                        $emp->employer_name ?? 'N/A',
                        $emp->job_title ?? 'N/A',
                        $emp->annual_salary ?? 'N/A',
                        is_array($emp->benefits) ? json_encode($emp->benefits) : ($emp->benefits ?? 'N/A')
                    ];
                })
            );
        } else {
            $this->warn('No employees found for this client');
        }
        
        return 0;
    }
}