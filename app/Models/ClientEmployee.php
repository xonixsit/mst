<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientEmployee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'employer_name',
        'job_title',
        'employee_id',
        'start_date',
        'end_date',
        'annual_salary',
        'employment_type',
        'work_description',
        'employer_address',
        'employer_city',
        'employer_state',
        'employer_zip_code',
        'pay_frequency',
        'work_location',
        'benefits',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'annual_salary' => 'decimal:2',
        'benefits' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Validation rules for employee data.
     *
     * @return array<string, string>
     */
    public static function validationRules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'employer_name' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'employee_id' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'annual_salary' => 'nullable|numeric|min:0|max:9999999999.99',
            'employment_type' => 'nullable|string|in:full-time,part-time,contract,temporary,terminated',
            'work_description' => 'nullable|string|max:1000',
            'employer_address' => 'nullable|string|max:255',
            'employer_city' => 'nullable|string|max:255',
            'employer_state' => 'nullable|string|max:255',
            'employer_zip_code' => 'nullable|string|max:10',
            'pay_frequency' => 'nullable|string|in:weekly,bi-weekly,semi-monthly,monthly,annually',
            'work_location' => 'nullable|string|in:office,remote,hybrid,field,multiple',
            'benefits' => 'nullable|array',
        ];
    }

    /**
     * Get the client that owns this employee record.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Check if this is current employment.
     */
    public function getIsCurrentEmploymentAttribute(): bool
    {
        return is_null($this->end_date) || $this->end_date->isFuture();
    }

    /**
     * Get formatted employer address.
     */
    public function getFormattedEmployerAddressAttribute(): ?string
    {
        if (!$this->employer_address) {
            return null;
        }

        $address = $this->employer_address;
        if ($this->employer_city) {
            $address .= ', ' . $this->employer_city;
        }
        if ($this->employer_state) {
            $address .= ', ' . $this->employer_state;
        }
        if ($this->employer_zip_code) {
            $address .= ' ' . $this->employer_zip_code;
        }

        return $address;
    }

    /**
     * Scope a query to only include current employment.
     */
    public function scopeCurrent($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>', now());
        });
    }
}