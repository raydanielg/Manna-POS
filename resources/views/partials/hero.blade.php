<section class="relative min-h-screen flex flex-col justify-center overflow-hidden hero-section" style="padding-top: 68px;">

    {{-- Animated wave/grid background --}}
    <div class="absolute inset-0 hero-bg">
        {{-- Deep gradient base --}}
        <div class="absolute inset-0" style="background: linear-gradient(135deg, #0f1f4b 0%, #0d2d6b 30%, #0a3d8f 55%, #0e4fa3 75%, #1565c0 100%);"></div>

        {{-- Animated grid lines --}}
        <div class="absolute inset-0 hero-grid"></div>

        {{-- Glowing orbs --}}
        <div class="absolute top-1/4 left-1/4 w-96 h-96 rounded-full orb-1" style="background: radial-gradient(circle, rgba(59,130,246,0.25) 0%, transparent 70%);"></div>
        <div class="absolute bottom-1/3 right-1/4 w-80 h-80 rounded-full orb-2" style="background: radial-gradient(circle, rgba(99,102,241,0.2) 0%, transparent 70%);"></div>
        <div class="absolute top-1/2 right-1/3 w-64 h-64 rounded-full orb-3" style="background: radial-gradient(circle, rgba(14,165,233,0.15) 0%, transparent 70%);"></div>

        {{-- Animated flowing lines SVG --}}
        <svg class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
            <defs>
                <linearGradient id="lineGrad1" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:rgba(99,179,255,0);"/>
                    <stop offset="50%" style="stop-color:rgba(99,179,255,0.5);"/>
                    <stop offset="100%" style="stop-color:rgba(99,179,255,0);"/>
                </linearGradient>
                <linearGradient id="lineGrad2" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:rgba(147,197,253,0);"/>
                    <stop offset="50%" style="stop-color:rgba(147,197,253,0.35);"/>
                    <stop offset="100%" style="stop-color:rgba(147,197,253,0);"/>
                </linearGradient>
                <linearGradient id="lineGrad3" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:rgba(196,181,253,0);"/>
                    <stop offset="50%" style="stop-color:rgba(196,181,253,0.3);"/>
                    <stop offset="100%" style="stop-color:rgba(196,181,253,0);"/>
                </linearGradient>
            </defs>

            {{-- Horizontal flowing lines --}}
            <line class="flow-line-h" x1="-100%" y1="25%" x2="200%" y2="25%" stroke="url(#lineGrad1)" stroke-width="1"/>
            <line class="flow-line-h delay-1" x1="-100%" y1="40%" x2="200%" y2="40%" stroke="url(#lineGrad2)" stroke-width="0.5"/>
            <line class="flow-line-h delay-2" x1="-100%" y1="60%" x2="200%" y2="60%" stroke="url(#lineGrad1)" stroke-width="1"/>
            <line class="flow-line-h delay-3" x1="-100%" y1="75%" x2="200%" y2="75%" stroke="url(#lineGrad3)" stroke-width="0.5"/>
            <line class="flow-line-h delay-4" x1="-100%" y1="15%" x2="200%" y2="15%" stroke="url(#lineGrad2)" stroke-width="0.7"/>
            <line class="flow-line-h delay-5" x1="-100%" y1="85%" x2="200%" y2="85%" stroke="url(#lineGrad1)" stroke-width="0.6"/>

            {{-- Diagonal flowing lines --}}
            <line class="flow-line-d" x1="-20%" y1="0%" x2="120%" y2="100%" stroke="url(#lineGrad1)" stroke-width="0.6"/>
            <line class="flow-line-d delay-2" x1="0%" y1="0%" x2="140%" y2="100%" stroke="url(#lineGrad2)" stroke-width="0.4"/>
            <line class="flow-line-d delay-4" x1="-40%" y1="0%" x2="100%" y2="100%" stroke="url(#lineGrad3)" stroke-width="0.5"/>
            <line class="flow-line-d delay-1" x1="20%" y1="0%" x2="160%" y2="100%" stroke="url(#lineGrad1)" stroke-width="0.3"/>

            {{-- Wave paths --}}
            <path class="wave-path" d="M-100,200 Q100,120 300,200 T700,200 T1100,200 T1500,200 T1900,200 T2300,200" fill="none" stroke="rgba(99,179,255,0.2)" stroke-width="1.5"/>
            <path class="wave-path delay-2" d="M-100,350 Q150,270 350,350 T750,350 T1150,350 T1550,350 T1950,350" fill="none" stroke="rgba(147,197,253,0.15)" stroke-width="1"/>
            <path class="wave-path delay-4" d="M-100,500 Q200,420 400,500 T800,500 T1200,500 T1600,500" fill="none" stroke="rgba(196,181,253,0.12)" stroke-width="1"/>
        </svg>

        {{-- Dot pattern overlay --}}
        <div class="absolute inset-0 hero-dots"></div>

        {{-- Bottom wave --}}
        <div class="absolute bottom-0 left-0 right-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" preserveAspectRatio="none" class="w-full">
                <path fill="white" fill-opacity="1" d="M0,64L48,69.3C96,75,192,85,288,80C384,75,480,53,576,48C672,43,768,53,864,64C960,75,1056,85,1152,80C1248,75,1344,53,1392,42.7L1440,32L1440,120L1392,120C1344,120,1248,120,1152,120C1056,120,960,120,864,120C768,120,672,120,576,120C480,120,384,120,288,120C192,120,96,120,48,120L0,120Z"></path>
            </svg>
        </div>
    </div>

    {{-- Hero Content --}}
    <div class="relative z-10 py-20 px-4 mx-auto max-w-screen-xl text-center lg:py-28 lg:px-12">

        {{-- Badge --}}
        <a href="#features" class="hero-badge inline-flex justify-between items-center py-1.5 px-2 pr-5 mb-8 text-sm text-blue-100 rounded-full border border-blue-400/30 hover:border-blue-400/60 transition-all duration-300" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(10px);">
            <span class="text-xs font-semibold bg-primary-500 rounded-full text-white px-3 py-1 mr-3">New</span>
            <span class="font-medium">MannaPOS v2.0 is live — See what's new</span>
            <svg class="ml-2 w-4 h-4 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
        </a>

        {{-- Main Headline --}}
        <h1 class="hero-title mb-6 text-4xl font-extrabold tracking-tight leading-tight text-white md:text-5xl lg:text-6xl xl:text-7xl">
            Transform Your Business<br>
            <span class="hero-gradient-text">with MannaPOS</span>
        </h1>

        {{-- Subheadline --}}
        <p class="hero-subtitle mb-10 text-lg font-normal text-blue-100/80 lg:text-xl max-w-2xl mx-auto leading-relaxed">
            The complete Point of Sale solution for modern businesses. Streamline operations, boost sales, and delight customers — all in one powerful platform.
        </p>

        {{-- CTA Buttons --}}
        <div class="hero-cta flex flex-col sm:flex-row items-center justify-center gap-4 mb-12">
            <a href="{{ route('register') }}" class="group w-full sm:w-auto inline-flex justify-center items-center py-3.5 px-8 text-base font-semibold text-white bg-primary-500 hover:bg-primary-400 rounded-xl shadow-lg shadow-primary-900/50 hover:shadow-primary-800/60 hover:-translate-y-0.5 transition-all duration-200">
                Get Started Free
                <svg class="ml-2 w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
            <a href="https://play.google.com" target="_blank" class="group w-full sm:w-auto inline-flex justify-center items-center py-3.5 px-8 text-base font-semibold text-white rounded-xl border border-white/20 hover:border-white/40 hover:-translate-y-0.5 transition-all duration-200" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(10px);">
                <svg class="mr-2 w-5 h-5 text-emerald-400" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3,20.5V3.5C3,2.91 3.4,2.38 4,2.22L13.05,12L4,21.78C3.4,21.62 3,21.09 3,20.5M16.29,15.45L6.05,21.34L14.53,12.86L16.29,15.45M20.05,10.36C20.45,10.66 20.71,11.13 20.71,11.66C20.71,12.22 20.41,12.71 19.96,13C19.56,13.26 19.09,13.35 18.63,13.22L16.94,12.42L15.23,16.32L20.05,10.36M11.43,7.59L4.74,3.22C5.23,3.08 5.75,3 6.25,3C7.21,3 8.14,3.28 8.93,3.81L13.03,6.37L11.43,7.59M16.88,5.69L18.57,6.5C18.89,6.63 19.19,6.83 19.43,7.09C19.77,7.47 19.95,7.97 19.92,8.5L14.13,14.72L16.88,5.69Z"/>
                </svg>
                Download Android App
            </a>
        </div>

        {{-- Android Phone Mockup --}}
        <div class="hero-phone relative mx-auto mb-12" style="perspective: 1000px;">
            <div class="phone-device relative mx-auto" style="width: 260px; height: 520px;">
                {{-- Phone Frame --}}
                <div class="absolute inset-0 rounded-[2.5rem] border-[6px] border-slate-800/90 shadow-2xl shadow-black/60 phone-frame" style="background: #0f172a;">
                    {{-- Notch --}}
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-28 h-6 bg-slate-800/90 rounded-b-2xl z-20"></div>
                    {{-- Side Buttons --}}
                    <div class="absolute -right-[7px] top-20 w-[3px] h-10 bg-slate-700 rounded-r-md"></div>
                    <div class="absolute -right-[7px] top-32 w-[3px] h-16 bg-slate-700 rounded-r-md"></div>
                    {{-- Screen --}}
                    <div class="absolute inset-[6px] rounded-[2rem] overflow-hidden bg-slate-950">
                        {{-- Status Bar --}}
                        <div class="flex items-center justify-between px-5 pt-2.5 pb-1 text-[9px] font-medium text-white/80">
                            <span>9:41</span>
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M17.778 8.232c-2.403-2.3-6.365-2.3-8.768 0l-.63.6-.63-.6c-2.403-2.3-6.365-2.3-8.768 0-2.964 2.838-2.964 7.44 0 10.278L8.38 19.9a2.25 2.25 0 003.24 0l6.4-6.39c2.964-2.838 2.964-7.44 0-10.278z"/></svg>
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M17 6a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3h8a3 3 0 003-3V6zM5 6a1 1 0 011-1h8a1 1 0 011 1v8a1 1 0 01-1 1H6a1 1 0 01-1-1V6z"/></svg>
                            </div>
                        </div>
                        {{-- App Content --}}
                        <div class="px-3 pt-1">
                            {{-- Greeting --}}
                            <div class="text-white/90 text-[10px] font-medium">Good Morning, James</div>
                            <div class="text-white/50 text-[8px] mb-2">Monday, Jun 16</div>
                            {{-- Sales Card --}}
                            <div class="rounded-xl p-3 mb-2" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <div class="text-white/80 text-[8px] font-medium uppercase tracking-wider mb-0.5">Today's Sales</div>
                                <div class="text-white text-base font-bold leading-tight">TZS 1,245,000</div>
                                <div class="flex items-center gap-1 mt-0.5">
                                    <svg class="w-2.5 h-2.5 text-emerald-200" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                    <span class="text-[8px] text-emerald-100 font-semibold">+18.5%</span>
                                    <span class="text-[7px] text-emerald-200/70">vs yesterday</span>
                                </div>
                            </div>
                            {{-- Mini Stats Row --}}
                            <div class="grid grid-cols-3 gap-1.5 mb-2">
                                <div class="rounded-lg p-1.5 text-center" style="background: rgba(255,255,255,0.06);">
                                    <div class="text-[7px] text-white/50 uppercase tracking-wider">Orders</div>
                                    <div class="text-white text-[10px] font-bold">48</div>
                                </div>
                                <div class="rounded-lg p-1.5 text-center" style="background: rgba(255,255,255,0.06);">
                                    <div class="text-[7px] text-white/50 uppercase tracking-wider">Items</div>
                                    <div class="text-white text-[10px] font-bold">156</div>
                                </div>
                                <div class="rounded-lg p-1.5 text-center" style="background: rgba(255,255,255,0.06);">
                                    <div class="text-[7px] text-white/50 uppercase tracking-wider">Avg</div>
                                    <div class="text-white text-[10px] font-bold">25.9k</div>
                                </div>
                            </div>
                            {{-- Recent Transactions List --}}
                            <div class="rounded-xl p-2.5" style="background: rgba(255,255,255,0.04);">
                                <div class="text-[8px] text-white/60 font-semibold mb-1.5 uppercase tracking-wider">Recent Sales</div>
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                                <svg class="w-2.5 h-2.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-white/90 text-[8px] font-medium">Invoice #2041</div>
                                                <div class="text-white/40 text-[6px]">2 min ago</div>
                                            </div>
                                        </div>
                                        <span class="text-emerald-400 text-[8px] font-bold">+85,000</span>
                                    </div>
                                    <div class="w-full h-px bg-white/5"></div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-5 h-5 rounded-full bg-blue-500/20 flex items-center justify-center">
                                                <svg class="w-2.5 h-2.5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-white/90 text-[8px] font-medium">Invoice #2040</div>
                                                <div class="text-white/40 text-[6px]">15 min ago</div>
                                            </div>
                                        </div>
                                        <span class="text-emerald-400 text-[8px] font-bold">+120,000</span>
                                    </div>
                                    <div class="w-full h-px bg-white/5"></div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-5 h-5 rounded-full bg-amber-500/20 flex items-center justify-center">
                                                <svg class="w-2.5 h-2.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-white/90 text-[8px] font-medium">Invoice #2039</div>
                                                <div class="text-white/40 text-[6px]">1 hr ago</div>
                                            </div>
                                        </div>
                                        <span class="text-emerald-400 text-[8px] font-bold">+45,000</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Bottom Nav --}}
                        <div class="absolute bottom-0 left-0 right-0 flex items-center justify-around py-2 px-4 border-t border-white/5" style="background: rgba(15,23,42,0.9); backdrop-filter: blur(8px);">
                            <div class="flex flex-col items-center gap-0.5">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span class="text-[6px] text-emerald-400 font-medium">Home</span>
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <svg class="w-4 h-4 text-white/30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span class="text-[6px] text-white/30 font-medium">Sales</span>
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <svg class="w-4 h-4 text-white/30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"/></svg>
                                <span class="text-[6px] text-white/30 font-medium">Stock</span>
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <svg class="w-4 h-4 text-white/30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                <span class="text-[6px] text-white/30 font-medium">More</span>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Reflection / Glow --}}
                <div class="absolute -inset-4 rounded-[3rem] opacity-40 blur-2xl -z-10 phone-glow" style="background: linear-gradient(135deg, #10b981 0%, #3b82f6 50%, #8b5cf6 100%);"></div>
                {{-- Floating Orbs around phone --}}
                <div class="absolute -top-4 -right-6 w-16 h-16 rounded-full opacity-30 blur-xl floating-orb" style="background: #10b981;"></div>
                <div class="absolute -bottom-6 -left-8 w-20 h-20 rounded-full opacity-20 blur-xl floating-orb-delay" style="background: #3b82f6;"></div>
            </div>
        </div>

        {{-- Stats bar --}}
        <div class="hero-stats inline-flex flex-wrap justify-center gap-8 md:gap-12 py-6 px-8 rounded-2xl border border-white/10" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px);">
            <div class="text-center">
                <div class="text-2xl font-bold text-white">5,000+</div>
                <div class="text-xs text-blue-200/70 mt-0.5 font-medium uppercase tracking-wide">Active Businesses</div>
            </div>
            <div class="hidden sm:block w-px bg-white/10 self-stretch"></div>
            <div class="text-center">
                <div class="text-2xl font-bold text-white">99.9%</div>
                <div class="text-xs text-blue-200/70 mt-0.5 font-medium uppercase tracking-wide">Uptime SLA</div>
            </div>
            <div class="hidden sm:block w-px bg-white/10 self-stretch"></div>
            <div class="text-center">
                <div class="text-2xl font-bold text-white">2M+</div>
                <div class="text-xs text-blue-200/70 mt-0.5 font-medium uppercase tracking-wide">Transactions/Month</div>
            </div>
            <div class="hidden sm:block w-px bg-white/10 self-stretch"></div>
            <div class="text-center">
                <div class="text-2xl font-bold text-white">4.9 ★</div>
                <div class="text-xs text-blue-200/70 mt-0.5 font-medium uppercase tracking-wide">Customer Rating</div>
            </div>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-28 left-1/2 -translate-x-1/2 z-10 scroll-indicator">
        <div class="flex flex-col items-center gap-1.5 text-blue-200/50">
            <span class="text-xs font-medium tracking-widest uppercase">Scroll</span>
            <div class="w-5 h-8 rounded-full border border-blue-200/30 flex items-start justify-center p-1">
                <div class="w-1 h-2 bg-blue-200/50 rounded-full scroll-dot"></div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Grid lines */
    .hero-grid {
        background-image:
            linear-gradient(rgba(99,179,255,0.07) 1px, transparent 1px),
            linear-gradient(90deg, rgba(99,179,255,0.07) 1px, transparent 1px);
        background-size: 60px 60px;
        animation: gridMove 20s linear infinite;
    }

    @keyframes gridMove {
        0% { background-position: 0 0; }
        100% { background-position: 60px 60px; }
    }

    /* Dot pattern */
    .hero-dots {
        background-image: radial-gradient(rgba(147,197,253,0.15) 1px, transparent 1px);
        background-size: 30px 30px;
        animation: dotsMove 15s linear infinite;
    }

    @keyframes dotsMove {
        0% { background-position: 0 0; }
        100% { background-position: 30px 30px; }
    }

    /* Orb animations */
    .orb-1 { animation: orbFloat 8s ease-in-out infinite; }
    .orb-2 { animation: orbFloat 10s ease-in-out infinite reverse; }
    .orb-3 { animation: orbFloat 12s ease-in-out infinite 2s; }

    @keyframes orbFloat {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(20px, -20px) scale(1.05); }
        66% { transform: translate(-15px, 15px) scale(0.95); }
    }

    /* Flowing horizontal lines */
    .flow-line-h {
        animation: flowRight 6s linear infinite;
    }
    .flow-line-h.delay-1 { animation-delay: -1s; }
    .flow-line-h.delay-2 { animation-delay: -2s; }
    .flow-line-h.delay-3 { animation-delay: -3s; }
    .flow-line-h.delay-4 { animation-delay: -4s; }
    .flow-line-h.delay-5 { animation-delay: -5s; }

    @keyframes flowRight {
        0% { transform: translateX(-50%); opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { transform: translateX(50%); opacity: 0; }
    }

    /* Diagonal lines */
    .flow-line-d {
        animation: flowDiag 9s linear infinite;
    }
    .flow-line-d.delay-1 { animation-delay: -2.25s; }
    .flow-line-d.delay-2 { animation-delay: -4.5s; }
    .flow-line-d.delay-4 { animation-delay: -6.75s; }

    @keyframes flowDiag {
        0% { transform: translateX(-30%) translateY(-10%); opacity: 0; }
        15% { opacity: 0.6; }
        85% { opacity: 0.6; }
        100% { transform: translateX(30%) translateY(10%); opacity: 0; }
    }

    /* Wave paths */
    .wave-path {
        animation: waveMove 12s linear infinite;
    }
    .wave-path.delay-2 { animation-delay: -4s; }
    .wave-path.delay-4 { animation-delay: -8s; }

    @keyframes waveMove {
        0% { transform: translateX(-10%); }
        100% { transform: translateX(10%); }
    }

    /* Gradient text */
    .hero-gradient-text {
        background: linear-gradient(90deg, #93c5fd 0%, #a5b4fc 50%, #67e8f9 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Scroll dot animation */
    .scroll-dot {
        animation: scrollBounce 2s ease-in-out infinite;
    }

    @keyframes scrollBounce {
        0%, 100% { transform: translateY(0); opacity: 1; }
        50% { transform: translateY(12px); opacity: 0.3; }
    }

    /* Hero content entrance animations */
    .hero-badge {
        animation: fadeInDown 0.7s ease-out both;
    }
    .hero-title {
        animation: fadeInUp 0.7s ease-out 0.15s both;
    }
    .hero-subtitle {
        animation: fadeInUp 0.7s ease-out 0.3s both;
    }
    .hero-cta {
        animation: fadeInUp 0.7s ease-out 0.45s both;
    }
    .hero-stats {
        animation: fadeInUp 0.7s ease-out 0.6s both;
    }
    .scroll-indicator {
        animation: fadeIn 1s ease-out 1s both;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    /* Phone mockup animations */
    .hero-phone {
        animation: fadeInUp 0.8s ease-out 0.5s both;
    }
    .phone-device {
        animation: phoneFloat 6s ease-in-out infinite;
    }
    .phone-glow {
        animation: glowPulse 4s ease-in-out infinite;
    }
    .floating-orb {
        animation: orbFloatAround 8s ease-in-out infinite;
    }
    .floating-orb-delay {
        animation: orbFloatAround 10s ease-in-out infinite reverse;
    }

    @keyframes phoneFloat {
        0%, 100% { transform: translateY(0) rotateY(-5deg) rotateX(2deg); }
        50% { transform: translateY(-12px) rotateY(-5deg) rotateX(2deg); }
    }
    @keyframes glowPulse {
        0%, 100% { opacity: 0.35; transform: scale(1); }
        50% { opacity: 0.55; transform: scale(1.05); }
    }
    @keyframes orbFloatAround {
        0%, 100% { transform: translate(0, 0); }
        25% { transform: translate(10px, -15px); }
        50% { transform: translate(-5px, 5px); }
        75% { transform: translate(15px, 10px); }
    }

    /* Screen content shimmer */
    .phone-frame::after {
        content: '';
        position: absolute;
        top: 0; left: -100%;
        width: 50%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.03), transparent);
        animation: screenShimmer 5s ease-in-out infinite;
        pointer-events: none;
        z-index: 30;
        border-radius: 2.5rem;
    }
    @keyframes screenShimmer {
        0% { left: -100%; }
        50%, 100% { left: 200%; }
    }
</style>
