<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CountrySeeder::class,
            CurrencySeeder::class,
            AdminSeeder::class,
            ProductDataSeeder::class,
            DemoSeeder::class,
            AdminDemoSeeder::class,
            BlogSeeder::class,
        ]);
    }
}
