<header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200">
    <nav class="max-w-screen-xl mx-auto px-4 lg:px-12">
        <div class="flex flex-wrap items-center justify-between mx-auto py-4">
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="{{ asset('icons8-dynamics-365-96.png') }}" alt="MannaPOS Logo" class="h-10 w-10 object-contain">
                <span class="self-center text-2xl font-bold whitespace-nowrap text-gray-900">{{ config('app.name', 'MannaPOS') }}</span>
            </a>
            <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                <a href="{{ route('login') }}" class="text-gray-900 hover:text-primary-600 font-medium rounded-lg text-sm px-4 py-2 md:px-5 md:py-2.5 transition-colors">Login</a>
                <a href="{{ route('register') }}" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 md:px-5 md:py-2.5 transition-all">Get Started</a>
                <button data-collapse-toggle="navbar-sticky" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200" aria-controls="navbar-sticky" aria-expanded="false" id="mobileMenuToggle">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                    </svg>
                </button>
            </div>
            <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
                <ul class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-transparent">
                    <li>
                        <a href="#features" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-primary-600 md:p-0 transition-colors">Features</a>
                    </li>
                    <li>
                        <a href="#why-choose-us" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-primary-600 md:p-0 transition-colors">Why Us</a>
                    </li>
                    <li>
                        <a href="#testimonials" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-primary-600 md:p-0 transition-colors">Testimonials</a>
                    </li>
                    <li>
                        <a href="#pricing" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-primary-600 md:p-0 transition-colors">Pricing</a>
                    </li>
                    <li>
                        <a href="#privacy" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-primary-600 md:p-0 transition-colors">Privacy</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const navbarSticky = document.getElementById('navbar-sticky');

        if (mobileMenuToggle && navbarSticky) {
            mobileMenuToggle.addEventListener('click', function() {
                navbarSticky.classList.toggle('hidden');
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
            });
        }
    });
</script>
