<section class="hero-section" id="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">Transform Your Business with MannaPOS</h1>
                <p class="hero-subtitle">The complete Point of Sale solution for modern businesses. Streamline operations, boost sales, and delight customers.</p>
                <div class="hero-buttons">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-large">Get Started Free</a>
                    <a href="#features" class="btn btn-secondary btn-large">Learn More</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number">10K+</div>
                        <div class="stat-label">Active Users</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">$50M+</div>
                        <div class="stat-label">Transactions</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">99.9%</div>
                        <div class="stat-label">Uptime</div>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-illustration">
                    <svg viewBox="0 0 500 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="50" y="50" width="400" height="300" rx="20" fill="#f3f4f6"/>
                        <rect x="70" y="70" width="360" height="40" rx="8" fill="#10B981"/>
                        <rect x="70" y="130" width="160" height="20" rx="4" fill="#d1d5db"/>
                        <rect x="70" y="160" width="200" height="20" rx="4" fill="#d1d5db"/>
                        <rect x="70" y="190" width="140" height="20" rx="4" fill="#d1d5db"/>
                        <rect x="250" y="130" width="180" height="80" rx="8" fill="#e5e7eb"/>
                        <rect x="70" y="230" width="360" height="100" rx="8" fill="#e5e7eb"/>
                        <circle cx="400" cy="320" r="30" fill="#10B981"/>
                        <path d="M390 320L398 328L410 312" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .hero-section {
        padding: 8rem 0 4rem;
        background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .hero-section .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .hero-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1.2;
        color: #1e1e1e;
        margin-bottom: 1.5rem;
        letter-spacing: -0.03em;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        color: #6b7280;
        line-height: 1.7;
        margin-bottom: 2rem;
    }

    .hero-buttons {
        display: flex;
        gap: 1rem;
        margin-bottom: 3rem;
    }

    .btn-large {
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #10B981;
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-secondary {
        background: white;
        color: #1e1e1e;
        border: 2px solid #e5e7eb;
    }

    .btn-secondary:hover {
        border-color: #10B981;
        color: #10B981;
    }

    .hero-stats {
        display: flex;
        gap: 3rem;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 800;
        color: #10B981;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }

    .hero-illustration {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
    }

    .hero-illustration svg {
        width: 100%;
        height: auto;
    }

    @media (max-width: 968px) {
        .hero-content {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .hero-buttons {
            justify-content: center;
        }

        .hero-stats {
            justify-content: center;
        }

        .hero-title {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 640px) {
        .hero-buttons {
            flex-direction: column;
        }

        .hero-stats {
            flex-direction: column;
            gap: 1.5rem;
        }
    }
</style>
