<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'comments' => 'nullable|string',
            'send_to_email' => 'required|email|max:255',
            'invoice_year' => 'required|integer|min:2024|max:2030',
            'items' => 'required|array|min:1',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Please select a client for this invoice.',
            'client_id.exists' => 'The selected client does not exist.',
            'title.required' => 'Invoice title is required.',
            'send_to_email.required' => 'Email address is required.',
            'send_to_email.email' => 'Please enter a valid email address.',
            'invoice_year.required' => 'Invoice year is required.',
            'items.required' => 'At least one invoice item is required.',
            'items.min' => 'At least one invoice item is required.',
            'items.*.service_name.required' => 'Service name is required for all items.',
            'items.*.quantity.required' => 'Quantity is required for all items.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.unit_price.required' => 'Unit price is required for all items.',
            'items.*.unit_price.min' => 'Unit price must be 0 or greater.',
        ];
    }
}