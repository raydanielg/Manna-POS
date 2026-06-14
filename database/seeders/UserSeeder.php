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
        // Admin User - Powerful credentials (can always login)
        User::firstOrCreate(
            ['email' => 'admin@manna.pos'],
            [
                'name' => 'MannaPOS Admin',
                'password' => Hash::make('Admin@2024!'),
                'role' => 'admin',
                'phone' => '+255123456789',
                'business_name' => 'MannaPOS',
                'business_type' => 'retail',
                'business_city' => 'Dar es Salaam',
                'business_country' => 'Tanzania',
                'currency' => 'TZS',
                'is_active' => true,
            ]
        );

        // Manager User
        User::firstOrCreate(
            ['email' => 'manager@manna.pos'],
            [
                'name' => 'MannaPOS Manager',
                'password' => Hash::make('Manager@2024!'),
                'role' => 'manager',
                'phone' => '+255123456790',
                'business_name' => 'MannaPOS',
                'business_type' => 'retail',
                'business_city' => 'Dar es Salaam',
                'business_country' => 'Tanzania',
                'currency' => 'TZS',
                'is_active' => true,
            ]
        );

        // Cashier User
        User::firstOrCreate(
            ['email' => 'cashier@manna.pos'],
            [
                'name' => 'MannaPOS Cashier',
                'password' => Hash::make('Cashier@2024!'),
                'role' => 'cashier',
                'phone' => '+255123456791',
                'business_name' => 'MannaPOS',
                'business_type' => 'retail',
                'business_city' => 'Dar es Salaam',
                'business_country' => 'Tanzania',
                'currency' => 'TZS',
                'is_active' => true,
            ]
        );

        // Staff User
        User::firstOrCreate(
            ['email' => 'staff@manna.pos'],
            [
                'name' => 'MannaPOS Staff',
                'password' => Hash::make('Staff@2024!'),
                'role' => 'staff',
                'phone' => '+255123456792',
                'business_name' => 'MannaPOS',
                'business_type' => 'retail',
                'business_city' => 'Dar es Salaam',
                'business_country' => 'Tanzania',
                'currency' => 'TZS',
                'is_active' => true,
            ]
        );

        // Regular User
        User::firstOrCreate(
            ['email' => 'user@manna.pos'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('User@2024!'),
                'role' => 'user',
                'phone' => '+255123456793',
                'business_name' => 'MannaPOS',
                'business_type' => 'retail',
                'business_city' => 'Dar es Salaam',
                'business_country' => 'Tanzania',
                'currency' => 'TZS',
                'is_active' => true,
            ]
        );
    }
}
