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
        Schema::create('client_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            
            // Employee Information
            $table->string('employer_name');
            $table->string('job_title')->nullable();
            $table->string('employee_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('annual_salary', 12, 2)->nullable();
            $table->string('employment_type')->nullable(); // full-time, part-time, contract
            $table->text('work_description')->nullable();
            
            // Employer Address
            $table->string('employer_address')->nullable();
            $table->string('employer_city')->nullable();
            $table->string('employer_state')->nullable();
            $table->string('employer_zip_code')->nullable();
            
            $table->timestamps();
            
            // Indexes for optimal query performance
            $table->index(['client_id']);
            $table->index(['employer_name']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_employees');
    }
};