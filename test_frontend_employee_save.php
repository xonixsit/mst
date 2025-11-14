<?php

// Test script to simulate frontend employee data saving
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Client;
use App\Models\User;
use App\Http\Controllers\Client\InformationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

try {
    echo "Testing frontend employee data saving...\n";
    
    // Find a client user
    $clientUser = User::first();
    
    if (!$clientUser) {
        echo "No users found. Creating a test user...\n";
        $clientUser = User::create([
            'name' => 'Test Client',
            'email' => 'testclient@example.com',
            'password' => bcrypt('password'),
            'role' => 'client'
        ]);
    }
    
    echo "Using client user: {$clientUser->email}\n";
    
    // Simulate authentication
    Auth::login($clientUser);
    
    // Find or create a client record for this user
    $client = Client::where('email', $clientUser->email)->first();
    if (!$client) {
        $client = Client::create([
            'user_id' => $clientUser->id,
            'first_name' => 'Test',
            'last_name' => 'Client',
            'email' => $clientUser->email,
            'phone' => '555-1234',
            'ssn' => '123-45-6789',
            'date_of_birth' => '1990-01-01',
            'marital_status' => 'single',
            'street_no' => '123 Test St',
            'zip_code' => '12345',
            'city' => 'Test City',
            'state' => 'TS',
            'country' => 'USA',
            'status' => 'active'
        ]);
        echo "Created client record for user.\n";
    }
    
    // Simulate the data structure that would come from the frontend
    // This mimics what the mapFieldsToBackend function would produce
    $frontendData = [
        'personal' => [
            'first_name' => 'Test',
            'last_name' => 'Client',
            'email' => 'testclient@example.com',
            'phone' => '555-1234',
            'ssn' => '123-45-6789',
            'date_of_birth' => '1990-01-01',
            'marital_status' => 'single',
            'street_no' => '123 Test St',
            'zip_code' => '12345',
            'city' => 'Test City',
            'state' => 'TS',
            'country' => 'USA',
        ],
        'spouse' => [],
        'employee' => [
            [
                'employerName' => 'Frontend Test Company',
                'position' => 'Frontend Developer',
                'startDate' => '2021-03-01',
                'endDate' => null,
                'employmentStatus' => 'full-time',
                'salary' => '80000.00',
                'payFrequency' => 'bi-weekly',
                'workLocation' => 'remote',
                'notes' => 'Full remote position',
                'benefits' => [
                    'healthInsurance' => true,
                    'retirementPlan' => true,
                    'dentalInsurance' => true,
                    'visionInsurance' => false
                ]
            ]
        ],
        'projects' => [],
        'assets' => [],
        'expenses' => []
    ];
    
    // Create a mock request
    $request = Request::create('/client/information/auto-save', 'POST', $frontendData);
    $request->headers->set('Content-Type', 'application/json');
    
    // Create controller instance
    $controller = app(InformationController::class);
    
    // Call the auto-save method
    echo "Calling auto-save method with frontend-formatted data...\n";
    $response = $controller->autoSave($request);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
    
    if ($response->getStatusCode() === 200) {
        // Check if the employee data was saved
        $client->refresh();
        $employee = $client->employees->first();
        
        if ($employee) {
            echo "\nEmployee data saved successfully!\n";
            echo "Employer Name: {$employee->employer_name}\n";
            echo "Job Title: {$employee->job_title}\n";
            echo "Employment Type: {$employee->employment_type}\n";
            echo "Annual Salary: {$employee->annual_salary}\n";
            echo "Pay Frequency: {$employee->pay_frequency}\n";
            echo "Work Location: {$employee->work_location}\n";
            echo "Benefits: " . json_encode($employee->benefits) . "\n";
        } else {
            echo "\nNo employee data found after save!\n";
        }
        
        echo "\nFrontend employee save test completed successfully!\n";
    } else {
        echo "\nFrontend employee save test failed!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}