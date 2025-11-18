<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Client;
use App\Models\Message;
use App\Services\NavigationCountService;
use Illuminate\Console\Command;

class DebugNavigationCounts extends Command
{
    protected $signature = 'debug:navigation-counts {user_id?}';
    protected $description = 'Debug navigation counts for a user';

    public function handle()
    {
        $userId = $this->argument('user_id') ?? 1;
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $this->info("Debugging navigation counts for {$user->name} (ID: {$user->id})");
        $this->info("User role: {$user->role}");
        $this->info("Is client: " . ($user->isClient() ? 'Yes' : 'No'));

        if ($user->isClient()) {
            $client = Client::where('user_id', $user->id)->first();
            
            if ($client) {
                $this->info("Client ID: {$client->id}");
                
                // Get all messages for this client
                $allMessages = Message::where('client_id', $client->id)->get();
                $this->info("Total messages for client: " . $allMessages->count());
                
                // Get messages where user is recipient
                $recipientMessages = Message::where('client_id', $client->id)
                    ->where('recipient_id', $user->id)
                    ->get();
                $this->info("Messages where user is recipient: " . $recipientMessages->count());
                
                // Get unread messages where user is recipient
                $unreadMessages = Message::where('client_id', $client->id)
                    ->where('recipient_id', $user->id)
                    ->unread()
                    ->get();
                $this->info("Unread messages where user is recipient: " . $unreadMessages->count());
                
                // Show details of each message
                foreach ($recipientMessages as $message) {
                    $this->info("Message ID {$message->id}: '{$message->subject}' - Read: " . ($message->is_read ? 'Yes' : 'No') . " - Sender: {$message->sender_id} - Recipient: {$message->recipient_id}");
                }
                
            } else {
                $this->error("No client profile found for this user.");
            }
        }

        // Get unread notifications
        $unreadNotifications = $user->unreadNotifications()->count();
        $this->info("Unread notifications: {$unreadNotifications}");

        // Get counts from service
        $counts = NavigationCountService::getClientCounts();
        $this->info("Service counts: " . json_encode($counts));

        return 0;
    }
}