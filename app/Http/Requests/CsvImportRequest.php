<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CsvImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Client::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'csv_file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:10240' // 10MB max
            ],
            'preview' => 'boolean',
            'skip_invalid' => 'boolean',
            'update_existing' => 'boolean',
            'assign_to_user' => 'nullable|exists:users,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'csv_file.required' => 'Please select a CSV file to import.',
            'csv_file.file' => 'The uploaded file is not valid.',
            'csv_file.mimes' => 'Only CSV files are allowed.',
            'csv_file.max' => 'The CSV file size cannot exceed 10MB.',
            'assign_to_user.exists' => 'The selected user does not exist.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'csv_file' => 'CSV file',
            'assign_to_user' => 'assigned user'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional validation logic can be added here
            if ($this->hasFile('csv_file')) {
                $file = $this->file('csv_file');
                
                // Check if file is actually readable
                if (!$file->isValid()) {
                    $validator->errors()->add('csv_file', 'The uploaded file is corrupted or invalid.');
                }
            }
        });
    }
}