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
        Schema::create('client_spouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            
            // Spouse Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('ssn')->nullable()->unique();
            $table->date('date_of_birth')->nullable();
            $table->string('occupation')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            
            $table->timestamps();
            
            // Indexes for optimal query performance
            $table->index(['client_id']);
            $table->index(['ssn']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_spouses');
    }
};