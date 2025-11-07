<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientSearchRequest extends FormRequest
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
            // Search and filtering
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|array',
            'status.*' => 'in:active,inactive,archived',
            'marital_status' => 'nullable|array',
            'marital_status.*' => 'in:single,married,divorced,widowed',
            'visa_status' => 'nullable|array',
            'visa_status.*' => 'string|max:255',
            'user_id' => 'nullable|array',
            'user_id.*' => 'exists:users,id',
            'city' => 'nullable|array',
            'city.*' => 'string|max:255',
            'state' => 'nullable|array',
            'state.*' => 'string|max:255',
            'country' => 'nullable|array',
            'country.*' => 'string|max:255',
            'occupation' => 'nullable|string|max:255',

            // Date range filters
            'registration_date_from' => 'nullable|date',
            'registration_date_to' => 'nullable|date|after_or_equal:registration_date_from',
            'date_of_birth_from' => 'nullable|date',
            'date_of_birth_to' => 'nullable|date|after_or_equal:date_of_birth_from',

            // Boolean filters
            'insurance_covered' => 'nullable|boolean',
            'has_spouse' => 'nullable|boolean',
            'has_active_projects' => 'nullable|boolean',
            'has_overdue_projects' => 'nullable|boolean',

            // Sorting
            'sort_by' => 'nullable|string|in:id,first_name,last_name,full_name,email,phone,mobile_number,date_of_birth,marital_status,occupation,status,city,state,country,visa_status,created_at,updated_at,projects_count,assets_count,expenses_count',
            'sort_direction' => 'nullable|string|in:asc,desc',

            // Pagination
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',

            // Export options
            'export_format' => 'nullable|string|in:csv,excel,pdf',
            'export_fields' => 'nullable|array',
            'export_fields.*' => 'string',
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
            'registration_date_to.after_or_equal' => 'The registration end date must be after or equal to the start date.',
            'date_of_birth_to.after_or_equal' => 'The date of birth end date must be after or equal to the start date.',
            'per_page.max' => 'The maximum number of items per page is 100.',
            'sort_by.in' => 'The selected sort field is invalid.',
            'sort_direction.in' => 'The sort direction must be either asc or desc.',
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
            'registration_date_from' => 'registration start date',
            'registration_date_to' => 'registration end date',
            'date_of_birth_from' => 'date of birth start',
            'date_of_birth_to' => 'date of birth end',
            'per_page' => 'items per page',
            'sort_by' => 'sort field',
            'sort_direction' => 'sort direction',
            'export_format' => 'export format',
            'export_fields' => 'export fields',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        if (!$this->has('per_page')) {
            $this->merge(['per_page' => 15]);
        }

        if (!$this->has('sort_by')) {
            $this->merge(['sort_by' => 'created_at']);
        }

        if (!$this->has('sort_direction')) {
            $this->merge(['sort_direction' => 'desc']);
        }

        // Convert single values to arrays for multi-select filters
        $arrayFields = ['status', 'marital_status', 'visa_status', 'user_id', 'city', 'state', 'country'];
        foreach ($arrayFields as $field) {
            if ($this->has($field) && !is_array($this->input($field))) {
                $this->merge([$field => [$this->input($field)]]);
            }
        }
    }
}