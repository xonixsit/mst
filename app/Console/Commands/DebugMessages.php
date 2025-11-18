<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\Client;
use App\Models\User;
use Illuminate\Console\Command;

class DebugMessages extends Command
{
    protected $signature = 'debug:messages {user_id?}';
    protected $description = 'Debug messages for a user';

    public function handle()
    {
        $userId = $this->argument('user_id') ?? 1;
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $this->info("Debugging messages for {$user->name} (ID: {$user->id}, Role: {$user->role})");

        if ($user->isClient()) {
            $client = Client::where('user_id', $user->id)->first();
            
            if (!$client) {
                $this->error("No client profile found for this user.");
                return 1;
            }

            $this->info("Client ID: {$client->id}");

            // Get all messages for this client
            $allMessages = Message::where('client_id', $client->id)->with(['sender', 'recipient'])->get();
            $this->info("Total messages for client: " . $allMessages->count());

            foreach ($allMessages as $message) {
                $senderName = $message->sender ? $message->sender->name : 'Unknown';
                $recipientName = $message->recipient ? $message->recipient->name : 'Unknown';
                
                $this->line("Message ID {$message->id}:");
                $this->line("  Subject: {$message->subject}");
                $this->line("  From: {$senderName} (ID: {$message->sender_id})");
                $this->line("  To: {$recipientName} (ID: {$message->recipient_id})");
                $this->line("  Read: " . ($message->is_read ? 'Yes' : 'No'));
                $this->line("  Created: {$message->created_at}");
                $this->line("  ---");
            }

            // Check what the client controller query would return
            $clientQuery = Message::where('client_id', $client->id)
                ->where(function ($q) use ($user) {
                    $q->where('sender_id', $user->id)
                      ->orWhere('recipient_id', $user->id);
                })
                ->with(['sender', 'recipient'])
                ->orderBy('created_at', 'desc')
                ->get();

            $this->info("Messages visible to client: " . $clientQuery->count());
            
        } else {
            // For admin users, show all messages they're involved in
            $adminMessages = Message::where('sender_id', $user->id)
                ->orWhere('recipient_id', $user->id)
                ->with(['sender', 'recipient', 'client.user'])
                ->orderBy('created_at', 'desc')
                ->get();

            $this->info("Messages involving admin: " . $adminMessages->count());

            foreach ($adminMessages as $message) {
                $senderName = $message->sender ? $message->sender->name : 'Unknown';
                $recipientName = $message->recipient ? $message->recipient->name : 'Unknown';
                $clientName = $message->client && $message->client->user ? $message->client->user->name : 'Unknown Client';
                
                $this->line("Message ID {$message->id}:");
                $this->line("  Subject: {$message->subject}");
                $this->line("  From: {$senderName} (ID: {$message->sender_id})");
                $this->line("  To: {$recipientName} (ID: {$message->recipient_id})");
                $this->line("  Client: {$clientName} (Client ID: {$message->client_id})");
                $this->line("  Read: " . ($message->is_read ? 'Yes' : 'No'));
                $this->line("  Created: {$message->created_at}");
                $this->line("  ---");
            }
        }

        return 0;
    }
}