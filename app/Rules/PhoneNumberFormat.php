<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumberFormat implements ValidationRule
{
    private bool $allowInternational;

    public function __construct(bool $allowInternational = true)
    {
        $this->allowInternational = $allowInternational;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove all non-digit characters except + for international numbers
        $cleanNumber = preg_replace('/[^\d+]/', '', $value);

        // Check if it's an international number
        $isInternational = str_starts_with($cleanNumber, '+');

        if ($isInternational && !$this->allowInternational) {
            $fail('The :attribute must be a domestic phone number.');
            return;
        }

        if ($isInternational) {
            // International number validation
            $numberWithoutPlus = substr($cleanNumber, 1);
            
            // International numbers should be 7-15 digits after country code
            if (strlen($numberWithoutPlus) < 7 || strlen($numberWithoutPlus) > 15) {
                $fail('The :attribute must be between 7 and 15 digits for international numbers.');
                return;
            }

            // Should not start with 0 after country code
            if (str_starts_with($numberWithoutPlus, '0')) {
                $fail('The :attribute has an invalid international format.');
                return;
            }
        } else {
            // Domestic US number validation
            $digitsOnly = preg_replace('/\D/', '', $value);
            
            // US numbers should be 10 digits (with area code) or 11 digits (with country code 1)
            if (strlen($digitsOnly) === 11 && str_starts_with($digitsOnly, '1')) {
                $digitsOnly = substr($digitsOnly, 1); // Remove leading 1
            }
            
            if (strlen($digitsOnly) !== 10) {
                $fail('The :attribute must be a valid 10-digit US phone number.');
                return;
            }

            // Extract area code and exchange
            $areaCode = substr($digitsOnly, 0, 3);
            $exchange = substr($digitsOnly, 3, 3);

            // Area code validation
            if ($areaCode[0] === '0' || $areaCode[0] === '1') {
                $fail('The :attribute has an invalid area code.');
                return;
            }

            // Exchange validation
            if ($exchange[0] === '0' || $exchange[0] === '1') {
                $fail('The :attribute has an invalid exchange code.');
                return;
            }

            // Check for invalid patterns
            $invalidPatterns = [
                '0000000000',
                '1111111111',
                '2222222222',
                '3333333333',
                '4444444444',
                '5555555555',
                '6666666666',
                '7777777777',
                '8888888888',
                '9999999999',
                '1234567890',
                '0123456789',
            ];

            if (in_array($digitsOnly, $invalidPatterns)) {
                $fail('The :attribute appears to be a test or invalid phone number.');
            }
        }
    }
}