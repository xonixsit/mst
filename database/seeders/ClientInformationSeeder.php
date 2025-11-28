<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientAsset;
use App\Models\ClientEmployee;
use App\Models\ClientExpense;
use App\Models\ClientProject;
use App\Models\ClientSpouse;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing admin users
        $admin = User::where('role', 'admin')->first();
        $taxProfessional = User::where('role', 'tax_professional')->first();

        // Create clients with comprehensive information
        $clients = Client::factory(10)->create([
            'user_id' => $admin->id,
        ]);

        foreach ($clients as $client) {
            // Add spouse information for married clients
            if ($client->marital_status === 'married') {
                ClientSpouse::factory()->create([
                    'client_id' => $client->id,
                ]);
            }

            // Add employment information
            ClientEmployee::factory(rand(1, 2))->create([
                'client_id' => $client->id,
            ]);

            // Add projects
            ClientProject::factory(rand(1, 3))->create([
                'client_id' => $client->id,
            ]);

            // Add assets
            ClientAsset::factory(rand(2, 5))->create([
                'client_id' => $client->id,
            ]);

            // Add expenses
            ClientExpense::factory(rand(5, 15))->create([
                'client_id' => $client->id,
            ]);
        }
    }
}