@extends('layouts.page')

@section('title', 'Terms of Service - ' . config('app.name', 'MannaPOS'))

@section('content')
<div class="pb-16">

        .document-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 25mm 20mm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            min-height: 297mm;
        }

        .document-header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .document-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }

        .document-subtitle {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .document-section {
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.75rem;
            margin-top: 1.5rem;
        }

        .section-title:first-child {
            margin-top: 0;
        }

        .section-content {
            font-size: 12px;
            color: #333;
            line-height: 1.7;
            text-align: justify;
        }

        .section-content p {
            margin-bottom: 0.75rem;
        }

        .section-content ul {
            margin: 0.5rem 0 0.75rem 1.5rem;
        }

        .section-content li {
            margin-bottom: 0.25rem;
        }

        .highlight {
            font-weight: 600;
            color: #2563eb;
        }

        .document-footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e5e5;
            font-size: 11px;
            color: #666;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .document-container {
                padding: 15mm 10mm;
                margin: 0 10px;
            }
        }
    </style>
</head>
<body>
    <div class="document-container">
        <a href="/" class="back-link">← Back to Home</a>
        
        <div class="document-header">
            <h1 class="document-title">Terms of Service</h1>
            <p class="document-subtitle">Last Updated: January 1, 2024</p>
        </div>

        <div class="document-section">
            <h2 class="section-title">1. Acceptance of Terms</h2>
            <div class="section-content">
                <p>By accessing and using MannaPOS ("the Service"), you accept and agree to be bound by the terms and provisions of this agreement. If you do not agree to abide by these terms, please do not use this Service.</p>
                <p>MannaPOS reserves the right to modify these terms at any time. Your continued use of the Service following any such modification constitutes your acceptance of the modified terms.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">2. Description of Service</h2>
            <div class="section-content">
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

        <div class="document-section">
            <h2 class="section-title">3. User Accounts</h2>
            <div class="section-content">
                <p>To use certain features of the Service, you must register for an account. You agree to provide accurate, current, and complete information during registration and to update such information to keep it accurate, current, and complete.</p>
                <p>You are responsible for safeguarding the password that you use to access the Service and for any activities or actions under your password. You agree not to disclose your password to any third party. You must notify us immediately upon becoming aware of any breach of security or unauthorized use of your account.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">4. User Responsibilities</h2>
            <div class="section-content">
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

        <div class="document-section">
            <h2 class="section-title">5. Subscription and Payment</h2>
            <div class="section-content">
                <p>MannaPOS offers various subscription plans. By subscribing to a plan, you agree to pay the applicable fees in accordance with the billing terms in effect at the time of subscription.</p>
                <p><span class="highlight">Billing Cycle:</span> Subscriptions are billed on a monthly or annual basis, depending on the plan selected. Your subscription will automatically renew unless you cancel at least 30 days before the end of the current billing period.</p>
                <p><span class="highlight">Refund Policy:</span> Refunds are handled on a case-by-case basis. Please contact our support team for refund requests.</p>
                <p><span class="highlight">Payment Methods:</span> We accept major credit cards, bank transfers, and mobile money payments. All payment information is processed securely through third-party payment processors.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">6. Intellectual Property</h2>
            <div class="section-content">
                <p>The Service and its original content, features, and functionality are and will remain the exclusive property of MannaPOS and its licensors. The Service is protected by copyright, trademark, and other laws.</p>
                <p>You may not reproduce, distribute, modify, create derivative works, publicly display, publicly perform, republish, download, store, or transmit any of the material on our Service, except as permitted by law or with our prior written consent.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">7. Data and Privacy</h2>
            <div class="section-content">
                <p>Your use of the Service is also governed by our Privacy Policy. Please review our Privacy Policy, which also governs the Service and informs users of our data collection practices.</p>
                <p>You agree that we may collect and use your data in accordance with our Privacy Policy. You retain ownership of your business data, and we will not use your data for purposes other than providing the Service unless you give explicit consent.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">8. Service Availability</h2>
            <div class="section-content">
                <p>We strive for 99.9% uptime but do not guarantee uninterrupted access to the Service. The Service may be temporarily unavailable due to maintenance, updates, or other reasons beyond our control.</p>
                <p>We are not liable for any loss or damage arising from service unavailability. We will provide advance notice of scheduled maintenance whenever possible.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">9. Limitation of Liability</h2>
            <div class="section-content">
                <p>To the maximum extent permitted by law, MannaPOS shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from:</p>
                <ul>
                    <li>Your access to or use of or inability to access or use the Service</li>
                    <li>Any conduct or content of any third party on the Service</li>
                    <li>Any content obtained from the Service</li>
                    <li>Unauthorized access, use, or alteration of your transmissions or content</li>
                </ul>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">10. Indemnification</h2>
            <div class="section-content">
                <p>You agree to defend, indemnify, and hold harmless MannaPOS and its affiliates, officers, directors, employees, agents, and third parties from any claim, demand, damage, loss, cost, or liability, including reasonable attorneys' fees, arising from:</p>
                <ul>
                    <li>Your use of the Service</li>
                    <li>Your violation of these Terms</li>
                    <li>Your violation of any third-party rights</li>
                    <li>Any content you post or share through the Service</li>
                </ul>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">11. Termination</h2>
            <div class="section-content">
                <p>We may terminate or suspend your account and access to the Service at our sole discretion, without prior notice, for conduct that we believe violates these Terms or is harmful to other users, us, or third parties, or for any other reason.</p>
                <p>Upon termination, your right to use the Service will immediately cease. All provisions of the Terms which by their nature should survive termination shall survive, including ownership provisions, warranty disclaimers, and limitations of liability.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">12. Governing Law</h2>
            <div class="section-content">
                <p>These Terms shall be governed by and construed in accordance with the laws of Tanzania, without regard to its conflict of law provisions. Any disputes arising from these Terms shall be resolved in the courts of Tanzania.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">13. Changes to Terms</h2>
            <div class="section-content">
                <p>We reserve the right to modify these Terms at any time. We will notify users of any material changes by posting the new Terms on this page and updating the "Last Updated" date. Your continued use of the Service after such modifications constitutes your acceptance of the new Terms.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">14. Contact Information</h2>
            <div class="section-content">
                <p>If you have any questions about these Terms of Service, please contact us:</p>
                <p><strong>Email:</strong> legal@mannapos.com<br>
                <strong>Address:</strong> MannaPOS Inc., 123 Business Avenue, Suite 100, Dar es Salaam, Tanzania<br>
                <strong>Phone:</strong> +255 123 456 789</p>
            </div>
        </div>

        <div class="document-footer">
            <p>© 2024 MannaPOS. All rights reserved. These Terms of Service constitute a legally binding agreement between you and MannaPOS.</p>
        </div>
</div>
</style>
@endsection
