<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => $this->faker->words(3, true),
            'purchase_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'specification' => [
                'brand' => $this->faker->company(),
                'model' => $this->faker->bothify('Model-????'),
                'serial_number' => $this->faker->uuid(),
                'color' => $this->faker->colorName(),
            ],
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
