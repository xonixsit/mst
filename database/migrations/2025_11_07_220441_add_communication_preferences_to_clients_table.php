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
            $table->json('communication_preferences')->nullable()->after('last_export_at');
            $table->timestamp('last_communication_at')->nullable()->after('communication_preferences');
            $table->string('preferred_communication_method')->default('email')->after('last_communication_at');
            $table->boolean('email_notifications_enabled')->default(true)->after('preferred_communication_method');
            $table->boolean('sms_notifications_enabled')->default(false)->after('email_notifications_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'communication_preferences',
                'last_communication_at',
                'preferred_communication_method',
                'email_notifications_enabled',
                'sms_notifications_enabled'
            ]);
        });
    }
};