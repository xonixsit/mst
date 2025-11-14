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
            // Change date columns that need encryption to text columns
            $table->text('date_of_birth')->nullable()->change();
            $table->text('date_of_entry_us')->nullable()->change();
            
            // Change phone number columns to text to accommodate encryption
            $table->text('mobile_number')->nullable()->change();
            $table->text('work_number')->nullable()->change();
            
            // Change SSN to text to accommodate encryption
            $table->text('ssn')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Revert back to original column types
            $table->date('date_of_birth')->change();
            $table->date('date_of_entry_us')->nullable()->change();
            $table->string('mobile_number')->change();
            $table->string('work_number')->nullable()->change();
            $table->string('ssn')->unique()->change();
        });
    }
};