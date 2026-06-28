<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoginAttempt;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AiFeatureExtractor
{
     /**
     * Extract features for a given user and request.
     *
     * @param User $user
     * @param Request $request
     * @param Carbon|string|null $timestamp  Optional timestamp for historical analysis
     * @return array
     */
    public function extract(User $user, Request $request, $timestamp = null): array
    {
        $now = $timestamp ? Carbon::parse($timestamp) : Carbon::now();
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        // Parse location from IP (cached)
        $location = $this->getLocationFromIp($ip);

        // Get user's recent attempts (last 24 hours)
        $recentAttempts = LoginAttempt::where('user_id', $user->id)
            ->where('attempted_at', '>=', $now->copy()->subHours(24))
            ->get();

        $totalAttempts = $recentAttempts->count();
        $failedAttempts = $recentAttempts->where('is_successful', false)->count();

        // Calculate features
        $features = [
            'status' => 1, // login attempt
            'hour' => (int) $now->format('H'),
            'day_of_week' => (int) $now->format('N'),
            'minute' => (int) $now->format('i'),
            'is_weekend' => $now->isWeekend() ? 1 : 0,
            'login_count_24h' => $totalAttempts,
            'failed_count_24h' => $failedAttempts,
            'fail_rate_24h' => $totalAttempts > 0 ? $failedAttempts / $totalAttempts : 0,
            'unique_ips_24h' => $recentAttempts->pluck('ip_address')->unique()->count(),
            'unique_countries_24h' => $recentAttempts->pluck('country')->unique()->count(),
            'unique_devices_24h' => $recentAttempts->pluck('device_type')->unique()->count(),
            'ip_freq' => LoginAttempt::where('ip_address', $ip)->count(),
            'country_freq' => LoginAttempt::where('country', $location['country'] ?? 'Unknown')->count(),
            'device_type_freq' => LoginAttempt::where('device_type', $this->parseDeviceType($userAgent))->count(),
            'browser_freq' => LoginAttempt::where('browser', $this->parseBrowser($userAgent))->count(),
        ];

        return $features;
    }
    
    private function getLocationFromIp(string $ip): array
    {
        try {
            $location = \Stevebauman\Location\Facades\Location::get($ip);
            return [
                'country' => $location->countryName ?? 'Unknown',
                'city' => $location->cityName ?? 'Unknown',
            ];
        } catch (\Exception $e) {
            return ['country' => 'Unknown', 'city' => 'Unknown'];
        }
    }
    
    private function parseDeviceType(string $userAgent): string
    {
        if (str_contains($userAgent, 'Mobile')) {
            return 'Mobile';
        }
        if (str_contains($userAgent, 'Tablet')) {
            return 'Tablet';
        }
        return 'Desktop';
    }
    
    private function parseBrowser(string $userAgent): string
    {
        if (str_contains($userAgent, 'Chrome') && !str_contains($userAgent, 'Edg')) {
            return 'Chrome';
        }
        if (str_contains($userAgent, 'Firefox')) {
            return 'Firefox';
        }
        if (str_contains($userAgent, 'Safari') && !str_contains($userAgent, 'Chrome')) {
            return 'Safari';
        }
        if (str_contains($userAgent, 'Edg')) {
            return 'Edge';
        }
        return 'Unknown';
    }
}