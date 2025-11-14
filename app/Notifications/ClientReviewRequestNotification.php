<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Client;

class ClientReviewRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Client $client,
        public array $sections,
        public ?string $message,
        public string $priority
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $sectionsText = empty($this->sections) ? 'all sections' : implode(', ', $this->sections);
        
        return (new MailMessage)
            ->subject("Review Request from {$this->client->full_name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have received a new review request from {$this->client->full_name}.")
            ->line("**Sections to review:** {$sectionsText}")
            ->line("**Priority:** " . ucfirst($this->priority))
            ->when($this->message, function ($mail) {
                return $mail->line("**Client message:** {$this->message}");
            })
            ->action('Review Client Information', url("/admin/clients/{$this->client->id}"))
            ->line('Please review the client information and provide feedback as needed.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'client_review_request',
            'client_id' => $this->client->id,
            'client_name' => $this->client->full_name,
            'client_email' => $this->client->email,
            'sections' => $this->sections,
            'message' => $this->message,
            'priority' => $this->priority,
            'requested_at' => now()->toISOString(),
        ];
    }
}