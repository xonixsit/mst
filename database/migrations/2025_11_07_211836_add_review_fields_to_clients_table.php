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
            $table->timestamp('review_requested_at')->nullable()->after('status');
            $table->json('review_sections')->nullable()->after('review_requested_at');
            $table->text('review_message')->nullable()->after('review_sections');
            $table->enum('review_priority', ['low', 'normal', 'high'])->default('normal')->after('review_message');
            $table->json('section_completion_status')->nullable()->after('review_priority');
            $table->timestamp('last_export_at')->nullable()->after('section_completion_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'review_requested_at',
                'review_sections',
                'review_message',
                'review_priority',
                'section_completion_status',
                'last_export_at'
            ]);
        });
    }
};
