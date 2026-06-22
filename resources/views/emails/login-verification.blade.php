<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login Verification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .header h1 { color: #fff; margin: 0; font-size: 24px; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
        .code-box { background: #fff; border: 2px dashed #667eea; padding: 20px; text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 8px; margin: 20px 0; border-radius: 8px; color: #333; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
        .btn { display: inline-block; background: #667eea; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 Login Verification Required</h1>
    </div>
    
    <div class="content">
        <p>Hi <strong>{{ $user->name }}</strong>,</p>
        
        <p>We detected unusual login activity for your account and need to verify your identity.</p>
        
        <div class="warning">
            <strong>⚠️ Suspicious Activity Detected</strong><br>
            @if($analysis['brute_force_detected'])
                • Multiple failed login attempts detected<br>
            @endif
            @foreach($analysis['factors'] as $factor)
                • {{ ucfirst(str_replace('_', ' ', $factor['factor'])) }}
            @endforeach
            <br><br>
            <strong>Risk Score: {{ $analysis['risk_score'] }}%</strong>
        </div>
        
        <p>Your verification code is:</p>
        
        <div class="code-box">
            {{ $code }}
        </div>
        
        <p>Enter this code on the verification page to complete your login.</p>
        
        <p><strong>Details of this login attempt:</strong></p>
        <ul>
            <li>IP Address: {{ $loginAttempt->ip_address }}</li>
            <li>Location: {{ $loginAttempt->city }}, {{ $loginAttempt->country }}</li>
            <li>Device: {{ $loginAttempt->device_type }}</li>
            <li>Browser: {{ $loginAttempt->browser }}</li>
            <li>Time: {{ $loginAttempt->attempted_at->format('M d, Y h:i A') }}</li>
        </ul>
        
        <div class="warning">
            <strong>⏰ This code expires in 10 minutes.</strong><br>
            If you didn't attempt this login, please secure your account immediately.
        </div>
        
        <p>Best regards,<br>
        <strong>{{ config('app.name') }} Security Team</strong></p>
        
        <hr>
        <p style="font-size: 12px; color: #999;">
            This is an automated security message. Please do not reply to this email.
        </p>
    </div>
</body>
</html>