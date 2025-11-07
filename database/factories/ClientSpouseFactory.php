<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientSpouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientSpouse>
 */
class ClientSpouseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClientSpouse::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => fake()->lastName(),
            'ssn' => fake()->optional()->regexify('\d{3}-\d{2}-\d{4}'),
            'date_of_birth' => fake()->optional()->dateTimeBetween('-80 years', '-18 years')?->format('Y-m-d'),
            'occupation' => fake()->optional()->jobTitle(),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
        ];
    }
}