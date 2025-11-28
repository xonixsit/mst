<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListAdminUsers extends Command
{
    protected $signature = 'admin:list';
    protected $description = 'List all admin users';

    public function handle()
    {
        $admins = User::where('role', 'admin')->get(['id', 'first_name', 'email', 'role']);
        
        if ($admins->isEmpty()) {
            $this->error('No admin users found.');
            return 1;
        }
        
        $this->info('Admin users:');
        foreach ($admins as $admin) {
            $this->line("ID: {$admin->id} - Name: {$admin->first_name} - Email: {$admin->email}");
        }
        
        return 0;
    }
}
