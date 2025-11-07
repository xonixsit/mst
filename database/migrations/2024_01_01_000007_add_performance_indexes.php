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
            // Composite indexes for common query patterns
            $table->index(['status', 'user_id'], 'clients_status_user_idx');
            $table->index(['marital_status', 'status'], 'clients_marital_status_idx');
            $table->index(['visa_status', 'status'], 'clients_visa_status_idx');
            $table->index(['created_at', 'status'], 'clients_created_status_idx');
        });

        Schema::table('client_projects', function (Blueprint $table) {
            // Composite indexes for project queries
            $table->index(['client_id', 'status'], 'projects_client_status_idx');
            $table->index(['project_type', 'status'], 'projects_type_status_idx');
            $table->index(['due_date', 'status'], 'projects_due_status_idx');
        });

        Schema::table('client_assets', function (Blueprint $table) {
            // Composite indexes for asset queries
            $table->index(['client_id', 'asset_type'], 'assets_client_type_idx');
            $table->index(['percentage_used_in_business', 'client_id'], 'assets_business_use_idx');
        });

        Schema::table('client_expenses', function (Blueprint $table) {
            // Composite indexes for expense queries
            $table->index(['client_id', 'category'], 'expenses_client_category_idx');
            $table->index(['expense_date', 'category'], 'expenses_date_category_idx');
            $table->index(['is_deductible', 'client_id'], 'expenses_deductible_client_idx');
        });

        Schema::table('client_employees', function (Blueprint $table) {
            // Composite indexes for employee queries
            $table->index(['client_id', 'employment_type'], 'employees_client_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('clients_status_user_idx');
            $table->dropIndex('clients_marital_status_idx');
            $table->dropIndex('clients_visa_status_idx');
            $table->dropIndex('clients_created_status_idx');
        });

        Schema::table('client_projects', function (Blueprint $table) {
            $table->dropIndex('projects_client_status_idx');
            $table->dropIndex('projects_type_status_idx');
            $table->dropIndex('projects_due_status_idx');
        });

        Schema::table('client_assets', function (Blueprint $table) {
            $table->dropIndex('assets_client_type_idx');
            $table->dropIndex('assets_business_use_idx');
        });

        Schema::table('client_expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_client_category_idx');
            $table->dropIndex('expenses_date_category_idx');
            $table->dropIndex('expenses_deductible_client_idx');
        });

        Schema::table('client_employees', function (Blueprint $table) {
            $table->dropIndex('employees_client_type_idx');
        });
    }
};