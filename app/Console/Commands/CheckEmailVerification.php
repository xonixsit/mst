<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckEmailVerification extends Command
{
    protected $signature = 'check:email-verification';
    protected $description = 'Check email verification status of users';

    public function handle()
    {
        $users = User::all();
        
        foreach ($users as $user) {
            $verified = $user->hasVerifiedEmail() ? 'Yes' : 'No';
            $this->info("{$user->email} ({$user->role}) - Verified: {$verified}");
            
            if (!$user->hasVerifiedEmail()) {
                $this->info("  Email verified at: " . ($user->email_verified_at ?? 'NULL'));
            }
        }
        
        return 0;
    }
}