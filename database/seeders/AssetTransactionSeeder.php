<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssetTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Asset::count() === 0) {
            $this->call(AssetSeeder::class);
        }
        if (User::count() === 0) {
            $this->call(UserSeeder::class);
        }

        // Buat 15 transaksi
        AssetTransaction::factory()->count(15)->create();
    }
}
