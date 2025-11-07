<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientSpouse;
use App\Rules\PhoneNumberFormat;
use App\Rules\SSNFormat;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ClientValidationService
{
    /**
     * Validate client data with business rules.
     *
     * @param array $data
     * @param int|null $clientId
     * @return array
     * @throws ValidationException
     */
    public function validateClientData(array $data, ?int $clientId = null): array
    {
        $rules = $this->getClientValidationRules($clientId);
        
        $validator = Validator::make($data, $rules, $this->getCustomMessages());
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        // Additional business logic validation
        $this->validateBusinessRules($data, $clientId);
        
        return $validator->validated();
    }

    /**
     * Validate spouse data.
     *
     * @param array $data
     * @param int|null $spouseId
     * @return array
     * @throws ValidationException
     */
    public function validateSpouseData(array $data, ?int $spouseId = null): array
    {
        $rules = $this->getSpouseValidationRules($spouseId);
        
        $validator = Validator::make($data, $rules, $this->getCustomMessages());
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Validate employee data.
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function validateEmployeeData(array $data): array
    {
        $rules = [
            'employer_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'salary' => 'required|numeric|min:0|max:999999999.99',
            'employment_type' => 'required|in:full_time,part_time,contract,temporary',
            'work_address' => 'nullable|string|max:500',
            'supervisor_name' => 'nullable|string|max:255',
            'supervisor_phone' => ['nullable', new PhoneNumberFormat()],
            'benefits' => 'nullable|string|max:1000',
        ];

        $validator = Validator::make($data, $rules, $this->getCustomMessages());
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Validate project data.
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function validateProjectData(array $data): array
    {
        $rules = [
            'project_name' => 'required|string|max:255',
            'project_type' => 'required|in:tax_preparation,consultation,audit,planning,other',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'description' => 'nullable|string|max:1000',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999.99',
            'hourly_rate' => 'nullable|numeric|min:0|max:999.99',
            'total_amount' => 'nullable|numeric|min:0|max:999999.99',
            'notes' => 'nullable|string|max:2000',
        ];

        $validator = Validator::make($data, $rules, $this->getCustomMessages());
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Validate asset data.
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function validateAssetData(array $data): array
    {
        $rules = [
            'asset_name' => 'required|string|max:255',
            'date_of_purchase' => 'required|date|before_or_equal:today',
            'percentage_used_in_business' => 'required|numeric|min:0|max:100',
            'cost_of_acquisition' => 'required|numeric|min:0|max:999999999.99',
            'any_reimbursement' => 'nullable|numeric|min:0|max:999999999.99',
            'asset_type' => 'required|in:equipment,vehicle,property,software,other',
            'depreciation_method' => 'nullable|in:straight_line,declining_balance,sum_of_years,units_of_production',
            'useful_life_years' => 'nullable|integer|min:1|max:50',
            'salvage_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];

        $validator = Validator::make($data, $rules, $this->getCustomMessages());
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Validate expense data.
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function validateExpenseData(array $data): array
    {
        $rules = [
            'expense_name' => 'required|string|max:255',
            'expense_category' => 'required|in:miscellaneous,residency,business,medical,charitable,other',
            'amount' => 'required|numeric|min:0|max:999999.99',
            'expense_date' => 'required|date|before_or_equal:today',
            'description' => 'nullable|string|max:1000',
            'receipt_available' => 'required|boolean',
            'tax_deductible' => 'required|boolean',
            'business_percentage' => 'nullable|numeric|min:0|max:100',
            'vendor_name' => 'nullable|string|max:255',
            'payment_method' => 'nullable|in:cash,check,credit_card,debit_card,bank_transfer,other',
            'notes' => 'nullable|string|max:1000',
        ];

        $validator = Validator::make($data, $rules, $this->getCustomMessages());
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Validate bulk client operation data.
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function validateBulkOperation(array $data): array
    {
        $rules = [
            'client_ids' => 'required|array|min:1',
            'client_ids.*' => 'required|integer|exists:clients,id',
            'operation' => 'required|in:update_status,send_email,export_data,archive,delete',
            'parameters' => 'nullable|array',
        ];

        // Additional rules based on operation type
        if (isset($data['operation'])) {
            switch ($data['operation']) {
                case 'update_status':
                    $rules['parameters.status'] = 'required|in:active,inactive,archived';
                    break;
                case 'send_email':
                    $rules['parameters.subject'] = 'required|string|max:255';
                    $rules['parameters.message'] = 'required|string|max:5000';
                    break;
                case 'export_data':
                    $rules['parameters.format'] = 'required|in:pdf,excel,csv';
                    break;
            }
        }

        $validator = Validator::make($data, $rules, $this->getCustomMessages());
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Get client validation rules.
     *
     * @param int|null $clientId
     * @return array
     */
    private function getClientValidationRules(?int $clientId = null): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'insurance_coverage' => 'required|boolean',
            'date_of_birth' => 'required|date|before:today|after:1900-01-01',
            'ssn' => [
                'required',
                new SSNFormat(),
                Rule::unique('clients', 'ssn')->ignore($clientId)
            ],
            'marital_status' => 'required|in:single,married,divorced,widowed,separated',
            'occupation' => 'required|string|max:255',
            'street_no' => 'required|string|max:255',
            'apartment_no' => 'nullable|string|max:50',
            'zip_code' => 'required|string|regex:/^\d{5}(-\d{4})?$/',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
            'country' => 'required|string|max:255',
            'mobile_number' => ['required', new PhoneNumberFormat()],
            'work_number' => ['nullable', new PhoneNumberFormat()],
            'email_id' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('clients', 'email_id')->ignore($clientId)
            ],
            'visa_status' => 'required|in:citizen,permanent_resident,h1b,f1,j1,l1,other',
            'date_of_entry_in_us' => 'nullable|date|before_or_equal:today',
            'status' => 'nullable|in:active,inactive,archived',
            'assigned_tax_professional_id' => 'nullable|integer|exists:users,id',
        ];
    }

    /**
     * Get spouse validation rules.
     *
     * @param int|null $spouseId
     * @return array
     */
    private function getSpouseValidationRules(?int $spouseId = null): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today|after:1900-01-01',
            'ssn' => [
                'required',
                new SSNFormat(),
                Rule::unique('client_spouses', 'ssn')->ignore($spouseId)
            ],
            'occupation' => 'required|string|max:255',
            'mobile_number' => ['required', new PhoneNumberFormat()],
            'work_number' => ['nullable', new PhoneNumberFormat()],
            'email_id' => [
                'nullable',
                'email:rfc,dns',
                'max:255',
                Rule::unique('client_spouses', 'email_id')->ignore($spouseId)
            ],
            'visa_status' => 'required|in:citizen,permanent_resident,h1b,f1,j1,l1,other',
            'date_of_entry_in_us' => 'nullable|date|before_or_equal:today',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    private function getCustomMessages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'email' => 'The :attribute must be a valid email address.',
            'unique' => 'The :attribute has already been taken.',
            'date' => 'The :attribute must be a valid date.',
            'before' => 'The :attribute must be a date before :date.',
            'after' => 'The :attribute must be a date after :date.',
            'numeric' => 'The :attribute must be a number.',
            'min' => 'The :attribute must be at least :min.',
            'max' => 'The :attribute may not be greater than :max.',
            'in' => 'The selected :attribute is invalid.',
            'regex' => 'The :attribute format is invalid.',
            'size' => 'The :attribute must be exactly :size characters.',
            'boolean' => 'The :attribute field must be true or false.',
            'integer' => 'The :attribute must be an integer.',
            'exists' => 'The selected :attribute is invalid.',
            'array' => 'The :attribute must be an array.',
            'zip_code.regex' => 'The zip code must be in the format 12345 or 12345-6789.',
            'state.size' => 'The state must be a 2-letter state code.',
            'date_of_birth.before' => 'The date of birth must be before today.',
            'date_of_birth.after' => 'The date of birth must be after 1900-01-01.',
            'percentage_used_in_business.max' => 'The percentage used in business cannot exceed 100%.',
            'client_ids.min' => 'At least one client must be selected.',
        ];
    }

    /**
     * Validate business rules.
     *
     * @param array $data
     * @param int|null $clientId
     * @throws ValidationException
     */
    private function validateBusinessRules(array $data, ?int $clientId = null): void
    {
        $errors = [];

        // Validate age requirements
        if (isset($data['date_of_birth'])) {
            $age = now()->diffInYears($data['date_of_birth']);
            if ($age < 18) {
                $errors['date_of_birth'] = ['Client must be at least 18 years old.'];
            }
        }

        // Validate visa status and entry date consistency
        if (isset($data['visa_status']) && isset($data['date_of_entry_in_us'])) {
            if (in_array($data['visa_status'], ['citizen']) && $data['date_of_entry_in_us']) {
                $errors['date_of_entry_in_us'] = ['US citizens do not need an entry date.'];
            }
            
            if (!in_array($data['visa_status'], ['citizen']) && !$data['date_of_entry_in_us']) {
                $errors['date_of_entry_in_us'] = ['Entry date is required for non-citizens.'];
            }
        }

        // Validate marital status consistency
        if (isset($data['marital_status']) && $data['marital_status'] !== 'married') {
            // If not married, spouse data should not be provided
            $spouseFields = ['spouse_first_name', 'spouse_last_name', 'spouse_ssn'];
            foreach ($spouseFields as $field) {
                if (isset($data[$field]) && !empty($data[$field])) {
                    $errors['marital_status'] = ['Spouse information should only be provided for married clients.'];
                    break;
                }
            }
        }

        if (!empty($errors)) {
            $validator = Validator::make([], []);
            foreach ($errors as $field => $messages) {
                foreach ($messages as $message) {
                    $validator->errors()->add($field, $message);
                }
            }
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate client search parameters.
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function validateSearchParameters(array $data): array
    {
        $rules = [
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,archived',
            'marital_status' => 'nullable|in:single,married,divorced,widowed,separated',
            'visa_status' => 'nullable|in:citizen,permanent_resident,h1b,f1,j1,l1,other',
            'assigned_tax_professional_id' => 'nullable|integer|exists:users,id',
            'registration_date_from' => 'nullable|date',
            'registration_date_to' => 'nullable|date|after_or_equal:registration_date_from',
            'per_page' => 'nullable|integer|in:10,25,50,100',
            'sort_by' => 'nullable|in:id,first_name,last_name,email_id,created_at',
            'sort_direction' => 'nullable|in:asc,desc',
        ];

        $validator = Validator::make($data, $rules, $this->getCustomMessages());
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }
}   