<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin User - Powerful credentials
        User::firstOrCreate(
            ['email' => 'admin@manna.pos'],
            [
                'name' => 'MannaPOS Admin',
                'password' => Hash::make('Admin@2024!'),
                'role' => 'admin',
                'business_name' => 'MannaPOS',
                'business_city' => 'Dar es Salaam',
                'business_country' => 'Tanzania',
                'currency' => 'TZS',
            ]
        );

        // Regular users
        User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );

        User::firstOrCreate(
            ['email' => 'jane@example.com'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );
    }
}
