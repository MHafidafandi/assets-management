<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'Administrator',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'staf@gmail.com'],
            [
                'name' => 'Budi Staff',
                'password' => Hash::make('password'),
                'role' => 'Staff',
                'email_verified_at' => now(),
            ]
        );
    }
}
