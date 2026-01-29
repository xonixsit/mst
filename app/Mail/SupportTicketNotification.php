<?php

namespace App\Mail;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupportTicketNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SupportTicket $ticket,
        public User $recipient,
        public string $type // 'created', 'updated', 'reply', 'assigned'
    ) {
        // Load required relationships
        $this->ticket->load(['client', 'user', 'assignedTo']);
    }

    public function envelope(): Envelope
    {
        $subject = match($this->type) {
            'created' => "New Support Ticket: {$this->ticket->ticket_number}",
            'updated' => "Support Ticket Updated: {$this->ticket->ticket_number}",
            'reply' => "New Reply on Ticket: {$this->ticket->ticket_number}",
            'assigned' => "Support Ticket Assigned: {$this->ticket->ticket_number}",
            default => "Support Ticket Notification: {$this->ticket->ticket_number}"
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $isForClient = $this->recipient->role === 'client';
        
        return new Content(
            view: $isForClient ? 'emails.support-ticket-client' : 'emails.support-ticket-admin',
            with: [
                'ticketModel' => $this->ticket,
                'recipient' => $this->recipient,
                'type' => $this->type,
                'client' => $this->ticket->client,
            ],
        );
    }
}