<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Models\SuspiciousActivity;
use App\Models\VerificationAttempt;
use App\Services\AiDetectionService;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AiLoginController extends Controller
{
    protected $aiDetection;
    protected $verificationService;
    
    public function __construct(AiDetectionService $aiDetection, VerificationService $verificationService)
    {
        $this->aiDetection = $aiDetection;
        $this->verificationService = $verificationService;
    }
    
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            
            $user = \App\Models\User::where('email', $request->email)->first();
            
            // Get real client IP
            $realIp = $this->getRealClientIp($request);
            
            // Record login attempt BEFORE authentication
            $loginAttempt = $this->recordLoginAttempt($user, $realIp, $request, false);
            
            // Check if user exists and password is correct
            if (!$user || !Hash::check($request->password, $user->password)) {
                $loginAttempt->update(['is_successful' => false]);
                
                // Check if this is a brute force attempt
                $failedCount = LoginAttempt::where('user_id', $user?->id ?? 0)
                    ->where('is_successful', false)
                    ->where('attempted_at', '>=', now()->subMinutes(5))
                    ->count();
                
                if ($failedCount >= 4) {
                    $this->handleBruteForceDetection($user, $loginAttempt);
                }
                
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
            
            // === AI ANALYSIS ===
            $analysis = $this->aiDetection->analyzeLogin($user, $request);
            
            // After $analysis = $this->aiDetection->analyzeLogin($user, $request);
            \Log::info('🔍 AI Analysis Result', [
                'is_suspicious' => $analysis['is_suspicious'],
                'brute_force_detected' => $analysis['brute_force_detected'],
                'risk_score' => $analysis['risk_score'],
                'factors' => $analysis['factors'],
                'expects_json' => $request->expectsJson(),
                'email' => $request->email,
            ]);

            // Update login attempt with AI results
            $loginAttempt->update([
                'is_successful' => true,
                'is_suspicious' => $analysis['is_suspicious'],
                'risk_score' => $analysis['risk_score'] / 100,
                'detection_factors' => $analysis['factors'],
            ]);
            
            // === SUSPICIOUS DETECTION ===
            if ($analysis['is_suspicious'] || $analysis['brute_force_detected']) {
                
                // Inside the `if ($analysis['is_suspicious'] || $analysis['brute_force_detected'])` block, add:
                \Log::info('🔐 Verification triggered. Redirecting to verify page.');
                \Log::info('Session data being set', [
                    'verification_token' => $verificationToken,
                    'login_attempt_id' => $loginAttempt->id,
                    'risk_score' => $analysis['risk_score'],
                ]);

                // Generate verification token
                $verificationToken = $this->generateVerificationToken($user, $loginAttempt, $analysis);
                
                // Send verification email
                $this->verificationService->sendVerificationCode($user, $loginAttempt, $analysis);
                
                \Log::info('📧 Email send attempted', [
                    'user_email' => $user->email,
                    'code' => session('verification_code') ?? 'not set', // we don't store code in session, but we can log from the service
                ]);
                
                // Store pending verification
                $this->storePendingVerification($user, $loginAttempt, $verificationToken, $analysis);
                
                // Create suspicious activity record
                $this->createSuspiciousActivity($user, $loginAttempt, $analysis);
                
                // ----- WEB vs API -----
                if ($request->expectsJson()) {
                    // API response
                    return response()->json([
                        'requires_verification' => true,
                        'verification_token' => $verificationToken,
                        'login_attempt_id' => $loginAttempt->id,
                        'risk_score' => $analysis['risk_score'],
                        'reasons' => $analysis['factors'],
                        'message' => 'Verification required. A code has been sent to your email.',
                        'verification_methods' => ['email_verification'],
                    ]);
                } else {
                    
                    // In the `else` part (web redirect), add before the redirect:
                    \Log::info('➡️ Redirecting to verify route');
                    
                    // Web response: store data in session and redirect to verification page
                    session()->put('verification_token', $verificationToken);
                    session()->put('login_attempt_id', $loginAttempt->id);
                    session()->put('risk_score', $analysis['risk_score']);
                    session()->put('reasons', $analysis['factors']);
                    
                    return redirect()->route('verify')->with('warning', 'Verification required. A code has been sent to your email.');
                }
            }
            
            // === NORMAL LOGIN ===
            $this->handleSuccessfulLogin($user, $loginAttempt);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'token' => $user->createToken('auth_token')->plainTextToken,
                    'user' => $user,
                    'message' => 'Login successful',
                ]);
            } else {
                Auth::login($user);
                return redirect()->route('dashboard')->with('success', 'Welcome back!');
            }

        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // If it's a web request, redirect back with error; if API, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Server error',
                    'message' => $e->getMessage(),
                ], 500);
            } else {
                return back()->withErrors(['email' => 'An error occurred. Please try again.'])->withInput();
            }
        }
    }
    
    /**
     * Show the verification form (web).
     */
    public function showVerification(Request $request)
    {
        \Log::info('📄 Verification page displayed', [
        'token' => session('verification_token'),
        'attempt_id' => session('login_attempt_id'),
        ]);
        
        $token = session('verification_token');
        $attemptId = session('login_attempt_id');
        $riskScore = session('risk_score');
        $reasons = session('reasons', []);
        
        if (!$token || !$attemptId) {
            return redirect()->route('login')->withErrors('Verification session expired. Please login again.');
        }
        
        return view('auth.verify', [
            'verification_token' => $token,
            'login_attempt_id' => $attemptId,
            'risk_score' => $riskScore,
            'reasons' => $reasons,
        ]);
    }
    
    /**
     * Handle the web verification form submission.
     */
    public function verifyWeb(Request $request)
    {
        $request->validate([
            'login_attempt_id' => 'required|exists:login_attempts,id',
            'verification_code' => 'required|string|size:6',
            'verification_token' => 'required|string',
        ]);
        
        $loginAttempt = LoginAttempt::findOrFail($request->login_attempt_id);
        $user = $loginAttempt->user;
        
        // Validate token
        if (!$this->validateVerificationToken($request->verification_token, $user, $loginAttempt)) {
            return back()->withErrors(['verification_code' => 'Invalid verification token.'])->withInput();
        }
        
        // Verify code
        $result = $this->verificationService->verifyCode(
            $user,
            $loginAttempt->id,
            $request->verification_code
        );
        
        if (!$result['success']) {
            return back()->withErrors(['verification_code' => $result['message']])->withInput();
        }
        
        // Mark suspicious activity as resolved
        $suspiciousActivity = SuspiciousActivity::where('activity_data->login_attempt_id', $loginAttempt->id)
            ->first();
        
        if ($suspiciousActivity) {
            $suspiciousActivity->update([
                'status' => SuspiciousActivity::STATUS_RESOLVED,
                'reviewed_at' => now(),
                'review_notes' => 'User verified via email 2FA',
            ]);
        }
        
        // Complete login
        $this->handleSuccessfulLogin($user, $loginAttempt);
        
        // Clear session data
        session()->forget(['verification_token', 'login_attempt_id', 'risk_score', 'reasons']);
        
        // Login the user
        Auth::login($user);
        
        return redirect()->route('dashboard')->with('success', 'Verification successful. You are now logged in.');
    }
    
    // ===== API verification endpoint (unchanged) =====
    public function verifyLogin(Request $request)
    {
        $request->validate([
            'login_attempt_id' => 'required|exists:login_attempts,id',
            'verification_code' => 'required|string|size:6',
            'verification_token' => 'required|string',
        ]);
        
        $loginAttempt = LoginAttempt::findOrFail($request->login_attempt_id);
        $user = $loginAttempt->user;
        
        // Validate token
        if (!$this->validateVerificationToken($request->verification_token, $user, $loginAttempt)) {
            return response()->json([
                'error' => 'Invalid verification token.',
            ], 401);
        }
        
        // Verify code
        $result = $this->verificationService->verifyCode(
            $user,
            $loginAttempt->id,
            $request->verification_code
        );
        
        if (!$result['success']) {
            return response()->json([
                'error' => $result['message'],
                'remaining_attempts' => $result['remaining_attempts'],
            ], 401);
        }
        
        // Mark suspicious activity as resolved
        $suspiciousActivity = SuspiciousActivity::where('activity_data->login_attempt_id', $loginAttempt->id)
            ->first();
        
        if ($suspiciousActivity) {
            $suspiciousActivity->update([
                'status' => SuspiciousActivity::STATUS_RESOLVED,
                'reviewed_at' => now(),
                'review_notes' => 'User verified via email 2FA',
            ]);
        }
        
        // Complete login
        $this->handleSuccessfulLogin($user, $loginAttempt);
        
        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user,
            'message' => 'Verification successful. You are now logged in.',
        ]);
    }
    
    // ===== Helper methods (unchanged) =====
    private function generateVerificationToken($user, $loginAttempt, $analysis): string
    {
        $data = [
            'user_id' => $user->id,
            'login_attempt_id' => $loginAttempt->id,
            'timestamp' => now()->timestamp,
        ];
        return hash('sha256', json_encode($data) . config('app.key'));
    }
    
    private function storePendingVerification($user, $loginAttempt, $token, $analysis): void
    {
        $file = storage_path('app/ai/models/pending_verifications.json');
        if (!file_exists($file)) {
            file_put_contents($file, json_encode(['tokens' => []]));
        }
        $pending = json_decode(file_get_contents($file), true) ?? ['tokens' => []];
        
        $pending['tokens'][$token] = [
            'user_id' => $user->id,
            'email' => $user->email,
            'username' => $user->name,
            'login_attempt_id' => $loginAttempt->id,
            'anomalies' => array_map(function($factor) {
                return $factor['factor'] ?? 'unknown';
            }, $analysis['factors']),
            'risk_score' => $analysis['risk_score'],
            'created_at' => now()->toISOString(),
            'expires_at' => now()->addMinutes(10)->toISOString(),
        ];
        
        file_put_contents($file, json_encode($pending, JSON_PRETTY_PRINT));
    }
    
    private function validateVerificationToken($token, $user, $loginAttempt): bool
    {
        $file = storage_path('app/ai/models/pending_verifications.json');
        if (!file_exists($file)) {
            return false;
        }
        
        $pending = json_decode(file_get_contents($file), true);
        if (!isset($pending['tokens'][$token])) {
            return false;
        }
        
        $data = $pending['tokens'][$token];
        
        // Check expiry
        if (now()->gt($data['expires_at'])) {
            unset($pending['tokens'][$token]);
            file_put_contents($file, json_encode($pending, JSON_PRETTY_PRINT));
            return false;
        }
        
        // Validate user and login attempt
        if ($data['user_id'] != $user->id || $data['login_attempt_id'] != $loginAttempt->id) {
            return false;
        }
        
        return true;
    }
    
    private function createSuspiciousActivity($user, $loginAttempt, $analysis): void
    {
        SuspiciousActivity::create([
            'user_id' => $user->id,
            'activity_type' => SuspiciousActivity::TYPE_LOGIN,
            'activity_data' => [
                'login_attempt_id' => $loginAttempt->id,
                'ip_address' => $loginAttempt->ip_address,
                'location' => "{$loginAttempt->city}, {$loginAttempt->country}",
                'device' => $loginAttempt->device_type,
                'risk_score' => $analysis['risk_score'],
                'factors' => $analysis['factors'],
                'brute_force' => $analysis['brute_force_detected'],
            ],
            'risk_score' => $analysis['risk_score'] / 100,
            'detection_reasons' => $analysis['factors'],
            'status' => SuspiciousActivity::STATUS_PENDING,
        ]);
    }
    
    private function handleBruteForceDetection($user, $loginAttempt): void
    {
        \Log::warning('Brute force attempt detected', [
            'user_id' => $user->id,
            'ip' => $loginAttempt->ip_address,
            'attempt_count' => LoginAttempt::where('user_id', $user->id)
                ->where('is_successful', false)
                ->where('attempted_at', '>=', now()->subMinutes(5))
                ->count(),
        ]);
        
        SuspiciousActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'brute_force_attempt',
            'activity_data' => [
                'ip_address' => $loginAttempt->ip_address,
                'attempt_count' => LoginAttempt::where('user_id', $user->id)
                    ->where('is_successful', false)
                    ->where('attempted_at', '>=', now()->subMinutes(5))
                    ->count(),
                'location' => "{$loginAttempt->city}, {$loginAttempt->country}",
            ],
            'risk_score' => 0.9,
            'detection_reasons' => ['Multiple failed login attempts detected', 'Potential brute force attack'],
            'status' => SuspiciousActivity::STATUS_PENDING,
        ]);
    }
    
    private function recordLoginAttempt($user, string $realIp, Request $request, $isSuccessful = false): LoginAttempt
    {
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
            'ip_address' => $loginAttempt->ip_address,
        ]);
    }
    
    private function clearFailedAttempts($user): void
    {
        // Your implementation (if any)
    }
    
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
        return $ip;
    }
    
    private function getLocationData(string $ip): ?array
    {
        try {
            $location = \Stevebauman\Location\Facades\Location::get($ip);
            if ($location) {
                return [
                    'country' => $location->countryName,
                    'city' => $location->cityName,
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
}