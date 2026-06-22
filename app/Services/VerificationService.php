<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoginAttempt;
use App\Models\VerificationAttempt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VerificationService
{
    public function sendVerificationCode(User $user, LoginAttempt $loginAttempt, array $analysis): void
    {
        $code = $this->generateVerificationCode();
        
        // Store verification data (expires in 10 minutes)
        $cacheKey = "verification:{$user->id}:{$loginAttempt->id}";
        Cache::put($cacheKey, [
            'code' => $code,
            'login_attempt_id' => $loginAttempt->id,
            'attempts' => 0,
            'max_attempts' => 3,
            'expires_at' => now()->addMinutes(10),
        ], 600); // 10 minutes
        
        // Queue verification email
        Mail::to($user->email)->send(new \App\Mail\LoginVerificationMail($user, $code, $loginAttempt, $analysis));
        
        // Log verification attempt
        VerificationAttempt::create([
            'user_id' => $user->id,
            'login_attempt_id' => $loginAttempt->id,
            'verification_method' => 'email_verification',
            'is_successful' => false, // Not yet verified
            'verification_data' => [
                'code_sent' => true,
                'expires_at' => now()->addMinutes(10)->toDateTimeString(),
            ],
            'verified_at' => now(),
        ]);
    }
    
    public function verifyCode(User $user, int $loginAttemptId, string $code): array
    {
        $cacheKey = "verification:{$user->id}:{$loginAttemptId}";
        $data = Cache::get($cacheKey);
        
        if (!$data) {
            return [
                'success' => false,
                'message' => 'Verification code expired or not found. Please try logging in again.',
                'remaining_attempts' => 0,
            ];
        }
        
        // Check if max attempts reached
        if ($data['attempts'] >= $data['max_attempts']) {
            Cache::forget($cacheKey);
            return [
                'success' => false,
                'message' => 'Too many verification attempts. Please try logging in again.',
                'remaining_attempts' => 0,
            ];
        }
        
        // Check expiry
        if (now()->gt($data['expires_at'])) {
            Cache::forget($cacheKey);
            return [
                'success' => false,
                'message' => 'Verification code has expired. Please try logging in again.',
                'remaining_attempts' => 0,
            ];
        }
        
        // Verify code
        if ($data['code'] !== $code) {
            $data['attempts']++;
            Cache::put($cacheKey, $data, 600);
            
            $remaining = $data['max_attempts'] - $data['attempts'];
            return [
                'success' => false,
                'message' => 'Invalid verification code.',
                'remaining_attempts' => $remaining,
            ];
        }
        
        // Code is valid - mark as verified
        Cache::forget($cacheKey);
        
        // Update verification attempt record
        VerificationAttempt::where('user_id', $user->id)
            ->where('login_attempt_id', $loginAttemptId)
            ->where('is_successful', false)
            ->update([
                'is_successful' => true,
                'verification_data' => [
                    'verified_at' => now()->toDateTimeString(),
                    'code' => $code,
                ],
            ]);
        
        return [
            'success' => true,
            'message' => 'Verification successful!',
            'remaining_attempts' => 0,
        ];
    }
    
    private function generateVerificationCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}