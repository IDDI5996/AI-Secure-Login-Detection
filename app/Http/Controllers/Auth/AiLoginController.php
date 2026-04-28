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
        $realIp = $this->getRealClientIp($request);
        
        // Record login attempt before authentication (using real IP)
        $loginAttempt = $this->recordLoginAttempt($user, $realIp, $request, false);
        
        // 1. Analyse the attempt with the AI engine regardless of success/failure
        // Pass user only if we found one; the engine can handle null.
        $analysis = $this->aiEngine->analyzeLoginAttempt($user ?? null, $request, $realIp);
        
        // 2. Update the login attempt with the risk analysis immediately
        $loginAttempt->update([
            'risk_score' => $analysis['risk_score'],
            'is_suspicious' => $analysis['is_suspicious'],
            'detection_factors' => $analysis['detection_factors'],
        ]);
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Invalid credentials – mark as failed
            $loginAttempt->update(['is_successful' => false]);
            
            // If the failed attempt is high risk, create a SuspiciousActivity and alert admins
            if ($analysis['is_suspicious']) {
                $this->handleSuspiciousFailedLogin($user, $loginAttempt, $analysis);
            }
            
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        // Successful login
        $loginAttempt->update(['is_successful' => true]);
        
        // If the successful login is suspicious, require verification
        if ($analysis['is_suspicious']) {
            $this->handleSuspiciousLogin($user, $loginAttempt, $analysis);
            
            return response()->json([
                'requires_verification' => true,
                'verification_methods' => $this->getAvailableVerificationMethods($user),
                'risk_score' => $analysis['risk_score'],
                'reasons' => $analysis['reasons']
            ]);
        }
        
        // Normal successful login
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
        
        // Record verification attempt
        VerificationAttempt::create([
            'user_id' => $user->id,
            'login_attempt_id' => $loginAttempt->id,
            'verification_method' => $request->verification_method,
            'is_successful' => $isVerified,
            'verified_at' => now()
        ]);
        
        if ($isVerified) {
            // Mark suspicious activity as resolved
            $suspiciousActivity = SuspiciousActivity::where('activity_data->login_attempt_id', $loginAttempt->id)
                ->first();
            
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
     * Extract the real client IP from trusted headers (Cloudflare, Render, etc.)
     */
    private function getRealClientIp(Request $request): string
    {
        // Check Cloudflare's CF-Connecting-IP header (most reliable)
        $ip = $request->header('CF-Connecting-IP');
        
        if (!$ip) {
            // Check True-Client-IP (also set by Cloudflare)
            $ip = $request->header('True-Client-IP');
        }
        
        if (!$ip) {
            // Check X-Forwarded-For (Render + Cloudflare)
            $forwardedFor = $request->header('X-Forwarded-For');
            if ($forwardedFor) {
                // First IP in the list is the original client
                $ips = explode(',', $forwardedFor);
                $ip = trim($ips[0]);
            }
        }
        
        if (!$ip) {
            // Fallback to Laravel's default IP detection
            $ip = $request->ip();
        }
        
        // Basic IP validation
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = $request->ip();
        }
        
        return $ip;
    }
    
    private function recordLoginAttempt($user, string $realIp, Request $request, $isSuccessful = false): LoginAttempt
    {
        // Get location using the real IP
        $location = $this->getLocationData($realIp);
        
        return LoginAttempt::create([
            'user_id' => $user?->id,
            'ip_address' => $realIp,
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
    
    /**
     * Handle a suspicious but FAILED login attempt.
     * Records the activity for security monitoring and alerts administrators.
     */
    private function handleSuspiciousFailedLogin($user, $loginAttempt, $analysis): void
    {
        // Create a SuspiciousActivity even without a verified user (user may be null)
        SuspiciousActivity::create([
            'user_id' => $user?->id,
            'activity_type' => SuspiciousActivity::TYPE_LOGIN,
            'activity_data' => [
                'login_attempt_id' => $loginAttempt->id,
                'ip_address' => $loginAttempt->ip_address,
                'email_attempted' => $loginAttempt->email ?? request('email'),
                'location' => "{$loginAttempt->city}, {$loginAttempt->country}",
                'device' => $loginAttempt->device_type,
                'is_successful' => false,
            ],
            'risk_score' => $analysis['risk_score'],
            'detection_reasons' => $analysis['reasons'],
            'status' => SuspiciousActivity::STATUS_PENDING
        ]);
        
        // Notify admins about the high-risk failed attempt (no user notification because the user is either unknown or failed)
        $this->notifyAdmins($user, $loginAttempt, $analysis);
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
    
    // ========== Helper methods ==========
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
    
    private function parseBrowser($userAgent): string
    {
        if (str_contains($userAgent, 'Chrome')) return 'Chrome';
        if (str_contains($userAgent, 'Firefox')) return 'Firefox';
        if (str_contains($userAgent, 'Safari')) return 'Safari';
        if (str_contains($userAgent, 'Edge')) return 'Edge';
        return 'Unknown';
    }
    
    private function parsePlatform($userAgent): string
    {
        if (str_contains($userAgent, 'Windows')) return 'Windows';
        if (str_contains($userAgent, 'Mac')) return 'macOS';
        if (str_contains($userAgent, 'Linux')) return 'Linux';
        if (str_contains($userAgent, 'Android')) return 'Android';
        if (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) return 'iOS';
        return 'Unknown';
    }
    
    private function parseDeviceType($userAgent): string
    {
        if (str_contains($userAgent, 'Mobile')) return 'Mobile';
        if (str_contains($userAgent, 'Tablet')) return 'Tablet';
        return 'Desktop';
    }
    
    private function getAvailableVerificationMethods($user): array
    {
        return ['2fa', 'email'];
    }
    
    private function verifyUser($user, $method, $code): bool
    {
        // Implement actual verification logic
        return true;
    }
    
    private function notifyAdmins($user, $loginAttempt, $analysis): void
    {
        // Implement admin notification logic
    }
    
    private function clearFailedAttempts($user): void
    {
        // Implement clearing logic
    }
}