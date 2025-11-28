<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class Invoice extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'client_id',
        'invoice_number',
        'title',
        'comments',
        'send_to_email',
        'invoice_year',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'status',
        'sent_at',
        'paid_at',
        'payment_method',
        'transaction_id',
        'amount_paid',
        'payment_notes',
        'archived_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total_price');
        $this->tax_amount = $this->subtotal * ($this->tax_rate / 100);
        $this->total_amount = $this->subtotal + $this->tax_amount;
    }

    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -4) + 1 : 1;
        
        return 'INV-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Mark invoice as paid and send notifications
     */
    public function markAsPaid(array $paymentData = []): void
    {
        $updateData = [
            'status' => 'paid',
            'paid_at' => isset($paymentData['payment_date']) ? $paymentData['payment_date'] : now(),
        ];

        // Store payment details if provided
        if (!empty($paymentData)) {
            $updateData['payment_method'] = $paymentData['payment_method'] ?? null;
            $updateData['transaction_id'] = $paymentData['transaction_id'] ?? null;
            $updateData['amount_paid'] = $paymentData['amount_paid'] ?? $this->total_amount;
            $updateData['payment_notes'] = $paymentData['payment_notes'] ?? null;
        }

        $this->update($updateData);

        // Send receipt email directly
        $this->sendReceiptEmail($paymentData);

        // Send notifications to client and admins
        $clientUser = $this->client->user;
        if ($clientUser) {
            $clientUser->notify(new \App\Notifications\InvoicePaidNotification($this));
        }

        // Notify admins
        $adminService = app(\App\Services\AdminNotificationService::class);
        $adminService->notifyInvoicePaid($this);
    }

    /**
     * Send receipt email to client and admin
     */
    private function sendReceiptEmail(array $paymentData = []): void
    {
        try {
            // Load the client relationship if not already loaded
            if (!$this->relationLoaded('client')) {
                $this->load('client.user');
            }

            // Get client email
            $clientEmail = $this->client->user->email ?? $this->send_to_email;
            
            // Get admin email from config
            $adminEmail = config('mail.from.address');

            if ($clientEmail) {
                // Send receipt to client
                \Mail::to($clientEmail)->send(new \App\Mail\ReceiptMail($this, $paymentData));

                // Send copy to admin
                if ($adminEmail && $adminEmail !== $clientEmail) {
                    \Mail::to($adminEmail)->send(new \App\Mail\ReceiptMail($this, $paymentData));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send receipt email', [
                'invoice_id' => $this->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}