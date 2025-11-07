<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientExpense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientExpense>
 */
class ClientExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClientExpense::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['miscellaneous', 'residency', 'business', 'medical', 'education', 'other'];
        $category = fake()->randomElement($categories);
        $isDeductible = fake()->boolean(80); // 80% chance of being deductible

        $particulars = match($category) {
            'business' => fake()->randomElement(['Office supplies', 'Travel expenses', 'Meals', 'Equipment', 'Software']),
            'medical' => fake()->randomElement(['Doctor visit', 'Prescription', 'Dental care', 'Vision care', 'Medical equipment']),
            'education' => fake()->randomElement(['Tuition', 'Books', 'Supplies', 'Course fees', 'Training']),
            'residency' => fake()->randomElement(['Moving expenses', 'Temporary lodging', 'Storage', 'Transportation']),
            default => fake()->randomElement(['Professional fees', 'Insurance', 'Utilities', 'Maintenance', 'Other expenses']),
        };

        return [
            'client_id' => Client::factory(),
            'category' => $category,
            'particulars' => $particulars,
            'tax_payer' => fake()->name(),
            'spouse' => fake()->optional(30)->name(),
            'amount' => fake()->numberBetween(50, 5000),
            'expense_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'remarks' => fake()->optional()->sentence(),
            'receipt_number' => fake()->optional(70)->bothify('RCP-####-???'),
            'is_deductible' => $isDeductible,
            'deductible_percentage' => $isDeductible ? fake()->randomElement([50, 75, 100]) : 0,
        ];
    }

    /**
     * Indicate that the expense is fully deductible.
     */
    public function fullyDeductible(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_deductible' => true,
            'deductible_percentage' => 100,
        ]);
    }

    /**
     * Indicate that the expense is business-related.
     */
    public function business(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'business',
            'particulars' => fake()->randomElement(['Office supplies', 'Travel expenses', 'Meals', 'Equipment']),
            'is_deductible' => true,
            'deductible_percentage' => 100,
        ]);
    }
}