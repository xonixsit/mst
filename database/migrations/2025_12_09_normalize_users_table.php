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
        // Create tax_professionals table
        Schema::create('tax_professionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('license_number')->nullable();
            $table->json('specializations')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id']);
        });

        // Remove tax professional fields from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'license_number',
                'specializations',
                'bio'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_professionals');

        Schema::table('users', function (Blueprint $table) {
            $table->string('license_number')->nullable()->after('zip_code');
            $table->json('specializations')->nullable()->after('license_number');
            $table->text('bio')->nullable()->after('specializations');
        });
    }
};
