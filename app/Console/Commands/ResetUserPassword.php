<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password {email}';
    protected $description = 'Generate a password reset link for a user';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $token = Password::createToken($user);
        $resetUrl = url(route('password.reset', ['token' => $token], false));

        $this->info("Password reset link for {$email}:");
        $this->line($resetUrl);

        return 0;
    }
}
