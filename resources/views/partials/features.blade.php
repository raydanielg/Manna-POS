<section class="features-section" id="features">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Powerful Features for Your Business</h2>
            <p class="section-subtitle">Everything you need to manage your business efficiently and grow your revenue</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z"/>
                        <path d="M2 17L12 22L22 17"/>
                        <path d="M2 12L12 17L22 12"/>
                    </svg>
                </div>
                <h3 class="feature-title">Inventory Management</h3>
                <p class="feature-description">Track stock levels in real-time, set low stock alerts, and manage suppliers effortlessly.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                    </svg>
                </div>
                <h3 class="feature-title">Payment Processing</h3>
                <p class="feature-description">Accept multiple payment methods including cash, card, mobile money, and bank transfers.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <path d="M20 8v6M23 11h-6"/>
                    </svg>
                </div>
                <h3 class="feature-title">Customer Management</h3>
                <p class="feature-description">Build customer profiles, track purchase history, and implement loyalty programs.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="feature-title">Sales Analytics</h3>
                <p class="feature-description">Get detailed reports and insights on sales performance, trends, and customer behavior.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="feature-title">24/7 Support</h3>
                <p class="feature-description">Round-the-clock customer support to help you resolve any issues quickly.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="feature-title">Secure & Reliable</h3>
                <p class="feature-description">Bank-level security to protect your data with 99.9% uptime guarantee.</p>
            </div>
        </div>
    </div>
</section>

<style>
    .features-section {
        padding: 6rem 0;
        background: white;
    }

    .features-section .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .section-header {
        text-align: center;
        margin-bottom: 4rem;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1e1e1e;
        margin-bottom: 1rem;
        letter-spacing: -0.02em;
    }

    .section-subtitle {
        font-size: 1.125rem;
        color: #6b7280;
        max-width: 600px;
        margin: 0 auto;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .feature-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 2rem;
        transition: all 0.3s ease;
    }

    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border-color: #10B981;
    }

    .feature-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #10B981, #059669);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .feature-icon svg {
        color: white;
    }

    .feature-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e1e1e;
        margin-bottom: 0.75rem;
    }

    .feature-description {
        font-size: 1rem;
        color: #6b7280;
        line-height: 1.6;
    }

    @media (max-width: 640px) {
        .section-title {
            font-size: 2rem;
        }

        .features-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
