<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientSpouse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'first_name',
        'middle_name',
        'last_name',
        'ssn',
        'date_of_birth',
        'occupation',
        'email',
        'phone',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'ssn', // Hide SSN in JSON responses for security
    ];

    /**
     * Validation rules for spouse data.
     *
     * @return array<string, string>
     */
    public static function validationRules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'ssn' => 'nullable|string|regex:/^\d{3}-\d{2}-\d{4}$/|unique:client_spouses,ssn',
            'date_of_birth' => 'nullable|date|before:today',
            'occupation' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|regex:/^[\+]?[1-9][\d]{0,15}$/',
        ];
    }

    /**
     * Get the client that owns this spouse record.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the spouse's full name.
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        
        return $name;
    }
}