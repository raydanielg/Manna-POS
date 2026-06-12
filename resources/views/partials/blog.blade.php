<section id="blog" class="blog-section">

    <div class="blog-container">

        {{-- Header --}}
        <div class="blog-header">
            <h2 class="blog-title">Our Blog</h2>
            <p class="blog-subtitle">Stay up to date with the latest tips, guides, and news to help your business grow and thrive with MannaPOS.</p>
        </div>

        {{-- Cards grid --}}
        <div class="blog-grid">

            {{-- Card 1 --}}
            <article class="blog-card">
                <a href="#" class="blog-card-img-wrap">
                    <img
                        src="https://images.unsplash.com/photo-1556740738-b6a63e27c4df?w=800&auto=format&fit=crop&q=60"
                        alt="Smart Inventory Tips"
                        class="blog-card-img"
                    >
                </a>
                <div class="blog-card-body">
                    <span class="blog-badge">Article</span>
                    <a href="#" class="blog-card-title-link">
                        <h3 class="blog-card-title">5 Smart Ways to Manage Inventory and Never Run Out of Stock</h3>
                    </a>
                    <p class="blog-card-desc">Learn how modern POS systems can automate reordering, track supplier deliveries, and keep your shelves perfectly stocked at all times.</p>
                    <div class="blog-card-author">
                        <img
                            src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&auto=format&fit=crop&q=60"
                            alt="Amina Hassan"
                            class="blog-author-avatar"
                        >
                        <div class="blog-author-info">
                            <span class="blog-author-name">Amina Hassan</span>
                            <span class="blog-author-meta">Jun 10, 2025 &middot; 8 min read</span>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Card 2 --}}
            <article class="blog-card">
                <a href="#" class="blog-card-img-wrap">
                    <img
                        src="https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=800&auto=format&fit=crop&q=60"
                        alt="Mobile Payments"
                        class="blog-card-img"
                    >
                </a>
                <div class="blog-card-body">
                    <span class="blog-badge">Article</span>
                    <a href="#" class="blog-card-title-link">
                        <h3 class="blog-card-title">How Mobile Payments Are Changing the Future of Retail in Africa</h3>
                    </a>
                    <p class="blog-card-desc">From M-Pesa to Tigopesa — discover how accepting mobile money can increase your sales and attract more customers to your store.</p>
                    <div class="blog-card-author">
                        <img
                            src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&auto=format&fit=crop&q=60"
                            alt="David Mwangi"
                            class="blog-author-avatar"
                        >
                        <div class="blog-author-info">
                            <span class="blog-author-name">David Mwangi</span>
                            <span class="blog-author-meta">May 28, 2025 &middot; 6 min read</span>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Card 3 --}}
            <article class="blog-card">
                <a href="#" class="blog-card-img-wrap">
                    <img
                        src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&auto=format&fit=crop&q=60"
                        alt="Sales Analytics"
                        class="blog-card-img"
                    >
                </a>
                <div class="blog-card-body">
                    <span class="blog-badge">Article</span>
                    <a href="#" class="blog-card-title-link">
                        <h3 class="blog-card-title">Using Sales Data to Make Smarter Business Decisions Every Day</h3>
                    </a>
                    <p class="blog-card-desc">Your POS holds a goldmine of insights. Here is how to read your reports, spot your best sellers, and grow your profits consistently.</p>
                    <div class="blog-card-author">
                        <img
                            src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=80&auto=format&fit=crop&q=60"
                            alt="Grace Omondi"
                            class="blog-author-avatar"
                        >
                        <div class="blog-author-info">
                            <span class="blog-author-name">Grace Omondi</span>
                            <span class="blog-author-meta">May 14, 2025 &middot; 10 min read</span>
                        </div>
                    </div>
                </div>
            </article>

        </div>{{-- /grid --}}

        {{-- View all link --}}
        <div class="blog-footer">
            <a href="#" class="blog-view-all">
                View all articles
                <svg viewBox="0 0 20 20" fill="currentColor" class="blog-view-all-icon">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>

    </div>
</section>

<style>
/* ─── Section ────────────────────────────────────────────── */
.blog-section {
    padding: 6rem 0 7rem;
    background: #f1f5f9;
}

.blog-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* ─── Header ─────────────────────────────────────────────── */
.blog-header {
    text-align: center;
    margin-bottom: 3.5rem;
}

.blog-title {
    font-size: clamp(1.85rem, 4vw, 2.5rem);
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.025em;
    margin-bottom: 0.85rem;
}

.blog-subtitle {
    font-size: 1.05rem;
    color: #64748b;
    max-width: 520px;
    margin: 0 auto;
    line-height: 1.7;
}

/* ─── Grid ───────────────────────────────────────────────── */
.blog-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.75rem;
}

@media (max-width: 900px) {
    .blog-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 580px) {
    .blog-grid { grid-template-columns: 1fr; }
}

/* ─── Card ───────────────────────────────────────────────── */
.blog-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    /* entrance */
    opacity: 0;
    transform: translateY(24px);
}

.blog-card.blog-visible {
    opacity: 1;
    transform: translateY(0);
}

.blog-card.blog-visible:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 36px rgba(15, 23, 42, 0.1);
}

/* ─── Image ──────────────────────────────────────────────── */
.blog-card-img-wrap {
    display: block;
    overflow: hidden;
    height: 210px;
    flex-shrink: 0;
}

.blog-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.blog-card:hover .blog-card-img {
    transform: scale(1.04);
}

/* ─── Body ───────────────────────────────────────────────── */
.blog-card-body {
    padding: 1.5rem 1.5rem 1.4rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    gap: 0;
}

/* ─── Badge ──────────────────────────────────────────────── */
.blog-badge {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.03em;
    color: #6d28d9;
    background: #ede9fe;
    padding: 0.25rem 0.7rem;
    border-radius: 100px;
    margin-bottom: 0.85rem;
    width: fit-content;
}

/* ─── Title ──────────────────────────────────────────────── */
.blog-card-title-link { text-decoration: none; }

.blog-card-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.45;
    margin: 0 0 0.7rem;
    transition: color 0.2s ease;
}

.blog-card-title-link:hover .blog-card-title {
    color: #2563eb;
}

/* ─── Description ────────────────────────────────────────── */
.blog-card-desc {
    font-size: 0.9rem;
    color: #64748b;
    line-height: 1.65;
    margin: 0 0 1.4rem;
    flex-grow: 1;
}

/* ─── Author ─────────────────────────────────────────────── */
.blog-card-author {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-top: auto;
    padding-top: 1rem;
    border-top: 1px solid #f1f5f9;
}

.blog-author-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 2px solid #e2e8f0;
}

.blog-author-info {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.blog-author-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
}

.blog-author-meta {
    font-size: 0.8rem;
    color: #94a3b8;
}

/* ─── Footer / View all ──────────────────────────────────── */
.blog-footer {
    display: flex;
    justify-content: center;
    margin-top: 3rem;
}

.blog-view-all {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
    font-weight: 600;
    color: #2563eb;
    text-decoration: none;
    padding: 0.65rem 1.5rem;
    border: 1.5px solid #bfdbfe;
    border-radius: 10px;
    background: #fff;
    transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
}

.blog-view-all:hover {
    background: #eff6ff;
    border-color: #93c5fd;
    transform: translateY(-2px);
}

.blog-view-all-icon {
    width: 16px;
    height: 16px;
    transition: transform 0.2s ease;
}

.blog-view-all:hover .blog-view-all-icon {
    transform: translateX(3px);
}
</style>

<script>
(function () {
    const cards = document.querySelectorAll('.blog-card');
    if (!cards.length) return;
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const card = entry.target;
                const idx = Array.from(cards).indexOf(card);
                setTimeout(() => card.classList.add('blog-visible'), idx * 100);
                io.unobserve(card);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });
    cards.forEach(card => io.observe(card));
})();
</script>
