<?php

namespace App\Mail;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Message $message,
        public User $recipient
    ) {
        // Load required relationships
        $this->message->load(['sender', 'recipient', 'client']);
    }

    public function envelope(): Envelope
    {
        $senderName = $this->message->sender->name;
        $isFromClient = $this->message->sender->role === 'client';
        
        $subject = $isFromClient 
            ? "New Message from Client: {$this->message->subject}"
            : "New Message from MySuperTax: {$this->message->subject}";

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $isFromClient = $this->message->sender->role === 'client';
        
        return new Content(
            view: $isFromClient ? 'emails.message-from-client' : 'emails.message-from-admin',
            with: [
                'messageModel' => $this->message,
                'recipient' => $this->recipient,
                'sender' => $this->message->sender,
                'client' => $this->message->client,
            ],
        );
    }
}