<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Indexes for frequently queried fields
            $table->index(['email'], 'clients_email_index');
            $table->index(['status'], 'clients_status_index');
            $table->index(['user_id'], 'clients_user_id_index');
            $table->index(['created_at'], 'clients_created_at_index');
            $table->index(['updated_at'], 'clients_updated_at_index');
            
            // Composite indexes for common query patterns
            $table->index(['status', 'created_at'], 'clients_status_created_index');
            $table->index(['user_id', 'status'], 'clients_user_status_index');
            $table->index(['first_name', 'last_name'], 'clients_name_index');
            
            // Full-text search index for name and email searches
            $table->index(['first_name', 'last_name', 'email'], 'clients_search_index');
            
            // Index for archived data queries
            $table->index(['archived_at'], 'clients_archived_at_index');
            $table->index(['status', 'archived_at'], 'clients_status_archived_index');
        });

        // Add indexes to related tables
        Schema::table('client_spouses', function (Blueprint $table) {
            $table->index(['client_id'], 'client_spouses_client_id_index');
        });

        Schema::table('client_employees', function (Blueprint $table) {
            $table->index(['client_id'], 'client_employees_client_id_index');
        });

        Schema::table('client_projects', function (Blueprint $table) {
            $table->index(['client_id'], 'client_projects_client_id_index');
            $table->index(['status'], 'client_projects_status_index');
        });

        Schema::table('client_assets', function (Blueprint $table) {
            $table->index(['client_id'], 'client_assets_client_id_index');
            $table->index(['date_of_purchase'], 'client_assets_date_index');
        });

        Schema::table('client_expenses', function (Blueprint $table) {
            $table->index(['client_id'], 'client_expenses_client_id_index');
            $table->index(['category'], 'client_expenses_category_index');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['client_id'], 'invoices_client_id_index');
            $table->index(['status'], 'invoices_status_index');
            $table->index(['created_at'], 'invoices_created_at_index');
            $table->index(['client_id', 'status'], 'invoices_client_status_index');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->index(['client_id'], 'documents_client_id_index');
            $table->index(['type'], 'documents_type_index');
            $table->index(['created_at'], 'documents_created_at_index');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index(['client_id'], 'messages_client_id_index');
            $table->index(['user_id'], 'messages_user_id_index');
            $table->index(['created_at'], 'messages_created_at_index');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['auditable_type', 'auditable_id'], 'audit_logs_auditable_index');
            $table->index(['user_id'], 'audit_logs_user_id_index');
            $table->index(['created_at'], 'audit_logs_created_at_index');
            $table->index(['event'], 'audit_logs_event_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('clients_email_index');
            $table->dropIndex('clients_status_index');
            $table->dropIndex('clients_user_id_index');
            $table->dropIndex('clients_created_at_index');
            $table->dropIndex('clients_updated_at_index');
            $table->dropIndex('clients_status_created_index');
            $table->dropIndex('clients_user_status_index');
            $table->dropIndex('clients_name_index');
            $table->dropIndex('clients_search_index');
            $table->dropIndex('clients_archived_at_index');
            $table->dropIndex('clients_status_archived_index');
        });

        Schema::table('client_spouses', function (Blueprint $table) {
            $table->dropIndex('client_spouses_client_id_index');
        });

        Schema::table('client_employees', function (Blueprint $table) {
            $table->dropIndex('client_employees_client_id_index');
        });

        Schema::table('client_projects', function (Blueprint $table) {
            $table->dropIndex('client_projects_client_id_index');
            $table->dropIndex('client_projects_status_index');
        });

        Schema::table('client_assets', function (Blueprint $table) {
            $table->dropIndex('client_assets_client_id_index');
            $table->dropIndex('client_assets_date_index');
        });

        Schema::table('client_expenses', function (Blueprint $table) {
            $table->dropIndex('client_expenses_client_id_index');
            $table->dropIndex('client_expenses_category_index');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_client_id_index');
            $table->dropIndex('invoices_status_index');
            $table->dropIndex('invoices_created_at_index');
            $table->dropIndex('invoices_client_status_index');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('documents_client_id_index');
            $table->dropIndex('documents_type_index');
            $table->dropIndex('documents_created_at_index');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_client_id_index');
            $table->dropIndex('messages_user_id_index');
            $table->dropIndex('messages_created_at_index');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('audit_logs_auditable_index');
            $table->dropIndex('audit_logs_user_id_index');
            $table->dropIndex('audit_logs_created_at_index');
            $table->dropIndex('audit_logs_event_index');
        });
    }
};