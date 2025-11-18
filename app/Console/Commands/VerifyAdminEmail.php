<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class VerifyAdminEmail extends Command
{
    protected $signature = 'verify:admin-email';
    protected $description = 'Manually verify admin email';

    public function handle()
    {
        $admin = User::where('role', 'admin')->first();
        
        if ($admin) {
            $admin->email_verified_at = now();
            $admin->save();
            
            $this->info("Admin email verified: {$admin->email}");
        } else {
            $this->error("No admin user found!");
        }
        
        return 0;
    }
}