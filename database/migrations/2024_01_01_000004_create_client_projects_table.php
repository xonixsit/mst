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
        Schema::create('client_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            
            // Project Information
            $table->string('project_name');
            $table->text('project_description')->nullable();
            $table->enum('project_type', ['tax_preparation', 'consultation', 'planning', 'audit', 'other']);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes for optimal query performance
            $table->index(['client_id']);
            $table->index(['status']);
            $table->index(['project_type']);
            $table->index(['due_date']);
            $table->index(['start_date', 'completion_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_projects');
    }
};