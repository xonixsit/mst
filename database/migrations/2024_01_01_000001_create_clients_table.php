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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('ssn')->unique();
            $table->date('date_of_birth');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed']);
            $table->string('occupation')->nullable();
            $table->boolean('insurance_covered')->default(false);
            
            // Address Information
            $table->string('street_no');
            $table->string('apartment_no')->nullable();
            $table->string('zip_code');
            $table->string('city');
            $table->string('state');
            $table->string('country')->default('USA');
            
            // Contact Information
            $table->string('mobile_number');
            $table->string('work_number')->nullable();
            
            // Immigration Information
            $table->string('visa_status')->nullable();
            $table->date('date_of_entry_us')->nullable();
            
            // Status and Metadata
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->timestamps();
            
            // Indexes for optimal query performance
            $table->index(['status', 'created_at']);
            $table->index(['email']);
            $table->index(['ssn']);
            $table->index(['last_name', 'first_name']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};