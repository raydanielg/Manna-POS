@extends('layouts.page')

@section('title', 'Privacy Policy - ' . config('app.name', 'MannaPOS'))

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
            <h1 class="document-title">Privacy Policy</h1>
            <p class="document-subtitle">Last Updated: January 1, 2024</p>
        </div>

        <div class="document-section">
            <h2 class="section-title">1. Introduction</h2>
            <div class="section-content">
                <p>Welcome to MannaPOS ("we," "our," or "us"). This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our Point of Sale (POS) software and services. Please read this Privacy Policy carefully. If you do not agree with the terms of this Privacy Policy, please do not access the site.</p>
                <p>We respect your privacy and are committed to protecting your personal data. This privacy policy will inform you as to how we look after your personal data when you visit our website and inform you of your privacy rights and how the law protects you.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">2. Information We Collect</h2>
            <div class="section-content">
                <p>We may collect information about you in a variety of ways. The information we may collect on the Site includes:</p>
                <ul>
                    <li><span class="highlight">Personal Data:</span> Personally identifiable information, such as your name, shipping address, email address, and telephone number, and demographic information, such as your age, gender, hometown, and interests, that you voluntarily give to us when you register with the Site or when you choose to participate in various activities related to the Site.</li>
                    <li><span class="highlight">Derivative Data:</span> Information our servers automatically collect when you access the Site, such as your IP address, browser type, operating system, access times, the page you were viewing before coming to our site, and the pages you access on our Site.</li>
                    <li><span class="highlight">Financial Data:</span> Financial information, such as data related to your payment method (e.g., valid credit card number, card brand, expiration date) that we may collect when you purchase, order, return, exchange, or request information about our services from the Site.</li>
                </ul>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">3. How We Use Your Information</h2>
            <div class="section-content">
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

        <div class="document-section">
            <h2 class="section-title">4. Information Sharing</h2>
            <div class="section-content">
                <p>We may share your personal information in the following situations:</p>
                <ul>
                    <li><span class="highlight">With Service Providers:</span> We may share your information with third-party service providers who perform services on our behalf, such as payment processing, data analysis, email delivery, hosting services, and customer service.</li>
                    <li><span class="highlight">For Business Transfers:</span> We may share or transfer your information in connection with a merger, sale of company assets, financing, or acquisition of all or a portion of our business to another company.</li>
                    <li><span class="highlight">With Affiliates:</span> We may share your information with our affiliates, in which case we will require those affiliates to honor this Privacy Policy.</li>
                    <li><span class="highlight">With Your Consent:</span> We may share your personal information for any other purpose with your consent.</li>
                </ul>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">5. Data Security</h2>
            <div class="section-content">
                <p>We have implemented appropriate technical and organizational security measures designed to protect the security of any personal information we process. However, despite our efforts to protect your personal information, no transmission of information over the Internet or electronic storage is 100% secure, and we cannot guarantee or warrant the absolute security of your information.</p>
                <p>We use encryption, secure protocols, and access controls to protect your data. Regular security audits and updates are performed to ensure the highest level of protection.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">6. Your Privacy Rights</h2>
            <div class="section-content">
                <p>Depending on your location, you may have the following rights regarding your personal information:</p>
                <ul>
                    <li><span class="highlight">Access:</span> Request access to your personal information</li>
                    <li><span class="highlight">Correction:</span> Request correction of inaccurate personal information</li>
                    <li><span class="highlight">Deletion:</span> Request deletion of your personal information</li>
                    <li><span class="highlight">Portability:</span> Request transfer of your personal information</li>
                    <li><span class="highlight">Objection:</span> Object to processing of your personal information</li>
                    <li><span class="highlight">Restriction:</span> Request restriction of processing your personal information</li>
                </ul>
                <p>To exercise these rights, please contact us at privacy@mannapos.com.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">7. Cookies and Tracking</h2>
            <div class="section-content">
                <p>We use cookies and similar tracking technologies to track activity on our Site and hold certain information. Cookies are files with a small amount of data which may include an anonymous unique identifier. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.</p>
                <p>We use cookies for the following purposes: to enable certain functions of the Site, to provide analytics, to store your preferences, to enable advertisements delivery, and to analyze usage patterns.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">8. Children's Privacy</h2>
            <div class="section-content">
                <p>Our Site is not intended for children under 13 years of age. We do not knowingly collect personally identifiable information from children under 13. If you are a parent or guardian and you believe your child has provided us with personal information, please contact us, and we will delete such information.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">9. International Data Transfers</h2>
            <div class="section-content">
                <p>Your information may be transferred to and maintained on computers located outside of your state, province, country, or other governmental jurisdiction where data protection laws may differ. Your information will be handled in accordance with this Privacy Policy.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">10. Changes to This Privacy Policy</h2>
            <div class="section-content">
                <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date. You are advised to review this Privacy Policy periodically for any changes.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">11. Contact Us</h2>
            <div class="section-content">
                <p>If you have any questions about this Privacy Policy, please contact us:</p>
                <p><strong>Email:</strong> privacy@mannapos.com<br>
                <strong>Address:</strong> MannaPOS Inc., 123 Business Avenue, Suite 100, Dar es Salaam, Tanzania<br>
                <strong>Phone:</strong> +255 123 456 789</p>
            </div>
        </div>

        <div class="document-footer">
            <p>© 2024 MannaPOS. All rights reserved. This Privacy Policy is part of our Terms of Service.</p>
        </div>
</div>
</style>
@endsection
