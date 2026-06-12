<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Features - {{ config('app.name', 'MannaPOS') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            "50":"#eff6ff","100":"#dbeafe","200":"#bfdbfe","300":"#93c5fd","400":"#60a5fa","500":"#3b82f6","600":"#2563eb","700":"#1d4ed8","800":"#1e40af","900":"#1e3a8a","950":"#172554"
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200">
        <nav class="max-w-screen-xl mx-auto px-4 lg:px-12">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center space-x-2.5">
                    <img src="{{ asset('icons8-dynamics-365-96.png') }}" alt="MannaPOS Logo" class="h-8 w-8 object-contain">
                    <span class="text-xl font-bold text-gray-900">{{ config('app.name', 'MannaPOS') }}</span>
                </a>
                <a href="/" class="text-gray-600 hover:text-gray-900 font-medium">← Back to Home</a>
            </div>
        </nav>
    </header>

    <main class="pt-24 pb-16">
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
    </main>

    <footer class="bg-gray-900 text-gray-400 py-8">
        <div class="max-w-screen-xl mx-auto px-4 lg:px-12 text-center">
            <p>© 2024 MannaPOS. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
