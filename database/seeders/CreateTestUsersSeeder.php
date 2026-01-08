<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateTestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@mysupertax.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'username' => 'admin',
                'password' => Hash::make('Admin@123456'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Tax Professional user
        User::firstOrCreate(
            ['email' => 'taxpro@mysupertax.com'],
            [
                'first_name' => 'Tax',
                'last_name' => 'Professional',
                'username' => 'taxpro',
                'password' => Hash::make('TaxPro@123456'),
                'role' => 'tax_professional',
                'email_verified_at' => now(),
            ]
        );

        // Client user
        User::firstOrCreate(
            ['email' => 'client@mysupertax.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Client',
                'username' => 'client',
                'password' => Hash::make('Client@123456'),
                'role' => 'client',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Test users created successfully!');
        $this->command->line('');
        $this->command->line('Admin Credentials:');
        $this->command->line('  Email: admin@mysupertax.com');
        $this->command->line('  Password: Admin@123456');
        $this->command->line('');
        $this->command->line('Tax Professional Credentials:');
        $this->command->line('  Email: taxpro@mysupertax.com');
        $this->command->line('  Password: TaxPro@123456');
        $this->command->line('');
        $this->command->line('Client Credentials:');
        $this->command->line('  Email: client@mysupertax.com');
        $this->command->line('  Password: Client@123456');
    }
}
