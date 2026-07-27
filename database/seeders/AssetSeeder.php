<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan kategori ada
        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }

        // Buat 20 aset
        Asset::factory()->count(20)->create();
    }
}
