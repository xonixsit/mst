<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientSpouse;
use App\Models\ClientEmployee;
use App\Models\ClientProject;
use App\Models\ClientAsset;
use App\Models\ClientExpense;
use App\Services\BulkEmailService;
use App\Services\BulkExportService;
use App\Services\CommunicationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ClientInformationService
{
    public function __construct(
        private BulkEmailService $bulkEmailService,
        private BulkExportService $bulkExportService,
        private CommunicationService $communicationService
    ) {}
    /**
     * Create a new client with all related information.
     *
     * @param array $clientData
     * @return Client
     * @throws Exception
     */
    public function createClient(array $clientData): Client
    {
        try {
            return DB::transaction(function () use ($clientData) {
                // Create the main client record
                $client = Client::create($this->extractClientData($clientData));

                // Create related records if provided
                $this->createRelatedRecords($client, $clientData);

                // Load all relationships for the response
                return $this->loadClientWithRelationships($client);
            });
        } catch (Exception $e) {
            Log::error('Failed to create client', [
                'error' => $e->getMessage(),
                'data' => $clientData
            ]);
            throw $e;
        }
    }

    /**
     * Retrieve a client by ID with all related information.
     *
     * @param int $clientId
     * @return Client|null
     */
    public function getClient(int $clientId): ?Client
    {
        return Client::with([
            'spouse',
            'employees',
            'projects',
            'assets',
            'expenses'
        ])->find($clientId);
    }

    /**
     * Update client information and related records.
     *
     * @param int $clientId
     * @param array $clientData
     * @return Client
     * @throws Exception
     */
    public function updateClient(int $clientId, array $clientData): Client
    {

        try {
            return DB::transaction(function () use ($clientId, $clientData) {
                $client = Client::findOrFail($clientId);
                
                // Track changes for notifications
                $originalData = $client->toArray();
                $newClientData = $this->extractClientData($clientData);
                $changes = $this->trackChanges($originalData, $newClientData);
                
                // Update main client record
                $client->update($newClientData);

                // Update related records
                $this->updateRelatedRecords($client, $clientData);

                // Send notification if there are significant changes
                if (!empty($changes) && $client->shouldReceiveNotification('email')) {
                    $this->communicationService->sendClientInformationUpdateNotification($client, $changes);
                }

                // Load all relationships for the response
                return $this->loadClientWithRelationships($client);
            });
        } catch (Exception $e) {
            Log::error('Failed to update client', [
                'client_id' => $clientId,
                'error' => $e->getMessage(),
                'data' => $clientData
            ]);
            throw $e;
        }
    }

    /**
     * Delete a client and all related records.
     *
     * @param int $clientId
     * @return bool
     * @throws Exception
     */
    public function deleteClient(int $clientId): bool
    {
        try {
            return DB::transaction(function () use ($clientId) {
                $client = Client::findOrFail($clientId);
                
                // Delete related records first
                $client->spouse()?->delete();
                $client->employees()->delete();
                $client->projects()->delete();
                $client->assets()->delete();
                $client->expenses()->delete();
                
                // Delete the client record
                return $client->delete();
            });
        } catch (Exception $e) {
            Log::error('Failed to delete client', [
                'client_id' => $clientId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get all clients with pagination and filtering.
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getClients(array $filters = [], int $perPage = 15)
    {
        $query = Client::with(['spouse', 'user']);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['marital_status'])) {
            $query->where('marital_status', $filters['marital_status']);
        }

        if (!empty($filters['visa_status'])) {
            $query->where('visa_status', $filters['visa_status']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Transform client data for API responses.
     *
     * @param Client $client
     * @return array
     */
    public function transformClientForResponse(Client $client): array
    {
        return [
            'id' => $client->id,
            'personal_details' => [
                'first_name' => $client->first_name,
                'middle_name' => $client->middle_name,
                'last_name' => $client->last_name,
                'full_name' => $client->full_name,
                'email' => $client->email,
                'phone' => $client->phone,
                'mobile_number' => $client->mobile_number,
                'work_number' => $client->work_number,
                'date_of_birth' => $client->date_of_birth?->format('Y-m-d'),
                'marital_status' => $client->marital_status,
                'occupation' => $client->occupation,
                'insurance_covered' => $client->insurance_covered,
                'visa_status' => $client->visa_status,
                'date_of_entry_us' => $client->date_of_entry_us?->format('Y-m-d'),
            ],
            'address' => [
                'street_no' => $client->street_no,
                'apartment_no' => $client->apartment_no,
                'city' => $client->city,
                'state' => $client->state,
                'zip_code' => $client->zip_code,
                'country' => $client->country,
                'formatted_address' => $client->formatted_address,
            ],
            'spouse' => $client->spouse ? $this->transformSpouseForResponse($client->spouse) : null,
            'employees' => $client->employees->map(fn($employee) => $this->transformEmployeeForResponse($employee)),
            'projects' => $client->projects->map(fn($project) => $this->transformProjectForResponse($project)),
            'assets' => $client->assets->map(fn($asset) => $this->transformAssetForResponse($asset)),
            'expenses' => $client->expenses->map(fn($expense) => $this->transformExpenseForResponse($expense)),
            'status' => $client->status,
            'created_at' => $client->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $client->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Transform client data for list view (summary).
     *
     * @param Client $client
     * @return array
     */
    public function transformClientForList(Client $client): array
    {
        return [
            'id' => $client->id,
            'name' => $client->full_name,
            'email' => $client->email,
            'phone' => $client->phone,
            'status' => $client->status,
            'marital_status' => $client->marital_status,
            'registration_date' => $client->created_at->format('m/d/Y'),
            'has_spouse' => $client->spouse !== null,
            'projects_count' => $client->projects_count ?? $client->projects->count(),
            'assets_count' => $client->assets_count ?? $client->assets->count(),
            'expenses_count' => $client->expenses_count ?? $client->expenses->count(),
        ];
    }

    /**
     * Extract client-specific data from the input array.
     *
     * @param array $data
     * @return array
     */
    private function extractClientData(array $data): array
    {
        // Handle nested personal data structure from frontend
        $personalData = $data['personal'] ?? $data;
        
        $allowedFields = [
            'user_id', 'first_name', 'middle_name', 'last_name', 'email', 'phone',
            'ssn', 'date_of_birth', 'marital_status', 'occupation', 'insurance_covered',
            'street_no', 'apartment_no', 'zip_code', 'city', 'state', 'country',
            'mobile_number', 'work_number', 'visa_status', 'date_of_entry_us', 'status'
        ];
        
        return array_intersect_key($personalData, array_flip($allowedFields));
    }

    /**
     * Create related records for a client.
     *
     * @param Client $client
     * @param array $data
     * @return void
     */
    private function createRelatedRecords(Client $client, array $data): void
    {
        // Create spouse record if provided and client is married
        if ($this->hasValidSpouseData($data) && $client->marital_status === 'married') {
            $client->spouse()->create($data['spouse']);
        }

        // Create employee records (handle both 'employee' and 'employees' keys)
        $employeeData = $data['employee'] ?? $data['employees'] ?? [];
        if (!empty($employeeData) && is_array($employeeData)) {
            foreach ($employeeData as $empData) {
                $transformedData = $this->transformEmployeeDataForStorage($empData);
                $client->employees()->create($transformedData);
            }
        }

        // Create project records
        if (!empty($data['projects']) && is_array($data['projects'])) {
            foreach ($data['projects'] as $projectData) {
                // Skip empty or invalid project data
                if (empty($projectData) || (empty($projectData['project_name']) && empty($projectData['name']))) {
                    continue;
                }
                
                // Map frontend field names to database field names
                if (isset($projectData['name']) && !isset($projectData['project_name'])) {
                    $projectData['project_name'] = $projectData['name'];
                    unset($projectData['name']);
                }
                if (isset($projectData['description']) && !isset($projectData['project_description'])) {
                    $projectData['project_description'] = $projectData['description'];
                    unset($projectData['description']);
                }
                if (isset($projectData['type']) && !isset($projectData['project_type'])) {
                    $projectData['project_type'] = $projectData['type'];
                    unset($projectData['type']);
                }
                
                $client->projects()->create($projectData);
            }
        }

        // Create asset records
        if (!empty($data['assets']) && is_array($data['assets'])) {
            foreach ($data['assets'] as $assetData) {
                // Skip empty or invalid asset data
                if (empty($assetData) || (empty($assetData['asset_name']) && empty($assetData['name']))) {
                    continue;
                }
                
                // Map frontend field names to database field names if needed
                if (isset($assetData['name']) && !isset($assetData['asset_name'])) {
                    $assetData['asset_name'] = $assetData['name'];
                    unset($assetData['name']);
                }
                
                $client->assets()->create($assetData);
            }
        }

        // Create expense records
        if (!empty($data['expenses']) && is_array($data['expenses'])) {
            foreach ($data['expenses'] as $expenseData) {
                $client->expenses()->create($expenseData);
            }
        }
    }    
/**
     * Update related records for a client.
     *
     * @param Client $client
     * @param array $data
     * @return void
     */
    private function updateRelatedRecords(Client $client, array $data): void
    {
        // Update or create spouse record
        if ($this->hasValidSpouseData($data) && $client->marital_status === 'married') {
            $client->spouse()->updateOrCreate(
                ['client_id' => $client->id],
                $data['spouse']
            );
        } elseif ($client->marital_status !== 'married') {
            // Remove spouse record if client is no longer married
            $client->spouse()?->delete();
        }
        
        // Update employee records (handle both 'employee' and 'employees' keys)
        $employeeData = $data['employee'] ?? $data['employees'] ?? [];
        if (!empty($employeeData)) {
            // Always delete and recreate for simplicity
            $client->employees()->delete();
            
            foreach ($employeeData as $empData) {
                $transformedData = $this->transformEmployeeDataForStorage($empData);
                $client->employees()->create($transformedData);
            }
        }

        // Update project records
        if (isset($data['projects'])) {
            $normalizedProjects = array_map(function($project) {
                // Map frontend field names to database field names
                if (isset($project['name']) && !isset($project['project_name'])) {
                    $project['project_name'] = $project['name'];
                    unset($project['name']);
                }
                if (isset($project['description']) && !isset($project['project_description'])) {
                    $project['project_description'] = $project['description'];
                    unset($project['description']);
                }
                if (isset($project['type']) && !isset($project['project_type'])) {
                    $project['project_type'] = $project['type'];
                    unset($project['type']);
                }
                return $project;
            }, $data['projects']);
            
            $this->updateRelatedCollection($client->projects(), $normalizedProjects);
        }

        // Update asset records
        if (isset($data['assets'])) {
            $normalizedAssets = array_map(function($asset) {
                // Map frontend field names to database field names if needed
                if (isset($asset['name']) && !isset($asset['asset_name'])) {
                    $asset['asset_name'] = $asset['name'];
                    unset($asset['name']);
                }
                return $asset;
            }, $data['assets']);
            
            $this->updateRelatedCollection($client->assets(), $normalizedAssets);
        }

        // Update expense records
        if (isset($data['expenses'])) {
            // Normalize expense field names from frontend format to database format
            $normalizedExpenses = array_map(function($expense) {
                // Map frontend field names to database field names
                if (isset($expense['taxPayer']) && !isset($expense['tax_payer'])) {
                    $expense['tax_payer'] = $expense['taxPayer'];
                    unset($expense['taxPayer']);
                }
                if (isset($expense['date']) && !isset($expense['expense_date'])) {
                    $expense['expense_date'] = $expense['date'];
                    unset($expense['date']);
                }
                if (isset($expense['receiptNumber']) && !isset($expense['receipt_number'])) {
                    $expense['receipt_number'] = $expense['receiptNumber'];
                    unset($expense['receiptNumber']);
                }
                if (isset($expense['deductiblePercentage']) && !isset($expense['deductible_percentage'])) {
                    $expense['deductible_percentage'] = $expense['deductiblePercentage'];
                    unset($expense['deductiblePercentage']);
                }
                if (isset($expense['deductible']) && !isset($expense['is_deductible'])) {
                    $expense['is_deductible'] = $expense['deductible'];
                    unset($expense['deductible']);
                }
                // Set is_deductible based on deductible_percentage if not explicitly set
                if (!isset($expense['is_deductible'])) {
                    $expense['is_deductible'] = isset($expense['deductible_percentage']) && floatval($expense['deductible_percentage']) > 0;
                }
                
                // Only set default deductible percentage if it's truly not provided (not 0 or empty string)
                if (!array_key_exists('deductible_percentage', $expense) || $expense['deductible_percentage'] === null) {
                    $expense['deductible_percentage'] = 100;
                } else {
                    // Ensure it's a number (convert empty string to 0)
                    $expense['deductible_percentage'] = $expense['deductible_percentage'] === '' ? 0 : floatval($expense['deductible_percentage']);
                }
                // Set defaults for required database fields if missing
                if (!isset($expense['particulars']) || empty($expense['particulars'])) {
                    $expense['particulars'] = 'Expense';
                }
                if (!isset($expense['tax_payer']) || empty($expense['tax_payer'])) {
                    $expense['tax_payer'] = 'Taxpayer';
                }
                if (!isset($expense['expense_date']) || empty($expense['expense_date'])) {
                    $expense['expense_date'] = date('Y-m-d');
                }
                return $expense;
            }, $data['expenses']);
            
            Log::info('Processing expenses update', [
                'client_id' => $client->id,
                'expense_count' => count($normalizedExpenses),
                'expenses_data' => $normalizedExpenses
            ]);
            $this->updateRelatedCollection($client->expenses(), $normalizedExpenses);
        }
    }

    /**
     * Update a collection of related records.
     *
     * @param \Illuminate\Database\Eloquent\Relations\HasMany $relation
     * @param array $records
     * @return void
     */
    private function updateRelatedCollection($relation, array $records): void
    {
        $existingIds = [];
        $modelClass = get_class($relation->getRelated());

        foreach ($records as $index => $recordData) {
            // Skip empty or incomplete records
            $isValid = $this->isValidRecordData($relation, $recordData);
            
            Log::info('Processing record in collection', [
                'model' => $modelClass,
                'index' => $index,
                'is_valid' => $isValid,
                'is_empty' => empty($recordData),
                'record_data' => $recordData
            ]);
            
            if (empty($recordData) || !$isValid) {
                Log::warning('Skipping invalid or empty record', [
                    'model' => $modelClass,
                    'index' => $index,
                    'reason' => empty($recordData) ? 'empty' : 'invalid'
                ]);
                continue;
            }
            
            // Check if this is an existing database record (numeric ID that exists in DB for this client)
            $recordId = $recordData['id'] ?? null;
            $isNumericId = $recordId && is_numeric($recordId);
            
            // Check if record exists and belongs to the current client
            $recordModelClass = get_class($relation->getRelated());
            $existsInDatabase = $isNumericId ? $recordModelClass::where('id', $recordId)->exists() : false;
            
            // If it exists, also verify it belongs to the current client
            $isExistingRecord = false;
            if ($existsInDatabase) {
                $record = $recordModelClass::find($recordId);
                // Get the parent model (Client) from the relation
                $parentModel = $relation->getParent();
                $isExistingRecord = $record && $parentModel && $record->client_id == $parentModel->id;
            }
            
            Log::info('Record ID check', [
                'model' => $modelClass,
                'record_id' => $recordId,
                'is_numeric' => $isNumericId,
                'exists_in_db' => $existsInDatabase,
                'is_existing_record' => $isExistingRecord
            ]);
            
            if ($isExistingRecord) {
                // Update existing record - remove 'id' from update data to avoid errors
                $updateData = $recordData;
                unset($updateData['id']);
                
                Log::info('Updating existing record', [
                    'model' => $modelClass,
                    'id' => $recordId,
                    'update_data' => $updateData
                ]);
                
                // Use the model directly to avoid relationship constraints
                $recordModelClass = get_class($relation->getRelated());
                $updateResult = $recordModelClass::where('id', $recordId)->update($updateData);
                
                Log::info('Update operation result', [
                    'model' => $modelClass,
                    'id' => $recordId,
                    'rows_affected' => $updateResult
                ]);
                
                $existingIds[] = $recordId;
            } else {
                // Create new record (either no ID, or frontend-generated temp ID)
                $createData = $recordData;
                // Remove frontend-generated ID if present (non-numeric or doesn't exist in DB)
                if (isset($createData['id']) && !is_numeric($createData['id'])) {
                    unset($createData['id']);
                }
                
                Log::info('Creating new record', [
                    'model' => $modelClass,
                    'data' => $createData,
                    'reason' => $recordId ? 'frontend_temp_id' : 'no_id'
                ]);
                
                $newRecord = $relation->create($createData);
                $existingIds[] = $newRecord->id;
                
                Log::info('Created new record successfully', [
                    'model' => $modelClass,
                    'new_id' => $newRecord->id
                ]);
            }
        }

        // Delete records that are no longer in the collection
        if (!empty($existingIds)) {
            Log::info('Deleting removed records', [
                'model' => $modelClass,
                'keeping_ids' => $existingIds
            ]);
            $relation->whereNotIn('id', $existingIds)->delete();
        } else {
            // If no valid records, delete all existing records
            Log::info('Deleting all records (no valid records in update)', [
                'model' => $modelClass
            ]);
            $relation->delete();
        }
    }

    /**
     * Load client with all relationships.
     *
     * @param Client $client
     * @return Client
     */
    private function loadClientWithRelationships(Client $client): Client
    {
        return $client->load([
            'spouse',
            'employees',
            'projects',
            'assets',
            'expenses'
        ]);
    }

    /**
     * Track changes between original and new data for notifications
     *
     * @param array $originalData
     * @param array $newData
     * @return array
     */
    private function trackChanges(array $originalData, array $newData): array
    {
        $changes = [];
        $significantFields = [
            'first_name', 'last_name', 'email', 'phone', 'mobile_number',
            'street_no', 'apartment_no', 'city', 'state', 'zip_code',
            'marital_status', 'occupation', 'visa_status'
        ];

        foreach ($significantFields as $field) {
            if (isset($newData[$field]) && 
                isset($originalData[$field]) && 
                $originalData[$field] !== $newData[$field]) {
                
                $changes[$field] = [
                    'old' => $originalData[$field],
                    'new' => $newData[$field]
                ];
            }
        }

        return $changes;
    }

    /**
     * Transform spouse data for API response.
     *
     * @param ClientSpouse $spouse
     * @return array
     */
    private function transformSpouseForResponse(ClientSpouse $spouse): array
    {
        return [
            'id' => $spouse->id,
            'first_name' => $spouse->first_name,
            'middle_name' => $spouse->middle_name,
            'last_name' => $spouse->last_name,
            'full_name' => $spouse->full_name,
            'email' => $spouse->email,
            'phone' => $spouse->phone,
            'date_of_birth' => $spouse->date_of_birth?->format('Y-m-d'),
            'occupation' => $spouse->occupation,
        ];
    }

    /**
     * Transform employee data for API response.
     *
     * @param ClientEmployee $employee
     * @return array
     */
    private function transformEmployeeForResponse(ClientEmployee $employee): array
    {
        return [
            'id' => $employee->id,
            'employer_name' => $employee->employer_name,
            'job_title' => $employee->job_title,
            'employee_id' => $employee->employee_id,
            'start_date' => $employee->start_date?->format('Y-m-d'),
            'end_date' => $employee->end_date?->format('Y-m-d'),
            'annual_salary' => $employee->annual_salary,
            'employment_type' => $employee->employment_type,
            'work_description' => $employee->work_description,
            'employer_address' => $employee->formatted_employer_address,
            'is_current_employment' => $employee->is_current_employment,
        ];
    }

    /**
     * Transform project data for API response.
     *
     * @param ClientProject $project
     * @return array
     */
    private function transformProjectForResponse(ClientProject $project): array
    {
        return [
            'id' => $project->id,
            'project_name' => $project->project_name,
            'project_description' => $project->project_description,
            'project_type' => $project->project_type,
            'status' => $project->status,
            'start_date' => $project->start_date?->format('Y-m-d'),
            'due_date' => $project->due_date?->format('Y-m-d'),
            'completion_date' => $project->completion_date?->format('Y-m-d'),
            'estimated_hours' => $project->estimated_hours,
            'actual_hours' => $project->actual_hours,
            'hours_variance' => $project->hours_variance,
            'progress_percentage' => $project->progress_percentage,
            'is_overdue' => $project->is_overdue,
            'notes' => $project->notes,
        ];
    }

    /**
     * Transform asset data for API response.
     *
     * @param ClientAsset $asset
     * @return array
     */
    private function transformAssetForResponse(ClientAsset $asset): array
    {
        return [
            'id' => $asset->id,
            'asset_name' => $asset->asset_name,
            'date_of_purchase' => $asset->date_of_purchase->format('Y-m-d'),
            'percentage_used_in_business' => $asset->percentage_used_in_business,
            'cost_of_acquisition' => $asset->cost_of_acquisition,
            'any_reimbursement' => $asset->any_reimbursement,
            'asset_type' => $asset->asset_type,
            'description' => $asset->description,
            'current_value' => $asset->current_value,
            'business_cost' => $asset->business_cost,
            'personal_cost' => $asset->personal_cost,
            'net_cost' => $asset->net_cost,
            'current_book_value' => $asset->current_book_value,
        ];
    }

    /**
     * Transform expense data for API response.
     *
     * @param ClientExpense $expense
     * @return array
     */
    private function transformExpenseForResponse(ClientExpense $expense): array
    {
        return [
            'id' => $expense->id,
            'category' => $expense->category,
            'particulars' => $expense->particulars,
            'tax_payer' => $expense->tax_payer,
            'spouse' => $expense->spouse,
            'amount' => $expense->amount,
            'expense_date' => $expense->expense_date->format('Y-m-d'),
            'remarks' => $expense->remarks,
            'receipt_number' => $expense->receipt_number,
            'is_deductible' => $expense->is_deductible,
            'deductible_percentage' => $expense->deductible_percentage,
            'deductible_amount' => $expense->deductible_amount,
            'non_deductible_amount' => $expense->non_deductible_amount,
            'has_receipt' => $expense->has_receipt,
            'formatted_description' => $expense->formatted_description,
        ];
    }

    /**
     * Validate if record data has required fields based on relation type.
     *
     * @param \Illuminate\Database\Eloquent\Relations\HasMany $relation
     * @param array $recordData
     * @return bool
     */
    private function isValidRecordData($relation, array $recordData): bool
    {
        $modelClass = get_class($relation->getRelated());
        
        // Check required fields based on model type
        switch ($modelClass) {
            case ClientProject::class:
                // For projects, ensure we have at least project_name
                return !empty($recordData['project_name']) || !empty($recordData['name']);
            
            case ClientAsset::class:
                // For assets, ensure we have at least asset_name
                return !empty($recordData['asset_name']) || !empty($recordData['name']);
            
            case ClientExpense::class:
                // For expenses, check required fields (only category and amount are truly required)
                $hasCategory = !empty($recordData['category']);
                $hasAmount = isset($recordData['amount']) && $recordData['amount'] !== '' && $recordData['amount'] !== null;
                
                Log::info('Validating expense record', [
                    'has_category' => $hasCategory,
                    'has_amount' => $hasAmount,
                    'category' => $recordData['category'] ?? null,
                    'amount' => $recordData['amount'] ?? null,
                    'particulars' => $recordData['particulars'] ?? null,
                    'tax_payer' => $recordData['tax_payer'] ?? $recordData['taxPayer'] ?? null,
                    'full_record' => $recordData
                ]);
                
                // Only require category and amount - other fields can be empty
                return $hasCategory && $hasAmount;
            
            default:
                // For other types, allow if not completely empty
                return !empty(array_filter($recordData, function($value) {
                    return !is_null($value) && $value !== '';
                }));
        }
    }

    /**
     * Get client statistics and summary data.
     *
     * @param int $clientId
     * @return array
     */
    public function getClientStatistics(int $clientId): array
    {
        $client = Client::with(['assets', 'expenses', 'projects'])->findOrFail($clientId);

        return [
            'total_assets' => $client->assets->count(),
            'total_asset_value' => $client->assets->sum('cost_of_acquisition'),
            'total_business_asset_value' => $client->assets->sum('business_cost'),
            'total_expenses' => $client->expenses->count(),
            'total_expense_amount' => $client->expenses->sum('amount'),
            'total_deductible_expenses' => $client->expenses->sum('deductible_amount'),
            'active_projects' => $client->projects->where('status', 'in_progress')->count(),
            'completed_projects' => $client->projects->where('status', 'completed')->count(),
            'overdue_projects' => $client->projects->filter(fn($p) => $p->is_overdue)->count(),
        ];
    }

    /**
     * Validate client data integrity.
     *
     * @param array $data
     * @return array
     */
    public function validateClientData(array $data): array
    {
        $errors = [];

        // Validate spouse data consistency
        if (!empty($data['spouse']) && (!isset($data['marital_status']) || $data['marital_status'] !== 'married')) {
            $errors['spouse'] = 'Spouse information can only be provided for married clients.';
        }

        // Validate asset percentages
        if (!empty($data['assets'])) {
            foreach ($data['assets'] as $index => $asset) {
                if (isset($asset['percentage_used_in_business']) && 
                    ($asset['percentage_used_in_business'] < 0 || $asset['percentage_used_in_business'] > 100)) {
                    $errors["assets.{$index}.percentage_used_in_business"] = 'Business usage percentage must be between 0 and 100.';
                }
            }
        }

        // Validate expense deductible percentages
        if (!empty($data['expenses'])) {
            foreach ($data['expenses'] as $index => $expense) {
                if (isset($expense['deductible_percentage']) && 
                    ($expense['deductible_percentage'] < 0 || $expense['deductible_percentage'] > 100)) {
                    $errors["expenses.{$index}.deductible_percentage"] = 'Deductible percentage must be between 0 and 100.';
                }
            }
        }

        return $errors;
    }

    /**
     * Perform bulk operations on multiple clients.
     *
     * @param array $clientIds
     * @param string $operation
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function performBulkOperation(array $clientIds, string $operation, array $data = []): array
    {
        try {
            return DB::transaction(function () use ($clientIds, $operation, $data) {
                $processed = 0;
                $errors = [];

                switch ($operation) {
                    case 'activate':
                        $processed = Client::whereIn('id', $clientIds)->update(['status' => 'active']);
                        $message = "Successfully activated {$processed} clients.";
                        break;

                    case 'deactivate':
                        $processed = Client::whereIn('id', $clientIds)->update(['status' => 'inactive']);
                        $message = "Successfully deactivated {$processed} clients.";
                        break;

                    case 'archive':
                        $processed = Client::whereIn('id', $clientIds)->update(['status' => 'archived']);
                        $message = "Successfully archived {$processed} clients.";
                        break;

                    case 'delete':
                        foreach ($clientIds as $clientId) {
                            try {
                                $this->deleteClient($clientId);
                                $processed++;
                            } catch (Exception $e) {
                                $errors[] = "Failed to delete client {$clientId}: " . $e->getMessage();
                            }
                        }
                        $message = "Successfully deleted {$processed} clients.";
                        break;

                    case 'assign_user':
                        if (empty($data['user_id'])) {
                            throw new Exception('User ID is required for assignment operation.');
                        }
                        $processed = Client::whereIn('id', $clientIds)->update(['user_id' => $data['user_id']]);
                        $message = "Successfully assigned {$processed} clients to user.";
                        break;

                    case 'update_status':
                        if (empty($data['status'])) {
                            throw new Exception('Status is required for status update operation.');
                        }
                        $processed = Client::whereIn('id', $clientIds)->update(['status' => $data['status']]);
                        $message = "Successfully updated status for {$processed} clients.";
                        break;

                    case 'send_email':
                        if (empty($data['subject']) || empty($data['message'])) {
                            throw new Exception('Subject and message are required for email operation.');
                        }
                        $result = $this->bulkEmailService->sendBulkEmails(
                            $clientIds,
                            $data['subject'],
                            $data['message']
                        );
                        $processed = $result['sent'];
                        $errors = array_map(fn($error) => "Failed to send email to client {$error['client_id']}: {$error['error']}", $result['errors']);
                        $message = "Successfully sent emails to {$processed} clients.";
                        break;

                    case 'export_selected':
                        $format = $data['format'] ?? 'excel';
                        $exportResponse = $this->bulkExportService->exportSelectedClients($clientIds, $format);
                        $processed = count($clientIds);
                        $message = "Successfully prepared export data for {$processed} clients.";
                        return [
                            'processed' => $processed,
                            'message' => $message,
                            'errors' => [],
                            'export_response' => $exportResponse
                        ];
                        break;

                    case 'request_documents':
                        if (empty($data['document_types'])) {
                            throw new Exception('Document types are required for document request operation.');
                        }
                        $result = $this->bulkEmailService->sendDocumentRequests(
                            $clientIds,
                            $data['document_types']
                        );
                        $processed = $result['sent'];
                        $errors = array_map(fn($error) => "Failed to request documents from client {$error['client_id']}: {$error['error']}", $result['errors']);
                        $message = "Successfully sent document requests to {$processed} clients.";
                        break;

                    default:
                        throw new Exception("Unknown bulk operation: {$operation}");
                }

                return [
                    'processed' => $processed,
                    'message' => $message,
                    'errors' => $errors
                ];
            });
        } catch (Exception $e) {
            Log::error('Bulk operation failed', [
                'operation' => $operation,
                'client_ids' => $clientIds,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Export clients data in various formats.
     *
     * @param array $filters
     * @param string $format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportClients(array $filters = [], string $format = 'excel')
    {
        $query = Client::with(['spouse', 'employees', 'projects', 'assets', 'expenses']);

        // Apply filters
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('phone', 'LIKE', "%{$searchTerm}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to'] . ' 23:59:59');
        }

        $clients = $query->get();

        switch ($format) {
            case 'excel':
                return $this->exportToExcel($clients);
            case 'csv':
                return $this->exportToCsv($clients);
            case 'pdf':
                return $this->exportToPdf($clients);
            default:
                throw new Exception("Unsupported export format: {$format}");
        }
    }

    /**
     * Export clients to Excel format.
     *
     * @param Collection $clients
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function exportToExcel(Collection $clients)
    {
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="clients_export_' . date('Y-m-d_H-i-s') . '.xlsx"',
        ];

        $data = $this->prepareExportData($clients);
        
        // For now, return CSV format as Excel (would need PhpSpreadsheet for proper Excel)
        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            
            // Write headers
            if (!empty($data)) {
                fputcsv($handle, array_keys($data[0]));
                
                // Write data
                foreach ($data as $row) {
                    fputcsv($handle, $row);
                }
            }
            
            fclose($handle);
        }, 'clients_export_' . date('Y-m-d_H-i-s') . '.csv', $headers);
    }

    /**
     * Export clients to CSV format.
     *
     * @param Collection $clients
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function exportToCsv(Collection $clients)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="clients_export_' . date('Y-m-d_H-i-s') . '.csv"',
        ];

        $data = $this->prepareExportData($clients);

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            
            // Write headers
            if (!empty($data)) {
                fputcsv($handle, array_keys($data[0]));
                
                // Write data
                foreach ($data as $row) {
                    fputcsv($handle, $row);
                }
            }
            
            fclose($handle);
        }, 'clients_export_' . date('Y-m-d_H-i-s') . '.csv', $headers);
    }

    /**
     * Export clients to PDF format.
     *
     * @param Collection $clients
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function exportToPdf(Collection $clients)
    {
        // For now, return a simple text-based PDF content
        // In a real implementation, you would use a PDF library like TCPDF or DomPDF
        
        $content = "Client Export Report\n";
        $content .= "Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($clients as $client) {
            $content .= "ID: {$client->id}\n";
            $content .= "Name: {$client->full_name}\n";
            $content .= "Email: {$client->email}\n";
            $content .= "Phone: {$client->phone}\n";
            $content .= "Status: {$client->status}\n";
            $content .= "Registration Date: {$client->created_at->format('Y-m-d')}\n";
            $content .= str_repeat('-', 50) . "\n";
        }

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="clients_export_' . date('Y-m-d_H-i-s') . '.pdf"',
        ]);
    }

    /**
     * Prepare data for export.
     *
     * @param Collection $clients
     * @return array
     */
    private function prepareExportData(Collection $clients): array
    {
        $data = [];

        foreach ($clients as $client) {
            $data[] = [
                'ID' => $client->id,
                'First Name' => $client->first_name,
                'Middle Name' => $client->middle_name,
                'Last Name' => $client->last_name,
                'Email' => $client->email,
                'Phone' => $client->phone,
                'Mobile Number' => $client->mobile_number,
                'Work Number' => $client->work_number,
                'SSN' => $client->ssn,
                'Date of Birth' => $client->date_of_birth?->format('Y-m-d'),
                'Marital Status' => $client->marital_status,
                'Occupation' => $client->occupation,
                'Insurance Covered' => $client->insurance_covered ? 'Yes' : 'No',
                'Street No' => $client->street_no,
                'Apartment No' => $client->apartment_no,
                'City' => $client->city,
                'State' => $client->state,
                'Zip Code' => $client->zip_code,
                'Country' => $client->country,
                'Visa Status' => $client->visa_status,
                'Date of Entry US' => $client->date_of_entry_us?->format('Y-m-d'),
                'Status' => $client->status,
                'Has Spouse' => $client->spouse ? 'Yes' : 'No',
                'Projects Count' => $client->projects->count(),
                'Assets Count' => $client->assets->count(),
                'Expenses Count' => $client->expenses->count(),
                'Registration Date' => $client->created_at->format('Y-m-d H:i:s'),
                'Last Updated' => $client->updated_at->format('Y-m-d H:i:s'),
            ];
        }

        return $data;
    }

    /**
     * Archive inactive clients based on retention policy.
     *
     * @param int $inactiveDays Number of days of inactivity before archiving
     * @return array
     */
    public function archiveInactiveClients(int $inactiveDays = 365): array
    {
        $cutoffDate = now()->subDays($inactiveDays);
        
        $clientsToArchive = Client::where('status', 'inactive')
            ->where('updated_at', '<', $cutoffDate)
            ->get();

        $archived = 0;
        $errors = [];

        foreach ($clientsToArchive as $client) {
            try {
                $oldStatus = $client->status;
                $client->update(['status' => 'archived']);
                $client->logStatusChange($oldStatus, 'archived');
                $archived++;
            } catch (Exception $e) {
                $errors[] = "Failed to archive client {$client->id}: " . $e->getMessage();
            }
        }

        return [
            'archived' => $archived,
            'errors' => $errors,
            'message' => "Successfully archived {$archived} inactive clients."
        ];
    }

    /**
     * Permanently delete archived clients based on retention policy.
     *
     * @param int $archivedDays Number of days in archived status before permanent deletion
     * @return array
     */
    public function deleteArchivedClients(int $archivedDays = 2555): array // ~7 years
    {
        $cutoffDate = now()->subDays($archivedDays);
        
        $clientsToDelete = Client::where('status', 'archived')
            ->where('updated_at', '<', $cutoffDate)
            ->get();

        $deleted = 0;
        $errors = [];

        foreach ($clientsToDelete as $client) {
            try {
                $this->deleteClient($client->id);
                $deleted++;
            } catch (Exception $e) {
                $errors[] = "Failed to delete archived client {$client->id}: " . $e->getMessage();
            }
        }

        return [
            'deleted' => $deleted,
            'errors' => $errors,
            'message' => "Successfully deleted {$deleted} archived clients."
        ];
    }

    /**
     * Get client data retention statistics.
     *
     * @return array
     */
    public function getRetentionStatistics(): array
    {
        $stats = [
            'active_clients' => Client::where('status', 'active')->count(),
            'inactive_clients' => Client::where('status', 'inactive')->count(),
            'archived_clients' => Client::where('status', 'archived')->count(),
            'total_clients' => Client::count(),
        ];

        // Calculate clients eligible for archival (inactive for more than 1 year)
        $archivalCutoff = now()->subDays(365);
        $stats['eligible_for_archival'] = Client::where('status', 'inactive')
            ->where('updated_at', '<', $archivalCutoff)
            ->count();

        // Calculate clients eligible for deletion (archived for more than 7 years)
        $deletionCutoff = now()->subDays(2555);
        $stats['eligible_for_deletion'] = Client::where('status', 'archived')
            ->where('updated_at', '<', $deletionCutoff)
            ->count();

        return $stats;
    }

    /**
     * Check if spouse data contains meaningful values.
     *
     * @param array $data
     * @return bool
     */
    private function hasValidSpouseData(array $data): bool
    {
        if (empty($data['spouse']) || !is_array($data['spouse'])) {
            return false;
        }

        $spouseData = $data['spouse'];
        
        // Check if at least first_name or last_name is provided
        return !empty($spouseData['first_name']) || !empty($spouseData['last_name']);
    }

    /**
     * Transform employee data from frontend format to database format.
     *
     * @param array $employeeData
     * @return array
     */
    private function transformEmployeeDataForStorage(array $employeeData): array
    {
        $transformed = [];
        
        // Map frontend field names to database field names
        $fieldMapping = [
            'employerName' => 'employer_name',
            'employer_name' => 'employer_name', // Handle both formats
            'position' => 'job_title',
            'job_title' => 'job_title', // Handle both formats
            'startDate' => 'start_date',
            'start_date' => 'start_date', // Handle both formats
            'endDate' => 'end_date',
            'end_date' => 'end_date', // Handle both formats
            'employmentType' => 'employment_type',
            'employment_type' => 'employment_type', // Handle both formats
            'salary' => 'annual_salary',
            'annual_salary' => 'annual_salary', // Handle both formats
            'payFrequency' => 'pay_frequency',
            'pay_frequency' => 'pay_frequency', // Handle both formats
            'workLocation' => 'work_location',
            'work_location' => 'work_location', // Handle both formats
            'notes' => 'work_description',
            'work_description' => 'work_description', // Handle both formats
            'employerAddress' => 'employer_address',
            'employer_address' => 'employer_address', // Handle both formats
            'employerCity' => 'employer_city',
            'employer_city' => 'employer_city', // Handle both formats
            'employerState' => 'employer_state',
            'employer_state' => 'employer_state', // Handle both formats
            'employerZipCode' => 'employer_zip_code',
            'employer_zip_code' => 'employer_zip_code', // Handle both formats
            'benefits' => 'benefits',
        ];
        
        // Transform the data
        foreach ($employeeData as $key => $value) {
            if (isset($fieldMapping[$key])) {
                $transformed[$fieldMapping[$key]] = $value;
            } elseif (in_array($key, ['id', 'client_id'])) {
                // Keep these fields as-is
                $transformed[$key] = $value;
            }
        }
        
        return $transformed;
    }
}