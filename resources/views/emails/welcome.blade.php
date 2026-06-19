<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to MannaPOS</title>
    <style>
        body { font-family: 'Segoe UI', Inter, -apple-system, sans-serif; background: #f3f4f6; margin: 0; padding: 0; }
        .container { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%); padding: 40px 32px; text-align: center; }
        .header img { width: 56px; height: 56px; margin-bottom: 16px; }
        .header h1 { color: #fff; font-size: 24px; font-weight: 800; margin: 0; letter-spacing: -0.5px; }
        .header p { color: rgba(255,255,255,0.75); font-size: 14px; margin: 8px 0 0; }
        .body { padding: 32px; color: #374151; font-size: 15px; line-height: 1.7; }
        .body h2 { font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 12px; }
        .details { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .details-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .details-row:last-child { border-bottom: none; }
        .details-label { color: #6b7280; font-weight: 500; }
        .details-value { color: #111827; font-weight: 600; }
        .cta { display: block; width: fit-content; margin: 24px auto 0; padding: 14px 32px; background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%); color: #fff; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 15px; }
        .footer { padding: 24px 32px; background: #f9fafb; text-align: center; font-size: 12px; color: #9ca3af; }
        .footer a { color: #6b7280; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('icons8-dynamics-365-96.png') }}" alt="MannaPOS">
            <h1>Welcome to MannaPOS!</h1>
            <p>Your business management journey starts now.</p>
        </div>
        <div class="body">
            <h2>Hi {{ $user->name }},</h2>
            <p>Thank you for signing up. Your MannaPOS account has been created successfully. Here are your account details:</p>

            <div class="details">
                <div class="details-row">
                    <span class="details-label">Business</span>
                    <span class="details-value">{{ $user->business_name }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Email</span>
                    <span class="details-value">{{ $user->email }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Phone</span>
                    <span class="details-value">{{ $user->phone }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Currency</span>
                    <span class="details-value">{{ $user->currency }}</span>
                </div>
            </div>

            <p>We have sent a separate email with your <strong>OTP verification code</strong>. Please verify your account within <strong>30 minutes</strong> to unlock all features.</p>

            <a href="{{ url('/verify-otp') }}" class="cta">Verify My Account</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} MannaPOS. All rights reserved.</p>
            <p><a href="{{ url('/') }}">mannapos.co.tz</a></p>
        </div>
    </div>
</body>
</html>
