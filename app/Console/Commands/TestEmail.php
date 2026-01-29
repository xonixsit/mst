<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;
use App\Models\User;

class TestEmail extends Command
{
    protected $signature = 'test:email {email}';
    protected $description = 'Test email sending';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error('User not found');
            return;
        }

        try {
            Mail::to($email)->send(new TwoFactorCodeMail($user, '123456'));
            $this->info('Email sent successfully to ' . $email);
        } catch (\Exception $e) {
            $this->error('Email failed: ' . $e->getMessage());
        }
    }
}