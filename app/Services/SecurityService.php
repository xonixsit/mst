<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SecurityService
{
    /**
     * Encrypt sensitive data for transmission.
     */
    public function encryptForTransmission(array $data): string
    {
        try {
            return Crypt::encryptString(json_encode($data));
        } catch (\Exception $e) {
            Log::error('Failed to encrypt data for transmission', [
                'error' => $e->getMessage(),
                'data_keys' => array_keys($data)
            ]);
            throw $e;
        }
    }

    /**
     * Decrypt data received from transmission.
     */
    public function decryptFromTransmission(string $encryptedData): array
    {
        try {
            $decrypted = Crypt::decryptString($encryptedData);
            return json_decode($decrypted, true);
        } catch (\Exception $e) {
            Log::error('Failed to decrypt transmitted data', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate secure token for API access.
     */
    public function generateSecureToken(int $length = 64): string
    {
        return Str::random($length);
    }

    /**
     * Hash sensitive data for storage.
     */
    public function hashSensitiveData(string $data): string
    {
        return Hash::make($data);
    }

    /**
     * Verify hashed sensitive data.
     */
    public function verifySensitiveData(string $data, string $hash): bool
    {
        return Hash::check($data, $hash);
    }

    /**
     * Sanitize data for logging (remove sensitive information).
     */
    public function sanitizeForLogging(array $data): array
    {
        $sensitiveFields = [
            'ssn', 'social_security_number', 'password', 'password_confirmation',
            'credit_card', 'bank_account', 'routing_number', 'account_number',
            'date_of_birth', 'dob', 'phone', 'mobile_number', 'work_number'
        ];

        $sanitized = $data;

        foreach ($sensitiveFields as $field) {
            if (isset($sanitized[$field])) {
                $sanitized[$field] = $this->maskSensitiveValue($sanitized[$field]);
            }
        }

        return $sanitized;
    }

    /**
     * Mask sensitive values for display.
     */
    public function maskSensitiveValue(string $value): string
    {
        $length = strlen($value);
        
        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        // Show first 2 and last 2 characters, mask the rest
        return substr($value, 0, 2) . str_repeat('*', $length - 4) . substr($value, -2);
    }

    /**
     * Validate data integrity using checksums.
     */
    public function generateChecksum(array $data): string
    {
        return hash('sha256', json_encode($data));
    }

    /**
     * Verify data integrity using checksums.
     */
    public function verifyChecksum(array $data, string $checksum): bool
    {
        return hash_equals($checksum, $this->generateChecksum($data));
    }

    /**
     * Generate secure session token.
     */
    public function generateSessionToken(): string
    {
        return hash('sha256', Str::random(128) . microtime(true));
    }

    /**
     * Validate session token format.
     */
    public function isValidSessionToken(string $token): bool
    {
        return preg_match('/^[a-f0-9]{64}$/', $token);
    }

    /**
     * Rate limit check for security operations.
     */
    public function checkRateLimit(string $key, int $maxAttempts = 5, int $decayMinutes = 15): bool
    {
        $cacheKey = "security_rate_limit:{$key}";
        $attempts = cache()->get($cacheKey, 0);

        if ($attempts >= $maxAttempts) {
            return false;
        }

        cache()->put($cacheKey, $attempts + 1, now()->addMinutes($decayMinutes));
        return true;
    }

    /**
     * Log security event.
     */
    public function logSecurityEvent(string $event, array $context = []): void
    {
        Log::channel('security')->info($event, array_merge($context, [
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ]));
    }
}