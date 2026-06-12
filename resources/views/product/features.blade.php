@extends('layouts.page')

@section('title', 'Features - ' . config('app.name', 'MannaPOS'))

@section('content')
<div class="pb-16">
        <div class="max-w-4xl mx-auto px-4 lg:px-12">
            <div class="bg-white rounded-2xl shadow-sm p-8 lg:p-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-6">MannaPOS Features</h1>
                <p class="text-gray-600 text-lg mb-8">Everything you need to run your business efficiently.</p>

                <div class="space-y-8">
                    <div class="border-b border-gray-200 pb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Inventory Management</h2>
                        <p class="text-gray-600 mb-4">Track stock levels in real-time, set low stock alerts, and manage suppliers effortlessly. Our intelligent inventory system helps you never run out of stock.</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li>Real-time stock tracking</li>
                            <li>Low stock alerts</li>
                            <li>Supplier management</li>
                            <li>Barcode scanning</li>
                            <li>Multi-location inventory</li>
                        </ul>
                    </div>

                    <div class="border-b border-gray-200 pb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Payment Processing</h2>
                        <p class="text-gray-600 mb-4">Accept multiple payment methods including cash, card, mobile money, and bank transfers. Fast and secure checkouts every time.</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li>Cash payments</li>
                            <li>Credit/Debit cards</li>
                            <li>Mobile money (M-Pesa, Tigo Pesa)</li>
                            <li>Bank transfers</li>
                            <li>Split payments</li>
                        </ul>
                    </div>

                    <div class="border-b border-gray-200 pb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Customer Management</h2>
                        <p class="text-gray-600 mb-4">Build customer profiles, track purchase history, and implement loyalty programs. Keep your customers coming back.</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li>Customer profiles</li>
                            <li>Purchase history</li>
                            <li>Loyalty programs</li>
                            <li>Customer insights</li>
                            <li>Targeted marketing</li>
                        </ul>
                    </div>

                    <div class="border-b border-gray-200 pb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Sales Analytics</h2>
                        <p class="text-gray-600 mb-4">Get detailed reports and insights on sales performance, trends, and customer behavior. Make data-driven decisions.</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li>Real-time dashboards</li>
                            <li>Sales reports</li>
                            <li>Product performance</li>
                            <li>Customer analytics</li>
                            <li>Trend analysis</li>
                        </ul>
                    </div>

                    <div class="border-b border-gray-200 pb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Multi-Location Support</h2>
                        <p class="text-gray-600 mb-4">Manage multiple locations from a single dashboard. Track inventory, sales, and performance across all your stores.</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li>Centralized management</li>
                            <li>Location-specific pricing</li>
                            <li>Inventory transfer</li>
                            <li>Performance comparison</li>
                            <li>Staff management</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Security & Reliability</h2>
                        <p class="text-gray-600 mb-4">Bank-level security to protect your data with 99.9% uptime guarantee. Your business is always online and secure.</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li>256-bit encryption</li>
                            <li>Role-based access</li>
                            <li>Automatic backups</li>
                            <li>99.9% uptime SLA</li>
                            <li>24/7 monitoring</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
