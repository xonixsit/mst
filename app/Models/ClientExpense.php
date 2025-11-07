<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientExpense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'is_deductible' => 'boolean',
        'deductible_percentage' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Validation rules for expense data.
     *
     * @return array<string, string>
     */
    public static function validationRules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'category' => 'required|in:miscellaneous,residency,business,medical,education,other',
            'particulars' => 'required|string|max:255',
            'tax_payer' => 'required|string|max:255',
            'spouse' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0|max:9999999999.99',
            'expense_date' => 'required|date',
            'remarks' => 'nullable|string|max:1000',
            'receipt_number' => 'nullable|string|max:255',
            'is_deductible' => 'boolean',
            'deductible_percentage' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Get the client that owns this expense.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the deductible amount.
     */
    public function getDeductibleAmountAttribute(): float
    {
        if (!$this->is_deductible) {
            return 0;
        }

        return $this->amount * ($this->deductible_percentage / 100);
    }

    /**
     * Get the non-deductible amount.
     */
    public function getNonDeductibleAmountAttribute(): float
    {
        return $this->amount - $this->deductible_amount;
    }

    /**
     * Check if expense has receipt documentation.
     */
    public function getHasReceiptAttribute(): bool
    {
        return !empty($this->receipt_number);
    }

    /**
     * Get formatted expense description.
     */
    public function getFormattedDescriptionAttribute(): string
    {
        $description = $this->particulars;
        if ($this->remarks) {
            $description .= ' - ' . $this->remarks;
        }
        return $description;
    }

    /**
     * Scope a query to only include deductible expenses.
     */
    public function scopeDeductible($query)
    {
        return $query->where('is_deductible', true)
                    ->where('deductible_percentage', '>', 0);
    }

    /**
     * Scope a query by category.
     */
    public function scopeOfCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to expenses in a specific year.
     */
    public function scopeInYear($query, $year)
    {
        return $query->whereYear('expense_date', $year);
    }

    /**
     * Scope a query to expenses by tax payer.
     */
    public function scopeByTaxPayer($query, $taxPayer)
    {
        return $query->where('tax_payer', $taxPayer);
    }

    /**
     * Scope a query to expenses with receipts.
     */
    public function scopeWithReceipts($query)
    {
        return $query->whereNotNull('receipt_number')
                    ->where('receipt_number', '!=', '');
    }
}