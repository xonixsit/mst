<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use App\Models\User;

class DebugMessageCounts extends Command
{
    protected $signature = 'debug:message-counts';
    protected $description = 'Debug message counts for statistics';

    public function handle()
    {
        $this->info('=== Message Statistics Debug ===');
        
        $this->info('Total Messages: ' . Message::count());
        $this->info('Unread Messages: ' . Message::where('is_read', false)->count());
        $this->info('High Priority Messages: ' . Message::where('priority', 'high')->count());
        
        // Check client messages - current method
        $clientMessages = Message::whereHas('sender', function ($q) {
            $q->where('role', 'client');
        })->count();
        $this->info('Client Messages (current method): ' . $clientMessages);
        
        // Let's see what roles exist
        $roles = User::distinct()->pluck('role')->toArray();
        $this->info('User roles in system: ' . implode(', ', $roles));
        
        // Let's see actual messages with sender info
        $this->info('=== Messages with sender info ===');
        $messages = Message::with('sender')->get();
        foreach ($messages as $message) {
            $senderRole = $message->sender ? $message->sender->role : 'NULL';
            $this->info("Message ID: {$message->id}, Sender Role: {$senderRole}");
        }
        
        // Alternative count method - check if sender is associated with a client
        $alternativeCount = Message::whereHas('sender.client')->count();
        $this->info('Alternative client messages count (users with client records): ' . $alternativeCount);
        
        // New method - distinct client count
        $distinctClientCount = Message::whereHas('sender', function ($q) {
            $q->where('role', 'client');
        })->distinct('client_id')->count('client_id');
        $this->info('Distinct clients who sent messages: ' . $distinctClientCount);
    }
}