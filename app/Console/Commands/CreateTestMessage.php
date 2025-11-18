<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Client;
use App\Models\Message;
use Illuminate\Console\Command;

class CreateTestMessage extends Command
{
    protected $signature = 'test:create-message {client_user_id} {--from-admin}';
    protected $description = 'Create a test message for a client user';

    public function handle()
    {
        $clientUserId = $this->argument('client_user_id');
        $clientUser = User::find($clientUserId);

        if (!$clientUser || !$clientUser->isClient()) {
            $this->error("Client user with ID {$clientUserId} not found or not a client.");
            return 1;
        }

        $client = Client::where('user_id', $clientUser->id)->first();
        if (!$client) {
            $this->error("Client profile not found for user {$clientUserId}.");
            return 1;
        }

        // Find an admin user to send from
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $this->error("No admin user found to send message from.");
            return 1;
        }

        // Create a test message FROM admin TO client
        $message = Message::create([
            'client_id' => $client->id,
            'sender_id' => $adminUser->id,
            'recipient_id' => $clientUser->id, // Client receives the message
            'subject' => 'Test Message from Admin',
            'body' => 'This is a test message to check if client can see and reply to admin messages. Please try to reply to this message.',
            'priority' => 'normal',
            'is_read' => false // Make sure it's unread
        ]);

        $this->info("Created test message (ID: {$message->id}):");
        $this->info("From: {$adminUser->name} (ID: {$adminUser->id})");
        $this->info("To: {$clientUser->name} (ID: {$clientUser->id})");
        $this->info("Client ID: {$client->id}");
        $this->info("Subject: {$message->subject}");
        $this->info("Is Read: " . ($message->is_read ? 'Yes' : 'No'));

        $this->info("\nNow the client should see a badge count of 1 in the Messages navigation.");

        return 0;
    }
}