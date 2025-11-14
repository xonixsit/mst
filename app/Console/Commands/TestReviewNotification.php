<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\User;
use App\Notifications\ClientReviewRequestNotification;

class TestReviewNotification extends Command
{
    protected $signature = 'test:review-notification';
    protected $description = 'Test the review request notification system';

    public function handle()
    {
        $client = Client::first();
        $user = User::first();
        
        if (!$client || !$user) {
            $this->error('Need at least one client and one user to test');
            return 1;
        }

        $this->info("Testing notification for client: {$client->full_name}");
        $this->info("Sending to user: {$user->email}");

        try {
            $user->notify(new ClientReviewRequestNotification(
                $client,
                ['personal', 'employee'],
                'Please review my updated employment information.',
                'high'
            ));

            $this->info('✅ Notification sent successfully!');
            $this->info('Check the database notifications table and email logs.');
            
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to send notification: " . $e->getMessage());
            return 1;
        }
    }
}