<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class DateFormat implements ValidationRule
{
    private string $format;
    private bool $allowFuture;
    private bool $allowPast;

    public function __construct(string $format = 'Y-m-d', bool $allowFuture = true, bool $allowPast = true)
    {
        $this->format = $format;
        $this->allowFuture = $allowFuture;
        $this->allowPast = $allowPast;
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

        try {
            $date = Carbon::createFromFormat($this->format, $value);
            
            // Check if the date was parsed correctly
            if ($date->format($this->format) !== $value) {
                $fail("The :attribute must be a valid date in format {$this->format}.");
                return;
            }

            $now = Carbon::now();

            // Check future date restriction
            if (!$this->allowFuture && $date->isAfter($now)) {
                $fail('The :attribute cannot be a future date.');
                return;
            }

            // Check past date restriction
            if (!$this->allowPast && $date->isBefore($now)) {
                $fail('The :attribute cannot be a past date.');
                return;
            }

            // Additional business logic validations
            $this->validateBusinessRules($attribute, $date, $fail);

        } catch (\Exception $e) {
            $fail("The :attribute must be a valid date in format {$this->format}.");
        }
    }

    /**
     * Additional business rule validations for dates
     */
    private function validateBusinessRules(string $attribute, Carbon $date, Closure $fail): void
    {
        // Date of birth should not be more than 150 years ago
        if (str_contains($attribute, 'birth') && $date->isBefore(Carbon::now()->subYears(150))) {
            $fail('The :attribute cannot be more than 150 years ago.');
            return;
        }

        // Date of birth should not be in the future
        if (str_contains($attribute, 'birth') && $date->isFuture()) {
            $fail('The :attribute cannot be in the future.');
            return;
        }

        // Entry date to US should not be before 1900
        if (str_contains($attribute, 'entry') && $date->isBefore(Carbon::create(1900, 1, 1))) {
            $fail('The :attribute cannot be before 1900.');
            return;
        }

        // Purchase dates should not be in the future
        if (str_contains($attribute, 'purchase') && $date->isFuture()) {
            $fail('The :attribute cannot be in the future.');
            return;
        }

        // Expense dates should not be more than 10 years ago for tax purposes
        if (str_contains($attribute, 'expense') && $date->isBefore(Carbon::now()->subYears(10))) {
            $fail('The :attribute cannot be more than 10 years ago for tax purposes.');
            return;
        }
    }
}