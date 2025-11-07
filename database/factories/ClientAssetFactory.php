<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientAsset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientAsset>
 */
class ClientAssetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClientAsset::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $assetTypes = ['equipment', 'vehicle', 'computer', 'furniture', 'machinery', 'property'];
        $assetType = fake()->randomElement($assetTypes);
        $costOfAcquisition = fake()->numberBetween(500, 50000);
        $businessPercentage = fake()->numberBetween(0, 100);

        return [
            'client_id' => Client::factory(),
            'asset_name' => fake()->words(2, true) . ' ' . ucfirst($assetType),
            'date_of_purchase' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'percentage_used_in_business' => $businessPercentage,
            'cost_of_acquisition' => $costOfAcquisition,
            'any_reimbursement' => fake()->optional(30)->numberBetween(0, $costOfAcquisition * 0.3),
            'asset_type' => $assetType,
            'description' => fake()->optional()->sentence(),
            'current_value' => fake()->optional()->numberBetween($costOfAcquisition * 0.3, $costOfAcquisition * 0.8),
            'depreciation_rate' => fake()->optional()->numberBetween(5, 25),
        ];
    }

    /**
     * Indicate that the asset is fully used for business.
     */
    public function businessUse(): static
    {
        return $this->state(fn (array $attributes) => [
            'percentage_used_in_business' => 100,
        ]);
    }

    /**
     * Indicate that the asset is a vehicle.
     */
    public function vehicle(): static
    {
        return $this->state(fn (array $attributes) => [
            'asset_type' => 'vehicle',
            'asset_name' => fake()->randomElement(['Honda Civic', 'Toyota Camry', 'Ford F-150', 'Chevrolet Silverado']),
            'cost_of_acquisition' => fake()->numberBetween(15000, 60000),
        ]);
    }
}