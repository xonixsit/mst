<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'tax_professional');
    }

    public function rules(): array
    {
        return [
            // Personal details
            'personal.firstName' => 'required|string|max:255',
            'personal.lastName' => 'required|string|max:255',
            'personal.middleName' => 'nullable|string|max:255',
            'personal.email' => 'required|email|max:255',
            'personal.phone' => 'nullable|string|max:20',
            'personal.ssn' => 'nullable|string|max:11',
            'personal.dateOfBirth' => 'nullable|date',
            'personal.maritalStatus' => 'nullable|in:single,married,divorced,widowed',
            'personal.occupation' => 'nullable|string|max:255',
            'personal.insuranceCovered' => 'nullable|boolean',
            'personal.streetNo' => 'nullable|string|max:255',
            'personal.apartmentNo' => 'nullable|string|max:255',
            'personal.zipCode' => 'nullable|string|max:10',
            'personal.city' => 'nullable|string|max:255',
            'personal.state' => 'nullable|string|max:255',
            'personal.country' => 'nullable|string|max:255',
            'personal.mobileNumber' => 'nullable|string|max:20',
            'personal.workNumber' => 'nullable|string|max:20',
            'personal.visaStatus' => 'nullable|string|max:255',
            'personal.date_of_entry_us' => 'nullable|date',

            // Spouse details
            'spouse.firstName' => 'nullable|string|max:255',
            'spouse.lastName' => 'nullable|string|max:255',
            'spouse.middleName' => 'nullable|string|max:255',
            'spouse.email' => 'nullable|email|max:255',
            'spouse.phone' => 'nullable|string|max:20',
            'spouse.ssn' => 'nullable|string|max:11',
            'spouse.dateOfBirth' => 'nullable|date',
            'spouse.occupation' => 'nullable|string|max:255',

            // Employee details
            'employee' => 'nullable|array',
            'employee.*.employer_name' => 'nullable|string|max:255',
            'employee.*.job_title' => 'nullable|string|max:255',
            'employee.*.employee_id' => 'nullable|string|max:255',
            'employee.*.start_date' => 'nullable|date',
            'employee.*.end_date' => 'nullable|date',
            'employee.*.annual_salary' => 'nullable|numeric|min:0',
            'employee.*.employment_type' => 'nullable|string|max:255',
            'employee.*.work_description' => 'nullable|string',

            // Projects
            'projects' => 'nullable|array',
            'projects.*.project_name' => 'nullable|string|max:255',
            'projects.*.project_description' => 'nullable|string',
            'projects.*.project_type' => 'nullable|string|max:255',
            'projects.*.status' => 'nullable|string|max:255',
            'projects.*.start_date' => 'nullable|date',
            'projects.*.due_date' => 'nullable|date',
            'projects.*.completion_date' => 'nullable|date',

            // Assets
            'assets' => 'nullable|array',
            'assets.*.asset_name' => 'nullable|string|max:255',
            'assets.*.date_of_purchase' => 'nullable|date',
            'assets.*.percentage_used_in_business' => 'nullable|numeric|min:0|max:100',
            'assets.*.cost_of_acquisition' => 'nullable|numeric|min:0',
            'assets.*.asset_type' => 'nullable|string|max:255',
            'assets.*.description' => 'nullable|string',

            // Expenses
            'expenses' => 'nullable|array',
            'expenses.*.category' => 'nullable|string|max:255',
            'expenses.*.particulars' => 'nullable|string|max:255',
            'expenses.*.tax_payer' => 'nullable|string|max:255',
            'expenses.*.expense_date' => 'nullable|date',
            'expenses.*.receipt_number' => 'nullable|string|max:255',
            'expenses.*.amount' => 'nullable|numeric|min:0',
            'expenses.*.deductible_percentage' => 'nullable|numeric|min:0|max:100',

            // Account creation
            'createAccount' => 'nullable|boolean',
            'sendCredentials' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'personal.firstName.required' => 'First name is required',
            'personal.lastName.required' => 'Last name is required',
            'personal.email.required' => 'Email is required',
            'personal.email.email' => 'Email must be a valid email address',
            'username.required_if' => 'Username is required when creating an account',
            'username.regex' => 'Username can only contain letters, numbers, and dots',
            'username.unique' => 'This username is already taken',
        ];
    }
}
