<section class="features-section" id="features">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Powerful Features for Your Business</h2>
            <p class="section-subtitle">Everything you need to manage your business efficiently and grow your revenue</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="https://cdn-icons-png.flaticon.com/512/3589/3589030.png" alt="Inventory Management" class="feature-icon-img">
                </div>
                <h3 class="feature-title">Inventory Management</h3>
                <p class="feature-description">Track stock levels in real-time, set low stock alerts, and manage suppliers effortlessly.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Payment Processing" class="feature-icon-img">
                </div>
                <h3 class="feature-title">Payment Processing</h3>
                <p class="feature-description">Accept multiple payment methods including cash, card, mobile money, and bank transfers.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Customer Management" class="feature-icon-img">
                </div>
                <h3 class="feature-title">Customer Management</h3>
                <p class="feature-description">Build customer profiles, track purchase history, and implement loyalty programs.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="https://cdn-icons-png.flaticon.com/512/2936/2936690.png" alt="Sales Analytics" class="feature-icon-img">
                </div>
                <h3 class="feature-title">Sales Analytics</h3>
                <p class="feature-description">Get detailed reports and insights on sales performance, trends, and customer behavior.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="https://cdn-icons-png.flaticon.com/512/10312/10312320.png" alt="24/7 Support" class="feature-icon-img">
                </div>
                <h3 class="feature-title">24/7 Support</h3>
                <p class="feature-description">Round-the-clock customer support to help you resolve any issues quickly.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="https://cdn-icons-png.flaticon.com/512/10009/10009508.png" alt="Secure & Reliable" class="feature-icon-img">
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
