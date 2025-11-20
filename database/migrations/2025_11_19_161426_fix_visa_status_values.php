<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix invalid visa status values to match the enum
        $mappings = [
            'L1' => 'l1',
            'H1B' => 'h1b',
            'F1' => 'f1',
            'Green Card' => 'permanent_resident',
            'H4' => 'h4',
            'F2' => 'f2',
            'J1' => 'j1',
            'J2' => 'j2',
            'L2' => 'l2',
            'O1' => 'o1',
            'O2' => 'o2',
            'E1' => 'e1',
            'E2' => 'e2',
            'TN' => 'tn',
            'B1/B2' => 'b1_b2',
            'B1-B2' => 'b1_b2',
            'K1' => 'k1',
            'K3' => 'k3',
            'Citizen' => 'citizen',
            'US Citizen' => 'citizen',
            'Permanent Resident' => 'permanent_resident',
        ];

        foreach ($mappings as $oldValue => $newValue) {
            DB::table('clients')
                ->where('visa_status', $oldValue)
                ->update(['visa_status' => $newValue]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the mappings if needed
        $reverseMappings = [
            'l1' => 'L1',
            'h1b' => 'H1B',
            'f1' => 'F1',
            'permanent_resident' => 'Green Card',
        ];

        foreach ($reverseMappings as $newValue => $oldValue) {
            DB::table('clients')
                ->where('visa_status', $newValue)
                ->update(['visa_status' => $oldValue]);
        }
    }
};