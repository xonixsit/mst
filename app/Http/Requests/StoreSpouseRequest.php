<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\SSNFormat;
use App\Rules\PhoneNumberFormat;
use App\Rules\DateFormat;
use App\Rules\EmailFormat;
use App\Services\ValidationErrorFormatter;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSpouseRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'ssn' => ['nullable', 'string', 'unique:client_spouses,ssn', new SSNFormat()],
            'date_of_birth' => ['nullable', new DateFormat('Y-m-d', false, true)],
            'occupation' => 'nullable|string|max:255',
            'email' => ['nullable', 'max:255', new EmailFormat()],
            'phone' => ['nullable', 'string', new PhoneNumberFormat()],
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
            'first_name.required' => 'Spouse first name is required.',
            'first_name.max' => 'Spouse first name cannot exceed 255 characters.',
            'last_name.required' => 'Spouse last name is required.',
            'last_name.max' => 'Spouse last name cannot exceed 255 characters.',
            'middle_name.max' => 'Spouse middle name cannot exceed 255 characters.',
            'ssn.unique' => 'This SSN is already registered for another spouse.',
            'occupation.max' => 'Spouse occupation cannot exceed 255 characters.',
            'email.max' => 'Spouse email cannot exceed 255 characters.',
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
            'first_name' => 'spouse first name',
            'middle_name' => 'spouse middle name',
            'last_name' => 'spouse last name',
            'date_of_birth' => 'spouse date of birth',
            'ssn' => 'spouse SSN',
            'occupation' => 'spouse occupation',
            'email' => 'spouse email',
            'phone' => 'spouse phone',
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
            // Validate that the client is married
            if ($this->filled('client_id')) {
                $client = \App\Models\Client::find($this->input('client_id'));
                if ($client && $client->marital_status !== 'married') {
                    $validator->errors()->add(
                        'client_id',
                        'Spouse information can only be added for married clients.'
                    );
                }
            }

            // Validate spouse age if date of birth is provided
            if ($this->filled('date_of_birth')) {
                $spouseBirthDate = \Carbon\Carbon::parse($this->input('date_of_birth'));
                $spouseAge = $spouseBirthDate->age;

                if ($spouseAge < 18) {
                    $validator->errors()->add(
                        'date_of_birth',
                        'Spouse must be at least 18 years old.'
                    );
                }

                if ($spouseAge > 100) {
                    $validator->errors()->add(
                        'date_of_birth',
                        'Please verify the spouse date of birth (age appears to be over 100).'
                    );
                }

                // Compare with client's age if available
                if ($this->filled('client_id')) {
                    $client = \App\Models\Client::find($this->input('client_id'));
                    if ($client && $client->date_of_birth) {
                        $clientAge = \Carbon\Carbon::parse($client->date_of_birth)->age;
                        $ageDifference = abs($clientAge - $spouseAge);

                        if ($ageDifference > 30) {
                            $validator->errors()->add(
                                'date_of_birth',
                                'Large age difference detected between client and spouse. Please verify.'
                            );
                        }
                    }
                }
            }

            // Validate that spouse SSN is different from client SSN
            if ($this->filled('ssn') && $this->filled('client_id')) {
                $client = \App\Models\Client::find($this->input('client_id'));
                if ($client && $client->ssn === $this->input('ssn')) {
                    $validator->errors()->add(
                        'ssn',
                        'Spouse SSN cannot be the same as client SSN.'
                    );
                }
            }

            // Validate that spouse email is different from client email
            if ($this->filled('email') && $this->filled('client_id')) {
                $client = \App\Models\Client::find($this->input('client_id'));
                if ($client && $client->email === $this->input('email')) {
                    $validator->errors()->add(
                        'email',
                        'Spouse email should be different from client email.'
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