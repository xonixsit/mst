<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use App\Models\Client;
use App\Models\User;

class DebugMessageAuth extends Command
{
    protected $signature = 'debug:message-auth {message_id}';
    protected $description = 'Debug message authorization issues';

    public function handle()
    {
        $messageId = $this->argument('message_id');
        $message = Message::with(['sender', 'recipient', 'client'])->find($messageId);
        
        if (!$message) {
            $this->error("Message {$messageId} not found");
            return;
        }

        $this->info("Message Details:");
        $this->line("ID: {$message->id}");
        $this->line("Client ID: {$message->client_id}");
        $this->line("Sender ID: {$message->sender_id}");
        $this->line("Recipient ID: {$message->recipient_id}");
        $this->line("Subject: {$message->subject}");
        
        $this->info("\nSender Details:");
        if ($message->sender) {
            $this->line("Name: {$message->sender->first_name} {$message->sender->last_name}");
            $this->line("Email: {$message->sender->email}");
            $this->line("Role: {$message->sender->role}");
        }
        
        $this->info("\nRecipient Details:");
        if ($message->recipient) {
            $this->line("Name: {$message->recipient->first_name} {$message->recipient->last_name}");
            $this->line("Email: {$message->recipient->email}");
            $this->line("Role: {$message->recipient->role}");
        }
        
        $this->info("\nClient Details:");
        if ($message->client) {
            $this->line("Client ID: {$message->client->id}");
            $this->line("User ID: {$message->client->user_id}");
            if ($message->client->user) {
                $this->line("Client User: {$message->client->user->first_name} {$message->client->user->last_name}");
                $this->line("Client Email: {$message->client->user->email}");
            }
        }
        
        // Check if recipient user has a client record
        if ($message->recipient && $message->recipient->role === 'client') {
            $recipientClient = Client::where('user_id', $message->recipient_id)->first();
            $this->info("\nRecipient's Client Record:");
            if ($recipientClient) {
                $this->line("Client ID: {$recipientClient->id}");
                $this->line("User ID: {$recipientClient->user_id}");
            } else {
                $this->error("No client record found for recipient user ID {$message->recipient_id}");
            }
        }
    }
}