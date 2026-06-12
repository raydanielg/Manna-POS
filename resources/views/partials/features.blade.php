<section id="features" class="feat-section">

    <div class="feat-bg-deco" aria-hidden="true">
        <div class="feat-bg-circle feat-bg-circle--1"></div>
        <div class="feat-bg-circle feat-bg-circle--2"></div>
        <div class="feat-bg-grid"></div>
    </div>

    <div class="feat-container">

        <div class="feat-header">
            <span class="feat-eyebrow">Everything You Need</span>
            <h2 class="feat-title">Powerful Features for<br>Your <span class="feat-title-accent">Growing Business</span></h2>
            <p class="feat-subtitle">From inventory to analytics — MannaPOS gives you the complete toolkit to run smarter, sell faster, and grow stronger.</p>
        </div>

        <div class="feat-grid">

            <div class="feat-card" data-color="blue">
                <div class="feat-icon-wrap">
                    <img src="https://cdn-icons-png.flaticon.com/512/3588/3588592.png" alt="Inventory" class="feat-icon-img">
                </div>
                <h3 class="feat-card-title">Inventory Management</h3>
                <p class="feat-card-desc">Track stock in real-time, set smart low-stock alerts, and manage multiple suppliers — all from one dashboard.</p>
            </div>

            <div class="feat-card" data-color="violet">
                <div class="feat-icon-wrap">
                    <img src="https://cdn-icons-png.flaticon.com/512/2645/2645890.png" alt="Payments" class="feat-icon-img">
                </div>
                <h3 class="feat-card-title">Payment Processing</h3>
                <p class="feat-card-desc">Accept cash, card, mobile money, and bank transfers. Fast checkouts with zero transaction headaches.</p>
            </div>

            <div class="feat-card" data-color="cyan">
                <div class="feat-icon-wrap">
                    <img src="https://cdn-icons-png.flaticon.com/512/1256/1256650.png" alt="Customers" class="feat-icon-img">
                </div>
                <h3 class="feat-card-title">Customer Management</h3>
                <p class="feat-card-desc">Build rich customer profiles, track purchase history, and run loyalty programs that keep buyers coming back.</p>
            </div>

            <div class="feat-card" data-color="orange">
                <div class="feat-icon-wrap">
                    <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="Analytics" class="feat-icon-img">
                </div>
                <h3 class="feat-card-title">Sales Analytics</h3>
                <p class="feat-card-desc">Deep-dive reports on revenue, product performance, and trends. Make decisions backed by real data.</p>
            </div>

            <div class="feat-card" data-color="green">
                <div class="feat-icon-wrap">
                    <img src="https://cdn-icons-png.flaticon.com/512/9195/9195785.png" alt="Support" class="feat-icon-img">
                </div>
                <h3 class="feat-card-title">24/7 Support</h3>
                <p class="feat-card-desc">Our dedicated team is always on standby. Live chat, phone, and email — we're here whenever you need us.</p>
            </div>

            <div class="feat-card" data-color="rose">
                <div class="feat-icon-wrap">
                    <img src="https://cdn-icons-png.flaticon.com/512/3064/3064197.png" alt="Security" class="feat-icon-img">
                </div>
                <h3 class="feat-card-title">Secure & Reliable</h3>
                <p class="feat-card-desc">Bank-grade encryption, role-based access, and automatic backups. Your data is safe — always.</p>
            </div>

        </div>
    </div>
</section>

<style>
.feat-section {
    position: relative;
    padding: 7rem 0 8rem;
    background: #f8faff;
    overflow: hidden;
}

.feat-bg-deco { position: absolute; inset: 0; pointer-events: none; }

.feat-bg-circle {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.45;
}
.feat-bg-circle--1 {
    width: 600px; height: 600px;
    top: -200px; right: -150px;
    background: radial-gradient(circle, #dbeafe, #e0e7ff);
}
.feat-bg-circle--2 {
    width: 500px; height: 500px;
    bottom: -150px; left: -100px;
    background: radial-gradient(circle, #d1fae5, #e0f2fe);
}
.feat-bg-grid {
    position: absolute; inset: 0;
    background-image:
        linear-gradient(rgba(99,130,220,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(99,130,220,0.04) 1px, transparent 1px);
    background-size: 40px 40px;
}

.feat-container {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

.feat-header {
    text-align: center;
    margin-bottom: 4.5rem;
}
.feat-eyebrow {
    display: inline-block;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #2563eb;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 0.35rem 1rem;
    border-radius: 100px;
    margin-bottom: 1.2rem;
}
.feat-title {
    font-size: clamp(2rem, 4vw, 2.75rem);
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    letter-spacing: -0.025em;
    margin-bottom: 1.1rem;
}
.feat-title-accent {
    background: linear-gradient(90deg, #2563eb 0%, #7c3aed 60%, #0ea5e9 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.feat-subtitle {
    font-size: 1.1rem;
    color: #64748b;
    max-width: 580px;
    margin: 0 auto;
    line-height: 1.7;
}

.feat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.75rem;
}
@media (max-width: 900px) { .feat-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 560px) { .feat-grid { grid-template-columns: 1fr; } }

/* ─── Card ───────────────────────────────────────────────── */
.feat-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 2rem 1.85rem 1.8rem;
    border: 1.5px solid #e2e8f0;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    /* entrance */
    opacity: 0;
    transform: translateY(28px);
}
.feat-card.feat-visible {
    opacity: 1;
    transform: translateY(0);
}
.feat-card.feat-visible:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.1);
}

/* ─── Icon wrapper ───────────────────────────────────────── */
.feat-icon-wrap {
    width: 68px;
    height: 68px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.4rem;
}

[data-color="blue"]   .feat-icon-wrap { background: linear-gradient(135deg,#eff6ff,#dbeafe); }
[data-color="violet"] .feat-icon-wrap { background: linear-gradient(135deg,#f5f3ff,#ede9fe); }
[data-color="cyan"]   .feat-icon-wrap { background: linear-gradient(135deg,#ecfeff,#cffafe); }
[data-color="orange"] .feat-icon-wrap { background: linear-gradient(135deg,#fff7ed,#ffedd5); }
[data-color="green"]  .feat-icon-wrap { background: linear-gradient(135deg,#f0fdf4,#dcfce7); }
[data-color="rose"]   .feat-icon-wrap { background: linear-gradient(135deg,#fff1f2,#ffe4e6); }

.feat-icon-img {
    width: 36px;
    height: 36px;
    object-fit: contain;
}

/* ─── Card text ─────────────────────────────────────────── */
.feat-card-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 0.6rem;
    letter-spacing: -0.01em;
}
.feat-card-desc {
    font-size: 0.93rem;
    color: #64748b;
    line-height: 1.7;
    margin: 0;
}
</style>

<script>
(function () {
    const cards = document.querySelectorAll('.feat-card');
    if (!cards.length) return;
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const card = entry.target;
                const idx = Array.from(cards).indexOf(card);
                setTimeout(() => card.classList.add('feat-visible'), idx * 90);
                io.unobserve(card);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    cards.forEach(card => io.observe(card));
})();
</script>
