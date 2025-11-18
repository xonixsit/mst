<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ClientWelcomeNotification;
use App\Notifications\DocumentApprovedNotification;
use App\Notifications\InvoiceCreatedNotification;
use Illuminate\Console\Command;

class TestNotifications extends Command
{
    protected $signature = 'test:notifications {user_id?}';
    protected $description = 'Send test notifications to a user';

    public function handle()
    {
        $userId = $this->argument('user_id') ?? 1;
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $this->info("Sending test notifications to {$user->name} ({$user->email})...");

        // Send welcome notification
        $user->notify(new ClientWelcomeNotification());
        $this->info('✓ Welcome notification sent');

        // Send document approved notification
        $user->notify(new DocumentApprovedNotification((object)[
            'name' => 'Test Document.pdf',
            'document_type' => 'w2'
        ]));
        $this->info('✓ Document approved notification sent');

        // Send invoice notification
        $user->notify(new InvoiceCreatedNotification((object)[
            'invoice_number' => 'INV-2024-001',
            'title' => 'Tax Preparation Services',
            'total_amount' => 299.99
        ]));
        $this->info('✓ Invoice notification sent');

        $this->info("\nTest notifications sent successfully!");
        $this->info("You can view them at: /client/notifications");

        return 0;
    }
}