<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetAttachment>
 */
class AssetAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory(),
            'file_name' => $this->faker->word().'.pdf',
            'file_path' => 'attachments/'.$this->faker->uuid().'.pdf',
            'file_size' => $this->faker->numberBetween(100, 500), // KB
            'mime_type' => 'application/pdf',
        ];
    }
}
