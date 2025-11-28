<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Invoice $invoice,
        public array $paymentData = []
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Payment Receipt - Invoice {$this->invoice->invoice_number}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.receipt',
            with: [
                'invoice' => $this->invoice,
                'paymentData' => $this->paymentData,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Generate receipt PDF and attach it
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('receipts.pdf', [
            'invoice' => $this->invoice,
            'paymentData' => $this->paymentData
        ]);
        
        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                "receipt-{$this->invoice->invoice_number}.pdf"
            )->withMime('application/pdf'),
        ];
    }

    private function formatCurrency($amount): string
    {
        return number_format($amount, 2);
    }
}
