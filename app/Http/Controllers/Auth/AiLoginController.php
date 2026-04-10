<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoginAttempt;
use App\Models\SuspiciousActivity;
use App\Models\VerificationAttempt;
use App\Services\AiDetectionEngin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Notifications\SuspiciousLoginNotification;
use App\Notifications\VerificationRequiredNotification;

class AiLoginController extends Controller
{
    protected $aiEngine;
    
    public function __construct(AiDetectionEngin $aiEngine)
    {
        $this->aiEngine = $aiEngine;
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        $user = \App\Models\User::where('email', $request->email)->first();
        
        // Get real client IP before anything else
        $realClientIp = $this->getRealClientIp($request);
        
        // Record login attempt with real IP
        $loginAttempt = $this->recordLoginAttempt($user, $request, false, $realClientIp);
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            $loginAttempt->update(['is_successful' => false]);
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        // Analyze login with AI, passing the real client IP
        $analysis = $this->aiEngine->analyzeLoginAttempt($user, $request, $realClientIp);
        
        // Update login attempt with analysis
        $loginAttempt->update([
            'is_successful' => true,
            'is_suspicious' => $analysis['is_suspicious'],
            'risk_score' => $analysis['risk_score'],
            'detection_factors' => $analysis['detection_factors']
        ]);
        
        if ($analysis['is_suspicious']) {
            $this->handleSuspiciousLogin($user, $loginAttempt, $analysis);
            return response()->json([
                'requires_verification' => true,
                'verification_methods' => $this->getAvailableVerificationMethods($user),
                'risk_score' => $analysis['risk_score'],
                'reasons' => $analysis['reasons']
            ]);
        }
        
        $this->handleSuccessfulLogin($user, $loginAttempt);
        
        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user,
            'message' => 'Login successful'
        ]);
    }
    
    public function verifyLogin(Request $request)
    {
        $request->validate([
            'login_attempt_id' => 'required|exists:login_attempts,id',
            'verification_method' => 'required|in:2fa,email,security_questions',
            'verification_code' => 'required'
        ]);
        
        $loginAttempt = LoginAttempt::findOrFail($request->login_attempt_id);
        $user = $loginAttempt->user;
        
        $isVerified = $this->verifyUser($user, $request->verification_method, $request->verification_code);
        
        VerificationAttempt::create([
            'user_id' => $user->id,
            'login_attempt_id' => $loginAttempt->id,
            'verification_method' => $request->verification_method,
            'is_successful' => $isVerified,
            'verified_at' => now()
        ]);
        
        if ($isVerified) {
            $suspiciousActivity = SuspiciousActivity::where('activity_data->login_attempt_id', $loginAttempt->id)->first();
            if ($suspiciousActivity) {
                $suspiciousActivity->update([
                    'status' => SuspiciousActivity::STATUS_RESOLVED,
                    'reviewed_at' => now()
                ]);
            }
            $this->handleSuccessfulLogin($user, $loginAttempt);
            return response()->json([
                'token' => $user->createToken('auth_token')->plainTextToken,
                'user' => $user,
                'message' => 'Verification successful'
            ]);
        }
        
        return response()->json([
            'error' => 'Verification failed',
            'remaining_attempts' => 2
        ], 401);
    }
    
    /**
     * Get the real client IP from trusted headers (Cloudflare, Render, etc.)
     */
    private function getRealClientIp(Request $request): string
    {
        $ip = $request->header('CF-Connecting-IP');
        if (!$ip) {
            $ip = $request->header('True-Client-IP');
        }
        if (!$ip) {
            $forwardedFor = $request->header('X-Forwarded-For');
            if ($forwardedFor) {
                $ips = explode(',', $forwardedFor);
                $ip = trim($ips[0]);
            }
        }
        if (!$ip) {
            $ip = $request->ip();
        }
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = $request->ip();
        }
        return $ip;
    }
    
    private function recordLoginAttempt($user, Request $request, $isSuccessful = false, ?string $realClientIp = null): LoginAttempt
    {
        $ip = $realClientIp ?? $this->getRealClientIp($request);
        $location = $this->getLocationData($ip);
        
        return LoginAttempt::create([
            'user_id' => $user?->id,
            'ip_address' => $ip,
            'user_agent' => $request->userAgent(),
            'country' => $location['country'] ?? null,
            'city' => $location['city'] ?? null,
            'browser' => $this->parseBrowser($request->userAgent()),
            'platform' => $this->parsePlatform($request->userAgent()),
            'device_type' => $this->parseDeviceType($request->userAgent()),
            'is_successful' => $isSuccessful,
            'attempted_at' => now()
        ]);
    }
    
    private function handleSuspiciousLogin($user, $loginAttempt, $analysis): void
    {
        SuspiciousActivity::create([
            'user_id' => $user->id,
            'activity_type' => SuspiciousActivity::TYPE_LOGIN,
            'activity_data' => [
                'login_attempt_id' => $loginAttempt->id,
                'ip_address' => $loginAttempt->ip_address,
                'location' => "{$loginAttempt->city}, {$loginAttempt->country}",
                'device' => $loginAttempt->device_type
            ],
            'risk_score' => $analysis['risk_score'],
            'detection_reasons' => $analysis['reasons'],
            'status' => SuspiciousActivity::STATUS_PENDING
        ]);
        
        $user->notify(new SuspiciousLoginNotification($loginAttempt));
        $user->notify(new VerificationRequiredNotification());
        $this->notifyAdmins($user, $loginAttempt, $analysis);
        $this->updateBehaviorProfile($user, $loginAttempt);
    }
    
    private function handleSuccessfulLogin($user, $loginAttempt): void
    {
        $this->updateBehaviorProfile($user, $loginAttempt);
        $this->clearFailedAttempts($user);
        \Log::info('User logged in', [
            'user_id' => $user->id,
            'ip' => $loginAttempt->ip_address,
            'time' => now()
        ]);
    }
    
    private function updateBehaviorProfile($user, $loginAttempt): void
    {
        $profile = $user->behaviorProfile;
        $profile->updateLoginPattern([
            'country' => $loginAttempt->country,
            'city' => $loginAttempt->city,
            'ip_address' => $loginAttempt->ip_address
        ]);
    }
    
    private function getLocationData(string $ip): ?array
    {
        try {
            $location = \Stevebauman\Location\Facades\Location::get($ip);
            if ($location) {
                return [
                    'country' => $location->countryName,
                    'city' => $location->cityName,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'isp' => $location->isp
                ];
            }
        } catch (\Exception $e) {
            \Log::error("Location lookup failed for IP {$ip}: " . $e->getMessage());
        }
        return null;
    }
    
    // Add stub methods for missing implementations (adjust as needed)
    private function parseBrowser($userAgent): string
    {
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        }
        if (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        }
        if (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        }
        if (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        }
        return 'Unknown';
    }
    
    private function parsePlatform($userAgent): string
    {
        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows';
        }
        if (strpos($userAgent, 'Mac') !== false) {
            return 'macOS';
        }
        if (strpos($userAgent, 'Linux') !== false) {
            return 'Linux';
        }
        if (strpos($userAgent, 'Android') !== false) {
            return 'Android';
        }
        if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            return 'iOS';
        }
        return 'Unknown';
    }
    
    private function parseDeviceType($userAgent): string
    {
        if (strpos($userAgent, 'Mobile') !== false) {
            return 'Mobile';
        }
        if (strpos($userAgent, 'Tablet') !== false) {
            return 'Tablet';
        }
        return 'Desktop';
    }
    
    private function getAvailableVerificationMethods($user): array
    {
        return ['2fa', 'email', 'security_questions'];
    }
    
    private function verifyUser($user, $method, $code): bool
    {
        // Implement actual verification logic
        return true; // Placeholder
    }
    
    private function notifyAdmins($user, $loginAttempt, $analysis): void
    {
        // Implement admin notifications
    }
    
    private function clearFailedAttempts($user): void
    {
        // Implement clearing failed attempts
    }
}