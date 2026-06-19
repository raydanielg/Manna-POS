<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Brand;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ProductDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Seeding product master data (Categories, Brands, Units, Products)...');

        // ── CATEGORIES ──
        $categories = [
            ['name' => 'Food & Groceries', 'description' => 'General food items and groceries'],
            ['name' => 'Beverages', 'description' => 'Drinks, sodas, juices, water'],
            ['name' => 'Household Items', 'description' => 'Cleaning and household essentials'],
            ['name' => 'Personal Care', 'description' => 'Toiletries and personal hygiene'],
            ['name' => 'Electronics', 'description' => 'Electronic gadgets and accessories'],
            ['name' => 'Dairy & Eggs', 'description' => 'Milk, cheese, yogurt, eggs'],
            ['name' => 'Bakery', 'description' => 'Bread, cakes, pastries'],
            ['name' => 'Frozen Foods', 'description' => 'Frozen meat, vegetables, snacks'],
            ['name' => 'Snacks & Confectionery', 'description' => 'Chips, biscuits, sweets, chocolate'],
            ['name' => 'Rice, Pasta & Grains', 'description' => 'Rice, spaghetti, maize flour, wheat'],
            ['name' => 'Cooking Oil & Spices', 'description' => 'Vegetable oil, spices, seasonings'],
            ['name' => 'Baby Care', 'description' => 'Diapers, baby food, wipes'],
            ['name' => 'Health & Wellness', 'description' => 'Vitamins, supplements, first aid'],
            ['name' => 'Pet Supplies', 'description' => 'Pet food and accessories'],
            ['name' => 'Office & Stationery', 'description' => 'Pens, paper, notebooks, folders'],
        ];
        foreach ($categories as $cat) {
            ProductCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // ── BRANDS ──
        $brands = [
            ['name' => 'Coca-Cola'],
            ['name' => 'Pepsi'],
            ['name' => 'Nestle'],
            ['name' => 'Unilever'],
            ['name' => 'Procter & Gamble'],
            ['name' => 'Samsung'],
            ['name' => 'Nokia'],
            ['name' => 'Tigo'],
            ['name' => 'Vodacom'],
            ['name' => 'Azam'],
            ['name' => 'Bakhresa (Azam)'],
            ['name' => 'Mohammed Enterprises'],
            ['name' => 'Kilimanjaro Water'],
            ['name' => 'Dangote'],
            ['name' => 'Mikumi Rice'],
            ['name' => 'Safi'],
            ['name' => 'Vim'],
            ['name' => 'Colgate'],
            ['name' => 'Nivea'],
            ['name' => 'Pampers'],
            ['name' => 'Huggies'],
            ['name' => 'Cadbury'],
            ['name' => 'Lays'],
            ['name' => 'Blue Band'],
            ['name' => 'KCC'],
        ];
        foreach ($brands as $brand) {
            Brand::firstOrCreate(['name' => $brand['name']], $brand);
        }

        // ── UNITS ──
        $units = [
            ['name' => 'Piece', 'short_name' => 'pcs'],
            ['name' => 'Kilogram', 'short_name' => 'kg'],
            ['name' => 'Gram', 'short_name' => 'g'],
            ['name' => 'Liter', 'short_name' => 'L'],
            ['name' => 'Milliliter', 'short_name' => 'ml'],
            ['name' => 'Carton', 'short_name' => 'ctn'],
            ['name' => 'Dozen', 'short_name' => 'dz'],
            ['name' => 'Pack', 'short_name' => 'pk'],
            ['name' => 'Box', 'short_name' => 'box'],
            ['name' => 'Bag', 'short_name' => 'bag'],
            ['name' => 'Bottle', 'short_name' => 'btl'],
            ['name' => 'Sachet', 'short_name' => 'sct'],
            ['name' => 'Pair', 'short_name' => 'pr'],
            ['name' => 'Roll', 'short_name' => 'rl'],
            ['name' => 'Can', 'short_name' => 'can'],
        ];
        foreach ($units as $unit) {
            Unit::firstOrCreate(['name' => $unit['name']], $unit);
        }

        // ── PRODUCTS ──
        $products = [
            // Beverages
            ['name' => 'Coca-Cola 500ml', 'sku' => 'CC-500', 'barcode' => '5449000000996', 'category_id' => 2, 'brand_id' => 1, 'unit_id' => 1, 'description' => 'Refreshing soft drink', 'purchase_price' => 1200, 'selling_price' => 1800, 'stock_quantity' => 150, 'reorder_level' => 20, 'status' => 'active'],
            ['name' => 'Coca-Cola 1.25L', 'sku' => 'CC-125', 'barcode' => '5449000001009', 'category_id' => 2, 'brand_id' => 1, 'unit_id' => 1, 'description' => 'Family size Coca-Cola', 'purchase_price' => 2200, 'selling_price' => 3200, 'stock_quantity' => 80, 'reorder_level' => 15, 'status' => 'active'],
            ['name' => 'Pepsi 500ml', 'sku' => 'PE-500', 'barcode' => '6221000000010', 'category_id' => 2, 'brand_id' => 2, 'unit_id' => 1, 'description' => 'Pepsi soft drink', 'purchase_price' => 1150, 'selling_price' => 1750, 'stock_quantity' => 120, 'reorder_level' => 20, 'status' => 'active'],
            ['name' => 'Fanta Orange 500ml', 'sku' => 'FN-ORG', 'barcode' => '6221000000011', 'category_id' => 2, 'brand_id' => 1, 'unit_id' => 1, 'description' => 'Orange flavored soda', 'purchase_price' => 1200, 'selling_price' => 1800, 'stock_quantity' => 100, 'reorder_level' => 15, 'status' => 'active'],
            ['name' => 'Sprite 500ml', 'sku' => 'SP-500', 'barcode' => '6221000000012', 'category_id' => 2, 'brand_id' => 1, 'unit_id' => 1, 'description' => 'Lemon-lime soda', 'purchase_price' => 1200, 'selling_price' => 1800, 'stock_quantity' => 90, 'reorder_level' => 15, 'status' => 'active'],
            ['name' => 'Kilimanjaro Water 1L', 'sku' => 'KW-1L', 'barcode' => '6221000000013', 'category_id' => 2, 'brand_id' => 13, 'unit_id' => 1, 'description' => 'Pure mineral water', 'purchase_price' => 800, 'selling_price' => 1200, 'stock_quantity' => 200, 'reorder_level' => 30, 'status' => 'active'],
            ['name' => 'Azam Juice Mango 1L', 'sku' => 'AJ-MG', 'barcode' => '6221000000014', 'category_id' => 2, 'brand_id' => 10, 'unit_id' => 1, 'description' => 'Mango fruit juice', 'purchase_price' => 2500, 'selling_price' => 3500, 'stock_quantity' => 60, 'reorder_level' => 10, 'status' => 'active'],
            ['name' => 'Nestle Milo 400g', 'sku' => 'NL-ML', 'barcode' => '6221000000015', 'category_id' => 2, 'brand_id' => 3, 'unit_id' => 2, 'description' => 'Chocolate malt drink powder', 'purchase_price' => 5500, 'selling_price' => 7500, 'stock_quantity' => 40, 'reorder_level' => 8, 'status' => 'active'],

            // Food & Groceries
            ['name' => 'Bread - White Sliced', 'sku' => 'BR-WH', 'barcode' => '6221000000001', 'category_id' => 1, 'brand_id' => null, 'unit_id' => 1, 'description' => 'Fresh white bread', 'purchase_price' => 800, 'selling_price' => 1200, 'stock_quantity' => 45, 'reorder_level' => 15, 'status' => 'active'],
            ['name' => 'Bread - Brown Sliced', 'sku' => 'BR-BR', 'barcode' => '6221000000016', 'category_id' => 1, 'brand_id' => null, 'unit_id' => 1, 'description' => 'Healthy brown bread', 'purchase_price' => 900, 'selling_price' => 1400, 'stock_quantity' => 30, 'reorder_level' => 10, 'status' => 'active'],
            ['name' => 'Azam Wheat Flour 2kg', 'sku' => 'AW-FL', 'barcode' => '6221000000017', 'category_id' => 1, 'brand_id' => 10, 'unit_id' => 2, 'description' => 'All purpose wheat flour', 'purchase_price' => 3200, 'selling_price' => 4500, 'stock_quantity' => 50, 'reorder_level' => 10, 'status' => 'active'],
            ['name' => 'Mikumi Rice Super 5kg', 'sku' => 'MR-5K', 'barcode' => '6221000000003', 'category_id' => 10, 'brand_id' => 15, 'unit_id' => 2, 'description' => 'Premium quality rice', 'purchase_price' => 15000, 'selling_price' => 18500, 'stock_quantity' => 25, 'reorder_level' => 8, 'status' => 'active'],
            ['name' => 'Mikumi Rice Super 10kg', 'sku' => 'MR-10K', 'barcode' => '6221000000018', 'category_id' => 10, 'brand_id' => 15, 'unit_id' => 2, 'description' => 'Premium rice family pack', 'purchase_price' => 28000, 'selling_price' => 35000, 'stock_quantity' => 15, 'reorder_level' => 5, 'status' => 'active'],
            ['name' => 'Sugar - White 2kg', 'sku' => 'SG-2KG', 'barcode' => '6221000000004', 'category_id' => 1, 'brand_id' => null, 'unit_id' => 2, 'description' => 'White refined sugar', 'purchase_price' => 5500, 'selling_price' => 7000, 'stock_quantity' => 80, 'reorder_level' => 15, 'status' => 'active'],
            ['name' => 'Sugar - White 1kg', 'sku' => 'SG-1KG', 'barcode' => '6221000000019', 'category_id' => 1, 'brand_id' => null, 'unit_id' => 2, 'description' => 'White refined sugar 1kg', 'purchase_price' => 2800, 'selling_price' => 3600, 'stock_quantity' => 100, 'reorder_level' => 20, 'status' => 'active'],
            ['name' => 'Dangote Cement 50kg', 'sku' => 'DC-50', 'barcode' => '6221000000020', 'category_id' => 1, 'brand_id' => 14, 'unit_id' => 2, 'description' => 'Portland cement', 'purchase_price' => 18000, 'selling_price' => 22000, 'stock_quantity' => 200, 'reorder_level' => 50, 'status' => 'active'],
            ['name' => 'Spaghetti 500g', 'sku' => 'SP-500', 'barcode' => '6221000000021', 'category_id' => 10, 'brand_id' => null, 'unit_id' => 2, 'description' => 'Italian pasta', 'purchase_price' => 1500, 'selling_price' => 2200, 'stock_quantity' => 60, 'reorder_level' => 12, 'status' => 'active'],
            ['name' => 'Maize Flour (Sembe) 5kg', 'sku' => 'MF-5K', 'barcode' => '6221000000022', 'category_id' => 10, 'brand_id' => null, 'unit_id' => 2, 'description' => 'White maize flour', 'purchase_price' => 6500, 'selling_price' => 8500, 'stock_quantity' => 40, 'reorder_level' => 10, 'status' => 'active'],

            // Cooking Oil & Spices
            ['name' => 'Cooking Oil 1L', 'sku' => 'CO-1L', 'barcode' => '6221000000002', 'category_id' => 11, 'brand_id' => 4, 'unit_id' => 4, 'description' => 'Pure vegetable cooking oil', 'purchase_price' => 3500, 'selling_price' => 4800, 'stock_quantity' => 60, 'reorder_level' => 10, 'status' => 'active'],
            ['name' => 'Cooking Oil 5L', 'sku' => 'CO-5L', 'barcode' => '6221000000023', 'category_id' => 11, 'brand_id' => 4, 'unit_id' => 4, 'description' => 'Vegetable cooking oil 5L', 'purchase_price' => 15000, 'selling_price' => 19500, 'stock_quantity' => 25, 'reorder_level' => 5, 'status' => 'active'],
            ['name' => 'Blue Band Margarine 250g', 'sku' => 'BB-250', 'barcode' => '6221000000024', 'category_id' => 11, 'brand_id' => 24, 'unit_id' => 2, 'description' => 'Spreadable margarine', 'purchase_price' => 2500, 'selling_price' => 3500, 'stock_quantity' => 50, 'reorder_level' => 10, 'status' => 'active'],
            ['name' => 'Curry Powder 100g', 'sku' => 'CP-100', 'barcode' => '6221000000025', 'category_id' => 11, 'brand_id' => null, 'unit_id' => 2, 'description' => 'Mixed curry spices', 'purchase_price' => 1200, 'selling_price' => 1800, 'stock_quantity' => 40, 'reorder_level' => 8, 'status' => 'active'],
            ['name' => 'Salt 1kg', 'sku' => 'ST-1K', 'barcode' => '6221000000026', 'category_id' => 11, 'brand_id' => null, 'unit_id' => 2, 'description' => 'Iodized table salt', 'purchase_price' => 800, 'selling_price' => 1200, 'stock_quantity' => 100, 'reorder_level' => 20, 'status' => 'active'],
            ['name' => 'Tea Leaves 250g', 'sku' => 'TL-250', 'barcode' => '6221000000027', 'category_id' => 11, 'brand_id' => null, 'unit_id' => 2, 'description' => 'Premium black tea', 'purchase_price' => 3000, 'selling_price' => 4500, 'stock_quantity' => 45, 'reorder_level' => 10, 'status' => 'active'],
            ['name' => 'Coffee 100g', 'sku' => 'CF-100', 'barcode' => '6221000000028', 'category_id' => 11, 'brand_id' => 3, 'unit_id' => 2, 'description' => 'Instant coffee powder', 'purchase_price' => 4000, 'selling_price' => 5500, 'stock_quantity' => 35, 'reorder_level' => 8, 'status' => 'active'],

            // Dairy & Eggs
            ['name' => 'Milk - Fresh 1L', 'sku' => 'MK-1L', 'barcode' => '6221000000009', 'category_id' => 6, 'brand_id' => 3, 'unit_id' => 4, 'description' => 'Fresh pasteurized milk', 'purchase_price' => 2800, 'selling_price' => 3800, 'stock_quantity' => 40, 'reorder_level' => 12, 'status' => 'active'],
            ['name' => 'Milk - Fresh 500ml', 'sku' => 'MK-500', 'barcode' => '6221000000029', 'category_id' => 6, 'brand_id' => 3, 'unit_id' => 4, 'description' => 'Fresh milk half liter', 'purchase_price' => 1500, 'selling_price' => 2200, 'stock_quantity' => 50, 'reorder_level' => 15, 'status' => 'active'],
            ['name' => 'Eggs - Tray 30pcs', 'sku' => 'EG-30', 'barcode' => '6221000000030', 'category_id' => 6, 'brand_id' => null, 'unit_id' => 7, 'description' => 'Fresh chicken eggs', 'purchase_price' => 9000, 'selling_price' => 12000, 'stock_quantity' => 20, 'reorder_level' => 5, 'status' => 'active'],
            ['name' => 'Cheese 200g', 'sku' => 'CH-200', 'barcode' => '6221000000031', 'category_id' => 6, 'brand_id' => 3, 'unit_id' => 2, 'description' => 'Processed cheese', 'purchase_price' => 4500, 'selling_price' => 6500, 'stock_quantity' => 25, 'reorder_level' => 6, 'status' => 'active'],
            ['name' => 'Yogurt 500g', 'sku' => 'YG-500', 'barcode' => '6221000000032', 'category_id' => 6, 'brand_id' => 3, 'unit_id' => 2, 'description' => 'Natural yogurt', 'purchase_price' => 2500, 'selling_price' => 3500, 'stock_quantity' => 30, 'reorder_level' => 8, 'status' => 'active'],

            // Snacks & Confectionery
            ['name' => 'Cadbury Dairy Milk 150g', 'sku' => 'CD-150', 'barcode' => '6221000000033', 'category_id' => 9, 'brand_id' => 22, 'unit_id' => 1, 'description' => 'Milk chocolate bar', 'purchase_price' => 3500, 'selling_price' => 5000, 'stock_quantity' => 40, 'reorder_level' => 8, 'status' => 'active'],
            ['name' => 'Lays Chips 150g', 'sku' => 'LY-150', 'barcode' => '6221000000034', 'category_id' => 9, 'brand_id' => 23, 'unit_id' => 1, 'description' => 'Potato chips', 'purchase_price' => 2000, 'selling_price' => 3000, 'stock_quantity' => 50, 'reorder_level' => 10, 'status' => 'active'],
            ['name' => 'Biscuits - Digestive 400g', 'sku' => 'BC-DG', 'barcode' => '6221000000035', 'category_id' => 9, 'brand_id' => null, 'unit_id' => 2, 'description' => 'Digestive biscuits', 'purchase_price' => 2800, 'selling_price' => 4000, 'stock_quantity' => 35, 'reorder_level' => 8, 'status' => 'active'],
            ['name' => 'Peanuts - Roasted 250g', 'sku' => 'PN-250', 'barcode' => '6221000000036', 'category_id' => 9, 'brand_id' => null, 'unit_id' => 2, 'description' => 'Roasted peanuts', 'purchase_price' => 1800, 'selling_price' => 2800, 'stock_quantity' => 45, 'reorder_level' => 10, 'status' => 'active'],

            // Household Items
            ['name' => 'Detergent Powder 1kg', 'sku' => 'DP-1KG', 'barcode' => '6221000000007', 'category_id' => 3, 'brand_id' => 5, 'unit_id' => 2, 'description' => 'Laundry detergent powder', 'purchase_price' => 4000, 'selling_price' => 5500, 'stock_quantity' => 35, 'reorder_level' => 10, 'status' => 'active'],
            ['name' => 'Bleach 750ml', 'sku' => 'BL-750', 'barcode' => '6221000000008', 'category_id' => 3, 'brand_id' => null, 'unit_id' => 4, 'description' => 'Household bleach', 'purchase_price' => 3000, 'selling_price' => 4200, 'stock_quantity' => 20, 'reorder_level' => 8, 'status' => 'active'],
            ['name' => 'Vim Dishwashing Paste 400g', 'sku' => 'VM-400', 'barcode' => '6221000000037', 'category_id' => 3, 'brand_id' => 17, 'unit_id' => 2, 'description' => 'Dish washing paste', 'purchase_price' => 1200, 'selling_price' => 1800, 'stock_quantity' => 60, 'reorder_level' => 15, 'status' => 'active'],
            ['name' => 'Toilet Paper 10 Rolls', 'sku' => 'TP-10', 'barcode' => '6221000000038', 'category_id' => 3, 'brand_id' => null, 'unit_id' => 1, 'description' => 'Soft toilet tissue', 'purchase_price' => 8000, 'selling_price' => 11000, 'stock_quantity' => 30, 'reorder_level' => 6, 'status' => 'active'],
            ['name' => 'Garbage Bags 30pcs', 'sku' => 'GB-30', 'barcode' => '6221000000039', 'category_id' => 3, 'brand_id' => null, 'unit_id' => 1, 'description' => 'Strong garbage bags', 'purchase_price' => 2500, 'selling_price' => 3500, 'stock_quantity' => 40, 'reorder_level' => 8, 'status' => 'active'],

            // Personal Care
            ['name' => 'Toothpaste - Mint 100ml', 'sku' => 'TP-MT', 'barcode' => '6221000000005', 'category_id' => 4, 'brand_id' => 18, 'unit_id' => 1, 'description' => 'Mint flavored toothpaste', 'purchase_price' => 2500, 'selling_price' => 3500, 'stock_quantity' => 5, 'reorder_level' => 10, 'status' => 'active'],
            ['name' => 'Soap - Bath Bar', 'sku' => 'SP-BB', 'barcode' => '6221000000006', 'category_id' => 4, 'brand_id' => 4, 'unit_id' => 1, 'description' => 'Moisturizing bath soap', 'purchase_price' => 1500, 'selling_price' => 2200, 'stock_quantity' => 3, 'reorder_level' => 12, 'status' => 'active'],
            ['name' => 'Nivea Lotion 400ml', 'sku' => 'NV-400', 'barcode' => '6221000000040', 'category_id' => 4, 'brand_id' => 19, 'unit_id' => 1, 'description' => 'Body moisturizing lotion', 'purchase_price' => 8500, 'selling_price' => 12000, 'stock_quantity' => 20, 'reorder_level' => 5, 'status' => 'active'],
            ['name' => 'Shampoo 250ml', 'sku' => 'SH-250', 'barcode' => '6221000000041', 'category_id' => 4, 'brand_id' => 5, 'unit_id' => 1, 'description' => 'Hair shampoo', 'purchase_price' => 4500, 'selling_price' => 6500, 'stock_quantity' => 25, 'reorder_level' => 6, 'status' => 'active'],
            ['name' => 'Toothbrush Soft', 'sku' => 'TB-SF', 'barcode' => '6221000000042', 'category_id' => 4, 'brand_id' => 18, 'unit_id' => 1, 'description' => 'Soft bristle toothbrush', 'purchase_price' => 800, 'selling_price' => 1200, 'stock_quantity' => 80, 'reorder_level' => 20, 'status' => 'active'],
            ['name' => 'Hand Sanitizer 500ml', 'sku' => 'HS-500', 'barcode' => '6221000000043', 'category_id' => 4, 'brand_id' => null, 'unit_id' => 1, 'description' => 'Alcohol-based sanitizer', 'purchase_price' => 3500, 'selling_price' => 5000, 'stock_quantity' => 35, 'reorder_level' => 8, 'status' => 'active'],

            // Baby Care
            ['name' => 'Pampers Diapers Size 4, 50pcs', 'sku' => 'PD-S4', 'barcode' => '6221000000044', 'category_id' => 12, 'brand_id' => 20, 'unit_id' => 1, 'description' => 'Baby disposable diapers', 'purchase_price' => 25000, 'selling_price' => 32000, 'stock_quantity' => 15, 'reorder_level' => 4, 'status' => 'active'],
            ['name' => 'Baby Wipes 80pcs', 'sku' => 'BW-80', 'barcode' => '6221000000045', 'category_id' => 12, 'brand_id' => 20, 'unit_id' => 1, 'description' => 'Gentle baby wet wipes', 'purchase_price' => 3500, 'selling_price' => 5000, 'stock_quantity' => 30, 'reorder_level' => 6, 'status' => 'active'],
            ['name' => 'Baby Food Cereal 500g', 'sku' => 'BF-500', 'barcode' => '6221000000046', 'category_id' => 12, 'brand_id' => 3, 'unit_id' => 2, 'description' => 'Infant cereal porridge', 'purchase_price' => 5500, 'selling_price' => 7500, 'stock_quantity' => 20, 'reorder_level' => 5, 'status' => 'active'],

            // Electronics
            ['name' => 'USB Flash Drive 32GB', 'sku' => 'USB-32', 'barcode' => '6221000000047', 'category_id' => 5, 'brand_id' => 6, 'unit_id' => 1, 'description' => '32GB USB 3.0 flash drive', 'purchase_price' => 15000, 'selling_price' => 22000, 'stock_quantity' => 20, 'reorder_level' => 5, 'status' => 'active'],
            ['name' => 'Phone Charger USB-C', 'sku' => 'CH-USBC', 'barcode' => '6221000000048', 'category_id' => 5, 'brand_id' => null, 'unit_id' => 1, 'description' => 'Fast charger type C', 'purchase_price' => 8000, 'selling_price' => 12000, 'stock_quantity' => 25, 'reorder_level' => 6, 'status' => 'active'],
            ['name' => 'Power Extension 4-Socket', 'sku' => 'PE-4S', 'barcode' => '6221000000049', 'category_id' => 5, 'brand_id' => null, 'unit_id' => 1, 'description' => '4-way power extension cable', 'purchase_price' => 12000, 'selling_price' => 18000, 'stock_quantity' => 15, 'reorder_level' => 4, 'status' => 'active'],
            ['name' => 'AA Batteries 4pcs', 'sku' => 'BT-AA4', 'barcode' => '6221000000050', 'category_id' => 5, 'brand_id' => null, 'unit_id' => 1, 'description' => 'Alkaline AA batteries', 'purchase_price' => 3500, 'selling_price' => 5000, 'stock_quantity' => 40, 'reorder_level' => 8, 'status' => 'active'],

            // Health & Wellness
            ['name' => 'Paracetamol 500mg 20tabs', 'sku' => 'PR-500', 'barcode' => '6221000000051', 'category_id' => 13, 'brand_id' => null, 'unit_id' => 1, 'description' => 'Pain relief tablets', 'purchase_price' => 1500, 'selling_price' => 2500, 'stock_quantity' => 50, 'reorder_level' => 15, 'status' => 'active'],
            ['name' => 'Vitamin C 1000mg 30tabs', 'sku' => 'VC-1000', 'barcode' => '6221000000052', 'category_id' => 13, 'brand_id' => null, 'unit_id' => 1, 'description' => 'Immune support vitamin C', 'purchase_price' => 8000, 'selling_price' => 12000, 'stock_quantity' => 20, 'reorder_level' => 5, 'status' => 'active'],
            ['name' => 'First Aid Kit Small', 'sku' => 'FA-SM', 'barcode' => '6221000000053', 'category_id' => 13, 'brand_id' => null, 'unit_id' => 1, 'description' => 'Basic first aid kit', 'purchase_price' => 15000, 'selling_price' => 22000, 'stock_quantity' => 10, 'reorder_level' => 3, 'status' => 'active'],

            // Office & Stationery
            ['name' => 'A4 Printing Paper 500 sheets', 'sku' => 'A4-500', 'barcode' => '6221000000054', 'category_id' => 15, 'brand_id' => null, 'unit_id' => 1, 'description' => 'Premium A4 copy paper', 'purchase_price' => 12000, 'selling_price' => 16000, 'stock_quantity' => 25, 'reorder_level' => 5, 'status' => 'active'],
            ['name' => 'Ballpoint Pens 12pcs', 'sku' => 'BP-12', 'barcode' => '6221000000055', 'category_id' => 15, 'brand_id' => null, 'unit_id' => 1, 'description' => 'Blue ink ball pens', 'purchase_price' => 3000, 'selling_price' => 4500, 'stock_quantity' => 30, 'reorder_level' => 6, 'status' => 'active'],
            ['name' => 'Notebook A4 200pg', 'sku' => 'NB-A4', 'barcode' => '6221000000056', 'category_id' => 15, 'brand_id' => null, 'unit_id' => 1, 'description' => 'Hardcover exercise book', 'purchase_price' => 3500, 'selling_price' => 5000, 'stock_quantity' => 40, 'reorder_level' => 8, 'status' => 'active'],
            ['name' => 'Calculator Basic', 'sku' => 'CAL-BS', 'barcode' => '6221000000057', 'category_id' => 15, 'brand_id' => null, 'unit_id' => 1, 'description' => 'Desktop calculator', 'purchase_price' => 8000, 'selling_price' => 12000, 'stock_quantity' => 15, 'reorder_level' => 4, 'status' => 'active'],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['sku' => $product['sku']], $product);
        }

        $this->command->info('Product data seeded successfully!');
        $this->command->info('Categories: ' . ProductCategory::count());
        $this->command->info('Brands: ' . Brand::count());
        $this->command->info('Units: ' . Unit::count());
        $this->command->info('Products: ' . Product::count());
    }
}
