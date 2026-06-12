@extends('layouts.page')

@section('title', 'Cookie Policy - ' . config('app.name', 'MannaPOS'))

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
            <h1 class="document-title">Cookie Policy</h1>
            <p class="document-subtitle">Last Updated: January 1, 2024</p>
        </div>

        <div class="document-section">
            <h2 class="section-title">1. What Are Cookies</h2>
            <div class="section-content">
                <p>Cookies are small text files that are placed on your computer or mobile device when you visit our website. They are widely used to make websites work more efficiently and to provide information to the owners of the site.</p>
                <p>Cookies allow us to recognize you and remember your preferences, understand how you use our services, and provide you with a personalized experience. This Cookie Policy explains how MannaPOS uses cookies and similar technologies.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">2. How We Use Cookies</h2>
            <div class="section-content">
                <p>We use cookies for the following purposes:</p>
                <ul>
                    <li><span class="highlight">Essential Cookies:</span> These cookies are necessary for the website to function properly. They enable basic functions such as page navigation, access to secure areas, and authentication.</li>
                    <li><span class="highlight">Performance Cookies:</span> These cookies help us understand how visitors interact with our website by collecting information about pages visited, time spent, and error messages encountered.</li>
                    <li><span class="highlight">Functionality Cookies:</span> These cookies remember your preferences and choices to provide enhanced features, such as language preferences and display settings.</li>
                    <li><span class="highlight">Targeting/Advertising Cookies:</span> These cookies track your browsing habits to deliver relevant advertisements and measure the effectiveness of our marketing campaigns.</li>
                </ul>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">3. Types of Cookies We Use</h2>
            <div class="section-content">
                <p><span class="highlight">Session Cookies:</span> Temporary cookies that expire when you close your browser. They are used to maintain your session and track your navigation during a single visit.</p>
                <p><span class="highlight">Persistent Cookies:</span> Cookies that remain on your device for a set period or until you delete them. They are used to remember your preferences for future visits.</p>
                <p><span class="highlight">First-Party Cookies:</span> Cookies set by MannaPOS directly on our domain. These are used for essential functionality and analytics.</p>
                <p><span class="highlight">Third-Party Cookies:</span> Cookies set by third-party services we use, such as Google Analytics, payment processors, and marketing tools.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">4. Third-Party Services</h2>
            <div class="section-content">
                <p>We use the following third-party services that may set cookies on your device:</p>
                <ul>
                    <li><span class="highlight">Google Analytics:</span> For website analytics and performance tracking</li>
                    <li><span class="highlight">Payment Processors:</span> For secure payment processing</li>
                    <li><span class="highlight">Marketing Platforms:</span> For email marketing and advertising</li>
                    <li><span class="highlight">Social Media:</span> For social sharing and authentication</li>
                </ul>
                <p>Each third-party service has its own cookie policy. We encourage you to review their policies for more information on how they use cookies.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">5. Managing Cookies</h2>
            <div class="section-content">
                <p>You have the right to decide whether to accept or reject cookies. You can set or amend your web browser controls to accept or refuse cookies. If you choose to reject cookies, you may still use our website, though your access to some functionality and areas may be restricted.</p>
                <p><span class="highlight">Browser Settings:</span> Most web browsers allow you to control cookies through their settings. The method for doing so varies from browser to browser.</p>
                <p><span class="highlight">Cookie Consent Tool:</span> We provide a cookie consent banner that allows you to manage your cookie preferences directly on our website.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">6. Your Cookie Choices</h2>
            <div class="section-content">
                <p>You can exercise your cookie preferences by:</p>
                <ul>
                    <li>Accepting all cookies for the best experience</li>
                    <li>Rejecting all non-essential cookies</li>
                    <li>Customizing your preferences for specific cookie categories</li>
                    <li>Withdrawing consent at any time through your browser settings</li>
                </ul>
                <p>Please note that disabling essential cookies may prevent you from using certain features of our service.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">7. Cookie Duration</h2>
            <div class="section-content">
                <p>The duration of cookies varies depending on their purpose:</p>
                <ul>
                    <li><span class="highlight">Session Cookies:</span> Expire when you close your browser</li>
                    <li><span class="highlight">Persistent Cookies:</span> Typically expire after 30 days to 2 years</li>
                    <li><span class="highlight">Analytics Cookies:</span> Usually expire after 2 years</li>
                    <li><span class="highlight">Marketing Cookies:</span> Typically expire after 1 year</li>
                </ul>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">8. Security of Cookies</h2>
            <div class="section-content">
                <p>We take appropriate measures to protect the security of cookies. We use secure protocols (HTTPS) and implement technical and organizational safeguards to prevent unauthorized access to cookies and the data they contain.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">9. Updates to This Policy</h2>
            <div class="section-content">
                <p>We may update this Cookie Policy from time to time to reflect changes in our practices, technology, legal requirements, or other reasons. We will notify users of any material changes by posting the updated policy on this page and updating the "Last Updated" date.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">10. Contact Us</h2>
            <div class="section-content">
                <p>If you have any questions about our use of cookies, please contact us:</p>
                <p><strong>Email:</strong> privacy@mannapos.com<br>
                <strong>Address:</strong> MannaPOS Inc., 123 Business Avenue, Suite 100, Dar es Salaam, Tanzania<br>
                <strong>Phone:</strong> +255 123 456 789</p>
            </div>
        </div>

        <div class="document-footer">
            <p>© 2024 MannaPOS. All rights reserved. This Cookie Policy should be read together with our Privacy Policy.</p>
        </div>
</div>
@endsection
