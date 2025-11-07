<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SSNFormat implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if SSN matches the format XXX-XX-XXXX
        if (!preg_match('/^\d{3}-\d{2}-\d{4}$/', $value)) {
            $fail('The :attribute must be in the format XXX-XX-XXXX.');
            return;
        }

        // Additional SSN validation rules
        $parts = explode('-', $value);
        $area = $parts[0];
        $group = $parts[1];
        $serial = $parts[2];

        // Check for invalid area numbers
        $invalidAreas = ['000', '666'];
        if (in_array($area, $invalidAreas) || (intval($area) >= 900 && intval($area) <= 999)) {
            $fail('The :attribute contains an invalid area number.');
            return;
        }

        // Check for invalid group numbers
        if ($group === '00') {
            $fail('The :attribute contains an invalid group number.');
            return;
        }

        // Check for invalid serial numbers
        if ($serial === '0000') {
            $fail('The :attribute contains an invalid serial number.');
            return;
        }

        // Check for known invalid patterns
        $invalidPatterns = [
            '123-45-6789', // Common test SSN
            '111-11-1111',
            '222-22-2222',
            '333-33-3333',
            '444-44-4444',
            '555-55-5555',
            '777-77-7777',
            '888-88-8888',
            '999-99-9999',
        ];

        if (in_array($value, $invalidPatterns)) {
            $fail('The :attribute appears to be a test or invalid SSN.');
        }
    }
}