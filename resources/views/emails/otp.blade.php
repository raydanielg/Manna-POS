<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Account</title>
    <style>
        body { font-family: 'Segoe UI', Inter, -apple-system, sans-serif; background: #f3f4f6; margin: 0; padding: 0; }
        .container { max-width: 480px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%); padding: 32px; text-align: center; }
        .header h1 { color: #fff; font-size: 20px; font-weight: 700; margin: 0; }
        .header p { color: rgba(255,255,255,0.75); font-size: 13px; margin: 6px 0 0; }
        .body { padding: 32px; color: #374151; font-size: 15px; line-height: 1.7; text-align: center; }
        .otp-box { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 2px dashed #3b82f6; border-radius: 12px; padding: 24px; margin: 24px 0; }
        .otp-code { font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #1d4ed8; font-family: 'Courier New', monospace; }
        .otp-label { font-size: 12px; color: #6b7280; margin-top: 8px; text-transform: uppercase; letter-spacing: 1px; }
        .expires { background: #fef2f2; color: #dc2626; padding: 10px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; margin: 20px 0; }
        .link-box { background: #f9fafb; border-radius: 10px; padding: 16px; margin-top: 20px; font-size: 13px; color: #6b7280; word-break: break-all; }
        .link-box a { color: #2563eb; font-weight: 600; text-decoration: none; }
        .footer { padding: 20px 32px; background: #f9fafb; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verify Your Account</h1>
            <p>One-Time Password for MannaPOS</p>
        </div>
        <div class="body">
            <p>Hi {{ $user->name }},</p>
            <p>Use the code below to verify your email address and activate your account:</p>

            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-label">6-Digit Verification Code</div>
            </div>

            <div class="expires">This code expires in 30 minutes. Do not share it with anyone.</div>

            <p>Or click the link below to activate your account instantly:</p>
            <div class="link-box">
                <a href="{{ url('/activate/' . $user->activation_token) }}">{{ url('/activate/' . $user->activation_token) }}</a>
            </div>

            <p style="margin-top:24px; font-size:13px; color:#6b7280;">If you did not create this account, please ignore this email.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} MannaPOS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
