<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientCredentialsMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Client $client,
        public string $username,
        public string $password
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your MySuperTax Account Credentials',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-credentials',
            with: [
                'client' => $this->client,
                'username' => $this->username,
                'password' => $this->password,
                'loginUrl' => route('client.login'),
            ],
        );
    }
}
