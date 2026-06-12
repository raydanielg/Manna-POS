<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product Updates - {{ config('app.name', 'MannaPOS') }}</title>
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
                <h1 class="text-4xl font-bold text-gray-900 mb-6">Product Updates</h1>
                <p class="text-gray-600 text-lg mb-8">Stay informed about the latest features and improvements.</p>

                <div class="space-y-8">
                    <div class="border-l-4 border-primary-500 pl-6">
                        <div class="text-sm text-primary-600 font-medium mb-2">January 15, 2024</div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3">MannaPOS v2.0 Released</h2>
                        <p class="text-gray-600 mb-4">Major update with completely redesigned mobile app, enhanced analytics dashboard, and new integrations including WhatsApp Business and e-commerce platforms.</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-1">
                            <li>New mobile app with offline support</li>
                            <li>Advanced analytics and reporting</li>
                            <li>WhatsApp Business integration</li>
                            <li>Multi-currency support</li>
                        </ul>
                    </div>

                    <div class="border-l-4 border-gray-300 pl-6">
                        <div class="text-sm text-gray-500 font-medium mb-2">December 1, 2023</div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3">Performance Improvements</h2>
                        <p class="text-gray-600 mb-4">Optimized database queries and improved caching for faster load times. Overall performance improved by 40%.</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-1">
                            <li>Faster product search</li>
                            <li>Improved checkout speed</li>
                            <li>Reduced API response times</li>
                        </ul>
                    </div>

                    <div class="border-l-4 border-gray-300 pl-6">
                        <div class="text-sm text-gray-500 font-medium mb-2">November 15, 2023</div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3">New Payment Gateway</h2>
                        <p class="text-gray-600 mb-4">Added support for additional payment gateways including local bank transfers and international card processors.</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-1">
                            <li>Bank transfer integration</li>
                            <li>International card support</li>
                            <li>Improved refund processing</li>
                        </ul>
                    </div>

                    <div class="border-l-4 border-gray-300 pl-6">
                        <div class="text-sm text-gray-500 font-medium mb-2">October 20, 2023</div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3">Customer Loyalty Features</h2>
                        <p class="text-gray-600 mb-4">New loyalty program features including points system, tiered rewards, and automated customer segmentation.</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-1">
                            <li>Points-based rewards</li>
                            <li>Tiered loyalty levels</li>
                            <li>Automated customer segmentation</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 p-6 bg-gray-50 rounded-lg">
                    <h3 class="font-bold text-gray-900 mb-2">Subscribe to Updates</h3>
                    <p class="text-gray-600 mb-4">Get notified about new features and improvements.</p>
                    <div class="flex gap-2">
                        <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <button class="bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700 transition-colors font-medium">Subscribe</button>
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
