<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Blog - {{ config('app.name', 'MannaPOS') }}</title>
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
                <h1 class="text-4xl font-bold text-gray-900 mb-6">Blog</h1>
                <p class="text-gray-600 text-lg mb-8">Latest news, updates, and insights from the MannaPOS team.</p>

                <div class="space-y-8">
                    <article class="border-b border-gray-200 pb-8">
                        <div class="text-sm text-gray-500 mb-2">January 15, 2024</div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3 hover:text-primary-600 cursor-pointer">MannaPOS v2.0: What's New</h2>
                        <p class="text-gray-600 mb-4">We're excited to announce the release of MannaPOS v2.0 with major improvements including enhanced analytics, new integrations, and a completely redesigned mobile app.</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Read More →</a>
                    </article>

                    <article class="border-b border-gray-200 pb-8">
                        <div class="text-sm text-gray-500 mb-2">December 20, 2023</div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3 hover:text-primary-600 cursor-pointer">5 Tips to Boost Your Retail Sales</h2>
                        <p class="text-gray-600 mb-4">Discover proven strategies to increase your retail sales and improve customer experience using modern POS technology.</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Read More →</a>
                    </article>

                    <article class="border-b border-gray-200 pb-8">
                        <div class="text-sm text-gray-500 mb-2">November 15, 2023</div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3 hover:text-primary-600 cursor-pointer">Understanding Inventory Management</h2>
                        <p class="text-gray-600 mb-4">Learn the fundamentals of effective inventory management and how MannaPOS can help you optimize your stock levels.</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Read More →</a>
                    </article>

                    <article class="border-b border-gray-200 pb-8">
                        <div class="text-sm text-gray-500 mb-2">October 10, 2023</div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3 hover:text-primary-600 cursor-pointer">Mobile Money Integration Guide</h2>
                        <p class="text-gray-600 mb-4">A complete guide to integrating M-Pesa, Tigo Pesa, and other mobile money services with your POS system.</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Read More →</a>
                    </article>

                    <article>
                        <div class="text-sm text-gray-500 mb-2">September 5, 2023</div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3 hover:text-primary-600 cursor-pointer">Customer Loyalty Programs That Work</h2>
                        <p class="text-gray-600 mb-4">How to design and implement effective loyalty programs that keep customers coming back to your business.</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Read More →</a>
                    </article>
                </div>

                <div class="mt-12 text-center">
                    <button class="bg-primary-600 text-white py-3 px-6 rounded-lg hover:bg-primary-700 transition-colors font-medium">Load More Posts</button>
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
