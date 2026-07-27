<?php

namespace Database\Seeders;

use App\Models\Role;
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
        $adminRole = Role::firstOrCreate(
            ['name' => 'Administrator'],
            ['description' => 'System administrator with full permissions']
        );

        $staffRole = Role::firstOrCreate(
            ['name' => 'Staff'],
            ['description' => 'Office staff member']
        );

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'staf@gmail.com'],
            [
                'name' => 'Budi Staff',
                'password' => Hash::make('password'),
                'role_id' => $staffRole->id,
                'email_verified_at' => now(),
            ]
        );
    }
}
