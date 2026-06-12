<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Documentation - {{ config('app.name', 'MannaPOS') }}</title>
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
                <h1 class="text-4xl font-bold text-gray-900 mb-6">Documentation</h1>
                <p class="text-gray-600 text-lg mb-8">Comprehensive guides to help you master MannaPOS.</p>

                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Getting Started Guide</h3>
                        <p class="text-gray-600 mb-4">Learn how to set up your account, add products, and make your first sale.</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Read Guide →</a>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Inventory Management</h3>
                        <p class="text-gray-600 mb-4">Master inventory tracking, stock alerts, and supplier management.</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Read Guide →</a>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Payment Processing</h3>
                        <p class="text-gray-600 mb-4">Configure payment methods and process transactions smoothly.</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Read Guide →</a>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Customer Management</h3>
                        <p class="text-gray-600 mb-4">Build customer profiles and implement loyalty programs.</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Read Guide →</a>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Reports & Analytics</h3>
                        <p class="text-gray-600 mb-4">Understand your business performance with detailed reports.</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Read Guide →</a>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Multi-Location Setup</h3>
                        <p class="text-gray-600 mb-4">Manage multiple stores from a single dashboard.</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Read Guide →</a>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Integrations</h3>
                        <p class="text-gray-600 mb-4">Connect MannaPOS with your favorite tools and services.</p>
                        <a href="#" class="text-primary-600 hover:underline font-medium">Read Guide →</a>
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
