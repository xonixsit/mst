<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientAsset extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'asset_name',
        'date_of_purchase',
        'percentage_used_in_business',
        'cost_of_acquisition',
        'any_reimbursement',
        'asset_type',
        'description',
        'current_value',
        'depreciation_rate',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_purchase' => 'date',
        'percentage_used_in_business' => 'decimal:2',
        'cost_of_acquisition' => 'decimal:2',
        'any_reimbursement' => 'decimal:2',
        'current_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Validation rules for asset data.
     *
     * @return array<string, string>
     */
    public static function validationRules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'asset_name' => 'required|string|max:255',
            'date_of_purchase' => 'required|date',
            'percentage_used_in_business' => 'required|numeric|min:0|max:100',
            'cost_of_acquisition' => 'required|numeric|min:0|max:9999999999.99',
            'any_reimbursement' => 'nullable|numeric|min:0|max:9999999999.99',
            'asset_type' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'current_value' => 'nullable|numeric|min:0|max:9999999999.99',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
        ];
    }

    /**
     * Get the client that owns this asset.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the business portion of the asset cost.
     */
    public function getBusinessCostAttribute(): float
    {
        return $this->cost_of_acquisition * ($this->percentage_used_in_business / 100);
    }

    /**
     * Get the personal portion of the asset cost.
     */
    public function getPersonalCostAttribute(): float
    {
        return $this->cost_of_acquisition * ((100 - $this->percentage_used_in_business) / 100);
    }

    /**
     * Get the net cost after reimbursements.
     */
    public function getNetCostAttribute(): float
    {
        return $this->cost_of_acquisition - ($this->any_reimbursement ?? 0);
    }

    /**
     * Calculate depreciation for a given year.
     */
    public function calculateDepreciation(int $year = null): float
    {
        $year = $year ?? now()->year;
        $purchaseYear = $this->date_of_purchase->year;
        $yearsOwned = max(0, $year - $purchaseYear + 1);

        if (!$this->depreciation_rate || $yearsOwned <= 0) {
            return 0;
        }

        $annualDepreciation = $this->business_cost * ($this->depreciation_rate / 100);
        return min($annualDepreciation * $yearsOwned, $this->business_cost);
    }

    /**
     * Get the current book value.
     */
    public function getCurrentBookValueAttribute(): float
    {
        $totalDepreciation = $this->calculateDepreciation();
        return max(0, $this->business_cost - $totalDepreciation);
    }

    /**
     * Scope a query to only include business assets.
     */
    public function scopeBusinessUse($query)
    {
        return $query->where('percentage_used_in_business', '>', 0);
    }

    /**
     * Scope a query by asset type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('asset_type', $type);
    }

    /**
     * Scope a query to assets purchased in a specific year.
     */
    public function scopePurchasedInYear($query, $year)
    {
        return $query->whereYear('date_of_purchase', $year);
    }
}