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
        Schema::create('client_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            
            // Expense Information
            $table->enum('category', ['miscellaneous', 'residency', 'business', 'medical', 'education', 'charitable', 'travel', 'office', 'professional', 'other']);
            $table->string('particulars'); // Description of the expense
            $table->string('tax_payer'); // Who incurred the expense
            $table->string('spouse')->nullable(); // Spouse involvement if applicable
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->text('remarks')->nullable();
            $table->string('receipt_number')->nullable();
            $table->boolean('is_deductible')->default(true);
            $table->decimal('deductible_percentage', 5, 2)->default(100); // 0-100%
            
            $table->timestamps();
            
            // Indexes for optimal query performance
            $table->index(['client_id']);
            $table->index(['category']);
            $table->index(['expense_date']);
            $table->index(['is_deductible']);
            $table->index(['tax_payer']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_expenses');
    }
};