<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Services;

use App\Models\User;
use App\Models\LoginAttempt;
use App\Models\UserBehaviorProfile;
use Illuminate\Support\Facades\Cache;
use Stevebauman\Location\Facades\Location;
use Illuminate\Http\Request;
/**
 * Description of AiDetectionEngin
 *
 * @author IDRISAH
 */
class AiDetectionEngin {
    private $riskFactors = [];
    private $riskScore = 0.0;
    private $detectionReasons = [];

    public function analyzeLoginAttempt(User $user, Request $request): array
    {
        $this->resetAnalysis();
        
        // Get or create user behavior profile
        $profile = $this->getUserProfile($user);
        
        // Analyze various factors
        $this->analyzeLocation($user, $request, $profile);
        $this->analyzeDevice($user, $request, $profile);
        $this->analyzeTimePattern($user, $profile);
        $this->analyzeVelocity($user);
        $this->analyzeIpReputation($request);
        
        // Calculate final risk score (0.0 to 1.0)
        $this->calculateRiskScore();
        
        return [
            'risk_score' => $this->riskScore,
            'is_suspicious' => $this->riskScore >= 0.7,
            'detection_factors' => $this->riskFactors,
            'reasons' => $this->detectionReasons
        ];
    }

    private function analyzeLocation($user, $request, $profile): void
    {
        $ip = $request->ip();
        $location = $this->getLocationFromIp($ip);
        
        $factorWeight = 0.3;
        $locationRisk = 0.0;
        
        if ($location) {
            // Check if location is in usual locations
            $isUsualLocation = $this->checkUsualLocation($profile, $location, $ip);
            
            if (!$isUsualLocation) {
                $locationRisk = 0.8;
                $this->detectionReasons[] = "Login from unusual location: {$location['city']}, {$location['country']}";
                
                // Check distance from last known location (if available)
                if (!empty($profile->usual_locations)) {
                    $lastLocation = end($profile->usual_locations);
                    if (isset($lastLocation['country']) && $lastLocation['country'] !== $location['country']) {
                        $locationRisk = 1.0;
                        $this->detectionReasons[] = "Login from different country";
                    }
                }
            }
        }
        
        $this->riskFactors['location'] = [
            'weight' => $factorWeight,
            'risk' => $locationRisk,
            'data' => $location
        ];
    }

    private function analyzeDevice($user, $request, $profile): void
    {
        $userAgent = $request->userAgent();
        $deviceFingerprint = $this->generateDeviceFingerprint($request);
        
        $factorWeight = 0.25;
        $deviceRisk = 0.0;
        
        // Parse user agent
        $browser = $this->parseBrowser($userAgent);
        $platform = $this->parsePlatform($userAgent);
        $deviceType = $this->parseDeviceType($userAgent);
        
        // Check against usual devices
        $usualDevices = $profile->device_fingerprints ?? [];
        
        if (!empty($usualDevices) && !in_array($deviceFingerprint, $usualDevices)) {
            $deviceRisk = 0.7;
            $this->detectionReasons[] = "Login from unrecognized device";
            
            // Check if device type is different
            if (!empty($usualDevices)) {
                $lastDevice = end($usualDevices);
                if ($this->parseDeviceType($lastDevice['user_agent']) !== $deviceType) {
                    $deviceRisk = 0.9;
                    $this->detectionReasons[] = "Login from different device type";
                }
            }
        }
        
        // If first login, add to devices
        if (empty($usualDevices)) {
            $this->updateDeviceProfile($profile, $deviceFingerprint, [
                'browser' => $browser,
                'platform' => $platform,
                'device_type' => $deviceType,
                'user_agent' => $userAgent
            ]);
        }
        
        $this->riskFactors['device'] = [
            'weight' => $factorWeight,
            'risk' => $deviceRisk,
            'data' => [
                'fingerprint' => $deviceFingerprint,
                'browser' => $browser,
                'platform' => $platform,
                'device_type' => $deviceType
            ]
        ];
    }

    private function analyzeTimePattern($user, $profile): void
    {
        $currentHour = now()->hour;
        $currentDay = now()->dayOfWeek;
        
        $factorWeight = 0.15;
        $timeRisk = 0.0;
        
        $usualTimes = $profile->usual_times ?? ['hour_ranges' => [], 'days' => []];
        
        // Check if current time is unusual
        if (!empty($usualTimes['hour_ranges'])) {
            $isUsualHour = false;
            foreach ($usualTimes['hour_ranges'] as $hourRange) {
                if (abs($currentHour - $hourRange) <= 2) { // Within 2 hours of usual time
                    $isUsualHour = true;
                    break;
                }
            }
            
            if (!$isUsualHour) {
                $timeRisk = 0.6;
                $this->detectionReasons[] = "Login at unusual hour";
            }
        }
        
        // Check if current day is unusual
        if (!empty($usualTimes['days']) && !in_array($currentDay, $usualTimes['days'])) {
            $timeRisk = max($timeRisk, 0.5);
            $this->detectionReasons[] = "Login on unusual day";
        }
        
        $this->riskFactors['time_pattern'] = [
            'weight' => $factorWeight,
            'risk' => $timeRisk,
            'data' => [
                'current_hour' => $currentHour,
                'current_day' => $currentDay,
                'usual_times' => $usualTimes
            ]
        ];
    }

    private function analyzeVelocity($user): void
    {
        // Check for rapid login attempts
        $recentAttempts = LoginAttempt::where('user_id', $user->id)
            ->where('attempted_at', '>=', now()->subMinutes(10))
            ->count();
        
        $factorWeight = 0.2;
        $velocityRisk = 0.0;
        
        if ($recentAttempts > 3) {
            $velocityRisk = min(1.0, $recentAttempts * 0.2);
            $this->detectionReasons[] = "Multiple login attempts in short period";
        }
        
        $this->riskFactors['velocity'] = [
            'weight' => $factorWeight,
            'risk' => $velocityRisk,
            'data' => ['recent_attempts' => $recentAttempts]
        ];
    }

    private function analyzeIpReputation($request): void
    {
        $ip = $request->ip();
        $factorWeight = 0.1;
        $reputationRisk = 0.0;
        
        // Check if IP is from known VPN/Tor/proxy
        $isSuspiciousIp = $this->checkIpReputation($ip);
        
        if ($isSuspiciousIp) {
            $reputationRisk = 1.0;
            $this->detectionReasons[] = "Login from suspicious IP (VPN/Proxy/Tor)";
        }
        
        // Check if IP is from high-risk country
        $location = $this->getLocationFromIp($ip);
        if ($location && $this->isHighRiskCountry($location['country'])) {
            $reputationRisk = max($reputationRisk, 0.8);
            $this->detectionReasons[] = "Login from high-risk country";
        }
        
        $this->riskFactors['ip_reputation'] = [
            'weight' => $factorWeight,
            'risk' => $reputationRisk,
            'data' => [
                'ip' => $ip,
                'is_suspicious' => $isSuspiciousIp,
                'country_risk' => $location['country'] ?? 'unknown'
            ]
        ];
    }

    private function calculateRiskScore(): void
    {
        $weightedSum = 0.0;
        $totalWeight = 0.0;
        
        foreach ($this->riskFactors as $factor) {
            $weightedSum += $factor['risk'] * $factor['weight'];
            $totalWeight += $factor['weight'];
        }
        
        // Normalize to 0-1 range
        $this->riskScore = $totalWeight > 0 ? $weightedSum / $totalWeight : 0.0;
        
        // Apply machine learning adjustment if available
        $mlAdjustment = $this->getMlAdjustment();
        $this->riskScore = min(1.0, max(0.0, $this->riskScore + $mlAdjustment));
    }

    // Helper methods
    private function getUserProfile(User $user): UserBehaviorProfile
    {
        return UserBehaviorProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['login_count' => 0]
        );
    }

    private function generateDeviceFingerprint(Request $request): string
    {
        $components = [
            $request->userAgent(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
            $request->ip()
        ];
        
        return hash('sha256', implode('|', $components));
    }

    private function getLocationFromIp(string $ip): ?array
    {
        return Cache::remember("location_{$ip}", 3600, function () use ($ip) {
            try {
                $location = Location::get($ip);
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
                // Log error
                \Log::error("Location lookup failed for IP {$ip}: " . $e->getMessage());
            }
            return null;
        });
    }

    private function checkIpReputation(string $ip): bool
    {
        // Implement IP reputation check
        // You can integrate with services like AbuseIPDB, IPQualityScore, etc.
        // For now, checking common VPN/proxy patterns
        $vpnRanges = [
            '103.21.244.0/22',
            '103.22.200.0/22',
            // Add more ranges
        ];
        
        foreach ($vpnRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }
        
        return false;
    }

    private function getMlAdjustment(): float
    {
        // Placeholder for ML model integration
        // Could use TensorFlow PHP, PyTorch via API, or custom algorithm
        return 0.0;
    }
}
