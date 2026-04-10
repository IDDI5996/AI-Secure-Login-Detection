<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoginAttempt;
use App\Models\UserBehaviorProfile;
use Illuminate\Support\Facades\Cache;
use Stevebauman\Location\Facades\Location;
use Illuminate\Http\Request;

class AiDetectionEngin
{
    private $riskFactors = [];
    private $riskScore = 0.0;
    private $detectionReasons = [];

    /**
     * Analyze login attempt with real client IP
     */
    public function analyzeLoginAttempt(User $user, Request $request, string $realClientIp = null): array
    {
        $this->resetAnalysis();
        
        // If real client IP not provided, try to extract from request
        if (!$realClientIp) {
            $realClientIp = $this->getRealClientIp($request);
        }
        
        // Get or create user behavior profile
        $profile = $this->getUserProfile($user);
        
        // Analyze various factors using real IP
        $this->analyzeLocation($user, $request, $profile, $realClientIp);
        $this->analyzeDevice($user, $request, $profile);
        $this->analyzeTimePattern($user, $profile);
        $this->analyzeVelocity($user);
        $this->analyzeIpReputation($request, $realClientIp);
        
        // Calculate final risk score (0.0 to 1.0)
        $this->calculateRiskScore();
        
        return [
            'risk_score' => $this->riskScore,
            'is_suspicious' => $this->riskScore >= 0.7,
            'detection_factors' => $this->riskFactors,
            'reasons' => $this->detectionReasons
        ];
    }

    /**
     * Extract real client IP from headers (Cloudflare, Render, etc.)
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

    private function analyzeLocation($user, $request, $profile, string $realClientIp): void
    {
        $location = $this->getLocationFromIp($realClientIp);
        
        $factorWeight = 0.3;
        $locationRisk = 0.0;
        
        if ($location) {
            // Check if location is in usual locations
            $isUsualLocation = $this->checkUsualLocation($profile, $location, $realClientIp);
            
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
        
        // For device fingerprint, we need to compare properly (usualDevices might be array of arrays)
        $isKnownDevice = false;
        if (!empty($usualDevices)) {
            foreach ($usualDevices as $device) {
                if (is_array($device) && isset($device['fingerprint']) && $device['fingerprint'] === $deviceFingerprint) {
                    $isKnownDevice = true;
                    break;
                } elseif (is_string($device) && $device === $deviceFingerprint) {
                    $isKnownDevice = true;
                    break;
                }
            }
        }
        
        if (!empty($usualDevices) && !$isKnownDevice) {
            $deviceRisk = 0.7;
            $this->detectionReasons[] = "Login from unrecognized device";
            
            // Check if device type is different
            if (!empty($usualDevices)) {
                $lastDevice = is_array($usualDevices[0]) ? $usualDevices[0] : ['user_agent' => ''];
                if ($this->parseDeviceType($lastDevice['user_agent'] ?? '') !== $deviceType) {
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

    private function analyzeIpReputation($request, string $realClientIp): void
    {
        $factorWeight = 0.1;
        $reputationRisk = 0.0;
        
        // Check if IP is from known VPN/Tor/proxy using real IP
        $isSuspiciousIp = $this->checkIpReputation($realClientIp);
        
        if ($isSuspiciousIp) {
            $reputationRisk = 1.0;
            $this->detectionReasons[] = "Login from suspicious IP (VPN/Proxy/Tor)";
        }
        
        // Check if IP is from high-risk country
        $location = $this->getLocationFromIp($realClientIp);
        if ($location && $this->isHighRiskCountry($location['country'])) {
            $reputationRisk = max($reputationRisk, 0.8);
            $this->detectionReasons[] = "Login from high-risk country";
        }
        
        $this->riskFactors['ip_reputation'] = [
            'weight' => $factorWeight,
            'risk' => $reputationRisk,
            'data' => [
                'ip' => $realClientIp,
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

    private function resetAnalysis(): void
    {
        $this->riskFactors = [];
        $this->riskScore = 0.0;
        $this->detectionReasons = [];
    }

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
            $request->ip() // This is fine for device fingerprinting, not for location
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
                \Log::error("Location lookup failed for IP {$ip}: " . $e->getMessage());
            }
            return null;
        });
    }

    private function checkIpReputation(string $ip): bool
    {
        // Implement IP reputation check
        $vpnRanges = [
            '103.21.244.0/22',
            '103.22.200.0/22',
        ];
        
        foreach ($vpnRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }
        
        return false;
    }

    private function ipInRange(string $ip, string $range): bool
    {
        list($subnet, $mask) = explode('/', $range);
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = ~((1 << (32 - $mask)) - 1);
        return ($ipLong & $maskLong) == ($subnetLong & $maskLong);
    }

    private function checkUsualLocation($profile, $location, string $ip): bool
    {
        $usualLocations = $profile->usual_locations ?? [];
        if (empty($usualLocations)) {
            return true; // First login, treat as usual
        }
        
        $ipRange = substr($ip, 0, strrpos($ip, '.'));
        foreach ($usualLocations as $usual) {
            if (isset($usual['ip_range']) && $usual['ip_range'] === $ipRange) {
                return true;
            }
            if (isset($usual['country']) && $usual['country'] === $location['country'] &&
                isset($usual['city']) && $usual['city'] === $location['city']) {
                return true;
            }
        }
        return false;
    }

    private function updateDeviceProfile($profile, string $fingerprint, array $deviceInfo): void
    {
        $devices = $profile->device_fingerprints ?? [];
        $devices[] = array_merge(['fingerprint' => $fingerprint], $deviceInfo);
        $profile->device_fingerprints = $devices;
        $profile->save();
    }

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

    private function isHighRiskCountry(string $country): bool
    {
        $highRisk = ['CN', 'RU', 'KP', 'IR', 'SY', 'CU', 'VE'];
        return in_array(strtoupper($country), $highRisk);
    }

    private function getMlAdjustment(): float
    {
        return 0.0;
    }
}