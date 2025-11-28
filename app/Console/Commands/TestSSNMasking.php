<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\User;

class TestSSNMasking extends Command
{
    protected $signature = 'test:ssn-masking';
    protected $description = 'Test SSN masking functionality';

    public function handle()
    {
        $this->info('Testing SSN masking functionality...');
        
        // Test the masking method
        $controller = new \App\Http\Controllers\Client\InformationController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('maskSSN');
        $method->setAccessible(true);
        
        $testCases = [
            '123456789' => '***-**-6789',
            '123-45-6789' => '***-**-6789',
            '12345' => '***45',
            '1234567' => '***-**-67',
            '123' => '123',
            '' => '',
            null => ''
        ];
        
        $this->info('Testing SSN masking patterns:');
        $this->table(['Input', 'Expected', 'Actual', 'Status'], 
            array_map(function($input, $expected) use ($method, $controller) {
                $actual = $method->invoke($controller, $input);
                $status = $actual === $expected ? '✅ Pass' : '❌ Fail';
                return [
                    $input ?? 'null',
                    $expected,
                    $actual,
                    $status
                ];
            }, array_keys($testCases), $testCases)
        );
        
        // Test with actual client data
        $client = Client::first();
        if ($client && $client->ssn) {
            $this->info("\nTesting with real client data:");
            $this->line("Original SSN: [HIDDEN FOR SECURITY]");
            $this->line("Masked SSN: " . $method->invoke($controller, $client->ssn));
            
            // Test the full prepareClientDataForForm method
            $prepareMethod = $reflection->getMethod('prepareClientDataForForm');
            $prepareMethod->setAccessible(true);
            $clientData = $prepareMethod->invoke($controller, $client);
            
            $this->line("SSN in form data: " . $clientData['personal']['ssn']);
            
            if ($client->spouse && $client->spouse->ssn) {
                $this->line("Spouse SSN in form data: " . $clientData['spouse']['ssn']);
            }
        } else {
            $this->info("\nNo client with SSN found for real data testing");
        }
        
        $this->info("\nSSN masking test completed!");
    }
}