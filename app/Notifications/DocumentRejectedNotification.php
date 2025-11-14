<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Document $document
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
            ->subject("Document Requires Attention: {$this->document->name} - MySuperTax")
            ->view('emails.document-rejected', [
                'document' => $this->document,
                'subject' => "Document Requires Attention: {$this->document->name}"
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'document_rejected',
            'document_id' => $this->document->id,
            'document_name' => $this->document->name,
            'document_type' => $this->document->document_type,
            'message' => "Your document '{$this->document->name}' requires attention before processing.",
        ];
    }
}