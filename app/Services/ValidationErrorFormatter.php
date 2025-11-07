<?php

namespace App\Services;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ValidationErrorFormatter
{
    /**
     * Format validation errors for consistent API responses
     */
    public static function formatErrors(Validator $validator, Request $request = null): array
    {
        $errors = $validator->errors()->toArray();
        $formattedErrors = [];

        foreach ($errors as $field => $messages) {
            $formattedErrors[$field] = [
                'field' => $field,
                'messages' => $messages,
                'first_message' => $messages[0] ?? '',
                'human_field' => self::humanizeFieldName($field),
                'input_value' => $request ? $request->input($field) : null,
            ];
        }

        return [
            'message' => 'The given data was invalid.',
            'errors' => $formattedErrors,
            'error_count' => count($formattedErrors),
            'failed_rules' => self::getFailedRules($validator),
            'summary' => self::generateErrorSummary($formattedErrors),
        ];
    }

    /**
     * Create a JSON response for validation errors
     */
    public static function jsonResponse(Validator $validator, Request $request = null, int $status = 422): JsonResponse
    {
        return response()->json(
            self::formatErrors($validator, $request),
            $status
        );
    }

    /**
     * Convert field names to human-readable format
     */
    private static function humanizeFieldName(string $field): string
    {
        // Handle array notation (e.g., assets.0.asset_name)
        $field = preg_replace('/\.\d+\./', '.*.', $field);
        
        $humanNames = [
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'date_of_birth' => 'Date of Birth',
            'marital_status' => 'Marital Status',
            'insurance_covered' => 'Insurance Coverage',
            'street_no' => 'Street Number',
            'apartment_no' => 'Apartment Number',
            'zip_code' => 'ZIP Code',
            'mobile_number' => 'Mobile Number',
            'work_number' => 'Work Number',
            'visa_status' => 'Visa Status',
            'date_of_entry_us' => 'Date of Entry to US',
            'spouse.first_name' => 'Spouse First Name',
            'spouse.middle_name' => 'Spouse Middle Name',
            'spouse.last_name' => 'Spouse Last Name',
            'spouse.date_of_birth' => 'Spouse Date of Birth',
            'employees.*.employer_name' => 'Employer Name',
            'employees.*.job_title' => 'Job Title',
            'employees.*.start_date' => 'Employment Start Date',
            'employees.*.end_date' => 'Employment End Date',
            'employees.*.annual_salary' => 'Annual Salary',
            'employees.*.employment_type' => 'Employment Type',
            'projects.*.project_name' => 'Project Name',
            'projects.*.project_type' => 'Project Type',
            'projects.*.start_date' => 'Project Start Date',
            'projects.*.due_date' => 'Project Due Date',
            'projects.*.completion_date' => 'Project Completion Date',
            'assets.*.asset_name' => 'Asset Name',
            'assets.*.date_of_purchase' => 'Purchase Date',
            'assets.*.percentage_used_in_business' => 'Business Usage Percentage',
            'assets.*.cost_of_acquisition' => 'Acquisition Cost',
            'assets.*.any_reimbursement' => 'Reimbursement Amount',
            'expenses.*.category' => 'Expense Category',
            'expenses.*.particulars' => 'Expense Particulars',
            'expenses.*.tax_payer' => 'Tax Payer',
            'expenses.*.amount' => 'Expense Amount',
            'expenses.*.expense_date' => 'Expense Date',
            'expenses.*.deductible_percentage' => 'Deductible Percentage',
        ];

        if (isset($humanNames[$field])) {
            return $humanNames[$field];
        }

        // Fallback: convert snake_case to Title Case
        return ucwords(str_replace(['_', '.'], [' ', ' '], $field));
    }

    /**
     * Get failed validation rules for debugging
     */
    private static function getFailedRules(Validator $validator): array
    {
        $failed = [];
        
        foreach ($validator->failed() as $field => $rules) {
            $failed[$field] = array_keys($rules);
        }

        return $failed;
    }

    /**
     * Generate a summary of validation errors
     */
    private static function generateErrorSummary(array $formattedErrors): array
    {
        $summary = [
            'total_errors' => count($formattedErrors),
            'fields_with_errors' => array_keys($formattedErrors),
            'error_types' => [],
            'sections_with_errors' => [],
        ];

        foreach ($formattedErrors as $field => $error) {
            // Categorize error types
            $firstMessage = strtolower($error['first_message']);
            
            if (str_contains($firstMessage, 'required')) {
                $summary['error_types']['required'][] = $field;
            } elseif (str_contains($firstMessage, 'format') || str_contains($firstMessage, 'invalid')) {
                $summary['error_types']['format'][] = $field;
            } elseif (str_contains($firstMessage, 'unique')) {
                $summary['error_types']['unique'][] = $field;
            } elseif (str_contains($firstMessage, 'date')) {
                $summary['error_types']['date'][] = $field;
            } else {
                $summary['error_types']['other'][] = $field;
            }

            // Categorize by sections
            if (str_starts_with($field, 'spouse.')) {
                $summary['sections_with_errors']['spouse'] = true;
            } elseif (str_starts_with($field, 'employees.')) {
                $summary['sections_with_errors']['employees'] = true;
            } elseif (str_starts_with($field, 'projects.')) {
                $summary['sections_with_errors']['projects'] = true;
            } elseif (str_starts_with($field, 'assets.')) {
                $summary['sections_with_errors']['assets'] = true;
            } elseif (str_starts_with($field, 'expenses.')) {
                $summary['sections_with_errors']['expenses'] = true;
            } else {
                $summary['sections_with_errors']['personal'] = true;
            }
        }

        // Convert boolean flags to arrays
        $summary['sections_with_errors'] = array_keys(array_filter($summary['sections_with_errors']));

        return $summary;
    }

    /**
     * Format errors for frontend consumption
     */
    public static function formatForFrontend(Validator $validator, Request $request = null): array
    {
        $formatted = self::formatErrors($validator, $request);
        
        // Simplify for frontend use
        $frontendErrors = [];
        foreach ($formatted['errors'] as $field => $error) {
            $frontendErrors[$field] = $error['first_message'];
        }

        return [
            'message' => $formatted['message'],
            'errors' => $frontendErrors,
            'summary' => $formatted['summary'],
        ];
    }
}