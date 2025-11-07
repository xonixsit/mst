<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'ssn' => fake()->regexify('\d{3}-\d{2}-\d{4}'),
            'date_of_birth' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'marital_status' => fake()->randomElement(['single', 'married', 'divorced', 'widowed']),
            'occupation' => fake()->jobTitle(),
            'insurance_covered' => fake()->boolean(),
            'street_no' => fake()->streetAddress(),
            'apartment_no' => fake()->optional()->secondaryAddress(),
            'zip_code' => fake()->postcode(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'country' => 'USA',
            'mobile_number' => fake()->phoneNumber(),
            'work_number' => fake()->optional()->phoneNumber(),
            'visa_status' => fake()->optional()->randomElement(['H1B', 'L1', 'F1', 'Green Card', 'Citizen']),
            'date_of_entry_us' => fake()->optional()->dateTimeBetween('-20 years', 'now')?->format('Y-m-d'),
            'status' => fake()->randomElement(['active', 'inactive', 'archived']),
        ];
    }

    /**
     * Indicate that the client is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the client is married.
     */
    public function married(): static
    {
        return $this->state(fn (array $attributes) => [
            'marital_status' => 'married',
        ]);
    }

    /**
     * Indicate that the client has insurance coverage.
     */
    public function withInsurance(): static
    {
        return $this->state(fn (array $attributes) => [
            'insurance_covered' => true,
        ]);
    }
}