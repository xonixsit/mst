<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Services\ClientInformationService;
use App\Services\BulkEmailService;
use App\Services\BulkExportService;
use App\Services\CommunicationService;

class TestEmployeeUpdate extends Command
{
    protected $signature = 'test:employee-update {client_id?}';
    protected $description = 'Test updating employee data';

    public function handle()
    {
        $clientId = $this->argument('client_id') ?? 2;
        
        $client = Client::with('employees')->find($clientId);
        
        if (!$client) {
            $this->error("Client with ID {$clientId} not found");
            return 1;
        }

        $this->info("Testing employee update for client: {$client->full_name}");
        
        // Show current employee data
        if ($client->employees->count() > 0) {
            $employee = $client->employees->first();
            $this->info("Current employee data:");
            $this->info("ID: {$employee->id}");
            $this->info("Employer: {$employee->employer_name}");
            $this->info("Position: {$employee->job_title}");
            $this->info("Salary: {$employee->annual_salary}");
        } else {
            $this->error("No employees found for this client");
            return 1;
        }

        // Test updating the employee data
        $employee = $client->employees->first();
        $testData = [
            'employee' => [
                [
                    'id' => $employee->id,
                    'employerName' => 'Microsoft Corporation',
                    'position' => 'Senior Software Engineer',
                    'salary' => '95000.00',
                    'notes' => 'Remote work with flexible hours',
                    'benefits' => [
                        'healthInsurance' => true,
                        'retirementPlan' => true,
                        'dentalInsurance' => true,
                        'visionInsurance' => true
                    ]
                ]
            ]
        ];

        try {
            // Create service instance with dependencies
            $bulkEmailService = app(BulkEmailService::class);
            $bulkExportService = app(BulkExportService::class);
            $communicationService = app(CommunicationService::class);
            
            $service = new ClientInformationService(
                $bulkEmailService,
                $bulkExportService,
                $communicationService
            );
            
            $this->info("Updating employee data...");
            $service->updateClient($client->id, $testData);
            
            // Refresh and show updated data
            $client->refresh();
            $updatedEmployee = $client->employees->first();
            
            $this->info("Updated employee data:");
            $this->info("ID: {$updatedEmployee->id}");
            $this->info("Employer: {$updatedEmployee->employer_name}");
            $this->info("Position: {$updatedEmployee->job_title}");
            $this->info("Salary: {$updatedEmployee->annual_salary}");
            $this->info("Notes: {$updatedEmployee->work_description}");
            
            $this->info("Employee update test completed successfully!");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error updating employee: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}