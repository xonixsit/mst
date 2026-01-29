<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewMessageNotification;
use App\Models\Message;
use App\Models\User;

class TestMessageEmail extends Command
{
    protected $signature = 'test:message-email {message_id} {recipient_email}';
    protected $description = 'Test message email notification';

    public function handle()
    {
        $messageId = $this->argument('message_id');
        $recipientEmail = $this->argument('recipient_email');
        
        $message = Message::with(['sender', 'recipient', 'client'])->find($messageId);
        
        if (!$message) {
            $this->error('Message not found');
            return;
        }

        $recipient = User::where('email', $recipientEmail)->first();
        
        if (!$recipient) {
            $this->error('Recipient not found');
            return;
        }

        try {
            Mail::to($recipientEmail)->send(new NewMessageNotification($message, $recipient));
            $this->info('Email sent successfully to ' . $recipientEmail);
        } catch (\Exception $e) {
            $this->error('Email failed: ' . $e->getMessage());
        }
    }
}