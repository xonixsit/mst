<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestUserRole extends Command
{
    protected $signature = 'test:user-role {email}';
    protected $description = 'Test user role methods';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error('User not found');
            return;
        }
        
        $this->info('User: ' . $user->name);
        $this->info('Email: ' . $user->email);
        $this->info('Role: ' . $user->role);
        $this->info('isClient(): ' . ($user->isClient() ? 'true' : 'false'));
        $this->info('isAdmin(): ' . ($user->isAdmin() ? 'true' : 'false'));
        $this->info('isTaxProfessional(): ' . ($user->isTaxProfessional() ? 'true' : 'false'));
    }
}