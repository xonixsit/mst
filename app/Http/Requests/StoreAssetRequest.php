<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\DateFormat;
use App\Services\ValidationErrorFormatter;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAssetRequest extends FormRequest
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
            'asset_name' => 'required|string|max:255',
            'date_of_purchase' => ['required', new DateFormat('Y-m-d', false, true)],
            'percentage_used_in_business' => 'required|numeric|min:0|max:100',
            'cost_of_acquisition' => 'required|numeric|min:0|max:9999999999.99',
            'any_reimbursement' => 'nullable|numeric|min:0|max:9999999999.99',
            'asset_type' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'current_value' => 'nullable|numeric|min:0|max:9999999999.99',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
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
            'asset_name.required' => 'Asset name is required.',
            'asset_name.max' => 'Asset name cannot exceed 255 characters.',
            'date_of_purchase.required' => 'Purchase date is required.',
            'percentage_used_in_business.required' => 'Business usage percentage is required.',
            'percentage_used_in_business.min' => 'Business usage percentage cannot be negative.',
            'percentage_used_in_business.max' => 'Business usage percentage cannot exceed 100%.',
            'cost_of_acquisition.required' => 'Acquisition cost is required.',
            'cost_of_acquisition.min' => 'Acquisition cost cannot be negative.',
            'any_reimbursement.min' => 'Reimbursement amount cannot be negative.',
            'current_value.min' => 'Current value cannot be negative.',
            'depreciation_rate.min' => 'Depreciation rate cannot be negative.',
            'depreciation_rate.max' => 'Depreciation rate cannot exceed 100%.',
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
            'asset_name' => 'asset name',
            'date_of_purchase' => 'purchase date',
            'percentage_used_in_business' => 'business usage percentage',
            'cost_of_acquisition' => 'acquisition cost',
            'any_reimbursement' => 'reimbursement amount',
            'asset_type' => 'asset type',
            'current_value' => 'current value',
            'depreciation_rate' => 'depreciation rate',
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
            // Validate reimbursement doesn't exceed cost
            if ($this->filled('any_reimbursement') && $this->filled('cost_of_acquisition')) {
                if ($this->input('any_reimbursement') > $this->input('cost_of_acquisition')) {
                    $validator->errors()->add(
                        'any_reimbursement',
                        'Reimbursement amount cannot exceed the cost of acquisition.'
                    );
                }
            }

            // Validate current value logic
            if ($this->filled('current_value') && $this->filled('cost_of_acquisition')) {
                $currentValue = $this->input('current_value');
                $costOfAcquisition = $this->input('cost_of_acquisition');
                
                // Warn if current value is significantly higher than acquisition cost
                if ($currentValue > ($costOfAcquisition * 2)) {
                    $validator->errors()->add(
                        'current_value',
                        'Current value seems unusually high compared to acquisition cost. Please verify.'
                    );
                }
            }

            // Validate depreciation rate logic
            if ($this->filled('depreciation_rate') && $this->filled('date_of_purchase')) {
                $purchaseDate = \Carbon\Carbon::parse($this->input('date_of_purchase'));
                $yearsOwned = $purchaseDate->diffInYears(now());
                $depreciationRate = $this->input('depreciation_rate');
                
                // Check if depreciation rate makes sense for the asset age
                if ($yearsOwned > 0 && $depreciationRate > ($yearsOwned * 20)) {
                    $validator->errors()->add(
                        'depreciation_rate',
                        'Depreciation rate seems high for an asset owned for ' . $yearsOwned . ' year(s).'
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