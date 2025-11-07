<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientEmployee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientEmployee>
 */
class ClientEmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClientEmployee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-5 years', '-1 year');
        $isCurrentJob = fake()->boolean(70); // 70% chance of current job

        return [
            'client_id' => Client::factory(),
            'employer_name' => fake()->company(),
            'job_title' => fake()->jobTitle(),
            'employee_id' => fake()->optional()->bothify('EMP-####'),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $isCurrentJob ? null : fake()->dateTimeBetween($startDate, 'now')->format('Y-m-d'),
            'annual_salary' => fake()->numberBetween(30000, 150000),
            'employment_type' => fake()->randomElement(['full-time', 'part-time', 'contract', 'temporary']),
            'work_description' => fake()->optional()->paragraph(),
            'employer_address' => fake()->streetAddress(),
            'employer_city' => fake()->city(),
            'employer_state' => fake()->stateAbbr(),
            'employer_zip_code' => fake()->postcode(),
        ];
    }

    /**
     * Indicate that this is current employment.
     */
    public function current(): static
    {
        return $this->state(fn (array $attributes) => [
            'end_date' => null,
        ]);
    }
}