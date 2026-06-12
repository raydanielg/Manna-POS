<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>System Status - {{ config('app.name', 'MannaPOS') }}</title>
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
                <div class="flex items-center justify-between mb-8">
                    <h1 class="text-4xl font-bold text-gray-900">System Status</h1>
                    <div class="flex items-center">
                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                        <span class="text-green-600 font-medium">All Systems Operational</span>
                    </div>
                </div>

                <div class="space-y-4 mb-12">
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-3"></span>
                            <span class="font-medium text-gray-900">Web Application</span>
                        </div>
                        <span class="text-green-600 font-medium">Operational</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-3"></span>
                            <span class="font-medium text-gray-900">API Services</span>
                        </div>
                        <span class="text-green-600 font-medium">Operational</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-3"></span>
                            <span class="font-medium text-gray-900">Database</span>
                        </div>
                        <span class="text-green-600 font-medium">Operational</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-3"></span>
                            <span class="font-medium text-gray-900">Payment Processing</span>
                        </div>
                        <span class="text-green-600 font-medium">Operational</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-3"></span>
                            <span class="font-medium text-gray-900">Email Services</span>
                        </div>
                        <span class="text-green-600 font-medium">Operational</span>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-6">Past Incidents</h2>
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-900">Scheduled Maintenance</span>
                            <span class="text-gray-500 text-sm">Dec 15, 2023</span>
                        </div>
                        <p class="text-gray-600 text-sm">Planned system upgrade completed successfully. No downtime experienced.</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-900">API Latency Issues</span>
                            <span class="text-gray-500 text-sm">Nov 20, 2023</span>
                        </div>
                        <p class="text-gray-600 text-sm">Resolved within 30 minutes. Affected API response times for some endpoints.</p>
                    </div>
                </div>

                <div class="mt-12 p-6 bg-gray-50 rounded-lg">
                    <h3 class="font-bold text-gray-900 mb-2">Uptime Statistics</h3>
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">99.9%</div>
                            <div class="text-gray-600 text-sm">Last 30 days</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">99.8%</div>
                            <div class="text-gray-600 text-sm">Last 90 days</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">99.7%</div>
                            <div class="text-gray-600 text-sm">Last 12 months</div>
                        </div>
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
