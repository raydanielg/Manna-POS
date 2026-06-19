<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $countries = [
            // East Africa
            ['name' => 'Tanzania', 'code' => 'TZ', 'phone_code' => '+255', 'flag_emoji' => '🇹🇿', 'region' => 'East Africa'],
            ['name' => 'Kenya', 'code' => 'KE', 'phone_code' => '+254', 'flag_emoji' => '🇰🇪', 'region' => 'East Africa'],
            ['name' => 'Uganda', 'code' => 'UG', 'phone_code' => '+256', 'flag_emoji' => '🇺🇬', 'region' => 'East Africa'],
            ['name' => 'Rwanda', 'code' => 'RW', 'phone_code' => '+250', 'flag_emoji' => '🇷🇼', 'region' => 'East Africa'],
            ['name' => 'Burundi', 'code' => 'BI', 'phone_code' => '+257', 'flag_emoji' => '🇧🇮', 'region' => 'East Africa'],
            ['name' => 'Ethiopia', 'code' => 'ET', 'phone_code' => '+251', 'flag_emoji' => '🇪🇹', 'region' => 'East Africa'],
            ['name' => 'South Sudan', 'code' => 'SS', 'phone_code' => '+211', 'flag_emoji' => '🇸🇸', 'region' => 'East Africa'],
            ['name' => 'Somalia', 'code' => 'SO', 'phone_code' => '+252', 'flag_emoji' => '🇸🇴', 'region' => 'East Africa'],
            ['name' => 'Djibouti', 'code' => 'DJ', 'phone_code' => '+253', 'flag_emoji' => '🇩🇯', 'region' => 'East Africa'],
            ['name' => 'Eritrea', 'code' => 'ER', 'phone_code' => '+291', 'flag_emoji' => '🇪🇷', 'region' => 'East Africa'],
            ['name' => 'Seychelles', 'code' => 'SC', 'phone_code' => '+248', 'flag_emoji' => '🇸🇨', 'region' => 'East Africa'],
            ['name' => 'Comoros', 'code' => 'KM', 'phone_code' => '+269', 'flag_emoji' => '🇰🇲', 'region' => 'East Africa'],
            ['name' => 'Madagascar', 'code' => 'MG', 'phone_code' => '+261', 'flag_emoji' => '🇲🇬', 'region' => 'East Africa'],
            ['name' => 'Mauritius', 'code' => 'MU', 'phone_code' => '+230', 'flag_emoji' => '🇲🇺', 'region' => 'East Africa'],
            ['name' => 'Malawi', 'code' => 'MW', 'phone_code' => '+265', 'flag_emoji' => '🇲🇼', 'region' => 'East Africa'],
            ['name' => 'Zambia', 'code' => 'ZM', 'phone_code' => '+260', 'flag_emoji' => '🇿🇲', 'region' => 'East Africa'],
            ['name' => 'Zimbabwe', 'code' => 'ZW', 'phone_code' => '+263', 'flag_emoji' => '🇿🇼', 'region' => 'East Africa'],
            ['name' => 'Mozambique', 'code' => 'MZ', 'phone_code' => '+258', 'flag_emoji' => '🇲🇿', 'region' => 'East Africa'],
            ['name' => 'Botswana', 'code' => 'BW', 'phone_code' => '+267', 'flag_emoji' => '🇧🇼', 'region' => 'East Africa'],
            // Central Africa
            ['name' => 'Democratic Republic of Congo', 'code' => 'CD', 'phone_code' => '+243', 'flag_emoji' => '🇨🇩', 'region' => 'Central Africa'],
        ];

        DB::statement('DELETE FROM countries');
        DB::table('countries')->insert($countries);
    }
}
