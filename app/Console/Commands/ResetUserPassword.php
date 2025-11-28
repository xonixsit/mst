<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password {email}';
    protected $description = 'Reset password for a specific user';

    public function handle()
    {
        $email = $this->argument('email');
        
        // Try exact match first, then with trimmed spaces
        $user = User::where('email', $email)->first() ?? 
                User::where('email', 'like', trim($email) . '%')->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        // Clean up the email (remove trailing spaces)
        $cleanEmail = trim($user->email);
        $user->email = $cleanEmail;
        
        $newPassword = 'password123';
        $user->password = Hash::make($newPassword);
        $user->save();

        $this->info("Email cleaned and password reset successfully!");
        $this->info("Email: {$user->email}");
        $this->info("New password: {$newPassword}");
        
        return 0;
    }
}
