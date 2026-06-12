@extends('layouts.page')

@section('title', 'API Reference - ' . config('app.name', 'MannaPOS'))

@section('content')
<div class="pb-16">
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
</div>
@endsection
