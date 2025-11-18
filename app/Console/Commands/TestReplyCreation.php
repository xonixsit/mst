<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestReplyCreation extends Command
{
    protected $signature = 'test:reply-creation {message_id} {user_id} {body}';
    protected $description = 'Test reply creation process';

    public function handle()
    {
        $messageId = $this->argument('message_id');
        $userId = $this->argument('user_id');
        $body = $this->argument('body');
        
        $originalMessage = Message::find($messageId);
        $user = User::find($userId);
        
        if (!$originalMessage || !$user) {
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
        
        $this->info("Testing reply creation:");
        $this->line("Original Message ID: {$messageId}");
        $this->line("User ID: {$userId}");
        $this->line("Client ID: {$client->id}");
        $this->line("Body: {$body}");
        
        // Check authorization
        $isParticipant = ($originalMessage->sender_id == $userId || $originalMessage->recipient_id == $userId);
        $isClientMessage = ($originalMessage->client_id == $client->id);
        
        if (!$isParticipant || !$isClientMessage) {
            $this->error('Authorization would fail');
            return;
        }
        
        $this->info('Authorization passed');
        
        // Determine recipient
        $recipientId = $originalMessage->sender_id == $userId 
            ? $originalMessage->recipient_id 
            : $originalMessage->sender_id;
            
        $this->line("Reply recipient ID: {$recipientId}");
        
        // Try to create the reply
        try {
            DB::beginTransaction();
            
            $replyData = [
                'client_id' => $client->id,
                'sender_id' => $userId,
                'recipient_id' => $recipientId,
                'subject' => 'Re: ' . $originalMessage->subject,
                'body' => $body,
                'priority' => $originalMessage->priority
            ];
            
            $this->line("Reply data:");
            foreach ($replyData as $key => $value) {
                $this->line("  {$key}: {$value}");
            }
            
            $replyMessage = Message::create($replyData);
            
            DB::commit();
            
            $this->info("Reply created successfully with ID: {$replyMessage->id}");
            
            // Verify it was saved
            $savedMessage = Message::find($replyMessage->id);
            if ($savedMessage) {
                $this->info("Reply verified in database");
            } else {
                $this->error("Reply not found in database after creation");
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error("Error creating reply: " . $e->getMessage());
            $this->line("Stack trace: " . $e->getTraceAsString());
        }
    }
}