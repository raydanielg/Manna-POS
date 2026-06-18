@extends('layouts.page')

@section('title', 'Privacy Policy — ' . config('app.name', 'MannaPOS'))

@push('styles')
<style>
    .legal-page {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        min-height: 100vh;
        padding: 3rem 1rem 4rem;
    }
    .a4-document {
        max-width: 210mm;
        margin: 0 auto;
        background: #ffffff;
        padding: 30mm 25mm;
        box-shadow: 0 4px 60px rgba(15,23,42,0.08), 0 1px 3px rgba(15,23,42,0.06);
        border-radius: 4px;
        position: relative;
    }
    .a4-document::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 5px;
        background: linear-gradient(90deg, #0a192f, #0d2d6b, #0a3d8f, #1565c0);
        border-radius: 4px 4px 0 0;
    }
    .legal-header {
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 1.5rem;
        margin-bottom: 2.5rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .legal-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
        margin: 0;
    }
    .legal-subtitle {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
        margin-top: 0.35rem;
    }
    .legal-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #eff6ff;
        color: #2563eb;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        border: 1px solid #dbeafe;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .legal-section {
        margin-bottom: 2rem;
        scroll-margin-top: 80px;
    }
    .legal-section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.85rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .legal-section-title::before {
        content: '';
        width: 4px;
        height: 20px;
        background: linear-gradient(180deg, #2563eb, #1d4ed8);
        border-radius: 2px;
        flex-shrink: 0;
    }
    .legal-content {
        font-size: 0.88rem;
        color: #334155;
        line-height: 1.75;
    }
    .legal-content p {
        margin-bottom: 0.9rem;
    }
    .legal-content ul {
        margin: 0.5rem 0 0.9rem 1.5rem;
    }
    .legal-content li {
        margin-bottom: 0.4rem;
    }
    .legal-highlight {
        font-weight: 700;
        color: #1d4ed8;
    }
    .legal-footer {
        margin-top: 3.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
        font-size: 0.78rem;
        color: #94a3b8;
        text-align: center;
    }
    .legal-back {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-bottom: 1.5rem;
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.15s;
    }
    .legal-back:hover { color: #2563eb; }
    @media (max-width: 768px) {
        .legal-page { padding: 1.5rem 0.5rem 2rem; }
        .a4-document { padding: 20mm 12mm; margin: 0 4px; }
        .legal-title { font-size: 1.4rem; }
    }
</style>
@endpush

@section('content')
<div class="legal-page">
    <div class="a4-document">
        <a href="/" class="legal-back">&larr; Back to Home</a>

        <div class="legal-header">
            <div>
                <h1 class="legal-title">Privacy Policy</h1>
                <p class="legal-subtitle">Last Updated: June 19, 2026</p>
            </div>
            <span class="legal-badge">Legal Document</span>
        </div>

        <div class="legal-section" id="sec-1">
            <h2 class="legal-section-title">1. Introduction</h2>
            <div class="legal-content">
                <p>Welcome to MannaPOS ("we," "our," or "us"). This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our Point of Sale (POS) software and services. Please read this Privacy Policy carefully. If you do not agree with the terms of this Privacy Policy, please do not access the site.</p>
                <p>We respect your privacy and are committed to protecting your personal data. This privacy policy will inform you as to how we look after your personal data when you visit our website and inform you of your privacy rights and how the law protects you.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-2">
            <h2 class="legal-section-title">2. Information We Collect</h2>
            <div class="legal-content">
                <p>We may collect information about you in a variety of ways. The information we may collect on the Site includes:</p>
                <ul>
                    <li><span class="legal-highlight">Personal Data:</span> Personally identifiable information, such as your name, shipping address, email address, and telephone number, and demographic information, such as your age, gender, hometown, and interests, that you voluntarily give to us when you register with the Site or when you choose to participate in various activities related to the Site.</li>
                    <li><span class="legal-highlight">Derivative Data:</span> Information our servers automatically collect when you access the Site, such as your IP address, browser type, operating system, access times, the page you were viewing before coming to our site, and the pages you access on our Site.</li>
                    <li><span class="legal-highlight">Financial Data:</span> Financial information, such as data related to your payment method (e.g., valid credit card number, card brand, expiration date) that we may collect when you purchase, order, return, exchange, or request information about our services from the Site.</li>
                </ul>
            </div>
        </div>

        <div class="legal-section" id="sec-3">
            <h2 class="legal-section-title">3. How We Use Your Information</h2>
            <div class="legal-content">
                <p>We use the information we collect in the following ways:</p>
                <ul>
                    <li>To provide, maintain, and improve our services</li>
                    <li>To process transactions and send related information</li>
                    <li>To send technical notices and support messages</li>
                    <li>To respond to comments and questions and provide customer service</li>
                    <li>To communicate about products, services, and events</li>
                    <li>To monitor and analyze trends, usage, and activities</li>
                    <li>To detect, prevent, and address technical issues and fraud</li>
                    <li>To comply with legal obligations and enforce our terms</li>
                </ul>
            </div>
        </div>

        <div class="legal-section" id="sec-4">
            <h2 class="legal-section-title">4. Information Sharing</h2>
            <div class="legal-content">
                <p>We may share your personal information in the following situations:</p>
                <ul>
                    <li><span class="legal-highlight">With Service Providers:</span> We may share your information with third-party service providers who perform services on our behalf, such as payment processing, data analysis, email delivery, hosting services, and customer service.</li>
                    <li><span class="legal-highlight">For Business Transfers:</span> We may share or transfer your information in connection with a merger, sale of company assets, financing, or acquisition of all or a portion of our business to another company.</li>
                    <li><span class="legal-highlight">With Affiliates:</span> We may share your information with our affiliates, in which case we will require those affiliates to honor this Privacy Policy.</li>
                    <li><span class="legal-highlight">With Your Consent:</span> We may share your personal information for any other purpose with your consent.</li>
                </ul>
            </div>
        </div>

        <div class="legal-section" id="sec-5">
            <h2 class="legal-section-title">5. Data Security</h2>
            <div class="legal-content">
                <p>We have implemented appropriate technical and organizational security measures designed to protect the security of any personal information we process. However, despite our efforts to protect your personal information, no transmission of information over the Internet or electronic storage is 100% secure, and we cannot guarantee or warrant the absolute security of your information.</p>
                <p>We use encryption, secure protocols, and access controls to protect your data. Regular security audits and updates are performed to ensure the highest level of protection.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-6">
            <h2 class="legal-section-title">6. Your Privacy Rights</h2>
            <div class="legal-content">
                <p>Depending on your location, you may have the following rights regarding your personal information:</p>
                <ul>
                    <li><span class="legal-highlight">Access:</span> Request access to your personal information</li>
                    <li><span class="legal-highlight">Correction:</span> Request correction of inaccurate personal information</li>
                    <li><span class="legal-highlight">Deletion:</span> Request deletion of your personal information</li>
                    <li><span class="legal-highlight">Portability:</span> Request transfer of your personal information</li>
                    <li><span class="legal-highlight">Objection:</span> Object to processing of your personal information</li>
                    <li><span class="legal-highlight">Restriction:</span> Request restriction of processing your personal information</li>
                </ul>
                <p>To exercise these rights, please contact us at <a href="mailto:privacy@mannapos.com" class="legal-highlight">privacy@mannapos.com</a>.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-7">
            <h2 class="legal-section-title">7. Cookies and Tracking</h2>
            <div class="legal-content">
                <p>We use cookies and similar tracking technologies to track activity on our Site and hold certain information. Cookies are files with a small amount of data which may include an anonymous unique identifier. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.</p>
                <p>We use cookies for the following purposes: to enable certain functions of the Site, to provide analytics, to store your preferences, to enable advertisements delivery, and to analyze usage patterns.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-8">
            <h2 class="legal-section-title">8. Children's Privacy</h2>
            <div class="legal-content">
                <p>Our Site is not intended for children under 13 years of age. We do not knowingly collect personally identifiable information from children under 13. If you are a parent or guardian and you believe your child has provided us with personal information, please contact us, and we will delete such information.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-9">
            <h2 class="legal-section-title">9. International Data Transfers</h2>
            <div class="legal-content">
                <p>Your information may be transferred to and maintained on computers located outside of your state, province, country, or other governmental jurisdiction where data protection laws may differ. Your information will be handled in accordance with this Privacy Policy.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-10">
            <h2 class="legal-section-title">10. Changes to This Privacy Policy</h2>
            <div class="legal-content">
                <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date. You are advised to review this Privacy Policy periodically for any changes.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-11">
            <h2 class="legal-section-title">11. Contact Us</h2>
            <div class="legal-content">
                <p>If you have any questions about this Privacy Policy, please contact us:</p>
                <p><strong class="legal-highlight">Email:</strong> privacy@mannapos.com<br>
                <strong class="legal-highlight">Address:</strong> MannaPOS Inc., 123 Business Avenue, Suite 100, Dar es Salaam, Tanzania<br>
                <strong class="legal-highlight">Phone:</strong> +255 123 456 789</p>
            </div>
        </div>

        <div class="legal-footer">
            <p>&copy; {{ date('Y') }} MannaPOS. All rights reserved. This Privacy Policy is part of our <a href="/terms" class="legal-highlight">Terms of Service</a>.</p>
        </div>
    </div>
</div>
@endsection
