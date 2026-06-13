<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MannaPOS - Modern Point of Sale System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .feature-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-cash-register text-2xl text-purple-600 mr-2"></i>
                        <span class="text-xl font-bold text-gray-900">MannaPOS</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/adminpanel" class="text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium transition">
                        Admin Panel
                    </a>
                    <a href="/login" class="bg-purple-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-purple-700 transition">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg pt-32 pb-20 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                Modern Point of Sale System
            </h1>
            <p class="text-xl text-purple-100 mb-8 max-w-2xl mx-auto">
                Streamline your business with our powerful POS system. Manage sales, inventory, customers, and more from one platform.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/adminpanel" class="bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition shadow-lg">
                    <i class="fas fa-rocket mr-2"></i>Get Started
                </a>
                <a href="#features" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-purple-600 transition">
                    <i class="fas fa-info-circle mr-2"></i>Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Powerful Features</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Everything you need to run your business efficiently</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-6 rounded-xl shadow-md card-hover transition duration-300">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-shopping-cart text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Sales Management</h3>
                    <p class="text-gray-600">Process sales quickly and efficiently with our intuitive interface</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-6 rounded-xl shadow-md card-hover transition duration-300">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-boxes text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Inventory Control</h3>
                    <p class="text-gray-600">Track stock levels, manage products, and get low stock alerts</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-6 rounded-xl shadow-md card-hover transition duration-300">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Customer Management</h3>
                    <p class="text-gray-600">Build relationships with your customers and track their purchases</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-6 rounded-xl shadow-md card-hover transition duration-300">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Analytics & Reports</h3>
                    <p class="text-gray-600">Get insights into your business performance with detailed reports</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white p-6 rounded-xl shadow-md card-hover transition duration-300">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-wifi text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Offline Mode</h3>
                    <p class="text-gray-600">Continue working even without internet connection</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white p-6 rounded-xl shadow-md card-hover transition duration-300">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-mobile-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Mobile App</h3>
                    <p class="text-gray-600">Access your business from anywhere with our mobile application</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-gray-900 py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold text-white mb-2">10K+</div>
                    <div class="text-gray-400">Active Users</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-white mb-2">1M+</div>
                    <div class="text-gray-400">Transactions</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-white mb-2">99.9%</div>
                    <div class="text-gray-400">Uptime</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-white mb-2">24/7</div>
                    <div class="text-gray-400">Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Ready to Transform Your Business?</h2>
            <p class="text-gray-600 mb-8">Join thousands of businesses already using MannaPOS</p>
            <a href="/adminpanel" class="bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition shadow-lg inline-block">
                <i class="fas fa-arrow-right mr-2"></i>Get Started Now
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-cash-register text-2xl text-purple-400 mr-2"></i>
                        <span class="text-xl font-bold">MannaPOS</span>
                    </div>
                    <p class="text-gray-400">Modern Point of Sale System for your business needs.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#features" class="hover:text-white transition">Features</a></li>
                        <li><a href="#" class="hover:text-white transition">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition">Mobile App</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Documentation</a></li>
                        <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Connect</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition text-xl"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition text-xl"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition text-xl"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition text-xl"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 MannaPOS. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>
