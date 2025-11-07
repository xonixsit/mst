<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DateFormat;
use App\Services\ValidationErrorFormatter;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'category' => 'required|in:miscellaneous,residency,business,medical,education,other',
            'particulars' => 'required|string|max:255',
            'tax_payer' => 'required|string|max:255',
            'spouse' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0|max:9999999999.99',
            'expense_date' => ['required', new DateFormat('Y-m-d', false, true)],
            'remarks' => 'nullable|string|max:1000',
            'receipt_number' => 'nullable|string|max:255',
            'is_deductible' => 'boolean',
            'deductible_percentage' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'Client selection is required.',
            'client_id.exists' => 'The selected client does not exist.',
            'category.required' => 'Expense category is required.',
            'category.in' => 'Please select a valid expense category.',
            'particulars.required' => 'Expense particulars are required.',
            'particulars.max' => 'Expense particulars cannot exceed 255 characters.',
            'tax_payer.required' => 'Tax payer name is required.',
            'tax_payer.max' => 'Tax payer name cannot exceed 255 characters.',
            'spouse.max' => 'Spouse name cannot exceed 255 characters.',
            'amount.required' => 'Expense amount is required.',
            'amount.min' => 'Expense amount cannot be negative.',
            'amount.max' => 'Expense amount is too large.',
            'expense_date.required' => 'Expense date is required.',
            'remarks.max' => 'Remarks cannot exceed 1000 characters.',
            'receipt_number.max' => 'Receipt number cannot exceed 255 characters.',
            'deductible_percentage.required' => 'Deductible percentage is required.',
            'deductible_percentage.min' => 'Deductible percentage cannot be negative.',
            'deductible_percentage.max' => 'Deductible percentage cannot exceed 100%.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'client_id' => 'client',
            'particulars' => 'expense particulars',
            'tax_payer' => 'tax payer',
            'expense_date' => 'expense date',
            'receipt_number' => 'receipt number',
            'is_deductible' => 'deductible status',
            'deductible_percentage' => 'deductible percentage',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate expense date is not too far in the future
            if ($this->filled('expense_date')) {
                $expenseDate = \Carbon\Carbon::parse($this->input('expense_date'));
                if ($expenseDate->isFuture() && $expenseDate->diffInDays(now()) > 30) {
                    $validator->errors()->add(
                        'expense_date',
                        'Expense date cannot be more than 30 days in the future.'
                    );
                }

                // Validate expense date is not too old for tax purposes
                if ($expenseDate->isBefore(\Carbon\Carbon::now()->subYears(10))) {
                    $validator->errors()->add(
                        'expense_date',
                        'Expense date cannot be more than 10 years ago for tax purposes.'
                    );
                }
            }

            // Validate deductible percentage logic
            if ($this->filled('is_deductible') && $this->filled('deductible_percentage')) {
                $isDeductible = $this->boolean('is_deductible');
                $deductiblePercentage = $this->input('deductible_percentage');

                if (!$isDeductible && $deductiblePercentage > 0) {
                    $validator->errors()->add(
                        'deductible_percentage',
                        'Deductible percentage should be 0 if the expense is not deductible.'
                    );
                }

                if ($isDeductible && $deductiblePercentage == 0) {
                    $validator->errors()->add(
                        'deductible_percentage',
                        'Deductible percentage should be greater than 0 if the expense is deductible.'
                    );
                }
            }

            // Validate amount based on category
            if ($this->filled('amount') && $this->filled('category')) {
                $amount = $this->input('amount');
                $category = $this->input('category');

                // Set reasonable limits based on category
                $categoryLimits = [
                    'miscellaneous' => 50000,
                    'residency' => 100000,
                    'business' => 500000,
                    'medical' => 100000,
                    'education' => 100000,
                    'other' => 50000,
                ];

                if (isset($categoryLimits[$category]) && $amount > $categoryLimits[$category]) {
                    $validator->errors()->add(
                        'amount',
                        "Amount seems unusually high for {$category} expenses. Please verify."
                    );
                }
            }

            // Validate receipt number format for certain categories
            if ($this->filled('receipt_number') && $this->filled('category')) {
                $receiptNumber = $this->input('receipt_number');
                $category = $this->input('category');

                // Business expenses should have proper receipt numbers
                if ($category === 'business' && !preg_match('/^[A-Z0-9\-]+$/i', $receiptNumber)) {
                    $validator->errors()->add(
                        'receipt_number',
                        'Business expense receipt numbers should contain only letters, numbers, and hyphens.'
                    );
                }
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ValidationErrorFormatter::jsonResponse($validator, $this)
        );
    }
}