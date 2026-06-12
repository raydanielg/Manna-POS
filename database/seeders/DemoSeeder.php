<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // Create or Update Demo User
        $user = User::updateOrCreate(
            ['email' => 'demo@mannapos.com'],
            [
                'name' => 'Mama Pita',
                'password' => Hash::make('password123'),
                'phone' => '+255712345678',
                'role' => 'user',
                'business_name' => 'Duka la Mama Pita',
                'business_type' => 'retail',
                'business_city' => 'Dar es Salaam',
                'business_country' => 'Tanzania',
                'business_address' => 'Sinza Mori, Plot 45',
                'currency' => 'TZS',
                'tax_percentage' => 18.0,
                'fiscal_year_start' => 'January',
            ]
        );

        // Create Categories
        $categories = [
            ['name' => 'Food & Groceries'],
            ['name' => 'Beverages'],
            ['name' => 'Household Items'],
            ['name' => 'Personal Care'],
            ['name' => 'Electronics'],
        ];

        foreach ($categories as $cat) {
            ProductCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // Create Brands
        $brands = [
            ['name' => 'Coca-Cola'],
            ['name' => 'Nestle'],
            ['name' => 'Unilever'],
            ['name' => 'P&G'],
            ['name' => 'Samsung'],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(['name' => $brand['name']], $brand);
        }

        // Create Units
        $units = [
            ['name' => 'Piece', 'short_name' => 'pcs'],
            ['name' => 'Kilogram', 'short_name' => 'kg'],
            ['name' => 'Liter', 'short_name' => 'L'],
            ['name' => 'Carton', 'short_name' => 'ctn'],
            ['name' => 'Dozen', 'short_name' => 'dz'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['name' => $unit['name']], $unit);
        }

        // Create Products
        $products = [
            [
                'name' => 'Coca-Cola 500ml',
                'sku' => 'CC-500',
                'barcode' => '5449000000996',
                'category_id' => 2,
                'brand_id' => 1,
                'unit_id' => 1,
                'description' => 'Refreshing soft drink',
                'purchase_price' => 1200,
                'selling_price' => 1800,
                'stock_quantity' => 150,
                'reorder_level' => 20,
                'status' => 'active',
            ],
            [
                'name' => 'Bread - White Sliced',
                'sku' => 'BR-WH',
                'barcode' => '6221000000001',
                'category_id' => 1,
                'brand_id' => null,
                'unit_id' => 1,
                'description' => 'Fresh white bread',
                'purchase_price' => 800,
                'selling_price' => 1200,
                'stock_quantity' => 45,
                'reorder_level' => 15,
                'status' => 'active',
            ],
            [
                'name' => 'Cooking Oil 1L',
                'sku' => 'CO-1L',
                'barcode' => '6221000000002',
                'category_id' => 1,
                'brand_id' => 3,
                'unit_id' => 3,
                'description' => 'Pure vegetable cooking oil',
                'purchase_price' => 3500,
                'selling_price' => 4800,
                'stock_quantity' => 60,
                'reorder_level' => 10,
                'status' => 'active',
            ],
            [
                'name' => 'Rice - Super 5kg',
                'sku' => 'RC-5KG',
                'barcode' => '6221000000003',
                'category_id' => 1,
                'brand_id' => null,
                'unit_id' => 2,
                'description' => 'Premium quality rice',
                'purchase_price' => 15000,
                'selling_price' => 18500,
                'stock_quantity' => 25,
                'reorder_level' => 8,
                'status' => 'active',
            ],
            [
                'name' => 'Sugar - White 2kg',
                'sku' => 'SG-2KG',
                'barcode' => '6221000000004',
                'category_id' => 1,
                'brand_id' => null,
                'unit_id' => 2,
                'description' => 'White refined sugar',
                'purchase_price' => 5500,
                'selling_price' => 7000,
                'stock_quantity' => 80,
                'reorder_level' => 15,
                'status' => 'active',
            ],
            [
                'name' => 'Toothpaste - Mint 100ml',
                'sku' => 'TP-MT',
                'barcode' => '6221000000005',
                'category_id' => 4,
                'brand_id' => 4,
                'unit_id' => 1,
                'description' => 'Mint flavored toothpaste',
                'purchase_price' => 2500,
                'selling_price' => 3500,
                'stock_quantity' => 5,
                'reorder_level' => 10,
                'status' => 'active',
            ],
            [
                'name' => 'Soap - Bath Bar',
                'sku' => 'SP-BB',
                'barcode' => '6221000000006',
                'category_id' => 4,
                'brand_id' => 3,
                'unit_id' => 1,
                'description' => 'Moisturizing bath soap',
                'purchase_price' => 1500,
                'selling_price' => 2200,
                'stock_quantity' => 3,
                'reorder_level' => 12,
                'status' => 'active',
            ],
            [
                'name' => 'Detergent Powder 1kg',
                'sku' => 'DP-1KG',
                'barcode' => '6221000000007',
                'category_id' => 3,
                'brand_id' => 4,
                'unit_id' => 2,
                'description' => 'Laundry detergent powder',
                'purchase_price' => 4000,
                'selling_price' => 5500,
                'stock_quantity' => 35,
                'reorder_level' => 10,
                'status' => 'active',
            ],
            [
                'name' => 'Bleach 750ml',
                'sku' => 'BL-750',
                'barcode' => '6221000000008',
                'category_id' => 3,
                'brand_id' => null,
                'unit_id' => 3,
                'description' => 'Household bleach',
                'purchase_price' => 3000,
                'selling_price' => 4200,
                'stock_quantity' => 20,
                'reorder_level' => 8,
                'status' => 'active',
            ],
            [
                'name' => 'Milk - Fresh 1L',
                'sku' => 'MK-1L',
                'barcode' => '6221000000009',
                'category_id' => 2,
                'brand_id' => 2,
                'unit_id' => 3,
                'description' => 'Fresh pasteurized milk',
                'purchase_price' => 2800,
                'selling_price' => 3800,
                'stock_quantity' => 40,
                'reorder_level' => 12,
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['sku' => $product['sku']], $product);
        }

        // Create Customers
        $customers = [
            ['name' => 'John Mwamba', 'phone' => '+255711111111', 'email' => 'john@gmail.com', 'address' => 'Sinza'],
            ['name' => 'Mary Kileo', 'phone' => '+255722222222', 'email' => 'mary@yahoo.com', 'address' => 'Mwenge'],
            ['name' => 'Ali Hassan', 'phone' => '+255733333333', 'email' => 'ali@hotmail.com', 'address' => 'Kijitonyama'],
            ['name' => 'Grace Nyamburi', 'phone' => '+255744444444', 'email' => 'grace@gmail.com', 'address' => 'Makumbusho'],
            ['name' => 'Peter Masanja', 'phone' => '+255755555555', 'email' => 'peter@yahoo.com', 'address' => 'Temeke'],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(['email' => $customer['email']], $customer);
        }

        // Create Suppliers
        $suppliers = [
            ['name' => 'Tanzania Beverages Ltd', 'phone' => '+255222222222', 'email' => 'info@tzbeverages.co.tz', 'address' => 'Industrial Area'],
            ['name' => 'National Food Corp', 'phone' => '+255333333333', 'email' => 'sales@nfc.co.tz', 'address' => 'Dar es Salaam'],
            ['name' => 'Household Supplies Co', 'phone' => '+255444444444', 'email' => 'orders@hsc.co.tz', 'address' => 'Mikocheni'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(['email' => $supplier['email']], $supplier);
        }

        // Create Sales for the last 7 days
        $salesData = [
            [
                'reference' => 'SALE-001',
                'customer_id' => 1,
                'sale_date' => now()->subDays(6),
                'subtotal' => 15000,
                'discount' => 0,
                'tax' => 2700,
                'total' => 17700,
                'paid' => 17700,
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'status' => 'completed',
                'items' => [
                    ['product_id' => 1, 'product_name' => 'Coca-Cola 500ml', 'quantity' => 5, 'unit_price' => 1800, 'discount' => 0, 'total' => 9000],
                    ['product_id' => 2, 'product_name' => 'Bread - White Sliced', 'quantity' => 5, 'unit_price' => 1200, 'discount' => 0, 'total' => 6000],
                ],
            ],
            [
                'reference' => 'SALE-002',
                'customer_id' => 2,
                'sale_date' => now()->subDays(5),
                'subtotal' => 25000,
                'discount' => 500,
                'tax' => 4410,
                'total' => 28910,
                'paid' => 28910,
                'payment_status' => 'paid',
                'payment_method' => 'mpesa',
                'status' => 'completed',
                'items' => [
                    ['product_id' => 3, 'product_name' => 'Cooking Oil 1L', 'quantity' => 3, 'unit_price' => 4800, 'discount' => 0, 'total' => 14400],
                    ['product_id' => 4, 'product_name' => 'Rice - Super 5kg', 'quantity' => 1, 'unit_price' => 18500, 'discount' => 500, 'total' => 18000],
                ],
            ],
            [
                'reference' => 'SALE-003',
                'customer_id' => null,
                'sale_date' => now()->subDays(4),
                'subtotal' => 8500,
                'discount' => 0,
                'tax' => 1530,
                'total' => 10030,
                'paid' => 10030,
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'status' => 'completed',
                'items' => [
                    ['product_id' => 5, 'product_name' => 'Sugar - White 2kg', 'quantity' => 1, 'unit_price' => 7000, 'discount' => 0, 'total' => 7000],
                    ['product_id' => 6, 'product_name' => 'Toothpaste - Mint 100ml', 'quantity' => 1, 'unit_price' => 3500, 'discount' => 0, 'total' => 3500],
                ],
            ],
            [
                'reference' => 'SALE-004',
                'customer_id' => 3,
                'sale_date' => now()->subDays(3),
                'subtotal' => 42000,
                'discount' => 2000,
                'tax' => 7200,
                'total' => 47200,
                'paid' => 47200,
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'status' => 'completed',
                'items' => [
                    ['product_id' => 4, 'product_name' => 'Rice - Super 5kg', 'quantity' => 2, 'unit_price' => 18500, 'discount' => 0, 'total' => 37000],
                    ['product_id' => 7, 'product_name' => 'Soap - Bath Bar', 'quantity' => 2, 'unit_price' => 2200, 'discount' => 0, 'total' => 4400],
                ],
            ],
            [
                'reference' => 'SALE-005',
                'customer_id' => 4,
                'sale_date' => now()->subDays(2),
                'subtotal' => 18000,
                'discount' => 0,
                'tax' => 3240,
                'total' => 21240,
                'paid' => 21240,
                'payment_status' => 'paid',
                'payment_method' => 'mpesa',
                'status' => 'completed',
                'items' => [
                    ['product_id' => 8, 'product_name' => 'Detergent Powder 1kg', 'quantity' => 3, 'unit_price' => 5500, 'discount' => 0, 'total' => 16500],
                    ['product_id' => 9, 'product_name' => 'Bleach 750ml', 'quantity' => 1, 'unit_price' => 4200, 'discount' => 0, 'total' => 4200],
                ],
            ],
            [
                'reference' => 'SALE-006',
                'customer_id' => null,
                'sale_date' => now()->subDays(1),
                'subtotal' => 32500,
                'discount' => 1500,
                'tax' => 5580,
                'total' => 36580,
                'paid' => 36580,
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'status' => 'completed',
                'items' => [
                    ['product_id' => 1, 'product_name' => 'Coca-Cola 500ml', 'quantity' => 10, 'unit_price' => 1800, 'discount' => 0, 'total' => 18000],
                    ['product_id' => 10, 'product_name' => 'Milk - Fresh 1L', 'quantity' => 4, 'unit_price' => 3800, 'discount' => 0, 'total' => 15200],
                ],
            ],
            [
                'reference' => 'SALE-007',
                'customer_id' => 5,
                'sale_date' => now(),
                'subtotal' => 28000,
                'discount' => 0,
                'tax' => 5040,
                'total' => 33040,
                'paid' => 33040,
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'status' => 'completed',
                'items' => [
                    ['product_id' => 3, 'product_name' => 'Cooking Oil 1L', 'quantity' => 4, 'unit_price' => 4800, 'discount' => 0, 'total' => 19200],
                    ['product_id' => 5, 'product_name' => 'Sugar - White 2kg', 'quantity' => 2, 'unit_price' => 7000, 'discount' => 0, 'total' => 14000],
                ],
            ],
        ];

        foreach ($salesData as $saleData) {
            $items = $saleData['items'];
            unset($saleData['items']);
            
            $sale = Sale::create($saleData);
            
            foreach ($items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'],
                    'total' => $item['total'],
                ]);
            }
        }

        // Create Purchases
        $purchasesData = [
            [
                'reference' => 'PUR-001',
                'supplier_id' => 1,
                'purchase_date' => now()->subDays(10),
                'subtotal' => 120000,
                'discount' => 5000,
                'tax' => 20700,
                'shipping' => 5000,
                'total' => 140700,
                'payment_status' => 'paid',
                'status' => 'received',
                'items' => [
                    ['product_id' => 1, 'product_name' => 'Coca-Cola 500ml', 'quantity' => 50, 'unit_cost' => 1200, 'total' => 60000],
                    ['product_id' => 10, 'product_name' => 'Milk - Fresh 1L', 'quantity' => 20, 'unit_cost' => 2800, 'total' => 56000],
                ],
            ],
            [
                'reference' => 'PUR-002',
                'supplier_id' => 2,
                'purchase_date' => now()->subDays(5),
                'subtotal' => 185000,
                'discount' => 0,
                'tax' => 33300,
                'shipping' => 3000,
                'total' => 221300,
                'payment_status' => 'paid',
                'status' => 'received',
                'items' => [
                    ['product_id' => 4, 'product_name' => 'Rice - Super 5kg', 'quantity' => 10, 'unit_cost' => 15000, 'total' => 150000],
                    ['product_id' => 5, 'product_name' => 'Sugar - White 2kg', 'quantity' => 5, 'unit_cost' => 5500, 'total' => 27500],
                ],
            ],
        ];

        foreach ($purchasesData as $purchaseData) {
            $items = $purchaseData['items'];
            unset($purchaseData['items']);
            
            $purchase = Purchase::create($purchaseData);
            
            foreach ($items as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total' => $item['total'],
                ]);
            }
        }

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Demo User: demo@mannapos.com / password123');
    }
}
