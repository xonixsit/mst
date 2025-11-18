<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestAdminLogin extends Command
{
    protected $signature = 'test:admin-login';
    protected $description = 'Test admin login functionality';

    public function handle()
    {
        $this->info('Testing admin login functionality...');
        
        // Check users
        $users = User::all();
        $this->info("Total users: " . $users->count());
        
        foreach ($users as $user) {
            $this->info("User: {$user->email} - Role: {$user->role}");
            $this->info("  isAdmin(): " . ($user->isAdmin() ? 'true' : 'false'));
            $this->info("  isTaxProfessional(): " . ($user->isTaxProfessional() ? 'true' : 'false'));
        }
        
        // Test login with first admin user
        $adminUser = User::where('role', 'admin')->first();
        if ($adminUser) {
            $this->info("\nTesting login with admin user: {$adminUser->email}");
            
            // Simulate login
            Auth::login($adminUser);
            $this->info("Login successful: " . (Auth::check() ? 'true' : 'false'));
            $this->info("Current user: " . Auth::user()->email);
            $this->info("Current user role: " . Auth::user()->role);
            $this->info("Is admin: " . (Auth::user()->isAdmin() ? 'true' : 'false'));
            
            // Test route resolution
            try {
                $dashboardUrl = route('admin.dashboard');
                $this->info("Admin dashboard URL: {$dashboardUrl}");
            } catch (\Exception $e) {
                $this->error("Error generating admin dashboard URL: " . $e->getMessage());
            }
            
            Auth::logout();
        } else {
            $this->error("No admin user found!");
        }
        
        return 0;
    }
}