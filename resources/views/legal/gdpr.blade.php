@extends('layouts.page')

@section('title', 'GDPR Compliance - ' . config('app.name', 'MannaPOS'))

@section('content')
<div class="pb-16">
<style>
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
            <h1 class="document-title">GDPR Compliance Statement</h1>
            <p class="document-subtitle">Last Updated: January 1, 2024</p>
        </div>

        <div class="document-section">
            <h2 class="section-title">1. Introduction</h2>
            <div class="section-content">
                <p>MannaPOS is committed to protecting the privacy and personal data of our users in accordance with the General Data Protection Regulation (GDPR) (EU) 2016/679. This GDPR Compliance Statement explains how we collect, process, and protect your personal data.</p>
                <p>This statement applies to all individuals whose personal data is processed by MannaPOS, including customers, employees, and website visitors located within the European Economic Area (EEA).</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">2. Data Controller</h2>
            <div class="section-content">
                <p>MannaPOS Inc. acts as the Data Controller for personal data processed through our services. Our contact details are:</p>
                <p><strong>Company Name:</strong> MannaPOS Inc.<br>
                <strong>Address:</strong> 123 Business Avenue, Suite 100, Dar es Salaam, Tanzania<br>
                <strong>Email:</strong> dpo@mannapos.com<br>
                <strong>Phone:</strong> +255 123 456 789</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">3. Legal Basis for Processing</h2>
            <div class="section-content">
                <p>We process personal data based on the following legal grounds under GDPR:</p>
                <ul>
                    <li><span class="highlight">Contractual Necessity:</span> Processing necessary to perform our obligations under service agreements</li>
                    <li><span class="highlight">Consent:</span> Processing based on your explicit consent for specific purposes</li>
                    <li><span class="highlight">Legal Obligation:</span> Processing required to comply with applicable laws and regulations</li>
                    <li><span class="highlight">Legitimate Interests:</span> Processing for our legitimate business interests, where not overridden by your rights</li>
                </ul>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">4. Data We Collect</h2>
            <div class="section-content">
                <p>We collect the following categories of personal data:</p>
                <ul>
                    <li><span class="highlight">Identity Data:</span> Name, title, date of birth, gender</li>
                    <li><span class="highlight">Contact Data:</strong> Email address, phone number, postal address</li>
                    <li><span class="highlight">Financial Data:</strong> Payment information, billing address, transaction history</li>
                    <li><span class="highlight">Technical Data:</strong> IP address, browser type, device information, cookies</li>
                    <li><span class="highlight">Profile Data:</strong> Username, password, preferences, account settings</li>
                    <li><span class="highlight">Usage Data:</strong> Service usage patterns, feature interactions, session data</li>
                </ul>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">5. Your GDPR Rights</h2>
            <div class="section-content">
                <p>Under GDPR, you have the following rights regarding your personal data:</p>
                <ul>
                    <li><span class="highlight">Right to Access:</span> Request a copy of your personal data we hold</li>
                    <li><span class="highlight">Right to Rectification:</span> Request correction of inaccurate or incomplete data</li>
                    <li><span class="highlight">Right to Erasure:</span> Request deletion of your personal data (right to be forgotten)</li>
                    <li><span class="highlight">Right to Restrict Processing:</span> Request limitation of how we process your data</li>
                    <li><span class="highlight">Right to Data Portability:</span> Request transfer of your data to another service</li>
                    <li><span class="highlight">Right to Object:</strong> Object to processing based on legitimate interests or direct marketing</li>
                    <li><span class="highlight">Right to Withdraw Consent:</strong> Withdraw consent at any time where processing is based on consent</li>
                </ul>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">6. Exercising Your Rights</h2>
            <div class="section-content">
                <p>To exercise your GDPR rights, please contact our Data Protection Officer at dpo@mannapos.com. We will respond to your request within one month of receipt, unless the request is complex, in which case we may extend this period by up to two additional months.</p>
                <p>We may request verification of your identity before processing your request to ensure we are disclosing data to the correct person.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">7. Data Retention</h2>
            <div class="section-content">
                <p>We retain personal data only for as long as necessary for the purposes for which it was collected. Retention periods vary based on the type of data and applicable legal requirements:</p>
                <ul>
                    <li><span class="highlight">Account Data:</strong> Retained while your account is active and for 7 years after closure</li>
                    <li><span class="highlight">Transaction Data:</strong> Retained for 7 years for tax and legal compliance</li>
                    <li><span class="highlight">Support Communications:</strong> Retained for 3 years</li>
                    <li><span class="highlight">Marketing Data:</strong> Retained until consent is withdrawn or 2 years of inactivity</li>
                    <li><span class="highlight">Analytics Data:</strong> Retained for 2 years in anonymized form</li>
                </ul>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">8. Data Security</h2>
            <div class="section-content">
                <p>We implement appropriate technical and organizational measures to ensure a level of security appropriate to the risk, including:</p>
                <ul>
                    <li>Encryption of data in transit and at rest</li>
                    <li>Access controls and authentication mechanisms</li>
                    <li>Regular security assessments and penetration testing</li>
                    <li>Employee training on data protection</li>
                    <li>Incident response procedures</li>
                </ul>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">9. International Data Transfers</h2>
            <div class="section-content">
                <p>Your personal data may be transferred to and processed in countries outside the EEA. We ensure adequate protection of your data by:</p>
                <ul>
                    <li>Using standard contractual clauses approved by the European Commission</li>
                    <li>Requiring third-party processors to maintain equivalent data protection standards</li>
                    <li>Complying with GDPR requirements for international transfers</li>
                </ul>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">10. Data Breaches</h2>
            <div class="section-content">
                <p>In the event of a personal data breach that poses a risk to your rights and freedoms, we will notify you without undue delay and, where feasible, within 72 hours of becoming aware of the breach. We will also notify the relevant supervisory authority where required.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">11. Children's Data</h2>
            <div class="section-content">
                <p>We do not knowingly collect personal data from children under 16 years of age without parental consent. If we become aware that we have collected personal data from a child without consent, we will take steps to delete such information immediately.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">12. Changes to This Statement</h2>
            <div class="section-content">
                <p>We may update this GDPR Compliance Statement from time to time to reflect changes in our practices, technology, or legal requirements. We will notify users of any material changes by posting the updated statement on this page.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">13. Right to Lodge a Complaint</h2>
            <div class="section-content">
                <p>If you believe that our processing of your personal data infringes GDPR requirements, you have the right to lodge a complaint with a supervisory authority in the EEA member state where you reside, work, or where the alleged infringement occurred.</p>
            </div>
        </div>

        <div class="document-section">
            <h2 class="section-title">14. Contact Information</h2>
            <div class="section-content">
                <p>For any GDPR-related inquiries, please contact our Data Protection Officer:</p>
                <p><strong>Email:</strong> dpo@mannapos.com<br>
                <strong>Address:</strong> MannaPOS Inc., 123 Business Avenue, Suite 100, Dar es Salaam, Tanzania<br>
                <strong>Phone:</strong> +255 123 456 789</p>
            </div>
        </div>

        <div class="document-footer">
            <p>© 2024 MannaPOS. All rights reserved. This GDPR Compliance Statement should be read together with our Privacy Policy.</p>
        </div>
</div>
</style>
@endsection
