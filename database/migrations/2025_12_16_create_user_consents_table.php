<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('consent_type'); // 'terms', 'privacy', 'marketing', 'analytics', 'cookies'
            $table->boolean('given')->default(false);
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('consent_version')->nullable(); // Store version of policy accepted
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'consent_type']);
            $table->index('created_at');
        });

        Schema::create('consent_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('action'); // 'accepted', 'rejected', 'withdrawn', 'updated'
            $table->json('consents'); // JSON array of consent types and their status
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('source')->nullable(); // 'banner', 'settings', 'registration'
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_logs');
        Schema::dropIfExists('user_consents');
    }
};
