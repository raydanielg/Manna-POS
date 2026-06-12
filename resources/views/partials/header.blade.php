<header class="landing-header">
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <img src="{{ asset('icons8-dynamics-365-96.png') }}" alt="MannaPOS Logo" class="brand-logo">
                <span class="brand-name">{{ config('app.name', 'MannaPOS') }}</span>
            </div>
            <div class="navbar-menu">
                <a href="#features" class="nav-link">Features</a>
                <a href="#why-choose-us" class="nav-link">Why Us</a>
                <a href="#testimonials" class="nav-link">Testimonials</a>
                <a href="{{ route('login') }}" class="nav-link btn-login">Login</a>
                <a href="{{ route('register') }}" class="nav-link btn-register">Get Started</a>
            </div>
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12h18M3 6h18M3 18h18"/>
                </svg>
            </button>
        </div>
    </nav>
</header>

<style>
    .landing-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        z-index: 1000;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .navbar {
        padding: 1rem 0;
    }

    .navbar .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
    }

    .brand-logo {
        width: 40px;
        height: 40px;
        object-fit: contain;
    }

    .brand-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e1e1e;
        letter-spacing: -0.02em;
    }

    .navbar-menu {
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .nav-link {
        text-decoration: none;
        color: #4b5563;
        font-weight: 500;
        font-size: 0.95rem;
        transition: color 0.2s ease;
    }

    .nav-link:hover {
        color: #10B981;
    }

    .btn-login {
        padding: 0.5rem 1.25rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .btn-login:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    .btn-register {
        padding: 0.5rem 1.25rem;
        background: #10B981;
        color: white;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .btn-register:hover {
        background: #059669;
    }

    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        color: #1e1e1e;
    }

    @media (max-width: 768px) {
        .navbar-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            flex-direction: column;
            padding: 1rem 2rem;
            gap: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar-menu.active {
            display: flex;
        }

        .mobile-menu-toggle {
            display: block;
        }

        .nav-link {
            width: 100%;
            text-align: center;
            padding: 0.75rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const navbarMenu = document.querySelector('.navbar-menu');

        if (mobileMenuToggle && navbarMenu) {
            mobileMenuToggle.addEventListener('click', function() {
                navbarMenu.classList.toggle('active');
            });
        }
    });
</script>
