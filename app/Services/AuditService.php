<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an audit event.
     *
     * @param Model $model
     * @param string $event
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param array $metadata
     * @return AuditLog
     */
    public function log(
        Model $model,
        string $event,
        ?array $oldValues = null,
        ?array $newValues = null,
        array $metadata = []
    ): AuditLog {
        return AuditLog::create([
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => Auth::id(),
            'user_type' => Auth::user() ? get_class(Auth::user()) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Log a model creation event.
     *
     * @param Model $model
     * @param array $metadata
     * @return AuditLog
     */
    public function logCreated(Model $model, array $metadata = []): AuditLog
    {
        return $this->log(
            $model,
            'created',
            null,
            $model->getAttributes(),
            $metadata
        );
    }

    /**
     * Log a model update event.
     *
     * @param Model $model
     * @param array $metadata
     * @return AuditLog
     */
    public function logUpdated(Model $model, array $metadata = []): AuditLog
    {
        return $this->log(
            $model,
            'updated',
            $model->getOriginal(),
            $model->getAttributes(),
            $metadata
        );
    }

    /**
     * Log a model deletion event.
     *
     * @param Model $model
     * @param array $metadata
     * @return AuditLog
     */
    public function logDeleted(Model $model, array $metadata = []): AuditLog
    {
        return $this->log(
            $model,
            'deleted',
            $model->getAttributes(),
            null,
            $metadata
        );
    }

    /**
     * Log a bulk operation event.
     *
     * @param string $modelClass
     * @param array $modelIds
     * @param string $operation
     * @param array $metadata
     * @return AuditLog
     */
    public function logBulkOperation(
        string $modelClass,
        array $modelIds,
        string $operation,
        array $metadata = []
    ): AuditLog {
        // Create a generic audit log for bulk operations
        return AuditLog::create([
            'auditable_type' => $modelClass,
            'auditable_id' => 0, // Use 0 for bulk operations
            'event' => "bulk_{$operation}",
            'old_values' => null,
            'new_values' => ['affected_ids' => $modelIds],
            'user_id' => Auth::id(),
            'user_type' => Auth::user() ? get_class(Auth::user()) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => array_merge(is_array($metadata) ? $metadata : [], [
                'operation' => $operation,
                'affected_count' => count($modelIds),
            ]),
        ]);
    }

    /**
     * Log a status change event.
     *
     * @param Model $model
     * @param string $oldStatus
     * @param string $newStatus
     * @param array $metadata
     * @return AuditLog
     */
    public function logStatusChange(
        Model $model,
        string $oldStatus,
        string $newStatus,
        array $metadata = []
    ): AuditLog {
        return $this->log(
            $model,
            'status_changed',
            ['status' => $oldStatus],
            ['status' => $newStatus],
            array_merge(is_array($metadata) ? $metadata : [], [
                'status_change' => [
                    'from' => $oldStatus,
                    'to' => $newStatus,
                ]
            ])
        );
    }

    /**
     * Log a client assignment event.
     *
     * @param Model $model
     * @param int|null $oldUserId
     * @param int|null $newUserId
     * @param array $metadata
     * @return AuditLog
     */
    public function logAssignment(
        Model $model,
        ?int $oldUserId,
        ?int $newUserId,
        array $metadata = []
    ): AuditLog {
        return $this->log(
            $model,
            'assigned',
            ['user_id' => $oldUserId],
            ['user_id' => $newUserId],
            array_merge(is_array($metadata) ? $metadata : [], [
                'assignment_change' => [
                    'from_user_id' => $oldUserId,
                    'to_user_id' => $newUserId,
                ]
            ])
        );
    }

    /**
     * Get audit logs for a specific model.
     *
     * @param Model $model
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLogsForModel(Model $model, int $limit = 50)
    {
        return AuditLog::forModel($model)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get audit logs for a user.
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLogsForUser(int $userId, int $limit = 50)
    {
        return AuditLog::byUser($userId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent audit logs.
     *
     * @param int $limit
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentLogs(int $limit = 100, array $filters = [])
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if (!empty($filters['event'])) {
            $query->byEvent($filters['event']);
        }

        if (!empty($filters['user_id'])) {
            $query->byUser($filters['user_id']);
        }

        if (!empty($filters['model_type'])) {
            $query->where('auditable_type', $filters['model_type']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->inDateRange($filters['start_date'], $filters['end_date']);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Clean up old audit logs.
     *
     * @param int $daysToKeep
     * @return int Number of deleted records
     */
    public function cleanup(int $daysToKeep = 365): int
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        return AuditLog::where('created_at', '<', $cutoffDate)->delete();
    }
}