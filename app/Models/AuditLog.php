<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'event',
        'old_values',
        'new_values',
        'user_id',
        'user_type',
        'ip_address',
        'user_agent',
        'metadata',
        'archived_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['changes', 'description'];

    /**
     * Get the auditable model.
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the changes made in a readable format.
     */
    public function getChangesAttribute(): array
    {
        $changes = [];
        
        // List of encrypted fields that should be masked in audit logs
        $encryptedFields = ['ssn', 'date_of_birth', 'date_of_entry_us', 'mobile_number', 'work_number'];
        
        // List of fields that should be hidden entirely
        $hiddenFields = [];
        
        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                
                if ($oldValue !== $newValue) {
                    // Check if this is an encrypted field
                    if (in_array($key, $encryptedFields)) {
                        // Detect if values are encrypted JSON strings
                        $isOldEncrypted = $this->isEncryptedValue($oldValue);
                        $isNewEncrypted = $this->isEncryptedValue($newValue);
                        
                        $changes[$key] = [
                            'old' => $isOldEncrypted ? '[Encrypted]' : ($oldValue ?? 'null'),
                            'new' => $isNewEncrypted ? '[Encrypted]' : ($newValue ?? 'null'),
                        ];
                    } elseif (!in_array($key, $hiddenFields)) {
                        // Format other fields normally
                        $changes[$key] = [
                            'old' => $this->formatValue($oldValue),
                            'new' => $this->formatValue($newValue),
                        ];
                    }
                }
            }
        }
        
        return $changes;
    }
    
    /**
     * Check if a value is encrypted.
     */
    private function isEncryptedValue($value): bool
    {
        if (!is_string($value) || empty($value)) {
            return false;
        }
        
        // First try to decode as base64
        $decoded = base64_decode($value, true);
        if ($decoded === false) {
            // If base64 decode fails, try direct JSON decode
            $decoded = json_decode($value, true);
        } else {
            // If base64 decode succeeds, try to parse as JSON
            $decoded = json_decode($decoded, true);
        }
        
        // Check if it looks like an encrypted payload (JSON with iv, value, mac)
        return is_array($decoded) && isset($decoded['iv']) && isset($decoded['value']) && isset($decoded['mac']);
    }
    
    /**
     * Format a value for display.
     */
    private function formatValue($value)
    {
        if ($value === null) {
            return 'null';
        }
        
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        
        if (is_array($value)) {
            return json_encode($value);
        }
        
        return (string) $value;
    }

    /**
     * Get a human-readable description of the audit event.
     */
    public function getDescriptionAttribute(): string
    {
        $modelName = class_basename($this->auditable_type);
        $userName = $this->user ? $this->user->name : 'System';
        
        switch ($this->event) {
            case 'created':
                return "{$userName} created {$modelName} #{$this->auditable_id}";
            case 'updated':
                return "{$userName} updated {$modelName} #{$this->auditable_id}";
            case 'deleted':
                return "{$userName} deleted {$modelName} #{$this->auditable_id}";
            case 'restored':
                return "{$userName} restored {$modelName} #{$this->auditable_id}";
            default:
                return "{$userName} performed {$this->event} on {$modelName} #{$this->auditable_id}";
        }
    }

    /**
     * Scope to filter by auditable model.
     */
    public function scopeForModel($query, $model)
    {
        return $query->where('auditable_type', get_class($model))
                    ->where('auditable_id', $model->id);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by event type.
     */
    public function scopeByEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}