<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('status')->default('new')->after('phone');
            $table->string('source')->nullable()->after('status');
            $table->text('notes')->nullable()->after('source');
            $table->unsignedBigInteger('assigned_to')->nullable()->after('notes');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['status', 'source', 'notes', 'assigned_to']);
        });
    }
};
