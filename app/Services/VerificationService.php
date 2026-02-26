<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Notifications\TwoFactorCodeNotification;

/**
 * Description of VerificationService
 *
 * @author IDRISAH
 */
class VerificationService {
    public function sendTwoFactorCode(User $user): bool
    {
        $code = $this->generateTwoFactorCode();
        
        Cache::put("2fa:{$user->id}", [
            'code' => $code,
            'expires_at' => now()->addMinutes(10)
        ], 600);
        
        $user->notify(new TwoFactorCodeNotification($code));
        
        return true;
    }
    
    public function verifyTwoFactorCode(User $user, string $code): bool
    {
        $stored = Cache::get("2fa:{$user->id}");
        
        if (!$stored || $stored['code'] !== $code) {
            return false;
        }
        
        if (now()->gt($stored['expires_at'])) {
            Cache::forget("2fa:{$user->id}");
            return false;
        }
        
        Cache::forget("2fa:{$user->id}");
        return true;
    }
    
    public function sendEmailVerification(User $user, string $ip): bool
    {
        $token = Str::random(64);
        
        Cache::put("email_verify:{$token}", [
            'user_id' => $user->id,
            'ip' => $ip,
            'expires_at' => now()->addMinutes(30)
        ], 1800);
        
        $user->notify(new EmailVerificationNotification($token, $ip));
        
        return true;
    }
    
    public function verifyEmailToken(string $token, string $ip): ?User
    {
        $data = Cache::get("email_verify:{$token}");
        
        if (!$data || $data['ip'] !== $ip) {
            return null;
        }
        
        if (now()->gt($data['expires_at'])) {
            Cache::forget("email_verify:{$token}");
            return null;
        }
        
        $user = User::find($data['user_id']);
        Cache::forget("email_verify:{$token}");
        
        return $user;
    }
    
    public function generateSecurityQuestions(User $user): array
    {
        $questions = $user->security_questions ?? [];
        
        if (empty($questions)) {
            $questions = $this->getDefaultQuestions();
            $user->update(['security_questions' => $questions]);
        }
        
        // Return random subset of questions
        return collect($questions)->random(2)->map(function ($q) {
            return [
                'id' => $q['id'],
                'question' => $q['question']
            ];
        })->toArray();
    }
    
    public function verifySecurityQuestions(User $user, array $answers): bool
    {
        $questions = $user->security_questions ?? [];
        
        foreach ($answers as $answer) {
            $question = collect($questions)->firstWhere('id', $answer['question_id']);
            
            if (!$question || !Hash::check($answer['answer'], $question['answer_hash'])) {
                return false;
            }
        }
        
        return true;
    }
    
    private function generateTwoFactorCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    private function getDefaultQuestions(): array
    {
        return [
            [
                'id' => 1,
                'question' => 'What is your mother\'s maiden name?',
                'answer_hash' => null
            ],
            [
                'id' => 2,
                'question' => 'What was the name of your first pet?',
                'answer_hash' => null
            ],
            [
                'id' => 3,
                'question' => 'What city were you born in?',
                'answer_hash' => null
            ]
        ];
    }
}
