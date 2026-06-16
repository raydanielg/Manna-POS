<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SystemConfig;
use App\Models\BusinessCategory;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@manna.pos'],
            [
                'name' => 'MannaPOS Admin',
                'password' => Hash::make('password'),
                'phone' => '+255700000000',
                'role' => 'admin',
                'business_name' => 'mannaPOS',
                'business_type' => 'technology',
                'business_city' => 'Dar es Salaam',
                'business_country' => 'Tanzania',
                'currency' => 'TZS',
                'tax_percentage' => 18.0,
                'fiscal_year_start' => 'January',
            ]
        );

        // Truncate and re-seed system data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        SystemConfig::truncate();
        BusinessCategory::truncate();
        PaymentGateway::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        SystemConfig::insert([
            ['key' => 'app_name', 'value' => 'mannaPOS', 'group' => 'general', 'type' => 'text'],
            ['key' => 'app_description', 'value' => 'Point of Sale System', 'group' => 'general', 'type' => 'text'],
            ['key' => 'app_url', 'value' => url('/'), 'group' => 'general', 'type' => 'text'],
            ['key' => 'app_email', 'value' => 'admin@mannapos.com', 'group' => 'general', 'type' => 'email'],
            ['key' => 'app_phone', 'value' => '+255 123 456 789', 'group' => 'general', 'type' => 'text'],
            ['key' => 'app_address', 'value' => 'Dar es Salaam, Tanzania', 'group' => 'general', 'type' => 'text'],
            ['key' => 'app_timezone', 'value' => 'Africa/Dar_es_Salaam', 'group' => 'localization', 'type' => 'text'],
            ['key' => 'app_currency', 'value' => 'TZS', 'group' => 'localization', 'type' => 'text'],
            ['key' => 'app_locale', 'value' => 'en', 'group' => 'localization', 'type' => 'text'],
            ['key' => 'mail_driver', 'value' => 'smtp', 'group' => 'email', 'type' => 'text'],
            ['key' => 'mail_from_address', 'value' => 'noreply@mannapos.com', 'group' => 'email', 'type' => 'email'],
            ['key' => 'mail_from_name', 'value' => 'mannaPOS', 'group' => 'email', 'type' => 'text'],
            ['key' => 'payment_default_gateway', 'value' => 'stripe', 'group' => 'payment', 'type' => 'text'],
            ['key' => 'payment_currency', 'value' => 'TZS', 'group' => 'payment', 'type' => 'text'],
            ['key' => 'session_lifetime', 'value' => '120', 'group' => 'security', 'type' => 'number'],
            ['key' => 'max_login_attempts', 'value' => '5', 'group' => 'security', 'type' => 'number'],
            ['key' => 'lockout_duration', 'value' => '15', 'group' => 'security', 'type' => 'number'],
            ['key' => 'enable_registration', 'value' => 'true', 'group' => 'features', 'type' => 'boolean'],
            ['key' => 'enable_subscriptions', 'value' => 'true', 'group' => 'features', 'type' => 'boolean'],
            ['key' => 'enable_multi_currency', 'value' => 'false', 'group' => 'features', 'type' => 'boolean'],
        ]);

        BusinessCategory::insert([
            ['name' => 'Retail', 'slug' => 'retail', 'description' => 'Retail businesses selling products directly to consumers', 'icon' => 'shopping-cart', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Restaurant', 'slug' => 'restaurant', 'description' => 'Restaurants, cafes, and food service businesses', 'icon' => 'utensils', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Wholesale', 'slug' => 'wholesale', 'description' => 'Wholesale and distribution businesses', 'icon' => 'warehouse', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Services', 'slug' => 'services', 'description' => 'Service-based businesses', 'icon' => 'briefcase', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Manufacturing', 'slug' => 'manufacturing', 'description' => 'Manufacturing and production businesses', 'icon' => 'industry', 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Healthcare', 'slug' => 'healthcare', 'description' => 'Hospitals, clinics, and healthcare providers', 'icon' => 'heart-pulse', 'is_active' => true, 'sort_order' => 6],
            ['name' => 'Education', 'slug' => 'education', 'description' => 'Schools, colleges, and educational institutions', 'icon' => 'school', 'is_active' => true, 'sort_order' => 7],
            ['name' => 'Technology', 'slug' => 'technology', 'description' => 'Tech companies, software, and IT services', 'icon' => 'cpu', 'is_active' => true, 'sort_order' => 8],
        ]);

        PaymentGateway::insert([
            ['name' => 'Stripe', 'code' => 'stripe', 'description' => 'Credit/debit card payments via Stripe', 'is_active' => false, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PayPal', 'code' => 'paypal', 'description' => 'PayPal payment processing', 'is_active' => false, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tigo Pesa', 'code' => 'tigo_pesa', 'description' => 'Tigo Tanzania mobile money', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'M-Pesa', 'code' => 'mpesa', 'description' => 'Vodacom Tanzania mobile money', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Airtel Money', 'code' => 'airtel_money', 'description' => 'Airtel Tanzania mobile money', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bank Transfer', 'code' => 'bank_transfer', 'description' => 'Direct bank transfer / wire transfer', 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
