<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        $currencies = [
            ['name' => 'Tanzanian Shilling', 'code' => 'TZS', 'symbol' => 'TSh', 'country' => 'Tanzania'],
            ['name' => 'Kenyan Shilling', 'code' => 'KES', 'symbol' => 'KSh', 'country' => 'Kenya'],
            ['name' => 'Ugandan Shilling', 'code' => 'UGX', 'symbol' => 'USh', 'country' => 'Uganda'],
            ['name' => 'Rwandan Franc', 'code' => 'RWF', 'symbol' => 'Fr', 'country' => 'Rwanda'],
            ['name' => 'Burundian Franc', 'code' => 'BIF', 'symbol' => 'Fr', 'country' => 'Burundi'],
            ['name' => 'Ethiopian Birr', 'code' => 'ETB', 'symbol' => 'Br', 'country' => 'Ethiopia'],
            ['name' => 'South Sudanese Pound', 'code' => 'SSP', 'symbol' => '£', 'country' => 'South Sudan'],
            ['name' => 'Somali Shilling', 'code' => 'SOS', 'symbol' => 'Sh', 'country' => 'Somalia'],
            ['name' => 'Djiboutian Franc', 'code' => 'DJF', 'symbol' => 'Fr', 'country' => 'Djibouti'],
            ['name' => 'Eritrean Nakfa', 'code' => 'ERN', 'symbol' => 'Nfk', 'country' => 'Eritrea'],
            ['name' => 'Seychellois Rupee', 'code' => 'SCR', 'symbol' => '₨', 'country' => 'Seychelles'],
            ['name' => 'Comorian Franc', 'code' => 'KMF', 'symbol' => 'Fr', 'country' => 'Comoros'],
            ['name' => 'Malagasy Ariary', 'code' => 'MGA', 'symbol' => 'Ar', 'country' => 'Madagascar'],
            ['name' => 'Mauritian Rupee', 'code' => 'MUR', 'symbol' => '₨', 'country' => 'Mauritius'],
            ['name' => 'Malawian Kwacha', 'code' => 'MWK', 'symbol' => 'K', 'country' => 'Malawi'],
            ['name' => 'Zambian Kwacha', 'code' => 'ZMW', 'symbol' => 'K', 'country' => 'Zambia'],
            ['name' => 'Zimbabwean Dollar', 'code' => 'ZWL', 'symbol' => '$', 'country' => 'Zimbabwe'],
            ['name' => 'Mozambican Metical', 'code' => 'MZN', 'symbol' => 'MT', 'country' => 'Mozambique'],
            ['name' => 'Botswana Pula', 'code' => 'BWP', 'symbol' => 'P', 'country' => 'Botswana'],
            ['name' => 'Congolese Franc', 'code' => 'CDF', 'symbol' => 'Fr', 'country' => 'DR Congo'],
            ['name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$', 'country' => 'United States'],
            ['name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'country' => 'European Union'],
            ['name' => 'British Pound', 'code' => 'GBP', 'symbol' => '£', 'country' => 'United Kingdom'],
            ['name' => 'South African Rand', 'code' => 'ZAR', 'symbol' => 'R', 'country' => 'South Africa'],
            ['name' => 'Nigerian Naira', 'code' => 'NGN', 'symbol' => '₦', 'country' => 'Nigeria'],
            ['name' => 'Ghanaian Cedi', 'code' => 'GHS', 'symbol' => '₵', 'country' => 'Ghana'],
        ];

        DB::statement('DELETE FROM currencies');
        DB::table('currencies')->insert($currencies);
    }
}
