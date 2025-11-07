<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DataArchivalService
{
    /**
     * Archive inactive clients and their related data.
     *
     * @param int $inactiveDays Number of days of inactivity before archival
     * @return array Statistics about the archival process
     */
    public function archiveInactiveClients(int $inactiveDays = 365): array
    {
        $cutoffDate = Carbon::now()->subDays($inactiveDays);
        $stats = [
            'clients_archived' => 0,
            'documents_archived' => 0,
            'invoices_archived' => 0,
            'messages_archived' => 0,
            'audit_logs_archived' => 0,
            'errors' => [],
        ];

        DB::beginTransaction();

        try {
            // Find inactive clients
            $inactiveClients = Client::where('status', 'inactive')
                ->where('updated_at', '<', $cutoffDate)
                ->whereNull('archived_at')
                ->get();

            foreach ($inactiveClients as $client) {
                $this->archiveClient($client, $stats);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $stats['errors'][] = "Transaction failed: " . $e->getMessage();
        }

        return $stats;
    }

    /**
     * Archive a specific client and all related data.
     *
     * @param Client $client
     * @param array &$stats
     * @return void
     */
    private function archiveClient(Client $client, array &$stats): void
    {
        try {
            // Archive client documents
            $documents = Document::where('client_id', $client->id)->get();
            foreach ($documents as $document) {
                $this->archiveDocument($document);
                $stats['documents_archived']++;
            }

            // Archive client invoices
            $invoices = Invoice::where('client_id', $client->id)->get();
            foreach ($invoices as $invoice) {
                $this->archiveInvoice($invoice);
                $stats['invoices_archived']++;
            }

            // Archive client messages
            $messages = Message::where('client_id', $client->id)->get();
            foreach ($messages as $message) {
                $this->archiveMessage($message);
                $stats['messages_archived']++;
            }

            // Archive audit logs
            $auditLogs = AuditLog::where('auditable_type', Client::class)
                ->where('auditable_id', $client->id)
                ->get();
            foreach ($auditLogs as $auditLog) {
                $this->archiveAuditLog($auditLog);
                $stats['audit_logs_archived']++;
            }

            // Mark client as archived
            $client->update([
                'archived_at' => now(),
                'status' => 'archived'
            ]);

            // Log the archival
            app(AuditService::class)->log($client, 'archived', null, null, [
                'context' => 'data_archival',
                'action' => 'Client archived due to inactivity',
                'archival_reason' => 'inactive_client_policy'
            ]);

            $stats['clients_archived']++;
        } catch (\Exception $e) {
            $stats['errors'][] = "Failed to archive client {$client->id}: " . $e->getMessage();
        }
    }

    /**
     * Archive a document by moving it to archive storage.
     *
     * @param Document $document
     * @return void
     */
    private function archiveDocument(Document $document): void
    {
        if ($document->file_path && Storage::exists($document->file_path)) {
            $archivePath = 'archived/' . $document->file_path;
            Storage::move($document->file_path, $archivePath);
            
            $document->update([
                'file_path' => $archivePath,
                'archived_at' => now()
            ]);
        }
    }

    /**
     * Archive an invoice.
     *
     * @param Invoice $invoice
     * @return void
     */
    private function archiveInvoice(Invoice $invoice): void
    {
        $invoice->update(['archived_at' => now()]);
    }

    /**
     * Archive a message.
     *
     * @param Message $message
     * @return void
     */
    private function archiveMessage(Message $message): void
    {
        $message->update(['archived_at' => now()]);
    }

    /**
     * Archive an audit log.
     *
     * @param AuditLog $auditLog
     * @return void
     */
    private function archiveAuditLog(AuditLog $auditLog): void
    {
        $auditLog->update(['archived_at' => now()]);
    }

    /**
     * Restore an archived client and all related data.
     *
     * @param Client $client
     * @return array Statistics about the restoration process
     */
    public function restoreArchivedClient(Client $client): array
    {
        $stats = [
            'client_restored' => false,
            'documents_restored' => 0,
            'invoices_restored' => 0,
            'messages_restored' => 0,
            'audit_logs_restored' => 0,
            'errors' => [],
        ];

        if (!$client->archived_at) {
            $stats['errors'][] = 'Client is not archived';
            return $stats;
        }

        DB::beginTransaction();

        try {
            // Restore client documents
            $documents = Document::where('client_id', $client->id)
                ->whereNotNull('archived_at')
                ->get();
            
            foreach ($documents as $document) {
                $this->restoreDocument($document);
                $stats['documents_restored']++;
            }

            // Restore client invoices
            $invoices = Invoice::where('client_id', $client->id)
                ->whereNotNull('archived_at')
                ->get();
            
            foreach ($invoices as $invoice) {
                $invoice->update(['archived_at' => null]);
                $stats['invoices_restored']++;
            }

            // Restore client messages
            $messages = Message::where('client_id', $client->id)
                ->whereNotNull('archived_at')
                ->get();
            
            foreach ($messages as $message) {
                $message->update(['archived_at' => null]);
                $stats['messages_restored']++;
            }

            // Restore audit logs
            $auditLogs = AuditLog::where('auditable_type', Client::class)
                ->where('auditable_id', $client->id)
                ->whereNotNull('archived_at')
                ->get();
            
            foreach ($auditLogs as $auditLog) {
                $auditLog->update(['archived_at' => null]);
                $stats['audit_logs_restored']++;
            }

            // Restore client
            $client->update([
                'archived_at' => null,
                'status' => 'active'
            ]);

            // Log the restoration
            app(AuditService::class)->log($client, 'restored', null, null, [
                'context' => 'data_archival',
                'action' => 'Client restored from archive',
                'restoration_reason' => 'manual_restoration'
            ]);

            $stats['client_restored'] = true;

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $stats['errors'][] = "Failed to restore client: " . $e->getMessage();
        }

        return $stats;
    }

    /**
     * Restore a document from archive storage.
     *
     * @param Document $document
     * @return void
     */
    private function restoreDocument(Document $document): void
    {
        if ($document->file_path && str_starts_with($document->file_path, 'archived/')) {
            $originalPath = str_replace('archived/', '', $document->file_path);
            
            if (Storage::exists($document->file_path)) {
                Storage::move($document->file_path, $originalPath);
                
                $document->update([
                    'file_path' => $originalPath,
                    'archived_at' => null
                ]);
            }
        }
    }

    /**
     * Permanently delete archived data older than specified days.
     *
     * @param int $archiveDays Number of days data should remain in archive before deletion
     * @return array Statistics about the deletion process
     */
    public function permanentlyDeleteArchivedData(int $archiveDays = 2555): array // 7 years default
    {
        $cutoffDate = Carbon::now()->subDays($archiveDays);
        $stats = [
            'clients_deleted' => 0,
            'documents_deleted' => 0,
            'invoices_deleted' => 0,
            'messages_deleted' => 0,
            'audit_logs_deleted' => 0,
            'files_deleted' => 0,
            'errors' => [],
        ];

        DB::beginTransaction();

        try {
            // Find clients archived longer than the retention period
            $clientsToDelete = Client::whereNotNull('archived_at')
                ->where('archived_at', '<', $cutoffDate)
                ->get();

            foreach ($clientsToDelete as $client) {
                $this->permanentlyDeleteClient($client, $stats);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $stats['errors'][] = "Transaction failed: " . $e->getMessage();
        }

        return $stats;
    }

    /**
     * Permanently delete a client and all related data.
     *
     * @param Client $client
     * @param array &$stats
     * @return void
     */
    private function permanentlyDeleteClient(Client $client, array &$stats): void
    {
        try {
            // Delete client documents and files
            $documents = Document::where('client_id', $client->id)->get();
            foreach ($documents as $document) {
                if ($document->file_path && Storage::exists($document->file_path)) {
                    Storage::delete($document->file_path);
                    $stats['files_deleted']++;
                }
                $document->forceDelete();
                $stats['documents_deleted']++;
            }

            // Delete client invoices
            $invoices = Invoice::where('client_id', $client->id)->get();
            foreach ($invoices as $invoice) {
                $invoice->items()->delete(); // Delete invoice items first
                $invoice->forceDelete();
                $stats['invoices_deleted']++;
            }

            // Delete client messages
            $messages = Message::where('client_id', $client->id)->get();
            foreach ($messages as $message) {
                $message->forceDelete();
                $stats['messages_deleted']++;
            }

            // Delete audit logs
            $auditLogs = AuditLog::where('auditable_type', Client::class)
                ->where('auditable_id', $client->id)
                ->get();
            foreach ($auditLogs as $auditLog) {
                $auditLog->delete();
                $stats['audit_logs_deleted']++;
            }

            // Delete related client data
            $client->spouse()->delete();
            $client->employees()->delete();
            $client->projects()->delete();
            $client->assets()->delete();
            $client->expenses()->delete();

            // Finally delete the client
            $client->forceDelete();
            $stats['clients_deleted']++;

        } catch (\Exception $e) {
            $stats['errors'][] = "Failed to delete client {$client->id}: " . $e->getMessage();
        }
    }

    /**
     * Get archival statistics.
     *
     * @return array
     */
    public function getArchivalStats(): array
    {
        return [
            'active_clients' => Client::where('status', 'active')->count(),
            'inactive_clients' => Client::where('status', 'inactive')->count(),
            'archived_clients' => Client::where('status', 'archived')->count(),
            'total_clients' => Client::count(),
            'archived_documents' => Document::whereNotNull('archived_at')->count(),
            'archived_invoices' => Invoice::whereNotNull('archived_at')->count(),
            'archived_messages' => Message::whereNotNull('archived_at')->count(),
            'archived_audit_logs' => AuditLog::whereNotNull('archived_at')->count(),
            'oldest_archived_client' => Client::whereNotNull('archived_at')
                ->orderBy('archived_at')
                ->first()?->archived_at,
            'newest_archived_client' => Client::whereNotNull('archived_at')
                ->orderBy('archived_at', 'desc')
                ->first()?->archived_at,
        ];
    }

    /**
     * Get retention policy recommendations.
     *
     * @return array
     */
    public function getRetentionRecommendations(): array
    {
        $recommendations = [];
        
        // Check for clients that should be archived
        $inactiveClients = Client::where('status', 'inactive')
            ->where('updated_at', '<', Carbon::now()->subDays(365))
            ->whereNull('archived_at')
            ->count();
            
        if ($inactiveClients > 0) {
            $recommendations[] = [
                'type' => 'archive',
                'priority' => 'medium',
                'message' => "{$inactiveClients} inactive clients are eligible for archival (inactive for over 1 year)",
                'action' => 'archive_inactive_clients',
                'count' => $inactiveClients
            ];
        }

        // Check for old archived data that can be deleted
        $oldArchivedClients = Client::whereNotNull('archived_at')
            ->where('archived_at', '<', Carbon::now()->subDays(2555)) // 7 years
            ->count();
            
        if ($oldArchivedClients > 0) {
            $recommendations[] = [
                'type' => 'delete',
                'priority' => 'low',
                'message' => "{$oldArchivedClients} archived clients are eligible for permanent deletion (archived for over 7 years)",
                'action' => 'delete_old_archived_data',
                'count' => $oldArchivedClients
            ];
        }

        // Check storage usage
        $archivedDocuments = Document::whereNotNull('archived_at')->count();
        if ($archivedDocuments > 1000) {
            $recommendations[] = [
                'type' => 'storage',
                'priority' => 'low',
                'message' => "{$archivedDocuments} documents are in archive storage - consider reviewing storage costs",
                'action' => 'review_storage_usage',
                'count' => $archivedDocuments
            ];
        }

        return $recommendations;
    }
}