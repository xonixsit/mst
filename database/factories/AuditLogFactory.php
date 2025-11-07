<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $events = ['created', 'updated', 'deleted', 'status_changed', 'assigned'];
        $event = $this->faker->randomElement($events);
        
        return [
            'auditable_type' => Client::class,
            'auditable_id' => Client::factory(),
            'event' => $event,
            'old_values' => $event === 'created' ? null : [
                'first_name' => $this->faker->firstName,
                'status' => 'active'
            ],
            'new_values' => $event === 'deleted' ? null : [
                'first_name' => $this->faker->firstName,
                'status' => $this->faker->randomElement(['active', 'inactive'])
            ],
            'user_id' => User::factory(),
            'user_type' => User::class,
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'metadata' => [
                'context' => 'client_management',
                'action' => ucfirst($event) . ' client',
                'source' => 'web_interface'
            ],
        ];
    }

    /**
     * Create an audit log for a created event.
     */
    public function created(): static
    {
        return $this->state(fn (array $attributes) => [
            'event' => 'created',
            'old_values' => null,
            'new_values' => [
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'email' => $this->faker->email,
                'status' => 'active'
            ],
        ]);
    }

    /**
     * Create an audit log for an updated event.
     */
    public function updated(): static
    {
        return $this->state(fn (array $attributes) => [
            'event' => 'updated',
            'old_values' => [
                'first_name' => 'Old Name',
                'status' => 'active'
            ],
            'new_values' => [
                'first_name' => 'New Name',
                'status' => 'active'
            ],
        ]);
    }

    /**
     * Create an audit log for a deleted event.
     */
    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'event' => 'deleted',
            'old_values' => [
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'status' => 'active'
            ],
            'new_values' => null,
        ]);
    }

    /**
     * Create an audit log for a status change event.
     */
    public function statusChanged(): static
    {
        return $this->state(fn (array $attributes) => [
            'event' => 'status_changed',
            'old_values' => ['status' => 'active'],
            'new_values' => ['status' => 'inactive'],
            'metadata' => array_merge($attributes['metadata'] ?? [], [
                'status_change' => [
                    'from' => 'active',
                    'to' => 'inactive'
                ]
            ]),
        ]);
    }

    /**
     * Create an audit log for a bulk operation.
     */
    public function bulkOperation(): static
    {
        return $this->state(fn (array $attributes) => [
            'auditable_id' => 0, // Bulk operations use 0
            'event' => 'bulk_status_update',
            'old_values' => null,
            'new_values' => [
                'affected_ids' => [1, 2, 3, 4, 5]
            ],
            'metadata' => [
                'operation' => 'status_update',
                'affected_count' => 5,
                'context' => 'bulk_operations'
            ],
        ]);
    }

    /**
     * Create an archived audit log.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'archived_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}