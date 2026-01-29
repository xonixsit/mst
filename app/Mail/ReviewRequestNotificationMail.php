<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewRequestNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Client $client,
        public array $sections,
        public ?string $message,
        public string $priority,
        public string $recipient_type
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->recipient_type === 'admin' 
            ? 'New Review Request from ' . $this->client->user->first_name . ' ' . $this->client->user->last_name
            : 'Review Request Confirmation - MySuperTax';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $view = $this->recipient_type === 'admin' 
            ? 'emails.review-request-admin'
            : 'emails.review-request-client';

        return new Content(
            view: $view,
            with: [
                'client' => $this->client,
                'sections' => $this->sections,
                'message' => $this->message,
                'priority' => $this->priority,
                'clientName' => $this->client->user->first_name . ' ' . $this->client->user->last_name,
                'adminUrl' => route('admin.clients.show', $this->client->id),
                'clientUrl' => route('client.information'),
            ],
        );
    }
}