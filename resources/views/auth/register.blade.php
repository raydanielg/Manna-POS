@extends('layouts.auth')

@section('form-header')
    <h2 class="auth-form-title">Register your business</h2>
    <p class="auth-form-subtitle">Start your journey with MannaPOS by registering your business details.</p>
@endsection

@section('form-content')
    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        <!-- Accordion Section 1: Business Details -->
        <div class="accordion-section active" data-section="1">
            <div class="accordion-header" onclick="toggleAccordion(1)">
                <div class="accordion-header-content">
                    <h3 class="accordion-title">Business Details</h3>
                    <p class="accordion-subtitle">Basic information about your business</p>
                </div>
                <div class="accordion-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <div class="accordion-content">

        <div class="form-group">
            <label for="business_name" class="form-label">Business Name *</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <input id="business_name" type="text" class="form-control @error('business_name') is-invalid @enderror" name="business_name" value="{{ old('business_name') }}" required placeholder="Business Name">
            </div>
            @error('business_name')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="start_date" class="form-label">Start Date</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <input id="start_date" type="date" class="form-control @error('start_date') is-invalid @enderror" name="start_date" value="{{ old('start_date') }}">
            </div>
            @error('start_date')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="currency" class="form-label">Currency *</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <select id="currency" class="form-control @error('currency') is-invalid @enderror" name="currency" required>
                    <option value="">Select Currency</option>
                    <option value="USD">USD - US Dollar</option>
                    <option value="TZS">TZS - Tanzanian Shilling</option>
                    <option value="KES">KES - Kenyan Shilling</option>
                    <option value="UGX">UGX - Ugandan Shilling</option>
                    <option value="EUR">EUR - Euro</option>
                    <option value="GBP">GBP - British Pound</option>
                </select>
            </div>
            @error('currency')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="business_logo" class="form-label">Upload Logo</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <input id="business_logo" type="file" class="form-control" name="business_logo" accept="image/*">
            </div>
        </div>

        <div class="form-group">
            <label for="website" class="form-label">Website</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                <input id="website" type="url" class="form-control @error('website') is-invalid @enderror" name="website" value="{{ old('website') }}" placeholder="Website">
            </div>
            @error('website')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone" class="form-label">Business contact number *</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required placeholder="Business contact number">
            </div>
            @error('phone')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="alternate_number" class="form-label">Alternate contact number</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <input id="alternate_number" type="tel" class="form-control @error('alternate_number') is-invalid @enderror" name="alternate_number" value="{{ old('alternate_number') }}" placeholder="Alternate contact number">
            </div>
            @error('alternate_number')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="country" class="form-label">Country *</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <input id="country" type="text" class="form-control @error('country') is-invalid @enderror" name="country" value="{{ old('country') }}" required placeholder="Country">
            </div>
            @error('country')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="state" class="form-label">State *</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <input id="state" type="text" class="form-control @error('state') is-invalid @enderror" name="state" value="{{ old('state') }}" required placeholder="State">
            </div>
            @error('state')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="city" class="form-label">City *</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ old('city') }}" required placeholder="City">
            </div>
            @error('city')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="zip_code" class="form-label">Zip Code *</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <input id="zip_code" type="text" class="form-control @error('zip_code') is-invalid @enderror" name="zip_code" value="{{ old('zip_code') }}" required placeholder="Zip/Postal Code">
            </div>
            @error('zip_code')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="landmark" class="form-label">Landmark *</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <input id="landmark" type="text" class="form-control @error('landmark') is-invalid @enderror" name="landmark" value="{{ old('landmark') }}" required placeholder="Landmark">
            </div>
            @error('landmark')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="timezone" class="form-label">Time zone *</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <select id="timezone" class="form-control @error('timezone') is-invalid @enderror" name="timezone" required>
                    <option value="">Time zone</option>
                    <option value="Africa/Dar_es_Salaam">Africa/Dar_es_Salaam</option>
                    <option value="Africa/Nairobi">Africa/Nairobi</option>
                    <option value="Africa/Kampala">Africa/Kampala</option>
                    <option value="Africa/Cairo">Africa/Cairo</option>
                    <option value="Africa/Johannesburg">Africa/Johannesburg</option>
                    <option value="Africa/Lagos">Africa/Lagos</option>
                    <option value="UTC">UTC</option>
                </select>
            </div>
            @error('timezone')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

            </div>
        </div>

        <!-- Accordion Section 2: Business Settings -->
        <div class="accordion-section" data-section="2">
            <div class="accordion-header" onclick="toggleAccordion(2)">
                <div class="accordion-header-content">
                    <h3 class="accordion-title">Business Settings</h3>
                    <p class="accordion-subtitle">Tax and financial settings</p>
                </div>
                <div class="accordion-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <div class="accordion-content">

        <div class="form-group">
            <label for="tax_label" class="form-label">Tax Label</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 14h.01M12 7h.01M9 10h.01M12 10h.01M15 10h.01M9 20h6"/></svg>
                <input id="tax_label" type="text" class="form-control @error('tax_label') is-invalid @enderror" name="tax_label" value="{{ old('tax_label') }}" placeholder="VAT / GST">
            </div>
            @error('tax_label')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="tax_number" class="form-label">Tax Number</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 14h.01M12 7h.01M9 10h.01M12 10h.01M15 10h.01M9 20h6"/></svg>
                <input id="tax_number" type="text" class="form-control @error('tax_number') is-invalid @enderror" name="tax_number" value="{{ old('tax_number') }}" placeholder="Tax Registration Number">
            </div>
            @error('tax_number')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

            </div>
        </div>

        <!-- Accordion Section 3: Owner Information -->
        <div class="accordion-section" data-section="3">
            <div class="accordion-header" onclick="toggleAccordion(3)">
                <div class="accordion-header-content">
                    <h3 class="accordion-title">Owner Information</h3>
                    <p class="accordion-subtitle">Your personal details</p>
                </div>
                <div class="accordion-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <div class="accordion-content">

        <div class="form-group">
            <label for="first_name" class="form-label">First Name *</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" placeholder="John">
            </div>
            @error('first_name')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="last_name" class="form-label">Last Name *</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" placeholder="Doe">
            </div>
            @error('last_name')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email Address *</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="name@company.com">
            </div>
            @error('email')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password *</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="••••••••">
            </div>
            @error('password')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm" class="form-label">Confirm Password *</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
            </div>
        </div>

            <div class="wizard-buttons">
                <button type="button" class="btn btn-secondary prev-btn">
                    <span class="btn-text">Previous</span>
                </button>
                <button type="submit" class="btn btn-primary submit-btn">
                    <span class="btn-text">Complete Registration</span>
                </button>
            </div>
        </div>
    </form>

    <div class="auth-footer">
        <span>Already have an account?</span>
        <a href="{{ route('login') }}" class="auth-footer-link">Sign in</a>
    </div>

    <script>
        // Re-attach link listeners for AJAX navigation
        document.addEventListener('DOMContentLoaded', function() {
            const authFooterLink = document.querySelector('.auth-footer-link');
            if (authFooterLink) {
                authFooterLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    if (url && url !== '#' && typeof navigateToPage === 'function') {
                        navigateToPage(url);
                    } else if (url) {
                        window.location.href = url;
                    }
                });
            }
        });
    </script>

    <style>
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e1e1e;
            margin: 1.5rem 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }
        .section-title:first-of-type {
            margin-top: 0;
        }

        .wizard-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }

        .step.active {
            opacity: 1;
        }

        .step.completed {
            opacity: 1;
        }

        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .step.active .step-number {
            background: #10B981;
            color: white;
        }

        .step.completed .step-number {
            background: #10B981;
            color: white;
        }

        .step-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: #6b7280;
            text-align: center;
        }

        .step.active .step-label {
            color: #10B981;
        }

        .wizard-step {
            display: none;
            opacity: 0;
            transform: translateX(20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .wizard-step.active {
            display: block;
            opacity: 1;
            transform: translateX(0);
        }

        .wizard-step.fade-in {
            animation: fadeInUp 0.4s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .wizard-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .wizard-buttons .btn {
            flex: 1;
        }

        .next-btn {
            background: #10B981;
            color: white;
            border: none;
            box-shadow: none;
        }

        .next-btn:hover {
            background: #059669;
            box-shadow: none;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
            border: none;
            box-shadow: none;
        }

        .btn-secondary:hover {
            background: #d1d5db;
            box-shadow: none;
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 8px;
            padding: 16px 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 9999;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            min-width: 300px;
            max-width: 400px;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }

        .toast-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
        }

        .toast-success .toast-icon {
            background: #10B981;
            color: white;
        }

        .toast-error .toast-icon {
            background: #EF4444;
            color: white;
        }

        .toast-info .toast-icon {
            background: #3B82F6;
            color: white;
        }

        .toast-message {
            flex: 1;
            color: #1e1e1e;
            font-size: 14px;
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            color: #6b7280;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: background 0.2s ease;
        }

        .toast-close:hover {
            background: #f3f4f6;
            color: #1e1e1e;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const registerForm = document.getElementById('registerForm');
            const steps = document.querySelectorAll('.wizard-step');
            const stepIndicators = document.querySelectorAll('.step');
            const nextBtns = document.querySelectorAll('.next-btn');
            const prevBtns = document.querySelectorAll('.prev-btn');
            let currentStep = 1;
            const totalSteps = 3;

            // Load saved form data from localStorage
            function loadFormData() {
                const savedData = localStorage.getItem('registerFormData');
                if (savedData) {
                    const data = JSON.parse(savedData);
                    Object.keys(data).forEach(key => {
                        const input = registerForm.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.value = data[key];
                        }
                    });
                    
                    // Restore current step
                    if (data.currentStep) {
                        currentStep = data.currentStep;
                        updateStepUI(currentStep);
                    }
                }
            }

            // Save form data to localStorage
            function saveFormData() {
                const formData = {};
                registerForm.querySelectorAll('input, select').forEach(input => {
                    if (input.name) {
                        formData[input.name] = input.value;
                    }
                });
                formData.currentStep = currentStep;
                localStorage.setItem('registerFormData', JSON.stringify(formData));
            }

            // Clear saved form data
            function clearFormData() {
                localStorage.removeItem('registerFormData');
            }

            // Load saved data on page load
            loadFormData();

            // Save data on input change
            registerForm.addEventListener('input', saveFormData);
            registerForm.addEventListener('change', saveFormData);

            function updateStepUI(step) {
                const currentStepEl = document.querySelector(`.wizard-step[data-step="${currentStep}"]`);
                const nextStepEl = document.querySelector(`.wizard-step[data-step="${step}"]`);

                // Fade out current step
                if (currentStepEl) {
                    currentStepEl.style.opacity = '0';
                    currentStepEl.style.transform = 'translateX(-20px)';
                }

                setTimeout(() => {
                    // Hide current step
                    if (currentStepEl) {
                        currentStepEl.classList.remove('active');
                    }

                    // Show next step with animation
                    if (nextStepEl) {
                        nextStepEl.classList.add('active');
                        nextStepEl.classList.add('fade-in');
                        setTimeout(() => {
                            nextStepEl.classList.remove('fade-in');
                        }, 400);
                    }

                    // Update step indicators
                    stepIndicators.forEach(indicator => {
                        const indicatorStep = parseInt(indicator.dataset.step);
                        indicator.classList.remove('active', 'completed');
                        if (indicatorStep === step) {
                            indicator.classList.add('active');
                        } else if (indicatorStep < step) {
                            indicator.classList.add('completed');
                        }
                    });
                }, 200);
            }

            function validateStep(step) {
                const currentStepEl = document.querySelector(`.wizard-step[data-step="${step}"]`);
                const requiredFields = currentStepEl.querySelectorAll('input[required], select[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                return isValid;
            }

            nextBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    if (validateStep(currentStep) && currentStep < totalSteps) {
                        currentStep++;
                        updateStepUI(currentStep);
                    }
                });
            });

            prevBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    if (currentStep > 1) {
                        currentStep--;
                        updateStepUI(currentStep);
                    }
                });
            });

            registerForm.addEventListener('submit', function(e) {
                const btn = this.querySelector('.submit-btn');
                const btnText = btn.querySelector('.btn-text');
                btn.classList.add('loading');
                btnText.textContent = 'Registering...';
                
                // Clear saved data on successful submit
                clearFormData();
            });

            // Toast notification system
            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `toast toast-${type}`;
                toast.innerHTML = `
                    <div class="toast-icon">
                        ${type === 'success' ? '✓' : type === 'error' ? '✕' : 'ℹ'}
                    </div>
                    <div class="toast-message">${message}</div>
                    <button class="toast-close">&times;</button>
                `;
                
                document.body.appendChild(toast);
                
                // Animate in
                setTimeout(() => toast.classList.add('show'), 10);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                }, 5000);
                
                // Close on click
                toast.querySelector('.toast-close').addEventListener('click', () => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                });
            }

            // Expose showToast globally
            window.showToast = showToast;
        });
    </script>
@endsection
