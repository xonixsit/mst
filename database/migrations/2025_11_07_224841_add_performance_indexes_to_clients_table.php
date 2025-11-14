<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to clients table if they don't exist
        $this->addIndexIfNotExists('clients', ['status'], 'clients_status_index');
        $this->addIndexIfNotExists('clients', ['created_at'], 'clients_created_at_index');
        $this->addIndexIfNotExists('clients', ['updated_at'], 'clients_updated_at_index');
        $this->addIndexIfNotExists('clients', ['status', 'created_at'], 'clients_status_created_index');
        $this->addIndexIfNotExists('clients', ['user_id', 'status'], 'clients_user_status_index');
        $this->addIndexIfNotExists('clients', ['first_name', 'last_name'], 'clients_name_index');
        $this->addIndexIfNotExists('clients', ['first_name', 'last_name', 'email'], 'clients_search_index');
        $this->addIndexIfNotExists('clients', ['archived_at'], 'clients_archived_at_index');
        $this->addIndexIfNotExists('clients', ['status', 'archived_at'], 'clients_status_archived_index');

        // Add indexes to core tables that we know exist
        if (Schema::hasTable('invoices')) {
            $this->addIndexIfNotExists('invoices', ['client_id'], 'invoices_client_id_index');
            $this->addIndexIfNotExists('invoices', ['status'], 'invoices_status_index');
            $this->addIndexIfNotExists('invoices', ['created_at'], 'invoices_created_at_index');
            $this->addIndexIfNotExists('invoices', ['client_id', 'status'], 'invoices_client_status_index');
        }

        if (Schema::hasTable('documents')) {
            $this->addIndexIfNotExists('documents', ['client_id'], 'documents_client_id_index');
            $this->addIndexIfNotExists('documents', ['document_type'], 'documents_document_type_index');
            $this->addIndexIfNotExists('documents', ['created_at'], 'documents_created_at_index');
        }

        if (Schema::hasTable('messages')) {
            $this->addIndexIfNotExists('messages', ['client_id'], 'messages_client_id_index');
            $this->addIndexIfNotExists('messages', ['sender_id'], 'messages_sender_id_index');
            $this->addIndexIfNotExists('messages', ['recipient_id'], 'messages_recipient_id_index');
            $this->addIndexIfNotExists('messages', ['created_at'], 'messages_created_at_index');
        }

        if (Schema::hasTable('audit_logs')) {
            $this->addIndexIfNotExists('audit_logs', ['auditable_type', 'auditable_id'], 'audit_logs_auditable_index');
            $this->addIndexIfNotExists('audit_logs', ['user_id'], 'audit_logs_user_id_index');
            $this->addIndexIfNotExists('audit_logs', ['created_at'], 'audit_logs_created_at_index');
            $this->addIndexIfNotExists('audit_logs', ['event'], 'audit_logs_event_index');
        }
    }

    /**
     * Helper method to add index if it doesn't exist.
     */
    private function addIndexIfNotExists(string $table, array $columns, string $indexName): void
    {
        if (!$this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                $table->index($columns, $indexName);
            });
        }
    }

    /**
     * Check if index exists.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $index) {
            if ($index->Key_name === $indexName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        $indexesToDrop = [
            'clients' => [
                'clients_status_index', 'clients_created_at_index', 'clients_updated_at_index',
                'clients_status_created_index', 'clients_user_status_index', 'clients_name_index',
                'clients_search_index', 'clients_archived_at_index', 'clients_status_archived_index'
            ],
            'invoices' => ['invoices_client_id_index', 'invoices_status_index', 'invoices_created_at_index', 'invoices_client_status_index'],
            'documents' => ['documents_client_id_index', 'documents_document_type_index', 'documents_created_at_index'],
            'messages' => ['messages_client_id_index', 'messages_sender_id_index', 'messages_recipient_id_index', 'messages_created_at_index'],
            'audit_logs' => ['audit_logs_auditable_index', 'audit_logs_user_id_index', 'audit_logs_created_at_index', 'audit_logs_event_index']
        ];

        foreach ($indexesToDrop as $table => $indexes) {
            if (Schema::hasTable($table)) {
                foreach ($indexes as $indexName) {
                    $this->dropIndexIfExists($table, $indexName);
                }
            }
        }
    }

    /**
     * Helper method to drop index if it exists.
     */
    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }
};