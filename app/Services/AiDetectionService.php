<?php

namespace App\Services;

use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AiDetectionService
{
    private $featureExtractor;
    private $modelPath;
    
    public function __construct(AiFeatureExtractor $featureExtractor)
    {
        $this->featureExtractor = $featureExtractor;
        $this->modelPath = storage_path('app/ai/models/predict.py');
    }
    
    public function analyzeLogin(User $user, Request $request): array
    {
        // 1. Extract features
        $features = $this->featureExtractor->extract($user, $request);
        
        // 2. Call Python model for prediction
        $result = $this->callPythonModel($features);
        
        // 3. Check brute force pattern
        $bruteForceDetected = $this->detectBruteForce($user);
        
        // 4. Combine results
        $isSuspicious = $result['is_suspicious'] || $bruteForceDetected;
        $riskScore = $result['risk_score'];
        
        // Boost risk score if brute force detected
        if ($bruteForceDetected) {
            $riskScore = max($riskScore, 80);
            $result['factors'][] = ['factor' => 'brute_force', 'value' => $bruteForceDetected, 'risk' => 1.0];
        }
        
        return [
            'risk_score' => $riskScore,
            'is_suspicious' => $isSuspicious,
            'factors' => $result['factors'] ?? [],
            'features' => $features,
            'model_score' => $result['decision_score'] ?? 0,
            'brute_force_detected' => $bruteForceDetected,
        ];
    }
    
    private function callPythonModel(array $features): array
    {
        try {
            // Prepare command
            $featureJson = json_encode($features);
            $command = sprintf(
                'python "%s" "%s" 2>&1',
                $this->modelPath,
                escapeshellarg($featureJson)
            );
            
            // Execute Python script
            $output = shell_exec($command);
            
            if ($output === null) {
                Log::error('Python model execution failed');
                return $this->getFallbackResult();
            }
            
            // Parse output
            $result = json_decode($output, true);
            
            if (!isset($result['risk_score'])) {
                Log::error('Invalid model output', ['output' => $output]);
                return $this->getFallbackResult();
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('AI model error: ' . $e->getMessage());
            return $this->getFallbackResult();
        }
    }
    
    private function detectBruteForce(User $user): bool
    {
        // Check for 4 failed attempts in last 5 minutes
        $failedAttempts = LoginAttempt::where('user_id', $user->id)
            ->where('is_successful', false)
            ->where('attempted_at', '>=', now()->subMinutes(5))
            ->count();
        
        // Also check for rapid attempts (within 2 minutes)
        $rapidAttempts = LoginAttempt::where('user_id', $user->id)
            ->where('attempted_at', '>=', now()->subMinutes(2))
            ->count();
        
        // If 4+ failures in 5 min OR 2+ attempts in 2 min
        if ($failedAttempts >= 4 || $rapidAttempts >= 2) {
            return true;
        }
        
        return false;
    }
    
    private function getFallbackResult(): array
    {
        return [
            'risk_score' => 50,
            'is_suspicious' => false,
            'factors' => [['factor' => 'model_unavailable', 'risk' => 0]],
            'decision_score' => 0,
        ];
    }
}