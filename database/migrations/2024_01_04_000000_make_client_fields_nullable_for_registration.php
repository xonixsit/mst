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
            // Make fields nullable that clients can fill in later
            $table->string('phone')->nullable()->change();
            $table->string('ssn')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->change();
            $table->string('street_no')->nullable()->change();
            $table->string('zip_code')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('mobile_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Revert back to not nullable (this might fail if there are null values)
            $table->string('phone')->nullable(false)->change();
            $table->string('ssn')->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable(false)->change();
            $table->string('street_no')->nullable(false)->change();
            $table->string('zip_code')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
            $table->string('state')->nullable(false)->change();
            $table->string('mobile_number')->nullable(false)->change();
        });
    }
};