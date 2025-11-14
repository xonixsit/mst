<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientSearchRequest;
use App\Http\Requests\BulkClientOperationRequest;
use App\Http\Requests\CsvImportRequest;
use App\Services\ClientInformationService;
use App\Services\CsvImportService;
use App\Services\ComprehensiveExportService;
use App\Services\AuditService;
use App\Services\CommunicationService;
use App\Repositories\ClientRepository;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function __construct(
        private ClientInformationService $clientService,
        private CsvImportService $csvImportService,
        private ComprehensiveExportService $comprehensiveExportService,
        private ClientRepository $clientRepository,
        private AuditService $auditService
    ) {}

    /**
     * Display the admin client list with advanced features
     */
    public function index(ClientSearchRequest $request): Response
    {
        $filters = $request->validated();
        
        $clients = $this->clientRepository->getClientsWithFilters(
            $filters,
            $request->get('per_page', 25),
            $request->get('sort_by', 'created_at'),
            $request->get('sort_direction', 'desc')
        );

        $stats = $this->clientRepository->getClientStats();

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients,
            'filters' => $filters,
            'stats' => $stats,
            'sortOptions' => [
                'id' => 'ID',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'email' => 'Email',
                'phone' => 'Phone',
                'created_at' => 'Registration Date',
                'status' => 'Status'
            ],
            'statusOptions' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
                'archived' => 'Archived'
            ],
            'perPageOptions' => [10, 25, 50, 100],
            'availableUsers' => \App\Models\User::select('id', 'name', 'email')->get()
        ]);
    }

    /**
     * Show the form for creating a new client
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Clients/Create');
    }

    /**
     * Store a newly created client
     */
    public function store(Request $request)
    {
        $client = $this->clientService->createClient($request->all());
        
        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified client
     */
    public function show(int $id): Response
    {
        $client = $this->clientRepository->getClientWithRelations($id);
        
        if (!$client) {
            abort(404, 'Client not found');
        }

        return Inertia::render('Admin/Clients/Show', [
            'client' => $client
        ]);
    }

    /**
     * Show the form for editing the specified client
     */
    public function edit(int $id): Response
    {
        $client = $this->clientRepository->getClientWithRelations($id);
        
        if (!$client) {
            abort(404, 'Client not found');
        }

        return Inertia::render('Admin/Clients/Edit', [
            'client' => $client
        ]);
    }

    /**
     * Update the specified client
     */
    public function update(Request $request, int $id)
    {
        $client = $this->clientService->updateClient($id, $request->all());
        
        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified client
     */
    public function destroy(int $id)
    {
        $this->clientService->deleteClient($id);
        
        return redirect()->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * Handle bulk operations on selected clients
     */
    public function bulkOperation(BulkClientOperationRequest $request)
    {
        $validated = $request->validated();
        
        // Log the bulk operation attempt
        $this->auditService->logBulkOperation(
            Client::class,
            $validated['client_ids'],
            $validated['operation'],
            [
                'user_id' => Auth::id(),
                'operation_data' => $validated['data'] ?? [],
                'client_count' => count($validated['client_ids'])
            ]
        );
        
        $result = $this->clientService->performBulkOperation(
            $validated['client_ids'],
            $validated['operation'],
            $validated['data'] ?? []
        );

        // Handle export operations differently
        if ($validated['operation'] === 'export_selected' && isset($result['export_response'])) {
            return $result['export_response'];
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'processed' => $result['processed'],
            'errors' => $result['errors'] ?? []
        ]);
    }

    /**
     * Export client data
     */
    public function export(Request $request)
    {
        $filters = $request->only(['search', 'status', 'date_from', 'date_to']);
        $format = $request->get('format', 'excel');
        
        return $this->clientService->exportClients($filters, $format);
    }

    /**
     * Get client statistics for dashboard
     */
    public function getStats()
    {
        return response()->json($this->clientRepository->getClientStats());
    }

    /**
     * Update client status
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,archived'
        ]);

        $client = Client::findOrFail($id);
        $oldStatus = $client->status;
        $newStatus = $request->status;

        if ($oldStatus !== $newStatus) {
            $client->update(['status' => $newStatus]);
            
            // Log the status change
            $client->logStatusChange($oldStatus, $newStatus);
            
            return response()->json([
                'success' => true,
                'message' => "Client status updated to {$newStatus}",
                'client' => $client
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Status is already set to the requested value'
        ]);
    }

    /**
     * Assign client to a user
     */
    public function assignUser(Request $request, int $id)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id'
        ]);

        $client = Client::findOrFail($id);
        $oldUserId = $client->user_id;
        $newUserId = $request->user_id;

        if ($oldUserId !== $newUserId) {
            $client->update(['user_id' => $newUserId]);
            
            // Log the assignment change
            $client->logAssignmentChange($oldUserId, $newUserId);
            
            return response()->json([
                'success' => true,
                'message' => 'Client assignment updated successfully',
                'client' => $client->load('user')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Client is already assigned to the specified user'
        ]);
    }

    /**
     * Get client audit logs
     */
    public function auditLogs(int $id)
    {
        $client = Client::findOrFail($id);
        $logs = $this->auditService->getLogsForModel($client, 100);

        return response()->json([
            'client' => $client,
            'audit_logs' => $logs
        ]);
    }

    /**
     * Get client interaction history
     */
    public function interactionHistory(int $id)
    {
        $client = Client::findOrFail($id);
        
        // Get audit logs related to this client
        $auditLogs = $this->auditService->getLogsForModel($client, 50);
        
        // Format the interaction history with properly formatted changes
        $history = $auditLogs->map(function ($log) {
            return [
                'id' => $log->id,
                'type' => 'audit',
                'event' => $log->event,
                'description' => $log->description,
                'user' => $log->user ? $log->user->name : 'System',
                'changes' => $log->changes, // This uses the getChangesAttribute accessor
                'metadata' => $log->metadata,
                'created_at' => $log->created_at,
            ];
        });

        return response()->json([
            'client' => $client,
            'history' => $history
        ]);
    }



    /**
     * Handle tax documents view
     */
    public function taxDocs(int $id)
    {
        $client = Client::findOrFail($id);
        
        // For now, return a placeholder response
        // In a full implementation, this would integrate with document management
        return Inertia::render('Admin/Clients/TaxDocs', [
            'client' => $client
        ]);
    }

    /**
     * Handle invoices view - redirect to invoice creation with client pre-selected
     */
    public function invoices(int $id)
    {
        $client = Client::findOrFail($id);
        
        // Redirect to invoice creation page with client pre-selected
        return redirect()->route('admin.invoices.create', ['client_id' => $client->id]);
    }

    /**
     * Get client financial summary including invoice history
     */
    public function financialSummary(Client $client)
    {
        $invoiceService = app(InvoiceService::class);
        $client->load(['invoices', 'assets', 'expenses']);
        
        $financialSummary = $invoiceService->getClientFinancialSummary($client);
        
        // Add asset and expense summaries
        $financialSummary['total_asset_value'] = $client->assets->sum('cost_of_acquisition');
        $financialSummary['total_business_asset_value'] = $client->assets->sum(function ($asset) {
            return $asset->cost_of_acquisition * ($asset->percentage_used_in_business / 100);
        });
        $financialSummary['total_expense_amount'] = $client->expenses->sum('amount');
        $financialSummary['expense_categories'] = $client->expenses->groupBy('category')->map(function ($expenses, $category) {
            return [
                'category' => $category,
                'count' => $expenses->count(),
                'total_amount' => $expenses->sum('amount'),
            ];
        })->values();
        
        return response()->json(['financial_summary' => $financialSummary]);
    }

    /**
     * Get client communication history and preferences
     */
    public function communicationSummary(Client $client)
    {
        $communicationService = app(CommunicationService::class);
        
        $communicationHistory = $communicationService->getCommunicationHistory($client);
        $preferences = $client->getCommunicationPreferences();
        
        $summary = [
            'preferences' => $preferences,
            'history' => $communicationHistory,
            'notification_settings' => [
                'email_enabled' => $client->email_notifications_enabled,
                'sms_enabled' => $client->sms_notifications_enabled,
                'preferred_method' => $client->preferred_communication_method,
            ],
        ];

        return response()->json(['communication_summary' => $summary]);
    }

    /**
     * Update client communication preferences
     */
    public function updateCommunicationPreferences(Request $request, Client $client)
    {
        $request->validate([
            'email_notifications_enabled' => 'boolean',
            'sms_notifications_enabled' => 'boolean',
            'preferred_communication_method' => 'in:email,sms,phone',
            'communication_preferences' => 'array',
            'communication_preferences.email_notifications' => 'boolean',
            'communication_preferences.document_notifications' => 'boolean',
            'communication_preferences.invoice_notifications' => 'boolean',
            'communication_preferences.reminder_notifications' => 'boolean',
            'communication_preferences.notification_frequency' => 'in:immediate,daily,weekly',
        ]);

        $client->update([
            'email_notifications_enabled' => $request->get('email_notifications_enabled', true),
            'sms_notifications_enabled' => $request->get('sms_notifications_enabled', false),
            'preferred_communication_method' => $request->get('preferred_communication_method', 'email'),
        ]);

        if ($request->has('communication_preferences')) {
            $client->updateCommunicationPreferences($request->communication_preferences);
        }

        return response()->json(['message' => 'Communication preferences updated successfully']);
    }

    /**
     * Archive a client and related data
     */
    public function archive(int $id)
    {
        $client = Client::findOrFail($id);
        $oldStatus = $client->status;
        
        if ($oldStatus === 'archived') {
            return response()->json([
                'success' => false,
                'message' => 'Client is already archived'
            ]);
        }

        $client->update(['status' => 'archived']);
        
        // Log the archival
        $client->logStatusChange($oldStatus, 'archived');
        
        return response()->json([
            'success' => true,
            'message' => 'Client archived successfully',
            'client' => $client
        ]);
    }

    /**
     * Restore an archived client
     */
    public function restore(int $id)
    {
        $client = Client::findOrFail($id);
        $oldStatus = $client->status;
        
        if ($oldStatus !== 'archived') {
            return response()->json([
                'success' => false,
                'message' => 'Client is not archived'
            ]);
        }

        $client->update(['status' => 'active']);
        
        // Log the restoration
        $client->logStatusChange($oldStatus, 'active');
        
        return response()->json([
            'success' => true,
            'message' => 'Client restored successfully',
            'client' => $client
        ]);
    }

    /**
     * Get archived clients list
     */
    public function archived(Request $request)
    {
        $filters = array_merge($request->only(['search', 'date_from', 'date_to']), ['status' => 'archived']);
        
        $clients = $this->clientRepository->getClientsWithFilters(
            $filters,
            $request->get('per_page', 25),
            $request->get('sort_by', 'updated_at'),
            $request->get('sort_direction', 'desc')
        );

        return Inertia::render('Admin/Clients/Archived', [
            'clients' => $clients,
            'filters' => $filters
        ]);
    }

    /**
     * Get client retention statistics
     */
    public function retentionStats()
    {
        $stats = $this->clientService->getRetentionStatistics();
        
        return response()->json($stats);
    }

    /**
     * Execute retention policy operations
     */
    public function executeRetentionPolicy(Request $request)
    {
        $request->validate([
            'operation' => 'required|in:archive_inactive,delete_archived',
            'days' => 'nullable|integer|min:1'
        ]);

        $operation = $request->operation;
        $days = $request->days;

        try {
            switch ($operation) {
                case 'archive_inactive':
                    $result = $this->clientService->archiveInactiveClients($days ?? 365);
                    break;
                case 'delete_archived':
                    $result = $this->clientService->deleteArchivedClients($days ?? 2555);
                    break;
                default:
                    throw new Exception("Unknown retention operation: {$operation}");
            }

            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show CSV import interface
     */
    public function importCsv(): Response
    {
        return Inertia::render('Admin/Clients/Import', [
            'availableUsers' => \App\Models\User::select('id', 'name', 'email')->get(),
            'sampleHeaders' => $this->csvImportService->getSampleHeaders()
        ]);
    }

    /**
     * Process CSV import with preview and validation
     */
    public function processCsvImport(CsvImportRequest $request)
    {
        $validated = $request->validated();
        
        try {
            $options = [
                'preview' => $validated['preview'] ?? false,
                'skip_invalid' => $validated['skip_invalid'] ?? true,
                'update_existing' => $validated['update_existing'] ?? false,
                'assign_to_user' => $validated['assign_to_user'] ?? null
            ];

            $result = $this->csvImportService->importClientsFromCsv(
                $request->file('csv_file'),
                $options
            );

            // Log the import attempt
            $this->auditService->logBulkOperation(
                Client::class,
                [],
                'csv_import',
                [
                    'user_id' => Auth::id(),
                    'filename' => $request->file('csv_file')->getClientOriginalName(),
                    'preview' => $options['preview'],
                    'result' => $result
                ]
            );

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
                'imported' => 0,
                'errors' => [$e->getMessage()],
                'skipped' => 0
            ], 500);
        }
    }

    /**
     * Download CSV import template
     */
    public function downloadCsvTemplate()
    {
        return $this->csvImportService->generateCsvTemplate();
    }

    /**
     * Get import statistics for uploaded CSV
     */
    public function getCsvImportStats(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240'
        ]);

        try {
            // Parse CSV for statistics only
            $csvData = $this->csvImportService->parseCsvFile($request->file('csv_file'));
            $stats = $this->csvImportService->getImportStatistics($csvData);

            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show comprehensive export interface
     */
    public function exportInterface(): Response
    {
        return Inertia::render('Admin/Clients/Export', [
            'availableTemplates' => $this->comprehensiveExportService->getAvailableTemplates(),
            'availableUsers' => \App\Models\User::select('id', 'name', 'email')->get()
        ]);
    }

    /**
     * Process comprehensive export with customizable options
     */
    public function processComprehensiveExport(Request $request)
    {
        $request->validate([
            'client_ids' => 'required|array|min:1',
            'client_ids.*' => 'exists:clients,id',
            'format' => 'required|in:excel,csv,pdf,json',
            'template' => 'required|in:basic,comprehensive,financial,contact,custom',
            'custom_fields' => 'array',
            'include_metadata' => 'boolean',
            'include_summary' => 'boolean',
            'include_relationships' => 'boolean'
        ]);

        try {
            $options = [
                'template' => $request->template,
                'custom_fields' => $request->custom_fields ?? [],
                'include_metadata' => $request->include_metadata ?? false,
                'include_summary' => $request->include_summary ?? false,
                'include_relationships' => $request->include_relationships ?? false
            ];

            // Log the export attempt
            $this->auditService->logBulkOperation(
                Client::class,
                $request->client_ids,
                'comprehensive_export',
                [
                    'user_id' => Auth::id(),
                    'format' => $request->format,
                    'template' => $request->template,
                    'client_count' => count($request->client_ids)
                ]
            );

            return $this->comprehensiveExportService->exportClients(
                $request->client_ids,
                $request->format,
                $options
            );

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get export statistics and preview
     */
    public function getExportStats(Request $request)
    {
        $request->validate([
            'client_ids' => 'required|array|min:1',
            'client_ids.*' => 'exists:clients,id'
        ]);

        try {
            $stats = $this->comprehensiveExportService->getExportStatistics($request->client_ids);

            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Schedule automated export
     */
    public function scheduleAutomatedExport(Request $request)
    {
        $request->validate([
            'frequency' => 'required|in:daily,weekly,monthly',
            'format' => 'required|in:excel,csv,pdf',
            'template' => 'required|in:basic,comprehensive,financial,contact',
            'recipients' => 'array',
            'recipients.*' => 'email',
            'filters' => 'array'
        ]);

        try {
            $options = [
                'frequency' => $request->frequency,
                'format' => $request->format,
                'template' => $request->template,
                'recipients' => $request->recipients ?? [],
                'filters' => $request->filters ?? []
            ];

            $result = $this->comprehensiveExportService->scheduleAutomatedExport($options);

            // Log the scheduling
            $this->auditService->logBulkOperation(
                Client::class,
                [],
                'schedule_automated_export',
                [
                    'user_id' => Auth::id(),
                    'schedule_options' => $options
                ]
            );

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule export: ' . $e->getMessage()
            ], 500);
        }
    }
}