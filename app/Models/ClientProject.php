<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientProject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'project_name',
        'project_description',
        'project_type',
        'status',
        'start_date',
        'due_date',
        'completion_date',
        'estimated_hours',
        'actual_hours',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completion_date' => 'date',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Validation rules for project data.
     *
     * @return array<string, string>
     */
    public static function validationRules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'project_name' => 'required|string|max:255',
            'project_description' => 'nullable|string|max:1000',
            'project_type' => 'required|in:tax_preparation,consultation,planning,audit,other',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'completion_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0|max:99999.99',
            'actual_hours' => 'nullable|numeric|min:0|max:99999.99',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Get the client that owns this project.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Check if the project is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Get the project progress percentage.
     */
    public function getProgressPercentageAttribute(): int
    {
        return match($this->status) {
            'pending' => 0,
            'in_progress' => 50,
            'completed' => 100,
            'cancelled' => 0,
            default => 0,
        };
    }

    /**
     * Get hours variance (actual vs estimated).
     */
    public function getHoursVarianceAttribute(): ?float
    {
        if (!$this->estimated_hours || !$this->actual_hours) {
            return null;
        }

        return $this->actual_hours - $this->estimated_hours;
    }

    /**
     * Scope a query to only include active projects.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }

    /**
     * Scope a query to only include overdue projects.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope a query by project type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('project_type', $type);
    }
}