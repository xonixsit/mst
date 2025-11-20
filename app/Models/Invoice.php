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
    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Send notifications to client and admins
        $clientUser = $this->client->user;
        if ($clientUser) {
            $clientUser->notify(new \App\Notifications\InvoicePaidNotification($this));
        }

        // Notify admins
        $adminService = app(\App\Services\AdminNotificationService::class);
        $adminService->notifyInvoicePaid($this);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}