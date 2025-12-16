<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserConsent extends Model
{
    protected $fillable = [
        'user_id',
        'consent_type',
        'given',
        'ip_address',
        'user_agent',
        'consent_version',
        'expires_at',
    ];

    protected $casts = [
        'given' => 'boolean',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function recordConsent(
        ?int $userId,
        string $consentType,
        bool $given,
        string $ipAddress,
        string $userAgent,
        ?string $version = null
    ): self {
        return self::updateOrCreate(
            [
                'user_id' => $userId,
                'consent_type' => $consentType,
            ],
            [
                'given' => $given,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'consent_version' => $version,
            ]
        );
    }

    public static function hasConsent(?int $userId, string $consentType): bool
    {
        return self::where('user_id', $userId)
            ->where('consent_type', $consentType)
            ->where('given', true)
            ->exists();
    }
}
