@extends('layouts.page')

@section('title', 'Documentation - ' . config('app.name', 'MannaPOS'))

@section('content')
<div class="pb-16">
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
</div>
@endsection
