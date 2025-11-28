<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminInvoicePaidNotification extends Notification
{

    public function __construct(
        public Invoice $invoice
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
            ->subject("Payment Received: Invoice #{$this->invoice->invoice_number} - MySuperTax")
            ->view('emails.admin.invoice-paid', [
                'invoice' => $this->invoice,
                'subject' => "Payment Received: Invoice #{$this->invoice->invoice_number}"
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'admin_invoice_paid',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'client_name' => $this->invoice->client->first_name . ' ' . $this->invoice->client->last_name,
            'amount' => $this->invoice->total_amount,
            'paid_at' => $this->invoice->paid_at,
            'message' => "Payment received: Invoice #{$this->invoice->invoice_number} - \${$this->invoice->total_amount} from {$this->invoice->client->first_name} {$this->invoice->client->last_name}",
        ];
    }
}