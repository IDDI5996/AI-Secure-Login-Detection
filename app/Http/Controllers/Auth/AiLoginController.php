<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoginAttempt;
use App\Models\SuspiciousActivity;
use App\Models\VerificationAttempt;
use App\Services\AiDetectionEngine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Notifications\SuspiciousLoginNotification;
use App\Notifications\VerificationRequiredNotification;

class AiLoginController extends Controller
{
    protected $aiEngine;
    
    public function __construct(AiDetectionEngine $aiEngine)
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
        
        // Record login attempt before authentication
        $loginAttempt = $this->recordLoginAttempt($user, $request, false);
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Invalid credentials
            $loginAttempt->update(['is_successful' => false]);
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        // Analyze login with AI
        $analysis = $this->aiEngine->analyzeLoginAttempt($user, $request);
        
        // Update login attempt with analysis
        $loginAttempt->update([
            'is_successful' => true,
            'is_suspicious' => $analysis['is_suspicious'],
            'risk_score' => $analysis['risk_score'],
            'detection_factors' => $analysis['detection_factors']
        ]);
        
        // If suspicious, require verification
        if ($analysis['is_suspicious']) {
            $this->handleSuspiciousLogin($user, $loginAttempt, $analysis);
            
            return response()->json([
                'requires_verification' => true,
                'verification_methods' => $this->getAvailableVerificationMethods($user),
                'risk_score' => $analysis['risk_score'],
                'reasons' => $analysis['reasons']
            ]);
        }
        
        // Normal login
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
            'remaining_attempts' => 2 // Implement attempt counter
        ], 401);
    }
    
    private function recordLoginAttempt($user, Request $request, $isSuccessful = false): LoginAttempt
    {
        $location = $this->getLocationData($request->ip());
        
        return LoginAttempt::create([
            'user_id' => $user?->id,
            'ip_address' => $request->ip(),
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
        // Create suspicious activity record
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
        
        // Send notifications
        $user->notify(new SuspiciousLoginNotification($loginAttempt));
        $user->notify(new VerificationRequiredNotification());
        
        // Notify admin panel members
        $this->notifyAdmins($user, $loginAttempt, $analysis);
        
        // Update user behavior profile
        $this->updateBehaviorProfile($user, $loginAttempt);
    }
    
    private function handleSuccessfulLogin($user, $loginAttempt): void
    {
        // Update user behavior profile
        $this->updateBehaviorProfile($user, $loginAttempt);
        
        // Clear failed attempts counter
        $this->clearFailedAttempts($user);
        
        // Log login
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
}
