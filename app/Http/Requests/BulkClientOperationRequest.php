<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkClientOperationRequest extends FormRequest
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
        return [
            'client_ids' => 'required|array|min:1',
            'client_ids.*' => 'exists:clients,id',
            'operation' => 'required|string|in:activate,deactivate,archive,assign_user,send_email,request_documents,export_selected,delete',
            
            // For user assignment operations
            'data.user_id' => 'required_if:operation,assign_user|exists:users,id',
            
            // For email operations
            'data.subject' => 'required_if:operation,send_email|string|max:255',
            'data.message' => 'required_if:operation,send_email|string|max:2000',
            
            // For document request operations
            'data.document_types' => 'required_if:operation,request_documents|array|min:1',
            'data.document_types.*' => 'string|in:w2,1099,receipts,bank_statements,tax_returns,id_documents',
            
            // For export operations
            'data.format' => 'required_if:operation,export_selected|in:csv,excel,pdf',
            
            // For delete operations
            'data.confirm_delete' => 'required_if:operation,delete|boolean|accepted',
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
            'client_ids.required' => 'At least one client must be selected.',
            'client_ids.min' => 'At least one client must be selected.',
            'client_ids.*.exists' => 'One or more selected clients do not exist.',
            'operation.required' => 'An operation must be specified.',
            'operation.in' => 'The selected operation is not valid.',
            'data.user_id.required_if' => 'User assignment is required for user assignment operations.',
            'data.subject.required_if' => 'Subject is required for email operations.',
            'data.message.required_if' => 'Message is required for email operations.',
            'data.document_types.required_if' => 'Document types are required for document request operations.',
            'data.document_types.min' => 'At least one document type must be selected.',
            'data.format.required_if' => 'Export format is required for export operations.',
            'data.confirm_delete.required_if' => 'Delete confirmation is required for delete operations.',
            'data.confirm_delete.accepted' => 'You must confirm the delete operation.',
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
            'client_ids' => 'selected clients',
            'data.user_id' => 'user assignment',
            'data.subject' => 'email subject',
            'data.message' => 'email message',
            'data.document_types' => 'document types',
            'data.format' => 'export format',
            'data.confirm_delete' => 'delete confirmation',
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
            // Validate client selection limits based on operation
            $clientCount = count($this->input('client_ids', []));
            
            switch ($this->input('operation')) {
                case 'delete':
                    if ($clientCount > 50) {
                        $validator->errors()->add('client_ids', 'Cannot delete more than 50 clients at once.');
                    }
                    break;
                    
                case 'send_email':
                    if ($clientCount > 100) {
                        $validator->errors()->add('client_ids', 'Cannot send emails to more than 100 clients at once.');
                    }
                    break;
                    
                case 'request_documents':
                    if ($clientCount > 100) {
                        $validator->errors()->add('client_ids', 'Cannot request documents from more than 100 clients at once.');
                    }
                    break;
                    
                case 'export_selected':
                    if ($clientCount > 1000) {
                        $validator->errors()->add('client_ids', 'Cannot export more than 1000 clients at once.');
                    }
                    break;
            }

            // Additional validation can be added here if needed
        });
    }

    /**
     * Get the validated data with additional processing.
     *
     * @return array
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Set default export format if not provided
        if ($validated['operation'] === 'export_selected' && empty($validated['data']['format'])) {
            $validated['data']['format'] = 'excel';
        }
        
        return $validated;
    }
}