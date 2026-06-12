<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>API Reference - {{ config('app.name', 'MannaPOS') }}</title>
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
                <h1 class="text-4xl font-bold text-gray-900 mb-6">API Reference</h1>
                <p class="text-gray-600 text-lg mb-8">Build custom integrations with the MannaPOS API.</p>

                <div class="bg-gray-900 rounded-lg p-6 mb-8">
                    <p class="text-green-400 font-mono text-sm mb-2">Base URL</p>
                    <p class="text-white font-mono">https://api.mannapos.com/v1</p>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-6">Authentication</h2>
                <p class="text-gray-600 mb-4">All API requests require an API key in the Authorization header:</p>
                <div class="bg-gray-100 rounded-lg p-4 mb-8">
                    <code class="text-sm">Authorization: Bearer YOUR_API_KEY</code>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-6">Endpoints</h2>
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded mr-3">GET</span>
                            <code class="text-gray-900 font-mono">/products</code>
                        </div>
                        <p class="text-gray-600">Retrieve all products in your inventory.</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded mr-3">POST</span>
                            <code class="text-gray-900 font-mono">/products</code>
                        </div>
                        <p class="text-gray-600">Create a new product in your inventory.</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded mr-3">GET</span>
                            <code class="text-gray-900 font-mono">/sales</code>
                        </div>
                        <p class="text-gray-600">Retrieve sales transactions and reports.</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded mr-3">POST</span>
                            <code class="text-gray-900 font-mono">/sales</code>
                        </div>
                        <p class="text-gray-600">Create a new sales transaction.</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded mr-3">GET</span>
                            <code class="text-gray-900 font-mono">/customers</code>
                        </div>
                        <p class="text-gray-600">Retrieve customer information and profiles.</p>
                    </div>
                </div>

                <div class="mt-12 p-6 bg-primary-50 rounded-lg">
                    <h3 class="font-bold text-primary-700 mb-2">Need Help?</h3>
                    <p class="text-gray-600 mb-4">Check our documentation or contact our support team for assistance with API integration.</p>
                    <a href="/documentation" class="text-primary-600 hover:underline font-medium">View Documentation →</a>
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
