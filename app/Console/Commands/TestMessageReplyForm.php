<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use App\Models\User;
use App\Models\Client;

class TestMessageReplyForm extends Command
{
    protected $signature = 'test:message-reply-form';
    protected $description = 'Test message reply form data';

    public function handle()
    {
        // Get a test message
        $message = Message::with(['sender', 'recipient', 'client'])->first();
        
        if (!$message) {
            $this->error('No messages found');
            return;
        }
        
        $this->info("Testing message ID: {$message->id}");
        $this->info("Subject: {$message->subject}");
        $this->info("Sender ID: {$message->sender_id}");
        $this->info("Recipient ID: {$message->recipient_id}");
        $this->info("Client ID: {$message->client_id}");
        
        if ($message->sender) {
            $this->info("Sender: {$message->sender->name} ({$message->sender->email})");
        }
        
        if ($message->recipient) {
            $this->info("Recipient: {$message->recipient->name} ({$message->recipient->email})");
        }
        
        if ($message->client) {
            $this->info("Client: {$message->client->id}");
        }
        
        // Test reply logic
        $currentUserId = $message->sender_id; // Simulate sender replying
        $senderId = $message->sender_id;
        $recipientId = $message->recipient_id;
        
        if ($senderId == $currentUserId) {
            $replyRecipientId = $recipientId;
        } else {
            $replyRecipientId = $senderId;
        }
        
        $this->info("Reply would go to user ID: {$replyRecipientId}");
        
        // Check if recipient exists
        $replyRecipient = User::find($replyRecipientId);
        if ($replyRecipient) {
            $this->info("Reply recipient: {$replyRecipient->name} ({$replyRecipient->email})");
        } else {
            $this->error("Reply recipient not found!");
        }
    }
}