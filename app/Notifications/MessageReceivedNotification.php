<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
            ->subject("New Message: {$this->message->subject} - MySuperTax")
            ->view('emails.message-received', [
                'message' => $this->message,
                'subject' => "New Message: {$this->message->subject}"
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'message_received',
            'message_id' => $this->message->id,
            'sender_name' => $this->message->sender->name,
            'subject' => $this->message->subject,
            'priority' => $this->message->priority ?? 'normal',
            'message' => "New message from {$this->message->sender->name}: {$this->message->subject}",
        ];
    }
}