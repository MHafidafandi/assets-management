<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Electronics',
            'Furniture',
            'Vehicles',
            'IT Equipment',
            'Machinery',
            'Office Supplies',
            'Property',
            'Software Licenses',
            'Network Equipment',
            'Mobile Devices',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category],
                ['is_active' => true]
            );
        }
    }
}
