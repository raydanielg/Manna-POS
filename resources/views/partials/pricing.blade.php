@php
    $plans = $plans ?? \App\Models\SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
@endphp

<section id="pricing" class="py-24 bg-slate-50 relative overflow-hidden">
    {{-- Decorative Background Elements --}}
    <div class="absolute inset-0 pointer-events-none opacity-40">
        <div class="absolute top-1/4 left-1/10 w-96 h-96 bg-primary-100 rounded-full mix-blend-multiply filter blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/10 w-96 h-96 bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Section Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="px-4 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider text-emerald-700 bg-emerald-100/80 inline-block mb-3">Simple & Honest Pricing</span>
            <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight sm:text-5xl mb-4">
                Designed to Scale with <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-emerald-600">Your Business</span>
            </h2>
            <p class="text-lg text-slate-600">
                Choose a plan that fits your current needs and upgrade anytime as your business grows. No hidden fees. All plans include a 14-day free trial.
            </p>

            {{-- Billing Period Toggle --}}
            <div class="mt-8 flex justify-center items-center">
                <span class="text-sm font-semibold text-slate-700" id="monthly-label">Monthly</span>
                <button type="button" id="billing-toggle" class="mx-3 relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-slate-200 transition-colors duration-200 ease-in-out focus:outline-none" role="switch" aria-checked="false">
                    <span id="billing-toggle-knob" aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0"></span>
                </button>
                <span class="text-sm font-semibold text-slate-700 flex items-center" id="yearly-label">
                    Yearly
                    <span class="ml-1.5 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-500 text-white animate-pulse">
                        Save ~17%
                    </span>
                </span>
            </div>
        </div>

        {{-- Pricing Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 items-stretch pricing-grid" id="pricingGrid">
            @foreach($plans as $plan)
                @php
                    $isFeatured = $plan->is_featured;
                    $badgeColor = $plan->badge_color ?? '#10B981';
                @endphp
                <div class="flex flex-col bg-white rounded-3xl transition-all duration-300 border relative {{ $isFeatured ? 'border-primary-500 shadow-xl scale-105 z-10 lg:-translate-y-2' : 'border-slate-200 shadow-sm hover:shadow-lg' }} {{ $plan->price_monthly == 0 ? 'pricing-card-free' : '' }}">
                    
                    {{-- Highlight banner for Featured Plan --}}
                    @if($isFeatured)
                        <div class="absolute -top-4 inset-x-0 flex justify-center">
                            <span class="inline-flex items-center px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-gradient-to-r from-primary-600 to-emerald-600 text-white shadow-md">
                                Most Popular
                            </span>
                        </div>
                    @endif

                    <div class="p-8 flex-1 flex flex-col">
                        {{-- Plan Header --}}
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-slate-950">{{ $plan->name }}</h3>
                                <p class="text-sm text-slate-500 mt-1 min-h-[40px]">{{ $plan->description }}</p>
                            </div>
                            <span class="w-3.5 h-3.5 rounded-full mt-2" style="background-color: {{ $badgeColor }}"></span>
                        </div>

                        {{-- Pricing Section --}}
                        <div class="my-6 min-h-[80px]">
                            <div class="monthly-price-display">
                                @if($plan->price_monthly == 0)
                                    <span class="text-5xl font-black text-slate-900">Free</span>
                                @else
                                    <span class="text-sm font-bold text-slate-500 align-super">TSh</span>
                                    <span class="text-5xl font-black text-slate-900 tracking-tight">{{ number_format($plan->price_monthly, 0) }}</span>
                                    <span class="text-sm font-semibold text-slate-500">/mo</span>
                                @endif
                            </div>
                            <div class="yearly-price-display hidden">
                                @if($plan->price_yearly == 0)
                                    <span class="text-5xl font-black text-slate-900">Free</span>
                                @else
                                    <span class="text-sm font-bold text-slate-500 align-super">TSh</span>
                                    <span class="text-5xl font-black text-slate-900 tracking-tight">{{ number_format($plan->price_yearly, 0) }}</span>
                                    <span class="text-sm font-semibold text-slate-500">/yr</span>
                                @endif
                                @if($plan->price_monthly > 0 && $plan->price_yearly > 0)
                                    @php
                                        $monthlyEquivalent = $plan->price_yearly / 12;
                                        $savings = ($plan->price_monthly * 12) - $plan->price_yearly;
                                    @endphp
                                    <p class="text-xs text-emerald-600 font-bold mt-1.5">
                                        Equivalent to TSh {{ number_format($monthlyEquivalent, 0) }}/mo (Save TSh {{ number_format($savings, 0) }})
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="border-t border-slate-100 my-4"></div>

                        {{-- Core limits --}}
                        <div class="space-y-2 mb-6">
                            <div class="flex items-center justify-between text-sm text-slate-700">
                                <span class="font-medium">Locations Included:</span>
                                <span class="font-bold text-slate-900">{{ $plan->max_locations >= 999999 ? 'Unlimited' : $plan->max_locations }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm text-slate-700">
                                <span class="font-medium">Products Capacity:</span>
                                <span class="font-bold text-slate-900">{{ $plan->max_products >= 999999 ? 'Unlimited' : number_format($plan->max_products) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm text-slate-700">
                                <span class="font-medium">Staff Accounts:</span>
                                <span class="font-bold text-slate-900">{{ $plan->max_users >= 999999 ? 'Unlimited' : $plan->max_users }}</span>
                            </div>
                        </div>

                        {{-- Features List --}}
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Key Features Included</p>
                        <ul class="space-y-3.5 flex-1 mb-8">
                            @if(is_array($plan->features))
                                @foreach($plan->features as $feature)
                                    <li class="flex items-start text-sm text-slate-600">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <span class="ml-2.5 font-medium">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            @endif
                        </ul>

                        {{-- Action Button --}}
                        <a href="{{ route('register', ['plan' => $plan->slug]) }}" class="block w-full py-4 px-6 rounded-2xl text-center font-bold text-sm transition-all duration-200 {{ $isFeatured ? 'bg-gradient-to-r from-primary-600 to-emerald-600 text-white shadow-lg hover:shadow-xl hover:opacity-95' : 'bg-slate-100 text-slate-800 hover:bg-slate-200 hover:text-slate-900' }}">
                            @if($plan->price_monthly == 0)
                                Get Started Free
                            @else
                                Start 14-Day Free Trial
                            @endif
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Billing period toggle script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('billing-toggle');
        const knob = document.getElementById('billing-toggle-knob');
        const monthlyDisplays = document.querySelectorAll('.monthly-price-display');
        const yearlyDisplays = document.querySelectorAll('.yearly-price-display');
        const monthlyLabel = document.getElementById('monthly-label');
        const yearlyLabel = document.getElementById('yearly-label');
        
        let isYearly = false;

        function updateBilling() {
            if (isYearly) {
                knob.style.transform = 'translateX(1.25rem)';
                toggleBtn.classList.remove('bg-slate-200');
                toggleBtn.classList.add('bg-emerald-500');
                monthlyDisplays.forEach(el => el.classList.add('hidden'));
                yearlyDisplays.forEach(el => el.classList.remove('hidden'));
                yearlyLabel.classList.add('text-emerald-600');
                monthlyLabel.classList.remove('text-emerald-600');
            } else {
                knob.style.transform = 'translateX(0)';
                toggleBtn.classList.add('bg-slate-200');
                toggleBtn.classList.remove('bg-emerald-500');
                yearlyDisplays.forEach(el => el.classList.add('hidden'));
                monthlyDisplays.forEach(el => el.classList.remove('hidden'));
                monthlyLabel.classList.add('text-slate-900');
                yearlyLabel.classList.remove('text-emerald-600');
            }
        }

        toggleBtn && toggleBtn.addEventListener('click', function () {
            isYearly = !isYearly;
            updateBilling();
        });

        // Initialize state
        updateBilling();
    });
</script>

<style>
    /* Mobile Pricing Responsive */
    @media (max-width: 768px) {
        #pricing {
            padding-top: 3rem;
            padding-bottom: 3rem;
        }
        #pricing .max-w-7xl {
            padding-left: 0;
            padding-right: 0;
        }
        #pricing h2 {
            font-size: 1.75rem;
        }
        #pricing p.text-lg {
            font-size: 0.9rem;
        }

        /* Horizontal scroll single row */
        .pricing-grid {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 0.75rem;
            padding: 0 1rem 1.5rem;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }
        .pricing-grid > div {
            min-width: 260px;
            max-width: 82vw;
            flex-shrink: 0;
            scroll-snap-align: center;
            border-radius: 1.25rem;
        }

        /* Free tier: compact, shown first */
        .pricing-grid .pricing-card-free {
            min-width: 220px;
            max-width: 78vw;
            order: -1;
        }
        .pricing-grid .pricing-card-free .p-8 {
            padding: 1rem;
        }

        /* Compact card internals */
        .pricing-grid .p-8 {
            padding: 1.25rem;
        }
        .pricing-grid h3 {
            font-size: 1.1rem;
        }
        .pricing-grid p.text-sm {
            font-size: 0.7rem;
            min-height: auto;
        }
        .pricing-grid .text-5xl {
            font-size: 1.75rem;
        }
        .pricing-grid .my-6 {
            margin-top: 0.75rem;
            margin-bottom: 0.75rem;
            min-height: auto;
        }
        .pricing-grid .space-y-2 > div {
            font-size: 0.72rem;
        }
        .pricing-grid ul.space-y-3\.5 {
            margin-bottom: 1rem;
        }
        .pricing-grid ul.space-y-3\.5 li {
            font-size: 0.72rem;
        }
        .pricing-grid ul.space-y-3\.5 li svg {
            width: 16px;
            height: 16px;
        }
        .pricing-grid a.block.w-full {
            padding: 0.7rem 1rem;
            font-size: 0.78rem;
            border-radius: 0.75rem;
        }
        .pricing-grid .absolute.-top-4 {
            top: -0.75rem;
        }
        .pricing-grid .absolute.-top-4 span {
            padding: 0.25rem 0.75rem;
            font-size: 0.6rem;
        }
    }
</style>
