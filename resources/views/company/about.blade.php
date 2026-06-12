<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>About Us - {{ config('app.name', 'MannaPOS') }}</title>
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
                <h1 class="text-4xl font-bold text-gray-900 mb-6">About MannaPOS</h1>
                
                <div class="prose prose-lg max-w-none">
                    <p class="text-gray-600 text-lg leading-relaxed mb-6">
                        MannaPOS is a leading Point of Sale (POS) solution designed to empower businesses across Africa and beyond. Founded in 2020, we've grown from a small startup to serving over 5,000 businesses, processing millions of transactions monthly.
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Our Mission</h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        To simplify business operations through innovative technology, enabling entrepreneurs to focus on what matters most — growing their business and serving their customers.
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Our Vision</h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        To become the most trusted POS platform in Africa, known for reliability, innovation, and exceptional customer service. We envision a future where every business, regardless of size, has access to enterprise-grade tools.
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Our Values</h2>
                    <div class="grid md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-primary-50 rounded-xl p-6">
                            <h3 class="font-bold text-primary-700 mb-2">Customer First</h3>
                            <p class="text-gray-600 text-sm">Every decision we make starts with our customers' success.</p>
                        </div>
                        <div class="bg-primary-50 rounded-xl p-6">
                            <h3 class="font-bold text-primary-700 mb-2">Innovation</h3>
                            <p class="text-gray-600 text-sm">We constantly push boundaries to deliver cutting-edge solutions.</p>
                        </div>
                        <div class="bg-primary-50 rounded-xl p-6">
                            <h3 class="font-bold text-primary-700 mb-2">Integrity</h3>
                            <p class="text-gray-600 text-sm">We operate with transparency and honesty in all our dealings.</p>
                        </div>
                        <div class="bg-primary-50 rounded-xl p-6">
                            <h3 class="font-bold text-primary-700 mb-2">Excellence</h3>
                            <p class="text-gray-600 text-sm">We strive for the highest quality in everything we do.</p>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Our Story</h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        MannaPOS was born out of frustration with existing POS solutions that were either too expensive, too complex, or not designed for African markets. Our founders, having experienced these challenges firsthand, set out to build a solution that would be accessible, affordable, and tailored to local needs.
                    </p>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Starting with a small team in Dar es Salaam, we've expanded our presence across Tanzania and neighboring countries. Our platform now supports multiple languages, currencies, and payment methods, making it the go-to choice for businesses of all sizes.
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Our Team</h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Our team of 50+ talented individuals includes software engineers, designers, customer success specialists, and business experts. We're united by our passion for technology and our commitment to helping businesses succeed.
                    </p>

                    <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Our Impact</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-600">5,000+</div>
                            <div class="text-gray-600 text-sm">Active Businesses</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-600">2M+</div>
                            <div class="text-gray-600 text-sm">Monthly Transactions</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-600">50+</div>
                            <div class="text-gray-600 text-sm">Team Members</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-600">99.9%</div>
                            <div class="text-gray-600 text-sm">Uptime</div>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Contact Us</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Want to learn more about MannaPOS? We'd love to hear from you. Reach out to us at <a href="mailto:info@mannapos.com" class="text-primary-600 hover:underline">info@mannapos.com</a> or visit our office at 123 Business Avenue, Suite 100, Dar es Salaam, Tanzania.
                    </p>
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
