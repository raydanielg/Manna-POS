<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account — MannaPOS</title>
    <link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#1e40af',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .step-panel { display: none; }
        .step-panel.active { display: block; }
        .social-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;
            padding: 0.55rem 0.5rem; border-radius: 10px; border: none; color: #fff;
            font-size: 0.78rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease;
            min-height: 40px;
        }
        .social-btn:hover { transform: translateY(-1px); opacity: 0.92; }
        .social-btn:active { transform: translateY(0); }
    </style>
</head>
<body class="h-full text-slate-900 font-sans antialiased">

<div class="min-h-screen flex flex-col lg:flex-row bg-slate-50">
    
    {{-- Left Pane: Benefits & Logo (Hidden on mobile/tablet) --}}
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12 xl:p-20 bg-slate-100/40 border-r border-slate-200 relative overflow-hidden">
        <div class="absolute inset-0 opacity-40" style="background-image: radial-gradient(circle at 1px 1px, #cbd5e1 1px, transparent 0); background-size: 24px 24px;"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-slate-50/60 via-transparent to-blue-50/40"></div>
        {{-- Header Logo --}}
        <div class="flex items-center space-x-3">
            <img src="{{ asset('icons8-dynamics-365-96.png') }}" alt="MannaPOS Logo" class="w-10 h-10 object-contain rounded-xl shadow-md shadow-blue-500/20">
            <span class="text-2xl font-extrabold tracking-tight text-slate-950">MannaPOS</span>
        </div>

        {{-- Registration Progress Tracker --}}
        <div class="max-w-md my-auto">
            <h2 class="mb-6 text-xl font-bold text-slate-900">Creating your account:</h2>
            <ul class="space-y-4" id="progress-list">
                {{-- Step 1 --}}
                <li class="flex items-center" id="track-personal">
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center mr-3 text-blue-600 icon-check">
                        <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center mr-3 text-slate-400 icon-pending hidden">
                        <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                    </div>
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center mr-3 text-blue-600 icon-spinner hidden">
                        <svg class="w-4 h-4 animate-spin" viewBox="0 0 100 101" fill="none"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" opacity="0.25"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/></svg>
                    </div>
                    <span class="text-sm font-medium text-slate-600 label-text">Enter your personal details</span>
                </li>

                {{-- Step 2 --}}
                <li class="flex items-center" id="track-password">
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center mr-3 text-blue-600 icon-check hidden">
                        <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center mr-3 text-slate-400 icon-pending">
                        <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                    </div>
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center mr-3 text-blue-600 icon-spinner hidden">
                        <svg class="w-4 h-4 animate-spin" viewBox="0 0 100 101" fill="none"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" opacity="0.25"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/></svg>
                    </div>
                    <span class="text-sm font-medium text-slate-600 label-text">Create a secure password</span>
                </li>

                {{-- Step 3 --}}
                <li class="flex items-center" id="track-business">
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center mr-3 text-blue-600 icon-check hidden">
                        <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center mr-3 text-slate-400 icon-pending">
                        <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                    </div>
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center mr-3 text-blue-600 icon-spinner hidden">
                        <svg class="w-4 h-4 animate-spin" viewBox="0 0 100 101" fill="none"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" opacity="0.25"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/></svg>
                    </div>
                    <span class="text-sm font-medium text-slate-600 label-text">Set up your business profile</span>
                </li>

                {{-- Step 4 --}}
                <li class="flex items-center" id="track-finalize">
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center mr-3 text-blue-600 icon-check hidden">
                        <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center mr-3 text-slate-400 icon-pending">
                        <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                    </div>
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center mr-3 text-blue-600 icon-spinner hidden">
                        <svg class="w-4 h-4 animate-spin" viewBox="0 0 100 101" fill="none"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" opacity="0.25"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/></svg>
                    </div>
                    <span class="text-sm font-medium text-slate-600 label-text">Creating your account...</span>
                </li>
            </ul>
        </div>

        {{-- Footer links --}}
        <div class="flex items-center space-x-6 text-sm font-medium text-slate-400">
            <a href="/about" class="hover:text-slate-600 transition-colors">About</a>
            <a href="/terms" class="hover:text-slate-600 transition-colors">Terms &amp; Conditions</a>
            <a href="/contact" class="hover:text-slate-600 transition-colors">Contact</a>
        </div>
    </div>

    {{-- Right Pane: Registration Form Card --}}
    <div class="flex-1 flex flex-col justify-center items-center p-6 sm:p-12 lg:p-20 bg-slate-50">
        
        {{-- Mobile Logo Header --}}
        <div class="flex lg:hidden items-center space-x-2.5 mb-8">
            <img src="{{ asset('icons8-dynamics-365-96.png') }}" alt="MannaPOS Logo" class="w-8 h-8 object-contain rounded-lg shadow-md">
            <span class="text-xl font-black text-slate-950">MannaPOS</span>
        </div>

        <div class="w-full max-w-[500px]">
            {{-- Form Card Container --}}
            <div class="bg-white rounded-lg border border-slate-200/80 shadow-lg p-8 sm:p-10">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight mb-2">Create your Free Account</h2>
                <p class="text-slate-500 text-sm mb-6">Enjoy a full-featured 14-day free trial. No credit card required.</p>

                {{-- Step Indicator Dots --}}
                <div class="flex items-center justify-center space-x-4 mb-6">
                    <div id="sd1" class="w-8 h-8 rounded-full border-2 border-blue-600 bg-blue-50 text-blue-700 flex items-center justify-center text-xs font-bold transition-all duration-300">1</div>
                    <div id="sline" class="h-0.5 w-12 bg-slate-200 transition-all duration-300"></div>
                    <div id="sd2" class="w-8 h-8 rounded-full border-2 border-slate-200 bg-slate-100 text-slate-400 flex items-center justify-center text-xs font-bold transition-all duration-300">2</div>
                </div>

                <form method="POST" action="{{ route('register') }}" id="regForm" novalidate>
                    @csrf

                    {{-- ── STEP 1: Personal / Account Details ── --}}
                    <div class="step-panel active" id="p1">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">First Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="first_name" id="fn" class="w-full px-4 py-2.5 border border-slate-200 focus:border-blue-500 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-slate-50/50" value="{{ old('first_name') }}" placeholder="John" autocomplete="given-name">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Last Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="last_name" id="ln" class="w-full px-4 py-2.5 border border-slate-200 focus:border-blue-500 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-slate-50/50" value="{{ old('last_name') }}" placeholder="Doe" autocomplete="family-name">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Phone Number <span class="text-rose-500">*</span></label>
                            <div class="flex -space-x-px shadow-sm rounded-xl" id="phone-wrapper">
                                {{-- Country Code Dropdown Toggle --}}
                                <button type="button" id="dropdown-phone-button" onclick="togglePhoneDropdown()" class="inline-flex items-center gap-1.5 shrink-0 z-10 text-slate-700 bg-slate-50 border border-slate-200 hover:bg-slate-100 hover:text-slate-900 font-semibold text-xs rounded-l-xl px-3 py-2.5 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all">
                                    <img id="current-flag" src="https://flagcdn.com/w40/tz.png" alt="flag" class="w-5 h-3.5 object-cover rounded-sm shadow-sm">
                                    <span id="current-code">+255</span>
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                {{-- Country Dropdown Menu --}}
                                <div id="dropdown-phone" class="hidden absolute z-20 bg-white border border-slate-200 rounded-xl shadow-xl w-64 mt-[42px] overflow-hidden">
                                    <ul class="p-1.5 text-sm text-slate-700 font-medium max-h-60 overflow-y-auto" id="phone-country-list">
                                        <li><button type="button" onclick="selectCountry('tz', '+255', 'https://flagcdn.com/w40/tz.png')" class="flex items-center w-full gap-2.5 px-3 py-2 hover:bg-slate-50 rounded-lg transition-colors"><img src="https://flagcdn.com/w40/tz.png" class="w-5 h-3.5 rounded-sm object-cover shadow-sm"><span>Tanzania</span><span class="ml-auto text-xs text-slate-400">+255</span></button></li>
                                        <li><button type="button" onclick="selectCountry('ke', '+254', 'https://flagcdn.com/w40/ke.png')" class="flex items-center w-full gap-2.5 px-3 py-2 hover:bg-slate-50 rounded-lg transition-colors"><img src="https://flagcdn.com/w40/ke.png" class="w-5 h-3.5 rounded-sm object-cover shadow-sm"><span>Kenya</span><span class="ml-auto text-xs text-slate-400">+254</span></button></li>
                                        <li><button type="button" onclick="selectCountry('ug', '+256', 'https://flagcdn.com/w40/ug.png')" class="flex items-center w-full gap-2.5 px-3 py-2 hover:bg-slate-50 rounded-lg transition-colors"><img src="https://flagcdn.com/w40/ug.png" class="w-5 h-3.5 rounded-sm object-cover shadow-sm"><span>Uganda</span><span class="ml-auto text-xs text-slate-400">+256</span></button></li>
                                        <li><button type="button" onclick="selectCountry('rw', '+250', 'https://flagcdn.com/w40/rw.png')" class="flex items-center w-full gap-2.5 px-3 py-2 hover:bg-slate-50 rounded-lg transition-colors"><img src="https://flagcdn.com/w40/rw.png" class="w-5 h-3.5 rounded-sm object-cover shadow-sm"><span>Rwanda</span><span class="ml-auto text-xs text-slate-400">+250</span></button></li>
                                        <li><button type="button" onclick="selectCountry('et', '+251', 'https://flagcdn.com/w40/et.png')" class="flex items-center w-full gap-2.5 px-3 py-2 hover:bg-slate-50 rounded-lg transition-colors"><img src="https://flagcdn.com/w40/et.png" class="w-5 h-3.5 rounded-sm object-cover shadow-sm"><span>Ethiopia</span><span class="ml-auto text-xs text-slate-400">+251</span></button></li>
                                        <li><button type="button" onclick="selectCountry('za', '+27', 'https://flagcdn.com/w40/za.png')" class="flex items-center w-full gap-2.5 px-3 py-2 hover:bg-slate-50 rounded-lg transition-colors"><img src="https://flagcdn.com/w40/za.png" class="w-5 h-3.5 rounded-sm object-cover shadow-sm"><span>South Africa</span><span class="ml-auto text-xs text-slate-400">+27</span></button></li>
                                        <li><button type="button" onclick="selectCountry('ng', '+234', 'https://flagcdn.com/w40/ng.png')" class="flex items-center w-full gap-2.5 px-3 py-2 hover:bg-slate-50 rounded-lg transition-colors"><img src="https://flagcdn.com/w40/ng.png" class="w-5 h-3.5 rounded-sm object-cover shadow-sm"><span>Nigeria</span><span class="ml-auto text-xs text-slate-400">+234</span></button></li>
                                        <li><button type="button" onclick="selectCountry('gh', '+233', 'https://flagcdn.com/w40/gh.png')" class="flex items-center w-full gap-2.5 px-3 py-2 hover:bg-slate-50 rounded-lg transition-colors"><img src="https://flagcdn.com/w40/gh.png" class="w-5 h-3.5 rounded-sm object-cover shadow-sm"><span>Ghana</span><span class="ml-auto text-xs text-slate-400">+233</span></button></li>
                                        <li><button type="button" onclick="selectCountry('us', '+1', 'https://flagcdn.com/w40/us.png')" class="flex items-center w-full gap-2.5 px-3 py-2 hover:bg-slate-50 rounded-lg transition-colors"><img src="https://flagcdn.com/w40/us.png" class="w-5 h-3.5 rounded-sm object-cover shadow-sm"><span>United States</span><span class="ml-auto text-xs text-slate-400">+1</span></button></li>
                                        <li><button type="button" onclick="selectCountry('gb', '+44', 'https://flagcdn.com/w40/gb.png')" class="flex items-center w-full gap-2.5 px-3 py-2 hover:bg-slate-50 rounded-lg transition-colors"><img src="https://flagcdn.com/w40/gb.png" class="w-5 h-3.5 rounded-sm object-cover shadow-sm"><span>United Kingdom</span><span class="ml-auto text-xs text-slate-400">+44</span></button></li>
                                        <li><button type="button" onclick="selectCountry('fr', '+33', 'https://flagcdn.com/w40/fr.png')" class="flex items-center w-full gap-2.5 px-3 py-2 hover:bg-slate-50 rounded-lg transition-colors"><img src="https://flagcdn.com/w40/fr.png" class="w-5 h-3.5 rounded-sm object-cover shadow-sm"><span>France</span><span class="ml-auto text-xs text-slate-400">+33</span></button></li>
                                        <li><button type="button" onclick="selectCountry('ca', '+1', 'https://flagcdn.com/w40/ca.png')" class="flex items-center w-full gap-2.5 px-3 py-2 hover:bg-slate-50 rounded-lg transition-colors"><img src="https://flagcdn.com/w40/ca.png" class="w-5 h-3.5 rounded-sm object-cover shadow-sm"><span>Canada</span><span class="ml-auto text-xs text-slate-400">+1</span></button></li>
                                    </ul>
                                </div>

                                {{-- Actual Phone Input --}}
                                <div class="relative w-full">
                                    <input type="tel" name="phone" id="ph" class="w-full pl-4 pr-4 py-2.5 border border-slate-200 focus:border-blue-500 rounded-r-xl text-sm focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-slate-50/50" value="{{ old('phone') }}" placeholder="712 345 678" autocomplete="tel">
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-400 font-semibold mt-1">Select your country code, then enter your phone number.</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" id="em" class="w-full px-4 py-2.5 border border-slate-200 focus:border-blue-500 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-slate-50/50" value="{{ old('email') }}" placeholder="name@company.com" autocomplete="email">
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Password <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input type="password" name="password" id="pw" class="w-full pl-4 pr-10 py-2.5 border border-slate-200 focus:border-blue-500 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-slate-50/50" placeholder="••••••••" autocomplete="new-password" oninput="checkStrength(this)">
                                    <button type="button" onclick="togglePasswordVisibility('pw', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" tabindex="-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="w-full bg-slate-150 h-1 rounded-full mt-2 overflow-hidden">
                                    <div id="strength-bar" class="h-full w-0 bg-rose-500 transition-all duration-300"></div>
                                </div>
                                <span id="strength-text" class="text-[10px] font-bold mt-1 block h-3"></span>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Confirm Password <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="pc" class="w-full pl-4 pr-10 py-2.5 border border-slate-200 focus:border-blue-500 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-slate-50/50" placeholder="••••••••" autocomplete="new-password">
                                    <button type="button" onclick="togglePasswordVisibility('pc', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" tabindex="-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Next Action Button --}}
                        <button type="button" onclick="goToStepTwo()" class="w-full py-3 px-6 bg-slate-900 hover:bg-slate-950 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                            <span>Continue</span>
                            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    {{-- ── STEP 2: Business setup ── --}}
                    <div class="step-panel" id="p2">
                        <div class="mb-4">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Business Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="business_name" id="bn" class="w-full px-4 py-2.5 border border-slate-200 focus:border-blue-500 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-slate-50/50" value="{{ old('business_name') }}" placeholder="e.g. Manna Supermarket">
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Business Type</label>
                            <select name="business_type" class="w-full px-4 py-2.5 border border-slate-200 focus:border-blue-500 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-slate-50/50 cursor-pointer">
                                <option value="">Select type</option>
                                <option value="retail" {{ old('business_type')=='retail'?'selected':'' }}>Retail Shop</option>
                                <option value="wholesale" {{ old('business_type')=='wholesale'?'selected':'' }}>Wholesale</option>
                                <option value="restaurant" {{ old('business_type')=='restaurant'?'selected':'' }}>Restaurant / Cafe</option>
                                <option value="supermarket" {{ old('business_type')=='supermarket'?'selected':'' }}>Supermarket</option>
                                <option value="pharmacy" {{ old('business_type')=='pharmacy'?'selected':'' }}>Pharmacy</option>
                                <option value="electronics" {{ old('business_type')=='electronics'?'selected':'' }}>Electronics</option>
                                <option value="services" {{ old('business_type')=='services'?'selected':'' }}>Services</option>
                                <option value="other" {{ old('business_type')=='other'?'selected':'' }}>Other</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Country <span class="text-rose-500">*</span></label>
                                <select name="business_country" class="w-full px-4 py-2.5 border border-slate-200 focus:border-blue-500 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-slate-50/50 cursor-pointer">
                                    <option value="">Select country</option>
                                    <option value="Tanzania" {{ old('business_country')=='Tanzania'?'selected':'' }}>Tanzania</option>
                                    <option value="Kenya" {{ old('business_country')=='Kenya'?'selected':'' }}>Kenya</option>
                                    <option value="Uganda" {{ old('business_country')=='Uganda'?'selected':'' }}>Uganda</option>
                                    <option value="Rwanda" {{ old('business_country')=='Rwanda'?'selected':'' }}>Rwanda</option>
                                    <option value="Ethiopia" {{ old('business_country')=='Ethiopia'?'selected':'' }}>Ethiopia</option>
                                    <option value="South Africa" {{ old('business_country')=='South Africa'?'selected':'' }}>South Africa</option>
                                    <option value="Nigeria" {{ old('business_country')=='Nigeria'?'selected':'' }}>Nigeria</option>
                                    <option value="Ghana" {{ old('business_country')=='Ghana'?'selected':'' }}>Ghana</option>
                                    <option value="Other" {{ old('business_country')=='Other'?'selected':'' }}>Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Currency <span class="text-rose-500">*</span></label>
                                <select name="currency" class="w-full px-4 py-2.5 border border-slate-200 focus:border-blue-500 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-slate-50/50 cursor-pointer">
                                    <option value="">Select currency</option>
                                    <option value="TZS" {{ old('currency')=='TZS'?'selected':'' }}>TZS — TSh</option>
                                    <option value="KES" {{ old('currency')=='KES'?'selected':'' }}>KES — KSh</option>
                                    <option value="UGX" {{ old('currency')=='UGX'?'selected':'' }}>UGX — USh</option>
                                    <option value="USD" {{ old('currency')=='USD'?'selected':'' }}>USD — $</option>
                                    <option value="EUR" {{ old('currency')=='EUR'?'selected':'' }}>EUR — €</option>
                                    <option value="GBP" {{ old('currency')=='GBP'?'selected':'' }}>GBP — £</option>
                                    <option value="ZAR" {{ old('currency')=='ZAR'?'selected':'' }}>ZAR — R</option>
                                    <option value="NGN" {{ old('currency')=='NGN'?'selected':'' }}>NGN — ₦</option>
                                    <option value="GHS" {{ old('currency')=='GHS'?'selected':'' }}>GHS — ₵</option>
                                </select>
                            </div>
                        </div>

                        {{-- Buttons Row --}}
                        <div class="flex items-center space-x-3">
                            <button type="button" onclick="goToStepOne()" class="w-1/3 py-3 border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-sm rounded-xl transition-all duration-150">
                                Back
                            </button>
                            <button type="submit" id="subBtn" class="flex-1 py-3 bg-slate-900 hover:bg-slate-950 text-white font-bold text-sm rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Create Account</span>
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Terms & Conditions Agreement text --}}
                <div class="mt-6 flex items-start space-x-2.5">
                    <input type="checkbox" id="terms-agree" checked class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 mt-0.5 cursor-pointer">
                    <label for="terms-agree" class="text-xs font-semibold text-slate-500 leading-relaxed cursor-pointer select-none">
                        By signing up, you are creating a MannaPOS account, and you agree to MannaPOS's <a href="/terms" class="text-blue-700 hover:underline font-bold">Terms of Use</a> and <a href="/privacy" class="text-blue-700 hover:underline font-bold">Privacy Policy</a>.
                    </label>
                </div>

                {{-- Account login link --}}
                <div class="mt-8 pt-6 border-t border-slate-100 text-center text-xs font-bold text-slate-500">
                    Already have an account? <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-extrabold ml-1">Sign in here</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ── Toast helper ──────────────────────────────────
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4500,
        timerProgressBar: true,
        customClass: { popup: 'toast-popup' },
    });
    function toast(icon, msg) {
        Toast.fire({ icon, title: msg });
    }

    // ── Step management ────────────────────────────────
    let currentStep = 1;
    function setStep(s) {
        currentStep = s;
        document.getElementById('p1').classList.toggle('active', s === 1);
        document.getElementById('p2').classList.toggle('active', s === 2);
        
        const d1 = document.getElementById('sd1');
        const d2 = document.getElementById('sd2');
        const sl = document.getElementById('sline');
        
        if (s === 1) {
            d1.className = 'w-8 h-8 rounded-full border-2 border-blue-600 bg-blue-50 text-blue-700 flex items-center justify-center text-xs font-bold transition-all duration-300';
            d1.textContent = '1';
            d2.className = 'w-8 h-8 rounded-full border-2 border-slate-200 bg-slate-100 text-slate-400 flex items-center justify-center text-xs font-bold transition-all duration-300';
            d2.textContent = '2';
            sl.className = 'h-0.5 w-12 bg-slate-200 transition-all duration-300';
        } else {
            d1.className = 'w-8 h-8 rounded-full border-2 border-blue-600 bg-blue-600 text-white flex items-center justify-center text-xs font-bold transition-all duration-300';
            d1.innerHTML = '&#10003;';
            d2.className = 'w-8 h-8 rounded-full border-2 border-blue-600 bg-blue-50 text-blue-700 flex items-center justify-center text-xs font-bold transition-all duration-300';
            d2.textContent = '2';
            sl.className = 'h-0.5 w-12 bg-blue-600 transition-all duration-300';
        }
    }

    // ── Phone Country Dropdown ────────────────────────
    function togglePhoneDropdown() {
        const dd = document.getElementById('dropdown-phone');
        dd.classList.toggle('hidden');
    }
    function selectCountry(code, dialCode, flagUrl) {
        document.getElementById('current-flag').src = flagUrl;
        document.getElementById('current-code').textContent = dialCode;
        document.getElementById('dropdown-phone').classList.add('hidden');
        // Prepend dial code to phone input if empty or starts with old code
        const phInput = document.getElementById('ph');
        const raw = phInput.value.trim().replace(/\D/g, '');
        if (!raw) {
            phInput.value = dialCode + ' ';
        }
        phInput.focus();
    }
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('phone-wrapper');
        const dd = document.getElementById('dropdown-phone');
        if (wrapper && !wrapper.contains(e.target)) {
            dd.classList.add('hidden');
        }
    });

    function goToStepOne() {
        setStep(1);
        syncTracker();
    }

    function goToStepTwo() {
        const fn = document.getElementById('fn').value.trim();
        const ln = document.getElementById('ln').value.trim();
        const ph = document.getElementById('ph').value.trim();
        const em = document.getElementById('em').value.trim();
        const pw = document.getElementById('pw').value;
        const pc = document.getElementById('pc').value;

        if (!fn || !ln) { toast('warning', 'Please enter your first and last name.'); document.getElementById('fn').focus(); return; }
        const dialCode = document.getElementById('current-code').textContent.trim();
        const fullPhone = dialCode + ' ' + ph;
        const digitsOnly = fullPhone.replace(/\D/g, '');
        if (!ph || digitsOnly.length < dialCode.replace(/\D/g, '').length + 6) { toast('warning', 'Please enter a valid phone number.'); document.getElementById('ph').focus(); return; }
        if (!em || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) { toast('warning', 'Please enter a valid email address.'); document.getElementById('em').focus(); return; }
        if (pw.length < 8) { toast('warning', 'Password must be at least 8 characters.'); document.getElementById('pw').focus(); return; }
        if (pw !== pc) { toast('error', 'Passwords do not match — please re-enter.'); document.getElementById('pc').focus(); return; }

        setStep(2);
        syncTracker();
        document.getElementById('bn').focus();
    }

    // ── Password strength ─────────────────────────────
    function checkStrength(el) {
        const v = el.value;
        const bar = document.getElementById('strength-bar');
        const text = document.getElementById('strength-text');
        
        let s = 0;
        if (v.length >= 8) s++; 
        if (/[A-Z]/.test(v)) s++; 
        if (/[0-9]/.test(v)) s++; 
        if (/[^A-Za-z0-9]/.test(v)) s++;
        
        const colors = ['bg-rose-500', 'bg-rose-500', 'bg-amber-500', 'bg-blue-500', 'bg-blue-500'];
        const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
        const textColors = ['text-rose-500', 'text-rose-500', 'text-amber-500', 'text-blue-600', 'text-blue-600'];
        
        bar.className = 'h-full transition-all duration-300 ' + (colors[s] || 'bg-rose-500');
        bar.style.width = v.length ? (s * 25) + '%' : '0%';
        
        text.textContent = v.length ? labels[s] || 'Strong' : '';
        text.className = 'text-[10px] font-bold mt-1 block h-3 ' + (textColors[s] || '');
    }

    // ── Toggle Password Visibility ─────────────────────
    function togglePasswordVisibility(id, btn) {
        const input = document.getElementById(id);
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.querySelector('svg').classList.toggle('text-blue-600', isPassword);
    }

    // ── Tracker State Helper ──────────────────────────
    function setTrackerState(trackId, state) {
        const el = document.getElementById(trackId);
        if (!el) return;
        el.querySelector('.icon-check').classList.toggle('hidden', state !== 'done');
        el.querySelector('.icon-pending').classList.toggle('hidden', state !== 'pending');
        el.querySelector('.icon-spinner').classList.toggle('hidden', state !== 'loading');
        el.querySelector('.label-text').className = 'text-sm font-medium transition-colors label-text ' + (state === 'done' ? 'text-blue-600' : (state === 'loading' ? 'text-blue-600' : 'text-slate-600'));
    }

    // Initial tracker sync
    function syncTracker() {
        const fn = document.getElementById('fn').value.trim();
        const ln = document.getElementById('ln').value.trim();
        const ph = document.getElementById('ph').value.trim();
        const em = document.getElementById('em').value.trim();
        const pw = document.getElementById('pw').value;
        const pc = document.getElementById('pc').value;
        const bn = document.getElementById('bn').value.trim();

        // Step 1: personal details
        if (fn && ln && ph && em) {
            setTrackerState('track-personal', 'done');
        } else {
            setTrackerState('track-personal', currentStep === 1 ? 'loading' : 'pending');
        }

        // Step 2: password
        if (pw.length >= 8 && pw === pc) {
            setTrackerState('track-password', 'done');
        } else if (pw || currentStep === 2) {
            setTrackerState('track-password', 'loading');
        } else {
            setTrackerState('track-password', 'pending');
        }

        // Step 3: business
        if (bn) {
            setTrackerState('track-business', 'done');
        } else if (currentStep === 2) {
            setTrackerState('track-business', 'loading');
        } else {
            setTrackerState('track-business', 'pending');
        }

        // Step 4: finalize
        setTrackerState('track-finalize', 'pending');
    }

    // Sync on input
    ['fn','ln','ph','em','pw','pc','bn'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', syncTracker);
    });
    document.querySelector('[name="business_country"]')?.addEventListener('change', syncTracker);

    // ── Submit logic (AJAX) ───────────────────────────
    document.getElementById('regForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const bn = document.getElementById('bn').value.trim();
        const country = document.querySelector('[name="business_country"]').value;
        const currency = document.querySelector('[name="currency"]').value;
        const agree = document.getElementById('terms-agree').checked;

        if (!bn) { toast('warning', 'Please enter your business name.'); document.getElementById('bn').focus(); return; }
        if (!country) { toast('warning', 'Please select your country.'); return; }
        if (!currency) { toast('warning', 'Please select your currency.'); return; }
        if (!agree) { toast('warning', 'You must agree to our Terms and Privacy Policy to continue.'); return; }

        const btn = document.getElementById('subBtn');
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span> Creating account…';

        // Update tracker to show finalizing
        setTrackerState('track-personal', 'done');
        setTrackerState('track-password', 'done');
        setTrackerState('track-business', 'done');
        setTrackerState('track-finalize', 'loading');

        const formData = new FormData(this);
        // prepend dial code to phone if needed
        const dialCode = document.getElementById('current-code').textContent.trim();
        const rawPhone = document.getElementById('ph').value.trim();
        if (!rawPhone.startsWith('+')) {
            formData.set('phone', dialCode + ' ' + rawPhone);
        }

        try {
            const resp = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                redirect: 'manual'
            });

            // Laravel usually redirects on success (302), or returns JSON
            if (resp.ok || resp.status === 302 || resp.type === 'opaqueredirect') {
                setTrackerState('track-finalize', 'done');
                toast('success', 'Account created successfully! Redirecting…');
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1200);
                return;
            }

            // Parse error response
            let data;
            try { data = await resp.json(); } catch (_) { data = null; }

            if (data && data.errors) {
                Object.values(data.errors).flat().forEach(msg => toast('error', msg));
            } else if (data && data.message) {
                toast('error', data.message);
            } else {
                toast('error', 'Something went wrong. Please try again.');
            }
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            syncTracker();
        } catch (err) {
            toast('error', 'Network error. Please check your connection and try again.');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            syncTracker();
        }
    });

    // ── Server-side errors → toasts ───────────────────
    @if($errors->any())
        @foreach($errors->all() as $err)
            toast('error', @json($err));
        @endforeach
    @endif

    // ── Jump to step 2 if step-2 field had errors ─────
    @if($errors->has('business_name') || $errors->has('business_country') || $errors->has('currency'))
        setStep(2);
    @endif

    syncTracker();
</script>
</body>
</html>
