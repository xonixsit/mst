<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\SSNFormat;
use App\Rules\PhoneNumberFormat;
use App\Rules\DateFormat;
use App\Rules\EmailFormat;
use App\Services\ValidationErrorFormatter;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateClientInformationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware/policies
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $clientId = $this->route('client') ?? $this->route('id');

        return [
            // Personal Details
            'first_name' => 'sometimes|required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'max:255',
                Rule::unique('users', 'email')->ignore(function () {
                    $clientId = request()->route('client');
                    $client = \App\Models\Client::find($clientId);
                    return $client ? $client->user_id : null;
                }),
                new EmailFormat()
            ],
            'phone' => ['sometimes', 'required', 'string', new PhoneNumberFormat()],
            'ssn' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('clients', 'ssn')->ignore($clientId),
                new SSNFormat()
            ],
            'date_of_birth' => ['sometimes', 'required', new DateFormat('Y-m-d', false, true)],
            'marital_status' => 'sometimes|required|in:single,married,divorced,widowed',
            'occupation' => 'nullable|string|max:255',
            'insurance_covered' => 'boolean',

            // Address Information
            'street_no' => 'sometimes|required|string|max:255',
            'apartment_no' => 'nullable|string|max:255',
            'zip_code' => 'sometimes|required|string|max:10',
            'city' => 'sometimes|required|string|max:255',
            'state' => 'sometimes|required|string|max:255',
            'country' => 'sometimes|required|string|max:255',

            // Contact Information
            'mobile_number' => ['sometimes', 'required', 'string', new PhoneNumberFormat()],
            'work_number' => ['nullable', 'string', new PhoneNumberFormat()],

            // Immigration Information
            'visa_status' => 'nullable|string|max:255',
            'date_of_entry_us' => ['nullable', new DateFormat('Y-m-d', false, true)],

            // System Information
            'user_id' => 'nullable|exists:users,id',
            'status' => 'sometimes|required|in:active,inactive,archived',

            // Spouse Information (conditional)
            'spouse' => 'nullable|array',
            'spouse.id' => 'nullable|exists:client_spouses,id',
            'spouse.first_name' => 'required_with:spouse|string|max:255',
            'spouse.middle_name' => 'nullable|string|max:255',
            'spouse.last_name' => 'required_with:spouse|string|max:255',
            'spouse.ssn' => [
                'nullable',
                'string',
                Rule::unique('client_spouses', 'ssn')->ignore($this->input('spouse.id')),
                new SSNFormat()
            ],
            'spouse.date_of_birth' => ['nullable', new DateFormat('Y-m-d', false, true)],
            'spouse.occupation' => 'nullable|string|max:255',
            'spouse.email' => ['nullable', 'max:255', new EmailFormat()],
            'spouse.phone' => ['nullable', 'string', new PhoneNumberFormat()],

            // Employee Information
            'employees' => 'nullable|array',
            'employees.*.id' => 'nullable|exists:client_employees,id',
            'employees.*.employer_name' => 'required|string|max:255',
            'employees.*.job_title' => 'nullable|string|max:255',
            'employees.*.employee_id' => 'nullable|string|max:255',
            'employees.*.start_date' => ['nullable', new DateFormat('Y-m-d', false, true)],
            'employees.*.end_date' => ['nullable', new DateFormat('Y-m-d', false, true), 'after_or_equal:employees.*.start_date'],
            'employees.*.annual_salary' => 'nullable|numeric|min:0|max:9999999999.99',
            'employees.*.employment_type' => 'nullable|string|in:full-time,part-time,contract,temporary,intern',
            'employees.*.work_description' => 'nullable|string|max:1000',
            'employees.*.employer_address' => 'nullable|string|max:255',
            'employees.*.employer_city' => 'nullable|string|max:255',
            'employees.*.employer_state' => 'nullable|string|max:255',
            'employees.*.employer_zip_code' => 'nullable|string|max:10',

            // Project Information
            'projects' => 'nullable|array',
            'projects.*.id' => 'nullable|exists:client_projects,id',
            'projects.*.project_name' => 'required|string|max:255',
            'projects.*.project_description' => 'nullable|string|max:1000',
            'projects.*.project_type' => 'required|in:tax_preparation,consultation,planning,audit,other',
            'projects.*.status' => 'required|in:pending,in_progress,completed,cancelled',
            'projects.*.start_date' => ['nullable', new DateFormat('Y-m-d', false, true)],
            'projects.*.due_date' => ['nullable', new DateFormat('Y-m-d', false, true), 'after_or_equal:projects.*.start_date'],
            'projects.*.completion_date' => ['nullable', new DateFormat('Y-m-d', false, true)],
            'projects.*.estimated_hours' => 'nullable|numeric|min:0|max:99999.99',
            'projects.*.actual_hours' => 'nullable|numeric|min:0|max:99999.99',
            'projects.*.notes' => 'nullable|string|max:2000',

            // Asset Information
            'assets' => 'nullable|array',
            'assets.*.id' => 'nullable|exists:client_assets,id',
            'assets.*.asset_name' => 'required|string|max:255',
            'assets.*.date_of_purchase' => ['required', new DateFormat('Y-m-d', false, true)],
            'assets.*.percentage_used_in_business' => 'required|numeric|min:0|max:100',
            'assets.*.cost_of_acquisition' => 'required|numeric|min:0|max:9999999999.99',
            'assets.*.any_reimbursement' => 'nullable|numeric|min:0|max:9999999999.99',
            'assets.*.asset_type' => 'nullable|string|max:255',
            'assets.*.description' => 'nullable|string|max:1000',
            'assets.*.current_value' => 'nullable|numeric|min:0|max:9999999999.99',
            'assets.*.depreciation_rate' => 'nullable|numeric|min:0|max:100',

            // Expense Information
            'expenses' => 'nullable|array',
            'expenses.*.id' => 'nullable|exists:client_expenses,id',
            'expenses.*.category' => 'required|string|max:50',
            'expenses.*.particulars' => 'required|string|max:255',
            'expenses.*.tax_payer' => 'required|string|max:255',
            'expenses.*.spouse' => 'nullable|string|max:255',
            'expenses.*.amount' => 'required|numeric|min:0|max:9999999999.99',
            'expenses.*.expense_date' => ['required', new DateFormat('Y-m-d', false, true)],
            'expenses.*.remarks' => 'nullable|string|max:1000',
            'expenses.*.receipt_number' => 'nullable|string|max:255',
            'expenses.*.is_deductible' => 'boolean',
            'expenses.*.deductible_percentage' => 'required|numeric|min:0|max:100',
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
            'ssn.unique' => 'This SSN is already registered in the system.',
            'spouse.ssn.unique' => 'This spouse SSN is already registered in the system.',
            'email.unique' => 'This email address is already registered.',
            'employees.*.end_date.after_or_equal' => 'The employment end date must be after or equal to the start date.',
            'projects.*.due_date.after_or_equal' => 'The project due date must be after or equal to the start date.',
            'assets.*.percentage_used_in_business.between' => 'The business usage percentage must be between 0 and 100.',
            'expenses.*.deductible_percentage.between' => 'The deductible percentage must be between 0 and 100.',
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'phone.required' => 'Phone number is required.',
            'ssn.required' => 'Social Security Number is required.',
            'date_of_birth.required' => 'Date of birth is required.',
            'marital_status.required' => 'Marital status is required.',
            'street_no.required' => 'Street number is required.',
            'zip_code.required' => 'ZIP code is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'country.required' => 'Country is required.',
            'mobile_number.required' => 'Mobile number is required.',
            'status.required' => 'Client status is required.',
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
            'first_name' => 'first name',
            'middle_name' => 'middle name',
            'last_name' => 'last name',
            'date_of_birth' => 'date of birth',
            'marital_status' => 'marital status',
            'insurance_covered' => 'insurance coverage',
            'street_no' => 'street number',
            'apartment_no' => 'apartment number',
            'zip_code' => 'ZIP code',
            'mobile_number' => 'mobile number',
            'work_number' => 'work number',
            'visa_status' => 'visa status',
            'date_of_entry_us' => 'date of entry to US',
            'spouse.first_name' => 'spouse first name',
            'spouse.last_name' => 'spouse last name',
            'spouse.date_of_birth' => 'spouse date of birth',
            'employees.*.employer_name' => 'employer name',
            'employees.*.job_title' => 'job title',
            'employees.*.start_date' => 'employment start date',
            'employees.*.end_date' => 'employment end date',
            'employees.*.annual_salary' => 'annual salary',
            'projects.*.project_name' => 'project name',
            'projects.*.project_type' => 'project type',
            'projects.*.start_date' => 'project start date',
            'projects.*.due_date' => 'project due date',
            'assets.*.asset_name' => 'asset name',
            'assets.*.date_of_purchase' => 'purchase date',
            'assets.*.cost_of_acquisition' => 'acquisition cost',
            'expenses.*.particulars' => 'expense particulars',
            'expenses.*.tax_payer' => 'tax payer',
            'expenses.*.expense_date' => 'expense date',
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
            // Custom validation: spouse information should only be provided for married clients
            if ($this->has('marital_status') && $this->input('marital_status') !== 'married' && $this->has('spouse')) {
                $validator->errors()->add('spouse', 'Spouse information can only be provided for married clients.');
            }

            // Validate asset reimbursement doesn't exceed cost
            if ($this->has('assets')) {
                foreach ($this->input('assets', []) as $index => $asset) {
                    if (isset($asset['any_reimbursement']) && isset($asset['cost_of_acquisition'])) {
                        if ($asset['any_reimbursement'] > $asset['cost_of_acquisition']) {
                            $validator->errors()->add(
                                "assets.{$index}.any_reimbursement",
                                'Reimbursement amount cannot exceed the cost of acquisition.'
                            );
                        }
                    }
                }
            }

            // Validate project completion date logic
            if ($this->has('projects')) {
                foreach ($this->input('projects', []) as $index => $project) {
                    if ($project['status'] === 'completed' && empty($project['completion_date'])) {
                        $validator->errors()->add(
                            "projects.{$index}.completion_date",
                            'Completion date is required for completed projects.'
                        );
                    }
                }
            }

            // Validate that existing records belong to the client being updated
            $clientId = $this->route('client') ?? $this->route('id');
            
            if ($this->has('spouse.id') && $this->input('spouse.id')) {
                $spouse = \App\Models\ClientSpouse::find($this->input('spouse.id'));
                if ($spouse && $spouse->client_id != $clientId) {
                    $validator->errors()->add('spouse.id', 'The spouse record does not belong to this client.');
                }
            }

            // Validate employee records belong to client
            if ($this->has('employees')) {
                foreach ($this->input('employees', []) as $index => $employee) {
                    if (!empty($employee['id'])) {
                        $employeeRecord = \App\Models\ClientEmployee::find($employee['id']);
                        if ($employeeRecord && $employeeRecord->client_id != $clientId) {
                            $validator->errors()->add(
                                "employees.{$index}.id",
                                'The employee record does not belong to this client.'
                            );
                        }
                    }
                }
            }

            // Validate project records belong to client
            if ($this->has('projects')) {
                foreach ($this->input('projects', []) as $index => $project) {
                    if (!empty($project['id'])) {
                        $projectRecord = \App\Models\ClientProject::find($project['id']);
                        if ($projectRecord && $projectRecord->client_id != $clientId) {
                            $validator->errors()->add(
                                "projects.{$index}.id",
                                'The project record does not belong to this client.'
                            );
                        }
                    }
                }
            }

            // Validate asset records belong to client
            if ($this->has('assets')) {
                foreach ($this->input('assets', []) as $index => $asset) {
                    if (!empty($asset['id'])) {
                        $assetRecord = \App\Models\ClientAsset::find($asset['id']);
                        if ($assetRecord && $assetRecord->client_id != $clientId) {
                            $validator->errors()->add(
                                "assets.{$index}.id",
                                'The asset record does not belong to this client.'
                            );
                        }
                    }
                }
            }

            // Validate expense records belong to client
            if ($this->has('expenses')) {
                foreach ($this->input('expenses', []) as $index => $expense) {
                    if (!empty($expense['id'])) {
                        $expenseRecord = \App\Models\ClientExpense::find($expense['id']);
                        if ($expenseRecord && $expenseRecord->client_id != $clientId) {
                            $validator->errors()->add(
                                "expenses.{$index}.id",
                                'The expense record does not belong to this client.'
                            );
                        }
                    }
                }
            }

            // Validate expense date is not too far in the future
            if ($this->has('expenses')) {
                foreach ($this->input('expenses', []) as $index => $expense) {
                    if (isset($expense['expense_date'])) {
                        $expenseDate = \Carbon\Carbon::parse($expense['expense_date']);
                        if ($expenseDate->isFuture() && $expenseDate->diffInDays(now()) > 30) {
                            $validator->errors()->add(
                                "expenses.{$index}.expense_date",
                                'Expense date cannot be more than 30 days in the future.'
                            );
                        }
                    }
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