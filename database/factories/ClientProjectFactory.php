<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientProject>
 */
class ClientProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClientProject::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $projectType = fake()->randomElement(['tax_preparation', 'consultation', 'planning', 'audit', 'other']);
        $status = fake()->randomElement(['pending', 'in_progress', 'completed', 'cancelled']);
        $startDate = fake()->dateTimeBetween('-6 months', '+1 month');
        $dueDate = fake()->dateTimeBetween($startDate, '+3 months');
        $estimatedHours = fake()->numberBetween(5, 40);

        return [
            'client_id' => Client::factory(),
            'project_name' => fake()->sentence(3),
            'project_description' => fake()->optional()->paragraph(),
            'project_type' => $projectType,
            'status' => $status,
            'start_date' => $startDate->format('Y-m-d'),
            'due_date' => $dueDate->format('Y-m-d'),
            'completion_date' => $status === 'completed' ? fake()->dateTimeBetween($startDate, $dueDate)->format('Y-m-d') : null,
            'estimated_hours' => $estimatedHours,
            'actual_hours' => $status === 'completed' ? fake()->numberBetween($estimatedHours - 5, $estimatedHours + 10) : null,
            'notes' => fake()->optional()->paragraph(),
        ];
    }

    /**
     * Indicate that the project is completed.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = new \DateTime($attributes['start_date']);
            $dueDate = new \DateTime($attributes['due_date']);
            
            return [
                'status' => 'completed',
                'completion_date' => fake()->dateTimeBetween($startDate, $dueDate)->format('Y-m-d'),
                'actual_hours' => fake()->numberBetween($attributes['estimated_hours'] - 5, $attributes['estimated_hours'] + 10),
            ];
        });
    }
}