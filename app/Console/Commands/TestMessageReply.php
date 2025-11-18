<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestMessageReply extends Command
{
    protected $signature = 'test:message-reply {message_id} {user_id}';
    protected $description = 'Test message reply functionality';

    public function handle()
    {
        $messageId = $this->argument('message_id');
        $userId = $this->argument('user_id');
        
        $message = Message::find($messageId);
        $user = User::find($userId);
        
        if (!$message || !$user) {
            $this->error('Message or user not found');
            return;
        }
        
        // Simulate authentication
        Auth::login($user);
        
        $client = Client::where('user_id', $userId)->first();
        
        if (!$client) {
            $this->error('Client profile not found for user');
            return;
        }
        
        $this->info("Testing reply authorization:");
        $this->line("User ID: {$userId}");
        $this->line("Client ID: {$client->id}");
        $this->line("Message ID: {$messageId}");
        $this->line("Message Client ID: {$message->client_id}");
        $this->line("Message Sender ID: {$message->sender_id}");
        $this->line("Message Recipient ID: {$message->recipient_id}");
        
        // Check authorization conditions
        $this->line("Checking participation:");
        $this->line("  message->sender_id: {$message->sender_id} (type: " . gettype($message->sender_id) . ")");
        $this->line("  message->recipient_id: {$message->recipient_id} (type: " . gettype($message->recipient_id) . ")");
        $this->line("  userId: {$userId} (type: " . gettype($userId) . ")");
        
        $senderMatch = ($message->sender_id == $userId);
        $recipientMatch = ($message->recipient_id == $userId);
        $isParticipant = ($senderMatch || $recipientMatch);
        $isClientMessage = ($message->client_id === $client->id);
        
        $this->line("  sender match: " . ($senderMatch ? 'YES' : 'NO'));
        $this->line("  recipient match: " . ($recipientMatch ? 'YES' : 'NO'));
        $this->line("Is participant: " . ($isParticipant ? 'YES' : 'NO'));
        $this->line("Is client message: " . ($isClientMessage ? 'YES' : 'NO'));
        
        if ($isParticipant && $isClientMessage) {
            $this->info("Authorization would PASS");
            
            // Test creating a reply
            $recipientId = $message->sender_id === $userId 
                ? $message->recipient_id 
                : $message->sender_id;
                
            $this->line("Reply would be sent to user ID: {$recipientId}");
            
        } else {
            $this->error("Authorization would FAIL");
        }
    }
}