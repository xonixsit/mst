<?php

namespace App\Traits;

use App\Models\AuditLog;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    /**
     * Boot the auditable trait.
     */
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            app(AuditService::class)->logCreated($model, [
                'context' => class_basename($model) . '_management',
                'action' => class_basename($model) . ' created'
            ]);
        });

        static::updated(function ($model) {
            if ($model->isDirty()) {
                $changes = [];
                foreach ($model->getDirty() as $key => $newValue) {
                    $changes[$key] = [
                        'old' => $model->getOriginal($key),
                        'new' => $newValue
                    ];
                }

                app(AuditService::class)->logUpdated($model, [
                    'context' => class_basename($model) . '_management',
                    'action' => class_basename($model) . ' updated',
                    'changes' => $changes
                ]);
            }
        });

        static::deleted(function ($model) {
            app(AuditService::class)->logDeleted($model, [
                'context' => class_basename($model) . '_management',
                'action' => class_basename($model) . ' deleted'
            ]);
        });

        // Only register restored event if the model uses soft deletes
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(static::class))) {
            static::restored(function ($model) {
                app(AuditService::class)->log($model, 'restored', null, $model->getAttributes(), [
                    'context' => class_basename($model) . '_management',
                    'action' => class_basename($model) . ' restored'
                ]);
            });
        }
    }

    /**
     * Get all audit logs for this model.
     */
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable')->orderBy('created_at', 'desc');
    }

    /**
     * Get recent audit logs for this model.
     */
    public function getRecentAuditLogs(int $limit = 10)
    {
        return $this->auditLogs()->with('user')->limit($limit)->get();
    }

    /**
     * Log a custom audit event for this model.
     */
    public function logAuditEvent(string $event, array $metadata = []): AuditLog
    {
        return app(AuditService::class)->log(
            $this,
            $event,
            null,
            null,
            array_merge($metadata, [
                'context' => class_basename($this) . '_management',
                'action' => $event
            ])
        );
    }

    /**
     * Log a status change for this model.
     */
    public function logStatusChange(string $oldStatus, string $newStatus, array $metadata = []): AuditLog
    {
        return app(AuditService::class)->logStatusChange(
            $this,
            $oldStatus,
            $newStatus,
            array_merge($metadata, [
                'context' => class_basename($this) . '_management'
            ])
        );
    }

    /**
     * Log an assignment change for this model.
     */
    public function logAssignmentChange(?int $oldUserId, ?int $newUserId, array $metadata = []): AuditLog
    {
        return app(AuditService::class)->logAssignment(
            $this,
            $oldUserId,
            $newUserId,
            array_merge($metadata, [
                'context' => class_basename($this) . '_management'
            ])
        );
    }
}