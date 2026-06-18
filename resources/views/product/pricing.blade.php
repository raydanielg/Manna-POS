@extends('layouts.page')

@section('title', 'Pricing Plans - ' . config('app.name', 'MannaPOS'))

@section('content')
<div class="py-12 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight sm:text-5xl mb-4">
                Simple, Transparent Pricing
            </h1>
            <p class="text-lg text-slate-600">
                Choose the plan that's right for your business. All plans include a 14-day free trial.
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

        {{-- Pricing Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 items-stretch mb-16">
            @foreach($plans as $plan)
                @php
                    $isFeatured = $plan->is_featured;
                    $badgeColor = $plan->badge_color ?? '#10B981';
                @endphp
                <div class="flex flex-col bg-white rounded-3xl transition-all duration-300 border relative {{ $isFeatured ? 'border-primary-500 shadow-xl scale-105 z-10 lg:-translate-y-2' : 'border-slate-200 shadow-sm hover:shadow-lg' }}">
                    
                    @if($isFeatured)
                        <div class="absolute -top-4 inset-x-0 flex justify-center">
                            <span class="inline-flex items-center px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-gradient-to-r from-primary-600 to-emerald-600 text-white shadow-md">
                                Most Popular
                            </span>
                        </div>
                    @endif

                    <div class="p-8 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-slate-950">{{ $plan->name }}</h3>
                                <p class="text-sm text-slate-500 mt-1 min-h-[40px]">{{ $plan->description }}</p>
                            </div>
                            <span class="w-3.5 h-3.5 rounded-full mt-2" style="background-color: {{ $badgeColor }}"></span>
                        </div>

                        {{-- Price displays --}}
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

        {{-- FAQ --}}
        <div class="bg-white rounded-3xl p-10 border border-slate-200 shadow-sm max-w-4xl mx-auto">
            <h2 class="text-3xl font-extrabold text-slate-950 mb-8 text-center">Frequently Asked Questions</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="font-bold text-slate-900 mb-2 text-lg">Can I change plans later?</h3>
                    <p class="text-slate-600 leading-relaxed">Yes, you can upgrade, downgrade or cancel your subscription plan at any time right from your dashboard settings. Subscriptions are billed pro-rata.</p>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 mb-2 text-lg">What payment methods do you accept?</h3>
                    <p class="text-slate-600 leading-relaxed">We accept all major payments including Mobile Money (M-Pesa, Tigo Pesa, Airtel Money, HaloPesa) via Selcom/TigoPesabusiness, Credit/Debit cards, and Bank transfers.</p>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 mb-2 text-lg">Is there a free trial?</h3>
                    <p class="text-slate-600 leading-relaxed">Absolutely! All paid plans come with a 14-day fully featured free trial. You do not need a credit card or payment setup to start testing MannaPOS.</p>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 mb-2 text-lg">Can I use MannaPOS offline?</h3>
                    <p class="text-slate-600 leading-relaxed">Yes, the mobile and tablet apps are built to allow caching. You can record sales and view products offline; sync happens automatically when connectivity is restored.</p>
                </div>
            </div>
        </div>
    </div>
</div>

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
@endsection
