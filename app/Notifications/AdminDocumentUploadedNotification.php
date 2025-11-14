<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminDocumentUploadedNotification extends Notification implements ShouldQueue
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
            ->subject("New Document Upload: {$this->document->name} - MySuperTax")
            ->view('emails.admin.document-uploaded', [
                'document' => $this->document,
                'subject' => "New Document Upload: {$this->document->name}"
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'admin_document_uploaded',
            'document_id' => $this->document->id,
            'document_name' => $this->document->name,
            'client_name' => $this->document->client->first_name . ' ' . $this->document->client->last_name,
            'document_type' => $this->document->document_type,
            'message' => "New document uploaded: {$this->document->name} by {$this->document->client->first_name} {$this->document->client->last_name}",
        ];
    }
}