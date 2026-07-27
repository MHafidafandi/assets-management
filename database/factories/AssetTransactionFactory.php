<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetTransaction>
 */
class AssetTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isReturned = $this->faker->boolean(70);
        $borrowedAt = $this->faker->dateTimeBetween('-1 year', '-1 month');

        return [
            'asset_id' => Asset::factory(),
            'user_id' => User::factory(),
            'borrowed_at' => $borrowedAt,
            'returned_at' => $isReturned ? $this->faker->dateTimeBetween($borrowedAt, 'now') : null,
            'is_returned' => $isReturned,
            'notes' => [
                'condition_when_borrowed' => $this->faker->sentence(),
                'condition_when_returned' => $isReturned ? $this->faker->sentence() : null,
            ],
        ];
    }
}
