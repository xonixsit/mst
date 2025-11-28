<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminMessageSentNotification extends Notification
{

    public function __construct(
        public Message $message
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Client Message: {$this->message->subject} - MySuperTax")
            ->view('emails.admin.message-sent', [
                'message' => $this->message,
                'subject' => "Client Message: {$this->message->subject}"
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'admin_message_received',
            'message_id' => $this->message->id,
            'sender_name' => $this->message->sender->name,
            'subject' => $this->message->subject,
            'priority' => $this->message->priority ?? 'normal',
            'message' => "New message from {$this->message->sender->name}: {$this->message->subject}",
        ];
    }
}