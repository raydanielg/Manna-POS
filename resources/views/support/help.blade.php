@extends('layouts.page')

@section('title', 'Help Center - ' . config('app.name', 'MannaPOS'))

@section('content')
<div class="pb-16">
        <div class="max-w-4xl mx-auto px-4 lg:px-12">
            <div class="bg-white rounded-2xl shadow-sm p-8 lg:p-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-6">Help Center</h1>
                <p class="text-gray-600 text-lg mb-8">Find answers to common questions and get help with MannaPOS.</p>

                <div class="mb-8">
                    <input type="text" placeholder="Search for help..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-12">
                    <div class="bg-primary-50 rounded-xl p-6">
                        <h3 class="font-bold text-primary-700 mb-2">Getting Started</h3>
                        <p class="text-gray-600 text-sm mb-4">Learn the basics of MannaPOS</p>
                        <a href="#" class="text-primary-600 hover:underline text-sm">View guides →</a>
                    </div>
                    <div class="bg-primary-50 rounded-xl p-6">
                        <h3 class="font-bold text-primary-700 mb-2">Account & Billing</h3>
                        <p class="text-gray-600 text-sm mb-4">Manage your subscription</p>
                        <a href="#" class="text-primary-600 hover:underline text-sm">View guides →</a>
                    </div>
                    <div class="bg-primary-50 rounded-xl p-6">
                        <h3 class="font-bold text-primary-700 mb-2">Features</h3>
                        <p class="text-gray-600 text-sm mb-4">Learn about all features</p>
                        <a href="#" class="text-primary-600 hover:underline text-sm">View guides →</a>
                    </div>
                    <div class="bg-primary-50 rounded-xl p-6">
                        <h3 class="font-bold text-primary-700 mb-2">Troubleshooting</h3>
                        <p class="text-gray-600 text-sm mb-4">Solve common issues</p>
                        <a href="#" class="text-primary-600 hover:underline text-sm">View guides →</a>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h2>
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">How do I get started with MannaPOS?</h3>
                        <p class="text-gray-600">Sign up for a free 14-day trial, set up your business profile, add your products, and start selling. Our onboarding wizard will guide you through the process.</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Can I use MannaPOS offline?</h3>
                        <p class="text-gray-600">Yes! MannaPOS works offline and syncs automatically when you're back online. Perfect for areas with unstable internet connections.</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">How do I contact support?</h3>
                        <p class="text-gray-600">You can reach our support team via email at support@mannapos.com, live chat, or phone at +255 123 456 789. We're available 24/7.</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Is my data secure?</h3>
                        <p class="text-gray-600">Absolutely. We use bank-level encryption, regular security audits, and automatic backups to ensure your data is always safe and accessible.</p>
                    </div>
                </div>

                <div class="mt-12 text-center">
                    <p class="text-gray-600 mb-4">Still need help?</p>
                    <a href="/contact" class="inline-block bg-primary-600 text-white py-3 px-6 rounded-lg hover:bg-primary-700 transition-colors font-medium">Contact Support</a>
                </div>
            </div>
        </div>
</div>
@endsection
