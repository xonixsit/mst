<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use App\Models\User;

class DebugSpecificMessage extends Command
{
    protected $signature = 'debug:specific-message {id}';
    protected $description = 'Debug a specific message for reply issues';

    public function handle()
    {
        $messageId = $this->argument('id');
        $message = Message::with(['sender', 'recipient', 'client'])->find($messageId);
        
        if (!$message) {
            $this->error("Message {$messageId} not found");
            return;
        }
        
        $this->info("=== MESSAGE DEBUG ===");
        $this->info("ID: {$message->id}");
        $this->info("Subject: {$message->subject}");
        $this->info("Sender ID: " . ($message->sender_id ?? 'NULL'));
        $this->info("Recipient ID: " . ($message->recipient_id ?? 'NULL'));
        $this->info("Client ID: " . ($message->client_id ?? 'NULL'));
        
        $this->info("\n=== SENDER INFO ===");
        if ($message->sender) {
            $this->info("Sender exists: {$message->sender->name} ({$message->sender->email})");
            $this->info("Sender role: {$message->sender->role}");
        } else {
            $this->error("Sender is NULL or doesn't exist!");
        }
        
        $this->info("\n=== RECIPIENT INFO ===");
        if ($message->recipient) {
            $this->info("Recipient exists: {$message->recipient->name} ({$message->recipient->email})");
            $this->info("Recipient role: {$message->recipient->role}");
        } else {
            $this->error("Recipient is NULL or doesn't exist!");
        }
        
        $this->info("\n=== CLIENT INFO ===");
        if ($message->client) {
            $this->info("Client exists: ID {$message->client->id}");
            if ($message->client->user) {
                $this->info("Client user: {$message->client->user->name} ({$message->client->user->email})");
            } else {
                $this->error("Client has no user!");
            }
        } else {
            $this->error("Client is NULL or doesn't exist!");
        }
        
        // Test reply logic
        $this->info("\n=== REPLY LOGIC TEST ===");
        $senderId = $message->sender_id;
        $recipientId = $message->recipient_id;
        
        if (!$senderId || !$recipientId) {
            $this->error("Cannot test reply logic - sender_id or recipient_id is null");
            return;
        }
        
        // Simulate admin user replying (assuming admin has ID 1)
        $adminUser = User::where('role', 'admin')->first();
        if ($adminUser) {
            $currentUserId = $adminUser->id;
            $this->info("Simulating reply from admin user ID: {$currentUserId}");
            
            if ($senderId == $currentUserId) {
                $replyRecipientId = $recipientId;
                $this->info("Admin was sender, reply goes to recipient ID: {$replyRecipientId}");
            } else {
                $replyRecipientId = $senderId;
                $this->info("Admin was recipient, reply goes to sender ID: {$replyRecipientId}");
            }
            
            // Check if reply recipient exists
            $replyRecipient = User::find($replyRecipientId);
            if ($replyRecipient) {
                $this->info("Reply recipient exists: {$replyRecipient->name}");
            } else {
                $this->error("Reply recipient does not exist!");
            }
        }
    }
}