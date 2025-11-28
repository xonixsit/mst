<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ErrorHandlingService;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class TestErrorHandling extends Command
{
    protected $signature = 'test:error-handling';
    protected $description = 'Test the new error handling system';

    public function handle()
    {
        $this->info('Testing Error Handling Service...');
        
        // Test validation error
        $this->info("\n1. Testing Validation Error:");
        try {
            throw ValidationException::withMessages([
                'email' => ['The email field is required.'],
                'phone' => ['The phone number format is invalid.']
            ]);
        } catch (ValidationException $e) {
            $result = ErrorHandlingService::formatValidationErrors($e);
            $this->line(json_encode($result, JSON_PRETTY_PRINT));
        }
        
        // Test database error
        $this->info("\n2. Testing Database Error:");
        try {
            throw new QueryException('mysql', 'SELECT * FROM non_existent_table', [], new \Exception('Table not found'));
        } catch (QueryException $e) {
            $result = ErrorHandlingService::handleApiError($e, ['action' => 'test_database_query']);
            $this->line(json_encode($result, JSON_PRETTY_PRINT));
        }
        
        // Test auto-save error
        $this->info("\n3. Testing Auto-save Error:");
        try {
            throw new \Exception('Auto-save operation failed due to network timeout');
        } catch (\Exception $e) {
            $result = ErrorHandlingService::handleApiError($e, ['action' => 'auto_save_test']);
            $this->line(json_encode($result, JSON_PRETTY_PRINT));
        }
        
        // Test actionable error
        $this->info("\n4. Testing Actionable Error:");
        $actionableError = ErrorHandlingService::createActionableError('ERR_SAVE_001', [
            'Check your internet connection',
            'Try refreshing the page',
            'Contact support if the problem persists'
        ]);
        $this->line(json_encode($actionableError, JSON_PRETTY_PRINT));
        
        $this->info("\nError handling test completed!");
    }
}