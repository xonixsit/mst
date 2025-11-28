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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status');
            $table->string('transaction_id')->nullable()->after('payment_method');
            $table->decimal('amount_paid', 10, 2)->nullable()->after('transaction_id');
            $table->text('payment_notes')->nullable()->after('amount_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'transaction_id', 'amount_paid', 'payment_notes']);
        });
    }
};