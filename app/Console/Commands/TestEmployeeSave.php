<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\ClientEmployee;

class TestEmployeeSave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:employee-save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test employee data saving';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = Client::first();
        
        if (!$client) {
            $this->error('No client found to test with');
            return 1;
        }

        $this->info("Testing employee save for client: {$client->full_name}");

        // Test data similar to what's in the error log
        $employeeData = [
            'client_id' => $client->id,
            'employer_name' => 'Not Specified222', // Default value
            'job_title' => 'Software Developer',
            'annual_salary' => 75000.00,
            'work_description' => 'Remote work 3 days per week',
            'benefits' => json_encode([
                'healthInsurance' => true,
                'retirementPlan' => true,
                'dentalInsurance' => false,
                'visionInsurance' => true
            ])
        ];

        try {
            $employee = ClientEmployee::create($employeeData);
            $this->info("Success! Created employee record with ID: {$employee->id}");
            
            // Clean up
            $employee->delete();
            $this->info("Test record cleaned up");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error creating employee record: " . $e->getMessage());
            return 1;
        }
    }
}