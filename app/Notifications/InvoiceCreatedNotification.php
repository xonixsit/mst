<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceCreatedNotification extends Notification
{
    use Queueable;

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
        // Generate PDF attachment
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', ['invoice' => $this->invoice]);
        
        return (new MailMessage)
            ->subject("New Invoice #{$this->invoice->invoice_number} - MySuperTax")
            ->view('emails.invoice', [
                'invoice' => $this->invoice,
                'emailData' => [
                    'subject' => "New Invoice #{$this->invoice->invoice_number} - MySuperTax",
                    'message' => "A new invoice has been created for your tax consulting services. Please review the details and make payment by the due date."
                ]
            ])
            ->attachData($pdf->output(), "invoice-{$this->invoice->invoice_number}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'invoice_created',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'amount' => $this->invoice->total_amount,
            'due_date' => $this->invoice->due_date,
            'message' => "New invoice #{$this->invoice->invoice_number} has been created for \${$this->invoice->total_amount}",
        ];
    }
}