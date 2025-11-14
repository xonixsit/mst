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
        Schema::table('client_employees', function (Blueprint $table) {
            // Add missing fields that the frontend is trying to save
            $table->string('employer_name')->nullable()->after('client_id');
            $table->string('pay_frequency')->nullable()->after('annual_salary');
            $table->string('work_location')->nullable()->after('pay_frequency');
            $table->json('benefits')->nullable()->after('work_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_employees', function (Blueprint $table) {
            $table->dropColumn(['employer_name', 'pay_frequency', 'work_location', 'benefits']);
        });
    }
};