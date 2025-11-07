<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@mysupertax.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create tax professional user
        User::create([
            'name' => 'Tax Professional',
            'email' => 'taxpro@mysupertax.com',
            'password' => Hash::make('password'),
            'role' => 'tax_professional',
            'email_verified_at' => now(),
        ]);

        // Create sample client user
        User::create([
            'name' => 'John Doe',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'email_verified_at' => now(),
        ]);
    }
}