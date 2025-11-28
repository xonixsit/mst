<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminReviewRequestNotification extends Notification
{

    protected $client;
    protected $reviewData;

    /**
     * Create a new notification instance.
     */
    public function __construct(Client $client, array $reviewData = [])
    {
        $this->client = $client;
        $this->reviewData = $reviewData;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $clientName = $this->client->user->name ?? 'Unknown Client';
        $priority = $this->reviewData['priority'] ?? 'normal';
        $sections = $this->reviewData['sections'] ?? [];
        $message = $this->reviewData['message'] ?? '';

        $mailMessage = (new MailMessage)
            ->subject("Review Request from {$clientName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A client has requested a review of their tax information.")
            ->line("**Client:** {$clientName}")
            ->line("**Email:** {$this->client->user->email}")
            ->line("**Priority:** " . ucfirst($priority));

        if (!empty($sections)) {
            $mailMessage->line("**Sections to Review:**");
            foreach ($sections as $section) {
                $sectionName = $this->getSectionDisplayName($section);
                $mailMessage->line("• {$sectionName}");
            }
        }

        if (!empty($message)) {
            $mailMessage->line("**Client Message:**")
                        ->line($message);
        }

        $mailMessage->action('Review Client Information', url("/admin/clients/{$this->client->id}"))
                    ->line('Please review the client\'s information and respond accordingly.')
                    ->line('Thank you for using MySuperTax!');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'review_request',
            'client_id' => $this->client->id,
            'client_name' => $this->client->user->name ?? 'Unknown Client',
            'client_email' => $this->client->user->email,
            'priority' => $this->reviewData['priority'] ?? 'normal',
            'sections' => $this->reviewData['sections'] ?? [],
            'message' => $this->reviewData['message'] ?? '',
            'requested_at' => now()->toISOString(),
        ];
    }

    /**
     * Get display name for section
     */
    private function getSectionDisplayName(string $section): string
    {
        $sectionNames = [
            'personal' => 'Personal Details',
            'spouse' => 'Spouse Information',
            'employee' => 'Employment Information',
            'projects' => 'Project Details',
            'assets' => 'Assets & Investments',
            'expenses' => 'Expenses & Deductions',
        ];

        return $sectionNames[$section] ?? ucfirst($section);
    }
}