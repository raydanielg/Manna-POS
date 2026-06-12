<header id="main-header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm">
    <nav class="max-w-screen-xl mx-auto px-4 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-[68px]">

            {{-- Logo --}}
            <a href="/" class="flex items-center space-x-2.5 flex-shrink-0">
                <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center shadow-md">
                    <img src="{{ asset('icons8-dynamics-365-96.png') }}" alt="MannaPOS Logo" class="h-6 w-6 object-contain brightness-0 invert">
                </div>
                <span class="text-xl font-bold text-gray-900 tracking-tight">{{ config('app.name', 'MannaPOS') }}</span>
            </a>

            {{-- Center Nav Links --}}
            <div class="hidden md:flex items-center space-x-1" id="navbar-sticky">
                <a href="/" class="px-4 py-2 text-sm font-medium text-primary-600 rounded-lg hover:bg-primary-50 transition-colors">Home</a>
                <a href="#features" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50 transition-colors">Features</a>
                <a href="#why-choose-us" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50 transition-colors">Why Us</a>
                <a href="#pricing" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50 transition-colors">Pricing</a>
                <a href="#testimonials" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50 transition-colors">Reviews</a>
            </div>

            {{-- Right Actions --}}
            <div class="flex items-center space-x-1">
                {{-- Talk to sales - hidden on small --}}
                <a href="tel:+255000000000" class="hidden lg:flex items-center space-x-1.5 px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span>Talk to sales</span>
                </a>

                {{-- Globe icon --}}
                <button class="hidden lg:flex p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </button>

                <div class="hidden md:block w-px h-5 bg-gray-200 mx-1"></div>

                <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-lg hover:bg-gray-50 transition-colors">Login</a>

                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 ml-1">
                    Sign up
                </a>

                {{-- Mobile menu toggle --}}
                <button id="mobileMenuToggle" type="button" class="md:hidden p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors ml-1">
                    <svg class="w-5 h-5" id="menu-open-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg class="w-5 h-5 hidden" id="menu-close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div class="md:hidden hidden pb-4 border-t border-gray-100 mt-1" id="mobile-menu">
            <div class="pt-3 space-y-1">
                <a href="/" class="block px-4 py-2.5 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg">Home</a>
                <a href="#features" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Features</a>
                <a href="#why-choose-us" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Why Us</a>
                <a href="#pricing" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Pricing</a>
                <a href="#testimonials" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Reviews</a>
                <div class="pt-2 border-t border-gray-100 flex items-center space-x-3 px-1">
                    <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 text-sm font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="flex-1 text-center py-2.5 text-sm font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">Sign up</a>
                </div>
            </div>
        </div>
    </nav>
</header>

<script>
    (function () {
        const toggle = document.getElementById('mobileMenuToggle');
        const menu = document.getElementById('mobile-menu');
        const openIcon = document.getElementById('menu-open-icon');
        const closeIcon = document.getElementById('menu-close-icon');

        toggle && toggle.addEventListener('click', function () {
            menu.classList.toggle('hidden');
            openIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });

        // Close mobile menu on nav link click
        menu && menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                menu.classList.add('hidden');
                openIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            });
        });

        // Scroll effect — add shadow when scrolled
        const header = document.getElementById('main-header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                header.classList.add('shadow-md');
                header.classList.remove('shadow-sm');
            } else {
                header.classList.remove('shadow-md');
                header.classList.add('shadow-sm');
            }
        }, { passive: true });
    })();
</script>
