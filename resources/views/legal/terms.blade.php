@extends('layouts.page')

@section('title', 'Terms of Service — ' . config('app.name', 'MannaPOS'))

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
                <h1 class="legal-title">Terms of Service</h1>
                <p class="legal-subtitle">Last Updated: June 19, 2026</p>
            </div>
            <span class="legal-badge">Legal Document</span>
        </div>

        <div class="legal-section" id="sec-1">
            <h2 class="legal-section-title">1. Acceptance of Terms</h2>
            <div class="legal-content">
                <p>By accessing and using MannaPOS ("the Service"), you accept and agree to be bound by the terms and provisions of this agreement. If you do not agree to abide by these terms, please do not use this Service.</p>
                <p>MannaPOS reserves the right to modify these terms at any time. Your continued use of the Service following any such modification constitutes your acceptance of the modified terms.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-2">
            <h2 class="legal-section-title">2. Description of Service</h2>
            <div class="legal-content">
                <p>MannaPOS is a Point of Sale (POS) software solution designed to help businesses manage sales, inventory, customers, and analytics. The Service includes but is not limited to:</p>
                <ul>
                    <li>Inventory management and tracking</li>
                    <li>Payment processing and transaction management</li>
                    <li>Customer relationship management</li>
                    <li>Sales analytics and reporting</li>
                    <li>Multi-location support</li>
                    <li>Integration with third-party services</li>
                </ul>
            </div>
        </div>

        <div class="legal-section" id="sec-3">
            <h2 class="legal-section-title">3. User Accounts</h2>
            <div class="legal-content">
                <p>To use certain features of the Service, you must register for an account. You agree to provide accurate, current, and complete information during registration and to update such information to keep it accurate, current, and complete.</p>
                <p>You are responsible for safeguarding the password that you use to access the Service and for any activities or actions under your password. You agree not to disclose your password to any third party. You must notify us immediately upon becoming aware of any breach of security or unauthorized use of your account.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-4">
            <h2 class="legal-section-title">4. User Responsibilities</h2>
            <div class="legal-content">
                <p>As a user of the Service, you agree to:</p>
                <ul>
                    <li>Use the Service only for lawful purposes and in accordance with these Terms</li>
                    <li>Not use the Service to transmit any viruses, malware, or harmful code</li>
                    <li>Not attempt to gain unauthorized access to the Service or related systems</li>
                    <li>Not interfere with or disrupt the Service or servers connected to the Service</li>
                    <li>Comply with all applicable laws and regulations</li>
                    <li>Maintain the confidentiality of your account credentials</li>
                </ul>
            </div>
        </div>

        <div class="legal-section" id="sec-5">
            <h2 class="legal-section-title">5. Subscription and Payment</h2>
            <div class="legal-content">
                <p>MannaPOS offers various subscription plans. By subscribing to a plan, you agree to pay the applicable fees in accordance with the billing terms in effect at the time of subscription.</p>
                <p><span class="legal-highlight">Billing Cycle:</span> Subscriptions are billed on a monthly or annual basis, depending on the plan selected. Your subscription will automatically renew unless you cancel at least 30 days before the end of the current billing period.</p>
                <p><span class="legal-highlight">Refund Policy:</span> Refunds are handled on a case-by-case basis. Please contact our support team for refund requests.</p>
                <p><span class="legal-highlight">Payment Methods:</span> We accept major credit cards, bank transfers, and mobile money payments. All payment information is processed securely through third-party payment processors.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-6">
            <h2 class="legal-section-title">6. Intellectual Property</h2>
            <div class="legal-content">
                <p>The Service and its original content, features, and functionality are and will remain the exclusive property of MannaPOS and its licensors. The Service is protected by copyright, trademark, and other laws.</p>
                <p>You may not reproduce, distribute, modify, create derivative works, publicly display, publicly perform, republish, download, store, or transmit any of the material on our Service, except as permitted by law or with our prior written consent.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-7">
            <h2 class="legal-section-title">7. Data and Privacy</h2>
            <div class="legal-content">
                <p>Your use of the Service is also governed by our <a href="/privacy" class="legal-highlight">Privacy Policy</a>. Please review our Privacy Policy, which also governs the Service and informs users of our data collection practices.</p>
                <p>You agree that we may collect and use your data in accordance with our Privacy Policy. You retain ownership of your business data, and we will not use your data for purposes other than providing the Service unless you give explicit consent.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-8">
            <h2 class="legal-section-title">8. Service Availability</h2>
            <div class="legal-content">
                <p>We strive for 99.9% uptime but do not guarantee uninterrupted access to the Service. The Service may be temporarily unavailable due to maintenance, updates, or other reasons beyond our control.</p>
                <p>We are not liable for any loss or damage arising from service unavailability. We will provide advance notice of scheduled maintenance whenever possible.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-9">
            <h2 class="legal-section-title">9. Limitation of Liability</h2>
            <div class="legal-content">
                <p>To the maximum extent permitted by law, MannaPOS shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from:</p>
                <ul>
                    <li>Your access to or use of or inability to access or use the Service</li>
                    <li>Any conduct or content of any third party on the Service</li>
                    <li>Any content obtained from the Service</li>
                    <li>Unauthorized access, use, or alteration of your transmissions or content</li>
                </ul>
            </div>
        </div>

        <div class="legal-section" id="sec-10">
            <h2 class="legal-section-title">10. Indemnification</h2>
            <div class="legal-content">
                <p>You agree to defend, indemnify, and hold harmless MannaPOS and its affiliates, officers, directors, employees, agents, and third parties from any claim, demand, damage, loss, cost, or liability, including reasonable attorneys' fees, arising from:</p>
                <ul>
                    <li>Your use of the Service</li>
                    <li>Your violation of these Terms</li>
                    <li>Your violation of any third-party rights</li>
                    <li>Any content you post or share through the Service</li>
                </ul>
            </div>
        </div>

        <div class="legal-section" id="sec-11">
            <h2 class="legal-section-title">11. Termination</h2>
            <div class="legal-content">
                <p>We may terminate or suspend your account and access to the Service at our sole discretion, without prior notice, for conduct that we believe violates these Terms or is harmful to other users, us, or third parties, or for any other reason.</p>
                <p>Upon termination, your right to use the Service will immediately cease. All provisions of the Terms which by their nature should survive termination shall survive, including ownership provisions, warranty disclaimers, and limitations of liability.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-12">
            <h2 class="legal-section-title">12. Governing Law</h2>
            <div class="legal-content">
                <p>These Terms shall be governed by and construed in accordance with the laws of Tanzania, without regard to its conflict of law provisions. Any disputes arising from these Terms shall be resolved in the courts of Tanzania.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-13">
            <h2 class="legal-section-title">13. Changes to Terms</h2>
            <div class="legal-content">
                <p>We reserve the right to modify these Terms at any time. We will notify users of any material changes by posting the new Terms on this page and updating the "Last Updated" date. Your continued use of the Service after such modifications constitutes your acceptance of the new Terms.</p>
            </div>
        </div>

        <div class="legal-section" id="sec-14">
            <h2 class="legal-section-title">14. Contact Information</h2>
            <div class="legal-content">
                <p>If you have any questions about these Terms of Service, please contact us:</p>
                <p><strong class="legal-highlight">Email:</strong> legal@mannapos.com<br>
                <strong class="legal-highlight">Address:</strong> MannaPOS Inc., 123 Business Avenue, Suite 100, Dar es Salaam, Tanzania<br>
                <strong class="legal-highlight">Phone:</strong> +255 123 456 789</p>
            </div>
        </div>

        <div class="legal-footer">
            <p>&copy; {{ date('Y') }} MannaPOS. All rights reserved. These Terms of Service constitute a legally binding agreement between you and MannaPOS.</p>
        </div>
    </div>
</div>
@endsection
