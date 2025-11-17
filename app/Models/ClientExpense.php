<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'category',
        'particulars',
        'tax_payer',
        'spouse',
        'amount',
        'expense_date',
        'remarks',
        'receipt_number',
        'is_deductible',
        'deductible_percentage',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deductible_percentage' => 'decimal:2',
        'expense_date' => 'date',
        'is_deductible' => 'boolean',
    ];

    /**
     * Get the client that owns the expense.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the deductible amount for this expense.
     */
    public function getDeductibleAmountAttribute(): float
    {
        if (!$this->is_deductible) {
            return 0;
        }

        return $this->amount * ($this->deductible_percentage / 100);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by tax payer.
     */
    public function scopeByTaxPayer($query, $taxPayer)
    {
        return $query->where('tax_payer', $taxPayer);
    }

    /**
     * Scope to filter deductible expenses only.
     */
    public function scopeDeductible($query)
    {
        return $query->where('is_deductible', true);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get formatted deductible amount.
     */
    public function getFormattedDeductibleAmountAttribute(): string
    {
        return '$' . number_format($this->deductible_amount, 2);
    }
}