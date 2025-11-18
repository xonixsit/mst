<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;

class CheckMessageData extends Command
{
    protected $signature = 'check:message-data {message_id}';
    protected $description = 'Check message data in database';

    public function handle()
    {
        $messageId = $this->argument('message_id');
        $message = Message::find($messageId);
        
        if (!$message) {
            $this->error("Message {$messageId} not found");
            return;
        }
        
        $this->info("Message {$messageId} data:");
        $this->line("ID: {$message->id}");
        $this->line("Client ID: " . ($message->client_id ?? 'NULL'));
        $this->line("Sender ID: {$message->sender_id}");
        $this->line("Recipient ID: {$message->recipient_id}");
        $this->line("Subject: {$message->subject}");
        $this->line("Created: {$message->created_at}");
        
        // Check all messages for this conversation
        $this->info("\nAll messages in database:");
        $messages = Message::orderBy('id')->get();
        foreach ($messages as $msg) {
            $this->line("ID: {$msg->id}, Client ID: " . ($msg->client_id ?? 'NULL') . ", Subject: {$msg->subject}");
        }
    }
}