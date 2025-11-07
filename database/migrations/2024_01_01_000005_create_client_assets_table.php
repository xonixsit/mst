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
        Schema::create('client_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            
            // Asset Information
            $table->string('asset_name');
            $table->date('date_of_purchase');
            $table->decimal('percentage_used_in_business', 5, 2)->default(0); // 0-100%
            $table->decimal('cost_of_acquisition', 12, 2);
            $table->decimal('any_reimbursement', 12, 2)->nullable()->default(0);
            $table->string('asset_type')->nullable(); // equipment, vehicle, property, etc.
            $table->text('description')->nullable();
            $table->decimal('current_value', 12, 2)->nullable();
            $table->decimal('depreciation_rate', 5, 2)->nullable();
            
            $table->timestamps();
            
            // Indexes for optimal query performance
            $table->index(['client_id']);
            $table->index(['asset_type']);
            $table->index(['date_of_purchase']);
            $table->index(['percentage_used_in_business']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_assets');
    }
};