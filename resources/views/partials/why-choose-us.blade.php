<section class="why-choose-us-section" id="why-choose-us">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Why Choose MannaPOS?</h2>
            <p class="section-subtitle">Built for businesses that want to grow faster and smarter</p>
        </div>
        <div class="why-choose-grid">
            <div class="why-choose-item">
                <div class="why-choose-number">01</div>
                <div class="why-choose-content">
                    <h3 class="why-choose-title">Easy to Use</h3>
                    <p class="why-choose-description">Intuitive interface that requires minimal training. Your team will be up and running in minutes, not days.</p>
                </div>
            </div>
            <div class="why-choose-item">
                <div class="why-choose-number">02</div>
                <div class="why-choose-content">
                    <h3 class="why-choose-title">Affordable Pricing</h3>
                    <p class="why-choose-description">Competitive pricing with no hidden fees. Choose a plan that fits your business needs and budget.</p>
                </div>
            </div>
            <div class="why-choose-item">
                <div class="why-choose-number">03</div>
                <div class="why-choose-content">
                    <h3 class="why-choose-title">Cloud-Based</h3>
                    <p class="why-choose-description">Access your business data from anywhere, anytime. No expensive hardware or software installations needed.</p>
                </div>
            </div>
            <div class="why-choose-item">
                <div class="why-choose-number">04</div>
                <div class="why-choose-content">
                    <h3 class="why-choose-title">Multi-Location Support</h3>
                    <p class="why-choose-description">Manage multiple stores or locations from a single dashboard. Track performance across all your outlets.</p>
                </div>
            </div>
            <div class="why-choose-item">
                <div class="why-choose-number">05</div>
                <div class="why-choose-content">
                    <h3 class="why-choose-title">Integration Ready</h3>
                    <p class="why-choose-description">Seamlessly integrate with accounting software, e-commerce platforms, and payment gateways.</p>
                </div>
            </div>
            <div class="why-choose-item">
                <div class="why-choose-number">06</div>
                <div class="why-choose-content">
                    <h3 class="why-choose-title">Regular Updates</h3>
                    <p class="why-choose-description">Continuous improvements and new features added regularly based on customer feedback.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .why-choose-us-section {
        padding: 6rem 0;
        background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
    }

    .why-choose-us-section .container {
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

    .why-choose-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
    }

    .why-choose-item {
        display: flex;
        gap: 1.5rem;
        background: white;
        padding: 2rem;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .why-choose-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        border-color: #10B981;
    }

    .why-choose-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: #10B981;
        line-height: 1;
        min-width: 60px;
    }

    .why-choose-content {
        flex: 1;
    }

    .why-choose-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e1e1e;
        margin-bottom: 0.5rem;
    }

    .why-choose-description {
        font-size: 1rem;
        color: #6b7280;
        line-height: 1.6;
    }

    @media (max-width: 640px) {
        .section-title {
            font-size: 2rem;
        }

        .why-choose-grid {
            grid-template-columns: 1fr;
        }

        .why-choose-item {
            flex-direction: column;
            gap: 1rem;
        }

        .why-choose-number {
            min-width: auto;
        }
    }
</style>
