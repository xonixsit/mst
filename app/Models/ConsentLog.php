<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsentLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'consents',
        'ip_address',
        'user_agent',
        'source',
    ];

    protected $casts = [
        'consents' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(
        ?int $userId,
        string $action,
        array $consents,
        string $ipAddress,
        string $userAgent,
        string $source = 'banner'
    ): self {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'consents' => $consents,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'source' => $source,
        ]);
    }
}
