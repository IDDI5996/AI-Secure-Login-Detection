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
     * Analyze a login attempt using the real client IP.
     *
     * @param User|null $user  Can be null for unknown users (failed attempts)
     * @param Request $request
     * @param string|null $realIp
     */
    public function analyzeLoginAttempt(?User $user = null, Request $request, ?string $realIp = null): array
    {
        $this->resetAnalysis();

        // Use provided real IP, fallback to request IP if not given
        $clientIp = $realIp ?? $request->ip();

        // If user is unknown (e.g., failed login for non-existent email)
        if (!$user) {
            // Only IP reputation analysis can be performed without user data
            $this->analyzeIpReputation($clientIp);
            $this->calculateRiskScore();

            return [
                'risk_score' => $this->riskScore,
                'is_suspicious' => $this->riskScore >= 0.7,
                'detection_factors' => $this->riskFactors,
                'reasons' => $this->detectionReasons
            ];
        }

        // Known user – full analysis
        $profile = $this->getUserProfile($user);

        $this->analyzeLocation($user, $clientIp, $profile);
        $this->analyzeDevice($user, $request, $profile);
        $this->analyzeTimePattern($user, $profile);
        $this->analyzeVelocity($user);
        $this->analyzeIpReputation($clientIp);

        $this->calculateRiskScore();

        return [
            'risk_score' => $this->riskScore,
            'is_suspicious' => $this->riskScore >= 0.7,
            'detection_factors' => $this->riskFactors,
            'reasons' => $this->detectionReasons
        ];
    }

    private function analyzeLocation($user, string $ip, $profile): void
    {
        $location = $this->getLocationFromIp($ip);

        $factorWeight = 0.3;
        $locationRisk = 0.0;

        if ($location) {
            $isUsualLocation = $this->checkUsualLocation($profile, $location, $ip);

            if (!$isUsualLocation) {
                $locationRisk = 0.8;
                $this->detectionReasons[] = "Login from unusual location: {$location['city']}, {$location['country']}";

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

        $browser = $this->parseBrowser($userAgent);
        $platform = $this->parsePlatform($userAgent);
        $deviceType = $this->parseDeviceType($userAgent);

        $usualDevices = $profile->device_fingerprints ?? [];

        if (!empty($usualDevices) && !in_array($deviceFingerprint, $usualDevices)) {
            $deviceRisk = 0.7;
            $this->detectionReasons[] = "Login from unrecognized device";

            if (!empty($usualDevices)) {
                $lastDevice = end($usualDevices);
                if (($lastDevice['device_type'] ?? '') !== $deviceType) {
                    $deviceRisk = 0.9;
                    $this->detectionReasons[] = "Login from different device type";
                }
            }
        }

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

        if (!empty($usualTimes['hour_ranges'])) {
            $isUsualHour = false;
            foreach ($usualTimes['hour_ranges'] as $hourRange) {
                if (abs($currentHour - $hourRange) <= 2) {
                    $isUsualHour = true;
                    break;
                }
            }
            if (!$isUsualHour) {
                $timeRisk = 0.6;
                $this->detectionReasons[] = "Login at unusual hour";
            }
        }

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

    private function analyzeIpReputation(string $ip): void
    {
        $factorWeight = 0.1;
        $reputationRisk = 0.0;

        $isSuspiciousIp = $this->checkIpReputation($ip);

        if ($isSuspiciousIp) {
            $reputationRisk = 1.0;
            $this->detectionReasons[] = "Login from suspicious IP (VPN/Proxy/Tor)";
        }

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

        $this->riskScore = $totalWeight > 0 ? $weightedSum / $totalWeight : 0.0;
        $mlAdjustment = $this->getMlAdjustment();
        $this->riskScore = min(1.0, max(0.0, $this->riskScore + $mlAdjustment));
    }

    // ========== Helper Methods ==========
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
                \Log::error("Location lookup failed for IP {$ip}: " . $e->getMessage());
            }
            return null;
        });
    }

    private function checkUsualLocation($profile, array $location, string $ip): bool
    {
        $usual = $profile->usual_locations ?? [];
        $ipRange = substr($ip, 0, strrpos($ip, '.'));
        foreach ($usual as $loc) {
            if (($loc['ip_range'] ?? '') === $ipRange) {
                return true;
            }
        }
        return false;
    }

    private function updateDeviceProfile($profile, string $fingerprint, array $data): void
    {
        $devices = $profile->device_fingerprints ?? [];
        $devices[] = array_merge($data, ['fingerprint' => $fingerprint, 'last_used' => now()->toDateTimeString()]);
        $profile->device_fingerprints = $devices;
        $profile->save();
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

    private function checkIpReputation(string $ip): bool
    {
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

    private function ipInRange(string $ip, string $cidr): bool
    {
        list($subnet, $mask) = explode('/', $cidr);
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = ~((1 << (32 - $mask)) - 1);
        return ($ipLong & $maskLong) == ($subnetLong & $maskLong);
    }

    private function isHighRiskCountry(string $country): bool
    {
        $highRisk = ['CN', 'RU', 'KP', 'IR', 'SY'];
        return in_array($country, $highRisk);
    }

    private function getMlAdjustment(): float
    {
        return 0.0;
    }
}