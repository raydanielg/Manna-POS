@extends('layouts.page')

@section('title', 'Integrations - ' . config('app.name', 'MannaPOS'))

@section('content')
<div class="pb-16">
        <div class="max-w-4xl mx-auto px-4 lg:px-12">
            <div class="bg-white rounded-2xl shadow-sm p-8 lg:p-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-6">Integrations</h1>
                <p class="text-gray-600 text-lg mb-8">Connect MannaPOS with your favorite tools and services.</p>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-2xl">💰</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">M-Pesa</h3>
                                <span class="text-green-600 text-sm">Connected</span>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm">Accept mobile payments seamlessly with M-Pesa integration.</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-2xl">💳</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Card Payments</h3>
                                <span class="text-green-600 text-sm">Connected</span>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm">Process credit and debit card payments securely.</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-2xl">📊</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">QuickBooks</h3>
                                <span class="text-gray-500 text-sm">Available</span>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm">Sync your accounting data with QuickBooks automatically.</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-2xl">📧</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Mailchimp</h3>
                                <span class="text-gray-500 text-sm">Available</span>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm">Build email marketing campaigns from your customer data.</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-2xl">📱</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">WhatsApp Business</h3>
                                <span class="text-gray-500 text-sm">Available</span>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm">Send order updates and promotions via WhatsApp.</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-2xl">📦</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">E-commerce Platforms</h3>
                                <span class="text-gray-500 text-sm">Available</span>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm">Connect with Shopify, WooCommerce, and more.</p>
                    </div>
                </div>

                <div class="mt-12 p-6 bg-primary-50 rounded-lg">
                    <h3 class="font-bold text-primary-700 mb-2">Need a Custom Integration?</h3>
                    <p class="text-gray-600 mb-4">Our API allows you to build custom integrations with any service you need.</p>
                    <a href="/api" class="text-primary-600 hover:underline font-medium">View API Documentation →</a>
                </div>
            </div>
        </div>
</div>
@endsection
