<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ErrorHandlingService
{
    /**
     * Error codes for different types of errors
     */
    const ERROR_CODES = [
        'VALIDATION_FAILED' => 'ERR_VAL_001',
        'DATABASE_ERROR' => 'ERR_DB_001',
        'AUTHORIZATION_FAILED' => 'ERR_AUTH_001',
        'RESOURCE_NOT_FOUND' => 'ERR_404_001',
        'AUTO_SAVE_FAILED' => 'ERR_SAVE_001',
        'FILE_UPLOAD_FAILED' => 'ERR_FILE_001',
        'EMAIL_SEND_FAILED' => 'ERR_EMAIL_001',
        'EXTERNAL_API_FAILED' => 'ERR_API_001',
        'ENCRYPTION_FAILED' => 'ERR_ENC_001',
        'SESSION_EXPIRED' => 'ERR_SESS_001',
        'RATE_LIMIT_EXCEEDED' => 'ERR_RATE_001',
        'UNKNOWN_ERROR' => 'ERR_UNK_001'
    ];

    /**
     * User-friendly error messages
     */
    const ERROR_MESSAGES = [
        'ERR_VAL_001' => 'The information you entered is invalid. Please check the highlighted fields and try again.',
        'ERR_DB_001' => 'A database error occurred while processing your request. Please try again in a few moments.',
        'ERR_AUTH_001' => 'You do not have permission to perform this action. Please contact your administrator.',
        'ERR_404_001' => 'The requested resource could not be found. It may have been moved or deleted.',
        'ERR_SAVE_001' => 'Failed to save your changes. Please check your internet connection and try again.',
        'ERR_FILE_001' => 'File upload failed. Please ensure the file is valid and under the size limit.',
        'ERR_EMAIL_001' => 'Failed to send email notification. The action was completed but notification delivery failed.',
        'ERR_API_001' => 'External service is temporarily unavailable. Please try again later.',
        'ERR_ENC_001' => 'Data encryption/decryption failed. Please contact technical support.',
        'ERR_SESS_001' => 'Your session has expired. Please log in again to continue.',
        'ERR_RATE_001' => 'Too many requests. Please wait a moment before trying again.',
        'ERR_UNK_001' => 'An unexpected error occurred. Please try again or contact support if the problem persists.'
    ];

    /**
     * Technical error messages for logging
     */
    const TECHNICAL_MESSAGES = [
        'ERR_VAL_001' => 'Validation failed for user input',
        'ERR_DB_001' => 'Database query execution failed',
        'ERR_AUTH_001' => 'Authorization check failed',
        'ERR_404_001' => 'Resource not found in database',
        'ERR_SAVE_001' => 'Auto-save operation failed',
        'ERR_FILE_001' => 'File upload or processing failed',
        'ERR_EMAIL_001' => 'Email notification delivery failed',
        'ERR_API_001' => 'External API call failed',
        'ERR_ENC_001' => 'Encryption or decryption operation failed',
        'ERR_SESS_001' => 'User session validation failed',
        'ERR_RATE_001' => 'Rate limit threshold exceeded',
        'ERR_UNK_001' => 'Unhandled exception occurred'
    ];

    /**
     * Handle and format errors for API responses
     */
    public static function handleApiError(\Exception $exception, array $context = []): array
    {
        $errorCode = self::getErrorCode($exception);
        $errorId = self::generateErrorId();
        
        // Log the error with full details
        self::logError($exception, $errorCode, $errorId, $context);
        
        return [
            'success' => false,
            'error' => [
                'code' => $errorCode,
                'message' => self::ERROR_MESSAGES[$errorCode] ?? self::ERROR_MESSAGES['ERR_UNK_001'],
                'error_id' => $errorId,
                'timestamp' => now()->toISOString(),
                'details' => self::getErrorDetails($exception)
            ]
        ];
    }

    /**
     * Handle and format errors for web responses
     */
    public static function handleWebError(\Exception $exception, array $context = []): array
    {
        $errorCode = self::getErrorCode($exception);
        $errorId = self::generateErrorId();
        
        // Log the error with full details
        self::logError($exception, $errorCode, $errorId, $context);
        
        return [
            'error' => self::ERROR_MESSAGES[$errorCode] ?? self::ERROR_MESSAGES['ERR_UNK_001'],
            'error_code' => $errorCode,
            'error_id' => $errorId,
            'support_message' => "If this problem persists, please contact support with error ID: {$errorId}"
        ];
    }

    /**
     * Determine error code based on exception type
     */
    private static function getErrorCode(\Exception $exception): string
    {
        switch (true) {
            case $exception instanceof ValidationException:
                return self::ERROR_CODES['VALIDATION_FAILED'];
            
            case $exception instanceof QueryException:
                return self::ERROR_CODES['DATABASE_ERROR'];
            
            case $exception instanceof AuthorizationException:
                return self::ERROR_CODES['AUTHORIZATION_FAILED'];
            
            case $exception instanceof NotFoundHttpException:
                return self::ERROR_CODES['RESOURCE_NOT_FOUND'];
            
            case str_contains($exception->getMessage(), 'auto-save'):
            case str_contains($exception->getMessage(), 'save'):
                return self::ERROR_CODES['AUTO_SAVE_FAILED'];
            
            case str_contains($exception->getMessage(), 'file'):
            case str_contains($exception->getMessage(), 'upload'):
                return self::ERROR_CODES['FILE_UPLOAD_FAILED'];
            
            case str_contains($exception->getMessage(), 'email'):
            case str_contains($exception->getMessage(), 'mail'):
                return self::ERROR_CODES['EMAIL_SEND_FAILED'];
            
            case str_contains($exception->getMessage(), 'api'):
            case str_contains($exception->getMessage(), 'external'):
                return self::ERROR_CODES['EXTERNAL_API_FAILED'];
            
            case str_contains($exception->getMessage(), 'encrypt'):
            case str_contains($exception->getMessage(), 'decrypt'):
                return self::ERROR_CODES['ENCRYPTION_FAILED'];
            
            case str_contains($exception->getMessage(), 'session'):
                return self::ERROR_CODES['SESSION_EXPIRED'];
            
            case str_contains($exception->getMessage(), 'rate limit'):
            case str_contains($exception->getMessage(), 'too many'):
                return self::ERROR_CODES['RATE_LIMIT_EXCEEDED'];
            
            default:
                return self::ERROR_CODES['UNKNOWN_ERROR'];
        }
    }

    /**
     * Generate unique error ID for tracking
     */
    private static function generateErrorId(): string
    {
        return 'ERR_' . strtoupper(uniqid()) . '_' . time();
    }

    /**
     * Log error with full context
     */
    private static function logError(\Exception $exception, string $errorCode, string $errorId, array $context): void
    {
        Log::error(self::TECHNICAL_MESSAGES[$errorCode] ?? 'Unknown error occurred', [
            'error_id' => $errorId,
            'error_code' => $errorCode,
            'exception_type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'context' => $context,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get safe error details for user display
     */
    private static function getErrorDetails(\Exception $exception): array
    {
        $details = [];
        
        // Only include safe details for users
        if ($exception instanceof ValidationException) {
            $details['validation_errors'] = $exception->errors();
        }
        
        // Add more specific details based on error type
        if (app()->environment('local', 'testing')) {
            $details['debug_message'] = $exception->getMessage();
            $details['debug_file'] = basename($exception->getFile());
            $details['debug_line'] = $exception->getLine();
        }
        
        return $details;
    }

    /**
     * Format validation errors for better user experience
     */
    public static function formatValidationErrors(ValidationException $exception): array
    {
        $errorCode = self::ERROR_CODES['VALIDATION_FAILED'];
        $errorId = self::generateErrorId();
        
        self::logError($exception, $errorCode, $errorId, [
            'validation_errors' => $exception->errors()
        ]);
        
        return [
            'success' => false,
            'error' => [
                'code' => $errorCode,
                'message' => 'Please correct the following errors:',
                'error_id' => $errorId,
                'validation_errors' => $exception->errors(),
                'timestamp' => now()->toISOString()
            ]
        ];
    }

    /**
     * Create user-friendly error message with action suggestions
     */
    public static function createActionableError(string $errorCode, array $suggestions = []): array
    {
        $baseMessage = self::ERROR_MESSAGES[$errorCode] ?? self::ERROR_MESSAGES['ERR_UNK_001'];
        
        if (!empty($suggestions)) {
            $baseMessage .= "\n\nSuggested actions:\n• " . implode("\n• ", $suggestions);
        }
        
        return [
            'message' => $baseMessage,
            'code' => $errorCode,
            'suggestions' => $suggestions
        ];
    }
}