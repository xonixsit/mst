<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailFormat implements ValidationRule
{
    private bool $allowDisposable;
    private array $blockedDomains;

    public function __construct(bool $allowDisposable = false, array $blockedDomains = [])
    {
        $this->allowDisposable = $allowDisposable;
        $this->blockedDomains = array_merge($blockedDomains, [
            'tempmail.org',
            '10minutemail.com',
            'guerrillamail.com',
            'mailinator.com',
            'throwaway.email',
        ]);
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Let required rule handle empty values
        }

        // Basic email format validation
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('The :attribute must be a valid email address.');
            return;
        }

        // Extract domain
        $domain = strtolower(substr(strrchr($value, '@'), 1));

        // Check blocked domains
        if (in_array($domain, $this->blockedDomains)) {
            $fail('The :attribute domain is not allowed.');
            return;
        }

        // Check for disposable email domains if not allowed
        if (!$this->allowDisposable && $this->isDisposableEmail($domain)) {
            $fail('The :attribute cannot be from a disposable email service.');
            return;
        }

        // Check for common typos in popular domains
        $this->checkCommonTypos($value, $fail);

        // Additional format validations
        $this->validateEmailStructure($value, $fail);
    }

    /**
     * Check if the domain is a known disposable email service
     */
    private function isDisposableEmail(string $domain): bool
    {
        $disposableDomains = [
            'tempmail.org', '10minutemail.com', 'guerrillamail.com', 'mailinator.com',
            'throwaway.email', 'temp-mail.org', 'fakeinbox.com', 'yopmail.com',
            'maildrop.cc', 'sharklasers.com', 'grr.la', 'guerrillamailblock.com'
        ];

        return in_array($domain, $disposableDomains);
    }

    /**
     * Check for common typos in popular email domains
     */
    private function checkCommonTypos(string $email, Closure $fail): void
    {
        $commonTypos = [
            'gmial.com' => 'gmail.com',
            'gmai.com' => 'gmail.com',
            'yahooo.com' => 'yahoo.com',
            'yaho.com' => 'yahoo.com',
            'hotmial.com' => 'hotmail.com',
            'hotmai.com' => 'hotmail.com',
            'outlok.com' => 'outlook.com',
            'outloo.com' => 'outlook.com',
        ];

        $domain = strtolower(substr(strrchr($email, '@'), 1));
        
        if (array_key_exists($domain, $commonTypos)) {
            $suggestion = $commonTypos[$domain];
            $fail("The :attribute domain appears to have a typo. Did you mean {$suggestion}?");
        }
    }

    /**
     * Validate email structure for additional requirements
     */
    private function validateEmailStructure(string $email, Closure $fail): void
    {
        // Check for consecutive dots
        if (strpos($email, '..') !== false) {
            $fail('The :attribute cannot contain consecutive dots.');
            return;
        }

        // Check for valid characters
        $localPart = substr($email, 0, strpos($email, '@'));
        
        // Local part cannot start or end with a dot
        if (str_starts_with($localPart, '.') || str_ends_with($localPart, '.')) {
            $fail('The :attribute local part cannot start or end with a dot.');
            return;
        }

        // Check length constraints
        if (strlen($localPart) > 64) {
            $fail('The :attribute local part cannot exceed 64 characters.');
            return;
        }

        if (strlen($email) > 254) {
            $fail('The :attribute cannot exceed 254 characters.');
            return;
        }
    }
}