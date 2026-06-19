<section class="why-choose-us-section" id="why-choose-us">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title animate__animated animate__fadeInDown">Why Choose MannaPOS?</h2>
            <p class="section-subtitle animate__animated animate__fadeInUp animate__delay-1s">Built for businesses that want to grow faster and smarter</p>
        </div>

        <div class="carousel-section">
            <div class="carousel-header">
                <div class="carousel-nav">
                    <button class="carousel-btn prev" id="carouselPrev" aria-label="Previous">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <button class="carousel-btn next" id="carouselNext" aria-label="Next">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
            <div class="carousel-wrapper">
                <div class="carousel-track" id="carouselTrack">
                    <div class="carousel-card">
                        <div class="carousel-number">01</div>
                        <h4 class="carousel-card-title">Easy to Use</h4>
                        <p class="carousel-card-desc">Intuitive interface that requires minimal training. Your team will be up and running in minutes, not days.</p>
                    </div>
                    <div class="carousel-card">
                        <div class="carousel-number">02</div>
                        <h4 class="carousel-card-title">Affordable Pricing</h4>
                        <p class="carousel-card-desc">Competitive pricing with no hidden fees. Choose a plan that fits your business needs and budget.</p>
                    </div>
                    <div class="carousel-card">
                        <div class="carousel-number">03</div>
                        <h4 class="carousel-card-title">Cloud-Based</h4>
                        <p class="carousel-card-desc">Access your business data from anywhere, anytime. No expensive hardware or software installations needed.</p>
                    </div>
                    <div class="carousel-card">
                        <div class="carousel-number">04</div>
                        <h4 class="carousel-card-title">Multi-Location Support</h4>
                        <p class="carousel-card-desc">Manage multiple stores or locations from a single dashboard. Track performance across all your outlets.</p>
                    </div>
                    <div class="carousel-card">
                        <div class="carousel-number">05</div>
                        <h4 class="carousel-card-title">Integration Ready</h4>
                        <p class="carousel-card-desc">Seamlessly integrate with accounting software, e-commerce platforms, and payment gateways.</p>
                    </div>
                    <div class="carousel-card">
                        <div class="carousel-number">06</div>
                        <h4 class="carousel-card-title">Regular Updates</h4>
                        <p class="carousel-card-desc">Continuous improvements and new features added regularly based on customer feedback.</p>
                    </div>
                    <div class="carousel-card">
                        <div class="carousel-number">07</div>
                        <h4 class="carousel-card-title">Offline Mode</h4>
                        <p class="carousel-card-desc">Keep selling even without internet. All data syncs automatically when you are back online.</p>
                    </div>
                    <div class="carousel-card">
                        <div class="carousel-number">08</div>
                        <h4 class="carousel-card-title">Role-Based Access</h4>
                        <p class="carousel-card-desc">Control exactly what each team member can see and do with granular permission settings.</p>
                    </div>
                    <div class="carousel-card">
                        <div class="carousel-number">09</div>
                        <h4 class="carousel-card-title">Real-Time Analytics</h4>
                        <p class="carousel-card-desc">Beautiful dashboards and reports that update instantly as transactions happen.</p>
                    </div>
                    <div class="carousel-card">
                        <div class="carousel-number">10</div>
                        <h4 class="carousel-card-title">Barcode Scanning</h4>
                        <p class="carousel-card-desc">Built-in barcode and QR code support for faster checkout and inventory management.</p>
                    </div>
                    <div class="carousel-card">
                        <div class="carousel-number">11</div>
                        <h4 class="carousel-card-title">Customer Loyalty</h4>
                        <p class="carousel-card-desc">Built-in points and rewards system to keep your customers coming back for more.</p>
                    </div>
                    <div class="carousel-card">
                        <div class="carousel-number">12</div>
                        <h4 class="carousel-card-title">24/7 Support</h4>
                        <p class="carousel-card-desc">Our friendly support team is always ready to help via chat, email, or phone.</p>
                    </div>
                </div>
            </div>
            <div class="carousel-dots" id="carouselDots"></div>
        </div>
    </div>
</section>

<style>
    .why-choose-us-section {
        padding: 6rem 0;
        background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 50%, #eff6ff 100%);
        overflow: hidden;
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

    /* Carousel Styles */
    .carousel-section {
        margin-top: 2rem;
        padding-top: 3rem;
        border-top: 1px solid #e5e7eb;
    }

    .carousel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
    }

    .carousel-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1e1e1e;
        letter-spacing: -0.01em;
    }

    .carousel-nav {
        display: flex;
        gap: 0.75rem;
    }

    .carousel-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 1.5px solid #e5e7eb;
        background: white;
        color: #374151;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s ease;
    }

    .carousel-btn:hover {
        background: #10B981;
        border-color: #10B981;
        color: white;
        transform: scale(1.08);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    }

    .carousel-btn:disabled {
        opacity: 0.35;
        cursor: not-allowed;
        transform: none;
        background: white;
        border-color: #e5e7eb;
        color: #9ca3af;
    }

    .carousel-wrapper {
        overflow: hidden;
        position: relative;
    }

    .carousel-track {
        display: flex;
        gap: 1.5rem;
        transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: transform;
    }

    .carousel-card {
        min-width: calc(33.333% - 1rem);
        flex-shrink: 0;
        background: white;
        padding: 2rem;
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }

    .carousel-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #10B981, #059669);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }

    .carousel-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 24px 48px rgba(0, 0, 0, 0.1);
        border-color: #10B981;
    }

    .carousel-card:hover::before {
        transform: scaleX(1);
    }

    .carousel-number {
        font-size: 2rem;
        font-weight: 800;
        color: #10B981;
        margin-bottom: 1rem;
        opacity: 0.8;
    }

    .carousel-card-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1e1e1e;
        margin-bottom: 0.75rem;
    }

    .carousel-card-desc {
        font-size: 0.95rem;
        color: #6b7280;
        line-height: 1.65;
    }

    .carousel-dots {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 2rem;
    }

    .carousel-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #d1d5db;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .carousel-dot.active {
        background: #10B981;
        width: 28px;
        border-radius: 5px;
    }

    @media (max-width: 1024px) {
        .why-choose-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .carousel-card {
            min-width: calc(50% - 0.75rem);
        }
    }

    @media (max-width: 640px) {
        .section-title {
            font-size: 2rem;
        }
        .carousel-title {
            font-size: 1.4rem;
        }
        .why-choose-grid {
            grid-template-columns: 1fr;
        }
        .why-choose-item {
            flex-direction: column;
            gap: 1rem;
        }
        .carousel-card {
            min-width: calc(100% - 0rem);
        }
        .carousel-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }
</style>

<script>
    (function() {
        const track = document.getElementById('carouselTrack');
        const prevBtn = document.getElementById('carouselPrev');
        const nextBtn = document.getElementById('carouselNext');
        const dotsContainer = document.getElementById('carouselDots');
        const cards = track.querySelectorAll('.carousel-card');

        let currentIndex = 0;

        function getCardsPerView() {
            if (window.innerWidth <= 640) return 1;
            if (window.innerWidth <= 1024) return 2;
            return 3;
        }

        function getMaxIndex() {
            return Math.max(0, cards.length - getCardsPerView());
        }

        function updateCarousel() {
            const cardWidth = cards[0].offsetWidth;
            const gap = 24;
            track.style.transform = 'translateX(-' + (currentIndex * (cardWidth + gap)) + 'px)';

            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex >= getMaxIndex();

            // Update dots
            const dots = dotsContainer.querySelectorAll('.carousel-dot');
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentIndex);
            });
        }

        function createDots() {
            dotsContainer.innerHTML = '';
            const max = getMaxIndex();
            for (let i = 0; i <= max; i++) {
                const dot = document.createElement('button');
                dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
                dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
                dot.addEventListener('click', () => {
                    currentIndex = i;
                    updateCarousel();
                });
                dotsContainer.appendChild(dot);
            }
        }

        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateCarousel();
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentIndex < getMaxIndex()) {
                currentIndex++;
                updateCarousel();
            }
        });

        // Touch swipe support
        let touchStartX = 0;
        let touchEndX = 0;

        track.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        track.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > 50) {
                if (diff > 0 && currentIndex < getMaxIndex()) {
                    currentIndex++;
                } else if (diff < 0 && currentIndex > 0) {
                    currentIndex--;
                }
                updateCarousel();
            }
        }, { passive: true });

        window.addEventListener('resize', () => {
            currentIndex = Math.min(currentIndex, getMaxIndex());
            createDots();
            updateCarousel();
        });

        // Auto-play (optional)
        let autoPlay = setInterval(() => {
            if (currentIndex < getMaxIndex()) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            updateCarousel();
        }, 5000);

        track.parentElement.addEventListener('mouseenter', () => clearInterval(autoPlay));
        track.parentElement.addEventListener('mouseleave', () => {
            autoPlay = setInterval(() => {
                if (currentIndex < getMaxIndex()) {
                    currentIndex++;
                } else {
                    currentIndex = 0;
                }
                updateCarousel();
            }, 5000);
        });

        // Intersection Observer for entrance animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.why-choose-item').forEach(el => observer.observe(el));

        createDots();
        updateCarousel();
    })();
</script>
