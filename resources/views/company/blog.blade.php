@extends('layouts.page')

@section('title', 'Blog - ' . config('app.name', 'MannaPOS'))

@section('content')
<div class="pb-16">
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
</div>
@endsection
