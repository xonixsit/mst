<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TaxProfessional extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'license_number',
        'specializations',
        'bio',
        'phone',
        'address',
        'city',
        'state',
        'zip_code'
    ];

    protected $casts = [
        'specializations' => 'array',
    ];

    /**
     * Get the user associated with this tax professional.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the clients managed by this tax professional.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'user_id', 'user_id');
    }
}
