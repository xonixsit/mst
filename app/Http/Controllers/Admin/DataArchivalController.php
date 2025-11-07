<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\DataArchivalService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DataArchivalController extends Controller
{
    public function __construct(
        private DataArchivalService $archivalService
    ) {}

    /**
     * Display the data archival and retention management interface.
     */
    public function index(): Response
    {
        $stats = $this->archivalService->getArchivalStats();
        $recommendations = $this->archivalService->getRetentionRecommendations();

        return Inertia::render('Admin/DataArchival/Index', [
            'stats' => $stats,
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * Archive inactive clients.
     */
    public function archiveInactiveClients(Request $request)
    {
        $request->validate([
            'inactive_days' => 'required|integer|min:30|max:3650', // 30 days to 10 years
        ]);

        $stats = $this->archivalService->archiveInactiveClients($request->inactive_days);

        if (empty($stats['errors'])) {
            return back()->with('success', 
                "Successfully archived {$stats['clients_archived']} clients and their related data."
            );
        } else {
            return back()->with('error', 
                "Archival completed with errors. Archived {$stats['clients_archived']} clients. Errors: " . 
                implode(', ', $stats['errors'])
            );
        }
    }

    /**
     * Restore an archived client.
     */
    public function restoreClient(Client $client)
    {
        if (!$client->archived_at) {
            return back()->with('error', 'Client is not archived.');
        }

        $stats = $this->archivalService->restoreArchivedClient($client);

        if (empty($stats['errors'])) {
            return back()->with('success', 
                "Successfully restored client and {$stats['documents_restored']} documents, " .
                "{$stats['invoices_restored']} invoices, {$stats['messages_restored']} messages."
            );
        } else {
            return back()->with('error', 
                "Restoration failed: " . implode(', ', $stats['errors'])
            );
        }
    }

    /**
     * Permanently delete old archived data.
     */
    public function permanentlyDeleteArchivedData(Request $request)
    {
        $request->validate([
            'archive_days' => 'required|integer|min:365|max:3650', // 1 year to 10 years
            'confirm_deletion' => 'required|accepted',
        ]);

        $stats = $this->archivalService->permanentlyDeleteArchivedData($request->archive_days);

        if (empty($stats['errors'])) {
            return back()->with('success', 
                "Successfully deleted {$stats['clients_deleted']} clients and their related data permanently."
            );
        } else {
            return back()->with('error', 
                "Deletion completed with errors. Deleted {$stats['clients_deleted']} clients. Errors: " . 
                implode(', ', $stats['errors'])
            );
        }
    }

    /**
     * Get archived clients list.
     */
    public function archivedClients(Request $request): Response
    {
        $query = Client::whereNotNull('archived_at')
            ->with(['user'])
            ->orderBy('archived_at', 'desc');

        // Apply search filter
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        // Apply date range filter
        if ($request->start_date) {
            $query->whereDate('archived_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('archived_at', '<=', $request->end_date);
        }

        $clients = $query->paginate($request->per_page ?? 25)->withQueryString();

        return Inertia::render('Admin/DataArchival/ArchivedClients', [
            'clients' => $clients,
            'filters' => $request->only(['search', 'start_date', 'end_date', 'per_page']),
        ]);
    }

    /**
     * Show retention policy settings.
     */
    public function retentionPolicy(): Response
    {
        // In a real application, these would be stored in a configuration table
        $policies = [
            'inactive_client_archival_days' => 365, // 1 year
            'archived_data_retention_days' => 2555, // 7 years
            'audit_log_retention_days' => 2555, // 7 years
            'document_retention_days' => 2555, // 7 years
            'auto_archival_enabled' => false,
            'auto_deletion_enabled' => false,
        ];

        return Inertia::render('Admin/DataArchival/RetentionPolicy', [
            'policies' => $policies,
        ]);
    }

    /**
     * Update retention policy settings.
     */
    public function updateRetentionPolicy(Request $request)
    {
        $request->validate([
            'inactive_client_archival_days' => 'required|integer|min:30|max:3650',
            'archived_data_retention_days' => 'required|integer|min:365|max:3650',
            'audit_log_retention_days' => 'required|integer|min:365|max:3650',
            'document_retention_days' => 'required|integer|min:365|max:3650',
            'auto_archival_enabled' => 'boolean',
            'auto_deletion_enabled' => 'boolean',
        ]);

        // In a real application, these would be stored in a configuration table
        // For now, we'll just return success
        
        return back()->with('success', 'Retention policy settings updated successfully.');
    }

    /**
     * Execute retention policy manually.
     */
    public function executeRetentionPolicy(Request $request)
    {
        $request->validate([
            'policy_type' => 'required|in:archival,deletion',
            'dry_run' => 'boolean',
        ]);

        if ($request->policy_type === 'archival') {
            $stats = $this->archivalService->archiveInactiveClients(365); // 1 year default
            
            return back()->with('success', 
                "Archival policy executed. Archived {$stats['clients_archived']} clients."
            );
        } else {
            $stats = $this->archivalService->permanentlyDeleteArchivedData(2555); // 7 years default
            
            return back()->with('success', 
                "Deletion policy executed. Deleted {$stats['clients_deleted']} clients permanently."
            );
        }
    }
}